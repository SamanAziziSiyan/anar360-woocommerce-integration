<?php

add_filter('manage_product_posts_columns', 'awca_my_custom_product_columns');
add_action('manage_product_posts_custom_column', 'awca_my_custom_product_list_label', 10, 2);
add_action('woocommerce_before_single_product_summary', 'awca_remove_woocommerce_single_product_image', 1);
add_action('woocommerce_before_single_product_summary', 'awca_my_custom_single_product_image', 20);


add_action('woocommerce_process_product_meta', 'awca_save_product_image_url_field');
add_action('woocommerce_product_options_general_product_data', 'awca_add_product_image_url_field');
add_filter('woocommerce_product_get_image', 'awca_replace_product_image_with_custom_url', 10, 5);




/**
 * Front End Related functions.
 *
 * @since    1.0.0
 */

function awca_my_custom_product_columns($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'date') {
            $new_columns['product_label'] = __('محصول', ANAR_WC_API_TEXT_DOMAIN);
        }
    }
    return $new_columns;
}

function awca_my_custom_product_list_label($column, $post_id)
{
    if ($column === 'product_label') {
        $anar_products = get_post_meta($post_id, '_anar_products', true);
        if (!empty($anar_products)) {
            echo '<span class="anar-label">انار</span>';
        }
    }
}

function awca_remove_woocommerce_single_product_image()
{
    global $product;

    $anar_products = get_post_meta($product->get_id(), '_anar_products', true);
    if (!empty($anar_products)) {
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    }
}

function awca_my_custom_single_product_image()
{
    global $product;

    $anar_products = get_post_meta($product->get_id(), '_anar_products', true);
    if (!empty($anar_products)) {
        $custom_image_url = get_post_meta($product->get_id(), '_product_image_url', true);

        if ($custom_image_url) {
            // Output custom image if it exists
            echo '<div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-4 images">';

            // Custom Image
            $product = wc_get_product($product->get_id());
            $custom_image_html = '<figure class="woocommerce-product-gallery__wrapper">
                                    <div class="woocommerce-product-gallery__image">
                                        <a href="' . esc_url($custom_image_url) . '" class="woocommerce-main-image zoom" itemprop="image" title="' . esc_attr($product->get_name()) . '" data-rel="prettyPhoto[product-gallery]">
                                            <img src="' . esc_url($custom_image_url) . '" class="wp-post-image" alt="' . esc_attr($product->get_name()) . '" />
                                        </a>
                                    </div>
                                </figure>';

            echo $custom_image_html;

            // Gallery
            $gallery_image_urls = [];

            // Retrieve the gallery images as a single value (string)
            // $gallery_image_meta = get_post_meta($product->get_id(), '_anar_gallery_images', true);

            // if (!empty($gallery_image_meta)) {
            //     // Convert serialized data to array if needed
            //     $gallery_image_urls = maybe_unserialize($gallery_image_meta);
            //     echo '<figure class="woocommerce-product-gallery__wrapper">';
            //     foreach ($gallery_image_urls as $image_url) {
            //         if ($image_url) {
            //             echo '<div class="woocommerce-product-gallery__image">
            //                         <a href="' . esc_url($image_url) . '" class="woocommerce-product-gallery__image zoom" itemprop="image" title="' . esc_attr($product->get_name()) . '" data-rel="prettyPhoto[product-gallery]">
            //                             <img src="' . esc_url($image_url) . '" class="wp-post-image" alt="' . esc_attr($product->get_name()) . '" />
            //                         </a>
            //                     </div>';
            //         }
            //     }
            //     echo '</figure>';

            echo '</div>'; // Close the gallery container
            // }
        }
    }
}

/**
 * Wordpress Admin related functions
 *
 * @since    1.0.0
 */


function awca_add_product_image_url_field()
{
    woocommerce_wp_text_input(
        array(
            'id'          => '_product_image_url',
            'label'       => __('آدرس تصویر سفارشی محصول', 'woocommerce'),
            'placeholder' => 'https://example.com/image.jpg',
            'desc_tip'    => 'true',
            'description' => __('آدرس تصویر سفارشی محصول را اینجا وارد کنید.', 'woocommerce')
        )
    );
}

function awca_save_product_image_url_field($post_id)
{
    $custom_image_url = isset($_POST['_product_image_url']) ? esc_url_raw($_POST['_product_image_url']) : '';
    update_post_meta($post_id, '_product_image_url', $custom_image_url);
}

function awca_replace_product_image_with_custom_url($image, $product, $size, $attr, $placeholder)
{
    $custom_image_url = get_post_meta($product->get_id(), '_product_image_url', true);

    if ($custom_image_url) {
        $custom_image_html = '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="wp-post-image" />';
        return $custom_image_html;
    }

    return $image;
}
