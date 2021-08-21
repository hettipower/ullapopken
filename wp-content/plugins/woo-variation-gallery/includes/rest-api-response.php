<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	if ( ! function_exists( 'woo_variation_gallery_images_rest_get_image' ) ) {
		function woo_variation_gallery_images_rest_get_image( $attachment_id ) {
			
			$attachment_post = get_post( $attachment_id );
			if ( is_null( $attachment_post ) ) {
				return false;
			}
			
			$attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( ! is_array( $attachment ) ) {
				return false;
			}
			
			return apply_filters( 'woo_variation_gallery_images_rest_get_image', array(
				'id'                => (int) $attachment_id,
				'date_created'      => wc_rest_prepare_date_response( $attachment_post->post_date, false ),
				'date_created_gmt'  => wc_rest_prepare_date_response( strtotime( $attachment_post->post_date_gmt ) ),
				'date_modified'     => wc_rest_prepare_date_response( $attachment_post->post_modified, false ),
				'date_modified_gmt' => wc_rest_prepare_date_response( strtotime( $attachment_post->post_modified_gmt ) ),
				'src'               => current( $attachment ),
				'name'              => get_the_title( $attachment_id ),
				'alt'               => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			), $attachment_id );
		}
	}
	
	if ( ! function_exists( 'woo_variation_gallery_images_rest_set_response' ) ) {
		function woo_variation_gallery_images_rest_set_response( $response, $object ) {
			
			if ( empty( $response->data ) ) {
				return $response;
			}
			
			$variation_id = $object->get_id();
			$product_id   = $object->get_parent_id();
			
			$variation_images                                 = (array) get_post_meta( $variation_id, 'woo_variation_gallery_images', true );
			$response->data[ 'woo_variation_gallery_images' ] = array();
			foreach ( $variation_images as $attachment_id ) {
				
				$image_info = woo_variation_gallery_images_rest_get_image( $attachment_id );
				if ( is_array( $image_info ) ) {
					array_push( $response->data[ 'woo_variation_gallery_images' ], $image_info );
				}
			}
			
			return $response;
		}
	}
	
	add_filter( "woocommerce_rest_prepare_product_variation_object", 'woo_variation_gallery_images_rest_set_response', 10, 2 );