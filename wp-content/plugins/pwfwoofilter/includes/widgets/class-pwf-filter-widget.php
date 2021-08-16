<?php

defined( 'ABSPATH' ) || exit;

/*
 * @category 	Widgets
 * @extends 	WP_Widget
 * @version 1.0
 */

add_action( 'widgets_init', 'Pwf_Filter_Widget::register_widget' );

class Pwf_Filter_Widget extends WP_Widget {

	function __construct() {

		$widget_ops = array(
			'classname'   => 'pwf-filter',
			'description' => esc_html__( 'This widget displays a form with items that you created in the filter post. When interacting with options, products are filtering.', 'pwf-woo-filter' ),
		);

		parent::__construct( 'pwf-woo-filter', 'PWF: ' . esc_html__( 'Product Filters', 'pwf-woo-filter' ), $widget_ops );
	}

	static function register_widget() {
		register_widget( __class__ );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		$title     = apply_filters( 'widget_title', $instance['title'] );
		$filter_id = absint( $instance['filter_id'] );

		if ( ! isset( $filter_id ) || ! is_int( $filter_id ) ) {
			return;
		}

		$filter_id     = apply_filters( 'pwf_filter_id', $filter_id );
		$render_filter = new Pwf_Render_Filter( $filter_id );
		$output        = $render_filter->get_html();

		if ( ! empty( $output ) ) {
			echo $args['before_widget'];

			if ( 'on' === $instance['display_title'] ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo wp_kses_post( $output );
			echo $args['after_widget'];
		}
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		$instance                  = $old_instance;
		$instance['title']         = esc_attr( $new_instance['title'] );
		$instance['filter_id']     = absint( $new_instance['filter_id'] );
		$instance['display_title'] = isset( $new_instance['display_title'] ) ? 'on' : 'off';

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		$fields = array(
			array(
				'type'    => 'text',
				'key'     => 'title',
				'id'      => $this->get_field_id( 'title' ),
				'name'    => $this->get_field_name( 'title' ),
				'title'   => esc_html__( 'title:', 'pwf-woo-filter' ),
				'default' => esc_html__( 'Filter products', 'pwf-woo-filter' ),
			),
			array(
				'type'    => 'select_filterpost',
				'key'     => 'filter_id',
				'id'      => $this->get_field_id( 'filter_id' ),
				'name'    => $this->get_field_name( 'filter_id' ),
				'title'   => esc_html__( 'Filter', 'pwf-woo-filter' ),
				'default' => '',
				'options' => self::get_filters(),
			),
			array(
				'type'    => 'checkbox',
				'key'     => 'display_title',
				'id'      => $this->get_field_id( 'display_title' ),
				'name'    => $this->get_field_name( 'display_title' ),
				'title'   => esc_html__( 'Display widget title:', 'pwf-woo-filter' ),
				'default' => 'off',
			),
		);

		$this->output_html_widget_fields( $instance, $fields );
	}

	public static function get_filters() {
		$query_args = array(
			'post_type'           => 'pwf_woofilter',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => -1,
		);

		$data    = array();
		$filters = get_posts( $query_args );
		if ( $filters ) {
			foreach ( $filters as $filter ) {
				$data[] = array(
					'id'    => $filter->ID,
					'title' => $filter->post_title,
				);
			}
		} else {
			$data[] = array(
				'id'    => '',
				'title' => esc_html__( 'Please add new filter', 'pwf-woo-filter' ),
			);
		}

		return $data;
	}

	private function output_description( $desc ) {
		if ( ! empty( $desc ) ) {
			echo '<span>' . wp_kses_post( $desc ) . '</span>';
		}
	}

	private function output_html_widget_fields( $instance, $fields ) {
		$defaults = array(
			'type'    => 'text',
			'id'      => '',
			'name'    => '',
			'title'   => '',
			'desc'    => '',
			'options' => array(),
			'default' => '',
		);

		foreach ( $fields as $field ) {

			$field = wp_parse_args( (array) $field, $defaults );
			extract( $field ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

			if ( isset( $instance[ $key ] ) ) {
				$value = $instance[ $key ];
			} else {
				$value = $default;
			}
			switch ( $field['type'] ) {

				case 'text':
					?>
					<p>
						<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?> </label><input class="widefat" type="text" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $value ); ?>" size="20" />
					</p>
					<?php
					$this->output_description( $field['desc'] );
					break;
				case 'checkbox':
					?>
					<?php
					$checked = '';
					if ( isset( $instance[ $key ] ) && 'on' === $instance[ $key ] ) {
						$checked = ' checked';
					} else {
						if ( 'on' === $default ) {
							$checked = $default;
						}
					}
					?>
					<p>
						<input class="widefat" type="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" <?php echo esc_attr( $checked ); ?>><label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?> </label>
					</p>
					<?php
					$this->output_description( $field['desc'] );
					break;
				case 'select_filterpost':
					?>
					<p>
						<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
						<select name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" class="widefat">
						<?php
						$options = $field['options'];
						foreach ( $options as $post ) {
							echo '<option value="' . esc_attr( $post['id'] ) . '" id="' . esc_attr( $post['id'] ) . '"', $value === $post['id'] ? ' selected="selected"' : '', '>', esc_html( $post['title'] ), '</option>';
						}
						?>
						</select>
					</p>
					<?php
					$this->output_description( $field['desc'] );
					break;
			}
		}
	}
}
