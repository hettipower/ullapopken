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
        $address = array(
            'address' => get_post_meta( get_the_ID(), 'stlo_address', true ),
            'lat' => (float)get_post_meta( get_the_ID(), 'stlo_latitude', true ),
            'lng' => (float)get_post_meta( get_the_ID(), 'stlo_longitude', true )
        );

        $item = array(
            'address' => $address,
            'title' => get_the_title(),
            'image' => get_post_meta( get_the_ID(), 'stlo_image', true ),
            'ID' => get_the_ID(),
            'link' => get_the_permalink(),
            'telephone' => get_post_meta( get_the_ID(), 'stlo_telephone', true )
        );

        array_push($dataArr , $item);

    endwhile; wp_reset_postdata(); endif;

    return $dataArr;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'store-locator/v1', '/stores/', array(
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
        $address = array(
            'address' => get_post_meta( get_the_ID(), 'stlo_address', true ),
            'lat' => (float)get_post_meta( get_the_ID(), 'stlo_latitude', true ),
            'lng' => (float)get_post_meta( get_the_ID(), 'stlo_longitude', true )
        );
        $city = (get_post_meta( get_the_ID(), 'stlo_city', true )) ? get_post_meta( get_the_ID(), 'stlo_city', true ) : false ;

        if( $city ) {
            $dataArr[$city][] = array(
                'address' => $address,
                'title' => get_the_title(),
                'ID' => get_the_ID(),
                'link' => get_the_permalink(),
                'city' => $city
            );
        }

    endwhile; wp_reset_postdata(); endif;

    return $dataArr;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'store-locator/v1', '/stores-cities/', array(
      'methods' => 'GET',
      'callback' => 'stores_group_with_cities',
      'permission_callback' => '__return_true'
    ) );
} );