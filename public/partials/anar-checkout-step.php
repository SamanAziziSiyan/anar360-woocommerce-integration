<?php
// Add a new checkout field
function kia_filter_checkout_fields($fields){
    $fields['extra_fields'] = array(
            'some_field' => array(
                'type' => 'text',
                'required'      => true,
                'label' => __( 'Some field' )
                )
            );

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'kia_filter_checkout_fields' );

// display the extra field on the checkout form
function kia_extra_checkout_fields(){

    $checkout = WC()->checkout(); ?>

    <div class="extra-fields">
    <h3><?php _e( 'Additional Fields' ); ?></h3>

    <?php foreach ( $checkout->checkout_fields['extra_fields'] as $key => $field ) : ?>

            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

        <?php endforeach; ?>
    </div>

<?php }
add_action( 'woocommerce_checkout_after_customer_details' ,'kia_extra_checkout_fields' );

// save the extra field when checkout is processed
function kia_save_extra_checkout_fields( $order_id, $posted ){
    if( isset( $posted['some_field'] ) ) {
        update_post_meta( $order_id, '_some_field', sanitize_text_field( $posted['some_field'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'kia_save_extra_checkout_fields', 10, 2 );

// display the extra data in the order admin panel
function kia_display_order_data_in_admin( $order ){  ?>
    <div class="order_data_column">
        <h4><?php _e( 'Extra Details', 'woocommerce' ); ?></h4>
        <?php
            echo '<p><strong>' . __( 'Some field' ) . ':</strong>' . get_post_meta( $order->id, '_some_field', true ) . '</p>'; ?>
    </div>
<?php }
add_action( 'woocommerce_admin_order_data_after_order_details', 'kia_display_order_data_in_admin' );
