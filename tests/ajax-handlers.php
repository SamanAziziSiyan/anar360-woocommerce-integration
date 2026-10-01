<?php
/** Lightweight behavioral tests for the admin AJAX boundary, without WordPress bootstrap. */
declare(strict_types=1);

const WEEK_IN_SECONDS = 604800;
$GLOBALS['hooks'] = [];
$GLOBALS['authorized'] = false;
$GLOBALS['nonce_valid'] = false;
$GLOBALS['transients'] = [];
$GLOBALS['api_result'] = null;
$GLOBALS['created'] = [];
$GLOBALS['creation_ok'] = true;

final class JsonResponse extends Exception
{
    public function __construct(public array $body, public int $status = 200)
    {
        parent::__construct();
    }
}

function add_action($name, $callback): void { $GLOBALS['hooks'][$name] = $callback; }
function current_user_can($cap): bool { return $cap === 'manage_options' && $GLOBALS['authorized']; }
function check_ajax_referer($action, $field, $die): bool { return $GLOBALS['nonce_valid']; }
function wp_unslash($value) { return $value; }
function wp_send_json_error($body, $status = 200): never { throw new JsonResponse(['success' => false, 'data' => $body], $status); }
function wp_send_json($body): never { throw new JsonResponse($body); }
function set_transient($key, $value, $duration): void { $GLOBALS['transients'][$key] = $value; }
function get_transient($key) { return $GLOBALS['transients'][$key] ?? false; }
function admin_url($path): string { return 'http://localhost/wp-admin/' . $path; }
function awca_save_activation_key(): bool { return true; }
function awca_check_activation_state(): bool { return true; }
function awca_combine_cats_arrays($one, $two): array { return array_map(fn($a, $b) => ['anarCat' => $a, 'wooCat' => $b], $one, $two); }
function awca_combine_attributes_arrays($one, $two): array { return array_map(fn($a, $b) => ['anarAttr' => $a, 'wooAttr' => $b], $one, $two); }
function awca_get_data_from_api($url) { return $GLOBALS['api_result']; }
function awca_prepare_results_for_product($item): array {
    return ['name' => 'Sample', 'regular_price' => '100', 'description' => '', 'image' => '', 'categories' => ['source-cat'], 'stock_quantity' => 1, 'gallery_images' => [], 'attributes' => [['name' => 'source-attr', 'values' => []]], 'variants' => []];
}
function awca_create_woocommerce_product($data, $categories, $attributes): int|false {
    $GLOBALS['created'][] = ['data' => $data, 'categories' => $categories, 'attributes' => $attributes];
    return $GLOBALS['creation_ok'] ? 42 : false;
}

require dirname(__DIR__) . '/includes/anar-ajax-request.php';

function response(string $handler, bool $authorized, bool $nonce, array $post = []): JsonResponse
{
    $GLOBALS['authorized'] = $authorized;
    $GLOBALS['nonce_valid'] = $nonce;
    $_POST = $post;
    try {
        $handler();
    } catch (JsonResponse $response) {
        return $response;
    }
    throw new RuntimeException('Handler failed to send JSON: ' . $handler);
}
function check(bool $ok, string $message): void {
    if (!$ok) throw new RuntimeException($message);
    echo "PASS $message\n";
}

foreach (['awca_handle_token_activation_ajax', 'awca_handle_pair_categories_ajax', 'awca_handle_product_creation_ajax'] as $handler) {
    check(isset($GLOBALS['hooks']['wp_ajax_' . $handler]), "$handler registered for authenticated users");
    check(!isset($GLOBALS['hooks']['wp_ajax_nopriv_' . $handler]), "$handler has no anonymous action");
    check(response($handler, false, true)->status === 403, "$handler rejects anonymous/unauthorized user");
    check(response($handler, true, false)->status === 403, "$handler rejects invalid nonce");
}

check(response('awca_handle_token_activation_ajax', true, true)->body['success'] === true, 'authorized activation succeeds');
check(response('awca_handle_pair_categories_ajax', true, true, ['anar-cats' => 'bad', 'product_categories' => []])->status === 400, 'invalid category input rejected');
$categories = ['anar-cats' => ['source-cat'], 'product_categories' => ['woo-cat']];
check(response('awca_handle_pair_categories_ajax', true, true, $categories)->body['success'] === true, 'authorized mapping saved');
check(get_transient('_anar_api_categories_transient') === ['source-cat'], 'authorized mapping persisted');
check(response('awca_handle_product_creation_ajax', true, true, ['anar-atts' => 'bad'])->status === 400, 'invalid attribute input rejected');
$attributes = ['anar-atts' => ['source-attr'], 'product_attributes' => ['woo-attr']];
check(response('awca_handle_product_creation_ajax', true, true, $attributes)->body['success'] === false, 'external API failure returns error');
$GLOBALS['api_result'] = (object) ['items' => [(object) ['id' => 1]]];
$result = response('awca_handle_product_creation_ajax', true, true, $attributes);
check($result->body['success'] === true && count($GLOBALS['created']) === 1, 'authorized product request creates product');
check($GLOBALS['created'][0]['data']['categories'] === ['source-cat'], 'product helper receives source categories');
check($GLOBALS['created'][0]['categories'][0]['wooCat'] === 'woo-cat', 'product helper receives category mapping');
check($GLOBALS['created'][0]['attributes'][0]['wooAttr'] === 'woo-attr', 'product helper receives attribute mapping');
$GLOBALS['creation_ok'] = false;
check(response('awca_handle_product_creation_ajax', true, true, $attributes)->status === 502, 'product mapping/creation failure reported');
