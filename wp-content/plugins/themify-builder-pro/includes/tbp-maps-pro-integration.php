<?php

/**
 * Enable displaying Posts in the Builder Maps Pro addon
 *
 * @return array
 */
function tbp_maps_pro_posts_data_provider( $providers ) {
	class Maps_Pro_Data_Provider_Posts extends Maps_Pro_Data_Provider {

		function get_id() {
			return 'posts';
		}

		function get_label() {
			return __( 'Posts', 'themify' );
		}

		function get_options() {
			return array(
				array(
					'type' => 'query_posts',
					'id' => 'post_type_post',
					'tax_id'=>'tax',
					'term_id'=>'tax_category',
					'slug_id'=>'post_slug',
				),
				array(
					'id' => 'per_page',
					'type' => 'number',
					'label' => __( 'Posts Per Page', 'themify' ),
					'help' => __( 'Enter the number of post to display.', 'themify' )
				),
				array(
					'id' => 'order',
					'type' => 'select',
					'label' => __( 'Order', 'themify' ),
					'help' => __( 'Descending = show newer posts first', 'themify' ),
					'order' =>true
				),
				array(
					'id' => 'orderby',
					'type' => 'select',
					'label' => __( 'Order By', 'themify' ),
					'options' => array(
						'date' => __( 'Date', 'themify' ),
						'id' => __( 'Id', 'themify' ),
						'author' => __( 'Author', 'themify' ),
						'title' => __( 'Title', 'themify' ),
						'name' => __( 'Name', 'themify' ),
						'modified' => __( 'Modified', 'themify' ),
						'rand' => __( 'Random', 'themify' ),
						'comment_count' => __( 'Comment Count', 'themify' )
					)
				),
				array(
					'id' => 'offset',
					'type' => 'number',
					'label' => __( 'Offset', 'themify' ),
					'help' => __( 'Enter the number of post to displace or pass over.', 'themify' )
				),
				array(
					'label' => __( 'Custom Field for Address', 'themify' ),
					'id' => 'custom_field',
					'type' => 'autocomplete',
					'dataset' => 'custom_fields',
					'help' => __( 'Name of the custom field that will be used as Address for marker.', 'themify' ),
				),
				array(
					'id' => 'marker_icon',
					'type' => 'image',
					'label' => __('Icon', 'tbp')
				),
			);
		}

		function get_items( $settings ) {
			global $post;

			$settings = wp_parse_args( $settings, array(
				'per_page' => 5,
				'custom_field' => '',
				'marker_icon' => '',
				'post_type' => 'post',
				'tax' => 'category',
				'post_slug' => '',
				'offset' => '',
				'order' => 'desc',
				'orderby' => 'date',
			) );
			$args = array(
				'post_status' => 'publish',
				'post_type' => $settings['post_type'],
				'posts_per_page' => $settings['per_page'],
				'order' => $settings['order'],
				'orderby' => $settings['orderby'],
				'suppress_filters' => false,
				'offset' => $settings['offset'],
			);
			if ( $settings['tax'] === 'post_slug' ) {
				if ($settings['post_slug']!=='') {
					$args['post__in'] = Themify_Builder_Model::parse_slug_to_ids( $settings['post_slug'], $settings['post_type'] );
				}
			} else {
				$terms = isset( $settings[ "tax_{$settings['tax']}" ] ) ? $settings[ "tax_{$settings['tax']}" ] : ( isset( $settings['tax_category'] ) ? $settings['tax_category'] : false );
				if ( $terms === false ) {
					return;
				}
				// deal with how category fields are saved
				$terms = preg_replace('/\|[multiple|single]*$/', '', $terms);

				$temp_terms = explode(',', $terms);
				$new_terms = array();
				$is_string = false;
				foreach ( $temp_terms as $t ) {
					if ( ! is_numeric( $t ) ) {
						$is_string = true;
					}
					if ( '' !== $t ) {
						array_push( $new_terms, trim( $t ) );
					}
				}
				if ( ! empty( $new_terms ) && ! in_array( '0', $new_terms ) ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => $settings['tax'],
							'field' => $is_string ? 'slug' : 'id',
							'terms' => $new_terms,
							'operator' => ( '-' === substr( $terms, 0, 1 ) ) ? 'NOT IN' : 'IN'
						)
					);
				}
			}

			$query = new WP_Query( apply_filters( 'tb_maps_pro_query', $args, $settings ) );
			if ( is_object( $post ) ){
				$saved_post = clone $post;
			}
			$items = array();
			while ( $query->have_posts() ) {
				$query->the_post();
				if ( ! $address = get_post_meta( get_the_id(), $settings['custom_field'], true ) ) {
					// skip posts that don't have the designated "address" meta field
					continue;
				}
				$text = sprintf(
					'
					<div style="float: left; margin-right: 10px;">
						<a href="%2$s">
							<img src="%1$s" alt="%3$s" />
						</a>
					</div>
					<div>
						<a href="%2$s"><strong>%3$s</strong></a>
					</div>
					<div>
						%4$s
					</div>',
					esc_attr( get_the_post_thumbnail_url( get_the_id(), 'thumbnail' ) ),
					esc_attr( get_permalink() ),
					esc_html( get_the_title() ),
					esc_html( get_the_excerpt() )
				);
				$items[] = array(
					'title' => $text,
					'image' => $settings['marker_icon'],
					'address' => $address,
				);
			}
			if ( isset( $saved_post ) && is_object( $saved_post ) ) {
				$post = $saved_post;
				setup_postdata( $saved_post );
			}

			return $items;
		}
	}

	$providers['posts'] = 'Maps_Pro_Data_Provider_Posts';
	return $providers;
}
add_filter( 'tb_maps_pro_data_providers', 'tbp_maps_pro_posts_data_provider' );