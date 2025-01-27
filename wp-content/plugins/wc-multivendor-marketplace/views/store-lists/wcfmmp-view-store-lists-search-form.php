<?php
/**
 * The Template for displaying store list search form.
 *
 * @package WCfM Markeplace Views Store List Search Form
 *
 * For edit coping this to yourtheme/wcfm/store/store-lists
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp, $post;

$paged  = max( 1, get_query_var( 'paged' ) );

$search_country = '';
$search_state   = '';

// GEO Locate Support
if( apply_filters( 'wcfmmp_is_allow_store_list_by_user_location', true ) ) {
	if( is_user_logged_in() && !$search_country ) {
		$user_location = get_user_meta( get_current_user_id(), 'wcfm_user_location', true );
		if( $user_location ) {
			$search_country = $user_location['country'];
			$search_state   = $user_location['state'];
		}
	}
			
	if( apply_filters( 'wcfm_is_allow_wc_geolocate', true ) && class_exists( 'WC_Geolocation' ) && !$search_country ) {
		$user_location = WC_Geolocation::geolocate_ip();
		$search_country = $user_location['country'];
		$search_state   = $user_location['state'];
	}
}

$search_query   = isset( $_GET['wcfmmp_store_search'] ) ? sanitize_text_field( $_GET['wcfmmp_store_search'] ) : '';
$search_category  = isset( $_GET['wcfmmp_store_category'] ) ? sanitize_text_field( $_GET['wcfmmp_store_category'] ) : '';
$search_country = isset( $_GET['wcfmmp_store_country'] ) ? sanitize_text_field( $_GET['wcfmmp_store_country'] ) : $search_country;
$search_state   = isset( $_GET['wcfmmp_store_state'] ) ? sanitize_text_field( $_GET['wcfmmp_store_state'] ) : $search_state;

// Country -> States
$country_obj   = new WC_Countries();
$countries     = $country_obj->countries;
$states        = $country_obj->states;
$state_options = array();
if( $state && isset( $states[$search_country] ) && is_array( $states[$search_country] ) ) {
	$state_options = $states[$search_country];
}
if( $search_state && empty( $state_options ) ) $state_options[$search_state] = $search_state;

if ( ! empty( $search_query ) ) {
	printf( '<h2>' . __( 'Search Results for: %s', 'wc-multivendor-marketplace' ) . '</h2>', $search_query );
}

$display_vendor_term = array();
?>

<form role="search" method="get" class="wcfmmp-store-search-form" action="">
	<?php if( $search ) { ?>
	  <input type="search" id="search" class="search-field wcfmmp-store-search" placeholder="<?php esc_attr_e( 'Search &hellip;', 'wc-multivendor-marketplace' ); ?>" value="<?php echo esc_attr( $search_query ); ?>" name="wcfmmp_store_search" title="<?php esc_attr_e( 'Search store &hellip;', 'wc-multivendor-marketplace' ); ?>" />
	<?php } ?>
	
	<?php if( $category && apply_filters( 'wcfmmp_is_allow_store_list_category_filter', true ) ) { ?>
		<?php
		$vendor_categories = $WCFMmp->wcfmmp_vendor->wcfmmp_get_vendor_taxonomy( 0, 'product_cat' );
		if ( $vendor_categories ) {
			$preferred_taxonomy = 'product_cat';
			?>
		  <select id="wcfmmp_store_category" name="wcfmmp_store_category" class="wcfm-select wcfm_ele">
		    <option value=""><?php _e( 'Choose Category', 'wc-multivendor-marketplace' ); ?></option>
			  <?php
				foreach( $vendor_categories as $vendor_category_id => $vendor_category ) {
					if( $vendor_category_id ) {
						if( is_array( $vendor_category ) && !empty( $vendor_category ) ) {
							$vendor_term = get_term( absint( $vendor_category_id ), $preferred_taxonomy ); 
							$tax_toggle_class = '';
							if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
								?>
								<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>><?php echo $vendor_term->name; ?></option>
								<?php
							}
							foreach( $vendor_category as $vendor_category_child_id => $vendor_category_child ) {
								$vendor_term = get_term( absint( $vendor_category_child_id ), $preferred_taxonomy );
								if( !is_array( $vendor_category_child ) ) {
									if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
										if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
										$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
										?>
										<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>><?php echo $vendor_term->name; ?></option>
										<?php
									}
								} else {
									?>
									<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>><?php echo $vendor_term->name; ?></option>
									<?php
									foreach( $vendor_category_child as $vendor_category_child2_id => $vendor_category_child2 ) {
										$vendor_term = get_term( absint( $vendor_category_child2_id ), $preferred_taxonomy ); 
										if( !is_array( $vendor_category_child2 ) ) {
											if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
												if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
												$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
												?>
												<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;<?php echo $vendor_term->name; ?></option>
												<?php
											}
										} else {
											?>
											<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;<?php echo $vendor_term->name; ?></option>
											<?php
											foreach( $vendor_category_child2 as $vendor_category_child3_id => $vendor_category_child3 ) {
												$vendor_term = get_term( absint( $vendor_category_child3_id ), $preferred_taxonomy ); 
												if( !is_array( $vendor_category_child3 ) ) {
													if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
														if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
														$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
														?>
														<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
														<?php
													}
												} else {
													?>
													<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
													<?php
													foreach( $vendor_category_child3 as $vendor_category_child4_id => $vendor_category_child4 ) {
														$vendor_term = get_term( absint( $vendor_category_child4_id ), $preferred_taxonomy ); 
														if( !is_array( $vendor_category_child4 ) ) {
															if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
																if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
																$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
																?>
																<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																<?php
															}
														} else {
															?>
															<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
															<?php
															foreach( $vendor_category_child4 as $vendor_category_child5_id => $vendor_category_child5 ) {
																$vendor_term = get_term( absint( $vendor_category_child5_id ), $preferred_taxonomy ); 
																if( !is_array( $vendor_category_child5 ) ) {
																	if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
																		if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
																		$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
																		?>
																		<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																		<?php
																	}
																} else {
																	?>
																	<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																	<?php
																	foreach( $vendor_category_child5 as $vendor_category_child6_id => $vendor_category_child6 ) {
																		$vendor_term = get_term( absint( $vendor_category_child6_id ), $preferred_taxonomy ); 
																		if( !is_array( $vendor_category_child6 ) ) {
																			if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
																				if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
																				$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
																				?>
																				<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																				<?php
																			}
																		} else {
																			?>
																			<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																			<?php
																			foreach( $vendor_category_child6 as $vendor_category_child7_id => $vendor_category_child7 ) {
																				$vendor_term = get_term( absint( $vendor_category_child7_id ), $preferred_taxonomy ); 
																				if( !is_array( $vendor_category_child7 ) ) {
																					if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
																						if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
																						$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
																						?>
																						<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																						<?php
																					}
																				} else {
																					?>
																					<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendor_term->name; ?></option>
																					<?php
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						} else {
							$vendor_term = get_term( absint( $vendor_category_id ), $preferred_taxonomy ); 
							if( $vendor_term && $vendor_term->term_id && $vendor_term->name ) {
								if( in_array( $vendor_term->term_id, $display_vendor_term) ) continue;
								$display_vendor_term[$vendor_term->term_id] = $vendor_term->term_id;
								?>
								<option value="<?php echo $vendor_term->term_id; ?>" <?php if( $vendor_term->term_id == $search_category ) echo 'selected'; ?>><?php echo $vendor_term->name; ?></option>
								<?php 
							}
						}
					} 
				}
				?>
			</select>
		<?php } ?>
  <?php } ?>
  
  <?php if( $radius ) { ?>
  	<input type="text" id="wcfmmp_radius_addr" name="wcfmmp_radius_addr" class="wcfmmp-radius-addr" placeholder="<?php esc_attr_e( 'Insert your address ..', 'wc-multivendor-marketplace' ); ?>" value="" />
  	<div class="wcfm_radius_slidecontainer">
  	  <input class="wcfmmp_radius_range" name="wcfmmp_radius_range" id="wcfmmp_radius_range" type="range" value="10" min="0" max="100" steps="6" />
  	  <span class="wcfmmp_radius_range_start">0</span>
  	  <span class="wcfmmp_radius_range_cur">10 KM</span>
  	  <span class="wcfmmp_radius_range_end">100</span>
  	</div>
		<input type="hidden" id="wcfmmp_radius_lat" name="wcfmmp_radius_lat" value="">
    <input type="hidden" id="wcfmmp_radius_lng" name="wcfmmp_radius_lng" value="">
  <?php } ?>
	
	<?php if( $country && apply_filters( 'wcfmmp_is_allow_store_list_country_filter', true ) ) { ?>
		<?php $WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmmp_store_country" => array( 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'value' => $search_country ) ) ); ?>
  <?php } ?>
  
  <?php if( $country && $state && apply_filters( 'wcfmmp_is_allow_store_list_state_filter', true ) ) { ?>
  	<?php $WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmmp_store_state" => array( 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'options' => $state_options, 'value' => $search_state ) ) ); ?>
  <?php } ?>
	
	<input type="hidden" id="pagination_base" name="pagination_base" value="<?php echo $pagination_base ?>" />
	<input type="hidden" id="wcfm_paged" name="wcfm_paged" value="<?php echo $paged ?>" />
	<input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( 'wcfmmp-stores-list-search' ); ?>" />
	<div class="wcfmmp-overlay" style="display: none;"><span class="wcfmmp-ajax-loader"></span></div>
</form>

<?php do_action( 'wcfmmp_after_store_list_serach_form' ); ?>