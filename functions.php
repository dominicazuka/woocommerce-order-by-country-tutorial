/*
 * Woocommerce Order by Country
 */
 
 function add_country_meta_data( $order_id ) {
    $order = wc_get_order( $order_id );
    $billing_country = $order->get_billing_country();
    update_post_meta( $order_id, '_billing_country', $billing_country );
}
add_action( 'woocommerce_checkout_order_processed', 'add_country_meta_data', 10, 1 );
function add_country_filter() {
    global $typenow;
    if ( 'shop_order' === $typenow ) {
        $countries = WC()->countries->get_countries();
        ?>
        <select name="billing_country">
            <option value=""><?php _e( 'All countries', 'woocommerce' ); ?></option>
            <?php foreach ( $countries as $key => $value ) { ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php if ( isset( $_GET['billing_country'] ) && $_GET['billing_country'] == $key ) echo 'selected="selected"'; ?>><?php echo esc_html( $value ); ?></option>
            <?php } ?>
        </select>
        <?php
    }
}
add_action( 'restrict_manage_posts', 'add_country_filter' );
function filter_orders_by_country( $query ) {
    global $pagenow, $typenow;
    if ( 'edit.php' === $pagenow && 'shop_order' === $typenow && isset( $_GET['billing_country'] ) && ! empty( $_GET['billing_country'] ) ) {
        $meta_query = array(
            array(
                'key'     => '_billing_country',
                'value'   => sanitize_text_field( $_GET['billing_country'] ),
                'compare' => '=',
            ),
        );
        $query->set( 'meta_query', $meta_query );
    }
}
add_action( 'pre_get_posts', 'filter_orders_by_country' );
