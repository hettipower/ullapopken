<?php
function stores_type_func() {

	$labels = array(
		'name'                  => _x( 'Stores', 'Post Type General Name', 'store-locator' ),
		'singular_name'         => _x( 'Store', 'Post Type Singular Name', 'store-locator' ),
		'menu_name'             => __( 'Stores', 'store-locator' ),
		'name_admin_bar'        => __( 'Store', 'store-locator' ),
		'archives'              => __( 'Item Archives', 'store-locator' ),
		'attributes'            => __( 'Item Attributes', 'store-locator' ),
		'parent_item_colon'     => __( 'Parent Item:', 'store-locator' ),
		'all_items'             => __( 'All Items', 'store-locator' ),
		'add_new_item'          => __( 'Add New Item', 'store-locator' ),
		'add_new'               => __( 'Add New', 'store-locator' ),
		'new_item'              => __( 'New Item', 'store-locator' ),
		'edit_item'             => __( 'Edit Item', 'store-locator' ),
		'update_item'           => __( 'Update Item', 'store-locator' ),
		'view_item'             => __( 'View Item', 'store-locator' ),
		'view_items'            => __( 'View Items', 'store-locator' ),
		'search_items'          => __( 'Search Item', 'store-locator' ),
		'not_found'             => __( 'Not found', 'store-locator' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'store-locator' ),
		'featured_image'        => __( 'Featured Image', 'store-locator' ),
		'set_featured_image'    => __( 'Set featured image', 'store-locator' ),
		'remove_featured_image' => __( 'Remove featured image', 'store-locator' ),
		'use_featured_image'    => __( 'Use as featured image', 'store-locator' ),
		'insert_into_item'      => __( 'Insert into item', 'store-locator' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'store-locator' ),
		'items_list'            => __( 'Items list', 'store-locator' ),
		'items_list_navigation' => __( 'Items list navigation', 'store-locator' ),
		'filter_items_list'     => __( 'Filter items list', 'store-locator' ),
	);
	$args = array(
		'label'                 => __( 'Store', 'store-locator' ),
		'description'           => __( 'Store Description', 'store-locator' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'store', $args );

}
add_action( 'init', 'stores_type_func', 0 );