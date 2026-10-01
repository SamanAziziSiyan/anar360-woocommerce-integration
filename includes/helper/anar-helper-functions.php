<?php



function awca_get_data_from_api($api_url)
{
    try {
        $token = awca_get_activation_key();
        $response = wp_remote_get(
            $api_url,
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $token,
                ),
            )
        );

        if (!is_wp_error($response) && $response['response']['code'] === 200) {
            $data = json_decode($response['body']);
            return $data;
        } else {
            $error_message = '';
            if (is_array($response)) {
                $error_message = $response['response']['message'];
            } elseif (is_wp_error($response)) {
                $error_message = $response->get_error_message();
            } else {
                $error_message = 'Unknown error';
            }
            throw new Exception('Failed to fetch data from API: ' . $error_message);
        }
    } catch (Exception $e) {
        Sentry\captureException($e);
        return false;
    }
}

function awca_product_short_desc($desc)
{
    $desc = trim($desc); // Trim whitespace
    if (strlen($desc) > 100) {
        $last_space = strrpos(substr($desc, 0, 100), ' ');

        if ($last_space !== false) {
            $limited_desc = substr($desc, 0, $last_space) . '...';
        } else {
            $limited_desc = substr($desc, 0, 100) . '...';
        }
    } else {
        $limited_desc = $desc;
    }
    return $limited_desc;
}

function awca_default_product_image()
{
    return function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src() : '';
}

function awca_product_price_digits_seprator($price)
{
    return number_format($price);
}
