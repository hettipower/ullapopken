<?php
// Register Custom Post Type
function menu_type_func() {

	$labels = array(
		'name'                  => _x( 'Menus', 'Post Type General Name', 'ullapopken' ),
		'singular_name'         => _x( 'Menu', 'Post Type Singular Name', 'ullapopken' ),
		'menu_name'             => __( 'Menus', 'ullapopken' ),
		'name_admin_bar'        => __( 'Menu', 'ullapopken' ),
		'archives'              => __( 'Item Archives', 'ullapopken' ),
		'attributes'            => __( 'Item Attributes', 'ullapopken' ),
		'parent_item_colon'     => __( 'Parent Item:', 'ullapopken' ),
		'all_items'             => __( 'All Items', 'ullapopken' ),
		'add_new_item'          => __( 'Add New Item', 'ullapopken' ),
		'add_new'               => __( 'Add New', 'ullapopken' ),
		'new_item'              => __( 'New Item', 'ullapopken' ),
		'edit_item'             => __( 'Edit Item', 'ullapopken' ),
		'update_item'           => __( 'Update Item', 'ullapopken' ),
		'view_item'             => __( 'View Item', 'ullapopken' ),
		'view_items'            => __( 'View Items', 'ullapopken' ),
		'search_items'          => __( 'Search Item', 'ullapopken' ),
		'not_found'             => __( 'Not found', 'ullapopken' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'ullapopken' ),
		'featured_image'        => __( 'Featured Image', 'ullapopken' ),
		'set_featured_image'    => __( 'Set featured image', 'ullapopken' ),
		'remove_featured_image' => __( 'Remove featured image', 'ullapopken' ),
		'use_featured_image'    => __( 'Use as featured image', 'ullapopken' ),
		'insert_into_item'      => __( 'Insert into item', 'ullapopken' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'ullapopken' ),
		'items_list'            => __( 'Items list', 'ullapopken' ),
		'items_list_navigation' => __( 'Items list navigation', 'ullapopken' ),
		'filter_items_list'     => __( 'Filter items list', 'ullapopken' ),
	);
	$args = array(
		'label'                 => __( 'Menu', 'ullapopken' ),
		'description'           => __( 'Menu Description', 'ullapopken' ),
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
	register_post_type( 'menu', $args );

}
add_action( 'init', 'menu_type_func', 0 );

function stores_type_func() {

	$labels = array(
		'name'                  => _x( 'Stores', 'Post Type General Name', 'ullapopken' ),
		'singular_name'         => _x( 'Store', 'Post Type Singular Name', 'ullapopken' ),
		'menu_name'             => __( 'Stores', 'ullapopken' ),
		'name_admin_bar'        => __( 'Store', 'ullapopken' ),
		'archives'              => __( 'Item Archives', 'ullapopken' ),
		'attributes'            => __( 'Item Attributes', 'ullapopken' ),
		'parent_item_colon'     => __( 'Parent Item:', 'ullapopken' ),
		'all_items'             => __( 'All Items', 'ullapopken' ),
		'add_new_item'          => __( 'Add New Item', 'ullapopken' ),
		'add_new'               => __( 'Add New', 'ullapopken' ),
		'new_item'              => __( 'New Item', 'ullapopken' ),
		'edit_item'             => __( 'Edit Item', 'ullapopken' ),
		'update_item'           => __( 'Update Item', 'ullapopken' ),
		'view_item'             => __( 'View Item', 'ullapopken' ),
		'view_items'            => __( 'View Items', 'ullapopken' ),
		'search_items'          => __( 'Search Item', 'ullapopken' ),
		'not_found'             => __( 'Not found', 'ullapopken' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'ullapopken' ),
		'featured_image'        => __( 'Featured Image', 'ullapopken' ),
		'set_featured_image'    => __( 'Set featured image', 'ullapopken' ),
		'remove_featured_image' => __( 'Remove featured image', 'ullapopken' ),
		'use_featured_image'    => __( 'Use as featured image', 'ullapopken' ),
		'insert_into_item'      => __( 'Insert into item', 'ullapopken' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'ullapopken' ),
		'items_list'            => __( 'Items list', 'ullapopken' ),
		'items_list_navigation' => __( 'Items list navigation', 'ullapopken' ),
		'filter_items_list'     => __( 'Filter items list', 'ullapopken' ),
	);
	$args = array(
		'label'                 => __( 'Store', 'ullapopken' ),
		'description'           => __( 'Store Description', 'ullapopken' ),
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