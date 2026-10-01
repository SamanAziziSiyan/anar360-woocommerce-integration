<?php
add_action('wp_ajax_awca_handle_token_activation_ajax', 'awca_handle_token_activation_ajax');

function awca_handle_token_activation_ajax()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Not authorized.'), 403);
    }
    if (!check_ajax_referer('awca_handle_token_activation_ajax_nonce', 'awca_handle_token_activation_ajax_field', false)) {
        wp_send_json_error(array('message' => 'Invalid request nonce.'), 403);
    }
    $activation = awca_save_activation_key();
    if ($activation) {
        $activation_status = awca_check_activation_state();
        if ($activation_status) {
            $response = array(
                'success' => true,
                'message' => 'توکن شما با موفقیت ثبت و تایید شد',
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'توکن شما از سمت انار تایید نشد',
            );
        }
    } else {
        $response = array(
            'status' => false,
            'activation_status' => true,
        );
    }
    wp_send_json($response);
}

add_action('wp_ajax_awca_handle_pair_categories_ajax', 'awca_handle_pair_categories_ajax');

function awca_handle_pair_categories_ajax()
{
    if (!current_user_can('manage_options') || !check_ajax_referer('awca_handle_pair_categories_ajax_nonce', 'awca_handle_pair_categories_ajax_field', false)) {
        wp_send_json_error(array('message' => 'Not authorized.'), 403);
    }
    $anarCats = isset($_POST['anar-cats']) ? wp_unslash($_POST['anar-cats']) : null;
    $wooCats = isset($_POST['product_categories']) ? wp_unslash($_POST['product_categories']) : null;
    if (!is_array($anarCats) || !is_array($wooCats) || count($anarCats) !== count($wooCats)) {
        wp_send_json_error(array('message' => 'Invalid category mapping.'), 400);
    }
    foreach (array_merge($anarCats, $wooCats) as $value) {
        if (!is_scalar($value)) {
            wp_send_json_error(array('message' => 'Invalid category mapping.'), 400);
        }
    }

    set_transient('_anar_api_categories_transient', $anarCats, WEEK_IN_SECONDS);
    set_transient('_anar_woocomerce_categories_transient', $wooCats, WEEK_IN_SECONDS);

    $response = array(
        'success' => true,
        'anar' =>  get_transient('_anar_api_categories_transient'),
        'woo' => get_transient('_anar_woocomerce_categories_transient'),
        'message' => 'معادل سازی دسته بندی ها با موفقیت ذخیره شد'
    );
    wp_send_json($response);
}

add_action('wp_ajax_awca_handle_product_creation_ajax', 'awca_handle_product_creation_ajax');

function awca_handle_product_creation_ajax()
{
    if (!current_user_can('manage_options') || !check_ajax_referer('awca_handle_product_creation_ajax_nonce', 'awca_handle_product_creation_ajax_field', false)) {
        wp_send_json_error(array('message' => 'Not authorized.'), 403);
    }
    $anarCats = get_transient('_anar_api_categories_transient');
    $wooCats = get_transient('_anar_woocomerce_categories_transient');
    $anarAttr = isset($_POST['anar-atts']) ? wp_unslash($_POST['anar-atts']) : null;
    $wooAttr = isset($_POST['product_attributes']) ? wp_unslash($_POST['product_attributes']) : null;
    if (!is_array($anarAttr) || !is_array($wooAttr) || count($anarAttr) !== count($wooAttr)) {
        wp_send_json_error(array('message' => 'Invalid attribute mapping.'), 400);
    }
    foreach (array_merge($anarAttr, $wooAttr) as $value) {
        if (!is_scalar($value)) {
            wp_send_json_error(array('message' => 'Invalid attribute mapping.'), 400);
        }
    }

    if ($anarCats !== false && $wooCats !== false) {
        $combinedCategories = awca_combine_cats_arrays($anarCats, $wooCats);
    } else {
        $response = array(
            'success' => false,
            'message' => 'ابتدا نیاز هست دسته بندی ها را در تب قبلی معادل سازی کنید',
        );
        wp_send_json_error($response);
    }


    $combinedattributes = awca_combine_attributes_arrays($anarAttr, $wooAttr);

    $responses = [];
    $prepared_products = [];
    $product_creation = [];

    $api_url = 'https://api.anar360.com/api/360/products';
    $awca_products = awca_get_data_from_api($api_url);

    if (is_object($awca_products) && isset($awca_products->items) && is_array($awca_products->items)) {
        $response = array(
            'success' => true,
            'woo_url' => admin_url('edit.php?post_type=product'),
            'products' => array()
        );

        foreach ($awca_products->items as $index => $item) {
            $prepared_product = awca_prepare_results_for_product($item);
            if (!is_array($prepared_product) || !isset($prepared_product['name'], $prepared_product['categories'], $prepared_product['attributes']) ||
                !is_array($prepared_product['categories']) || !is_array($prepared_product['attributes'])) {
                wp_send_json_error(array('message' => 'Invalid product data from API.'), 502);
            }
            $prepared_products[] = $prepared_product;
        }
        foreach ($prepared_products as $index => $product_item) {
            $product_creation_data = array(
                'name' => $product_item['name'],
                'regular_price' => $product_item['regular_price'],
                'description' => $product_item['description'],
                'image' => $product_item['image'],
                'categories' => $product_item['categories'],
                'stock_quantity' => $product_item['stock_quantity'],
                'gallery_images' => $product_item['gallery_images'],
                'attributes' => $product_item['attributes'],
                'variants' => $product_item['variants']
            );

            $product_id = awca_create_woocommerce_product($product_creation_data, $combinedCategories, $combinedattributes);

            $product_creation[] = [
                'product_item' => $product_item,
                'formatted_product_data' => $product_creation_data,
                'product_id' => $product_id
            ];

            if ($product_id) {
                $responses[] = array(
                    'success' => true,
                    'message' => "Product with ID $product_id created",
                    'data' => $product_creation_data,
                );
            } else {
                $response['success'] = false;
                $responses[] = array(
                    'success' => false,
                    'message' => "A product was not created",
                    'data' => $product_creation_data,
                );
            }
        }
        $response['prepared_products'] = $prepared_products;
        $response['product_creation'] = $product_creation;

        $response['responses'] = $responses;
        if (!$response['success']) {
            wp_send_json_error($response, 502);
        }
        wp_send_json($response);
    } else {
        $response = array(
            'success' => false,
            'message' => 'Failed to fetch products data from API.',
        );
        wp_send_json_error($response, 502);
    }
}
