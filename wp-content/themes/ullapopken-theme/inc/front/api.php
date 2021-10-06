<?php
function all_stores_list_func() {
    $dataArr = array();

    $storesQuery = new WP_Query(
        array(
            'post_type' => 'store',
            'posts_per_page' => -1
        )
    );
    if ( $storesQuery->have_posts() ) : while ( $storesQuery->have_posts() ) : $storesQuery->the_post();
        $address = get_field( 'address' );
        $gallery_images = get_field( 'gallery' );

        $item = array(
            'address' => $address,
            'title' => get_the_title(),
            'image' => $gallery_images[0]['url'],
            'ID' => get_the_ID(),
            'link' => get_the_permalink(),
            'telephone' => get_field( 'telephone' )
        );

        array_push($dataArr , $item);

    endwhile; wp_reset_postdata(); endif;

    return $dataArr;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'ullapopken/v1', '/stores/', array(
      'methods' => 'GET',
      'callback' => 'all_stores_list_func',
      'permission_callback' => '__return_true'
    ) );
} );

function stores_group_with_cities() {
    $dataArr = array();

    $storesQuery = new WP_Query(
        array(
            'post_type' => 'store',
            'posts_per_page' => -1
        )
    );
    if ( $storesQuery->have_posts() ) : while ( $storesQuery->have_posts() ) : $storesQuery->the_post();
        $address = get_field( 'address' );
        $gallery_images = get_field( 'gallery' );
        $city = (isset($address['city'])) ? $address['city'] : false ;

        if( $city ) {
            $dataArr[$city][] = array(
                'address' => $address,
                'title' => get_the_title(),
                'ID' => get_the_ID(),
                'link' => get_the_permalink(),
                'city' => (isset($address['city'])) ? $address['city'] : false
            );
        }

    endwhile; wp_reset_postdata(); endif;

    return $dataArr;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'ullapopken/v1', '/stores-cities/', array(
      'methods' => 'GET',
      'callback' => 'stores_group_with_cities',
      'permission_callback' => '__return_true'
    ) );
} );