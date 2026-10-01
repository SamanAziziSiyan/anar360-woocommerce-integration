<?php
function awca_get_product_id_by_title($product_title)
{
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'title' => $product_title
    );

    $products = new WP_Query($args);

    if ($products->have_posts()) {
        $product = $products->next_post();
        return $product->ID;
    } else {
        return false;
    }
}

function awca_prepare_results_for_product($product)
{
    if (!is_object($product)) {
        return null;
    }

    $prepared_product = array();

    $prepared_product['image'] = isset($product->mainImage) ? $product->mainImage : '';
    $prepared_product['name'] = isset($product->title) ? $product->title : '';
    $prepared_product['description'] = isset($product->description) ? $product->description : '';
    $prepared_product['regular_price'] = isset($product->variants[0]->price) ? $product->variants[0]->price : 0;
    $prepared_product['stock_quantity'] = isset($product->variants[0]->stock) ? $product->variants[0]->stock : 0;
    $prepared_product['categories'] = isset($product->categories) ? $product->categories : '';
    $prepared_product['formatted_price'] = awca_product_price_digits_seprator($product->variants[0]->priceForResell);

    $categories = isset($product->categories) ? $product->categories : array();
    $category_names = array();
    foreach ($categories as $category) {
        $category_names[] = isset($category->name) ? $category->name : 'این دسته بندی است';
    }
    $prepared_product['categories'] = $category_names;


    $attributes = isset($product->attributes) ? $product->attributes : array();
    $attribute_data = array();

    foreach ($attributes as $attribute) {
        $attribute_name = isset($attribute->name) ? $attribute->name : '';
        $attribute_values = isset($attribute->values) ? $attribute->values : array();

        $attribute_data[] = array(
            'name' => $attribute_name,
            'values' => $attribute_values
        );
    }

    $prepared_product['attributes'] = $attribute_data;


    $variants = isset($product->variants) ? $product->variants : array();
    $variant_data = array();

    foreach ($variants as $variant) {
        $variant_data[] = $variant;
    }

    $prepared_product['variants'] = $variant_data;

    $gallery_images = array();
    if (isset($product->images) && is_array($product->images)) {
        foreach ($product->images as $image) {
            if (isset($image->_type) && $image->_type === 'image' && isset($image->src)) {
                $gallery_images[] = $image->src;
            }
        }
    }
    $prepared_product['gallery_images'] = $gallery_images;

    return $prepared_product;
}

function awca_create_woocommerce_product($product_data, $combinedCategories, $combinedattributes)
{
    try {
        if (!function_exists('wc_get_product')) {
            return false;
        }

        $existing_product_id = awca_get_product_id_by_title($product_data['name']);

        if ($existing_product_id) {
            $product = wc_get_product($existing_product_id);
        } else {
            if (isset($product_data['variants']) && !empty($product_data['variants'])) {
                $product = new WC_Product_Variable();
            } else {
                $product = new WC_Product_Simple();
            }
        }

        $product->set_name($product_data['name']);
        $product->set_regular_price($product_data['regular_price']);
        $product->set_description($product_data['description']);
        $product->set_manage_stock(true);
        $product->set_stock_quantity($product_data['stock_quantity']);
        $product->set_category_ids(awca_map_product_categories($product_data['categories'], $combinedCategories));

        $product_id = $product->save();

        if (isset($product_data['attributes']) && isset($product_data['variants'])) {
            $product_attributes = $product_data['attributes'];
            $variants = $product_data['variants'];

            $mapped_attributes = awca_map_product_variations($product_attributes, $combinedattributes);
            awca_assign_attributes_to_product($product_id, $mapped_attributes);

            // Uncomment and implement this function to create product variations
            // awca_create_product_variations($product_id, $variants);
        }

        if (isset($product_data['image']) && !empty($product_data['image'])) {
            $image_url = $product_data['image'];
            update_post_meta($product_id, '_product_image_url', $image_url);
        }

        if (isset($product_data['gallery_images'])) {
            update_post_meta($product_id, '_anar_gallery_images', $product_data['gallery_images']);
        }
        update_post_meta($product_id, '_anar_products', 'true');

        return $product_id;
    } catch (Exception $e) {
        error_log('Error in awca_create_woocommerce_product: ' . $e->getMessage());
        Sentry\captureException($e);
        return false;
    }
}
