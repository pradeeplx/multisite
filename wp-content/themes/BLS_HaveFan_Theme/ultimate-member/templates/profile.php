<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>



<div class="um <?php echo esc_attr( $this->get_class( $mode ) ); ?> um-<?php echo esc_attr( $form_id ); ?> um-role-<?php echo esc_attr( um_user( 'role' ) ); ?> ">
 <div style="display: none">
                            	<p id="being_a_fan_id"><?php the_field('being_a_fan_sub_heading','options');?></p>
                            	<p id="biography_id"><?php the_field('biography_sub_heading','options');?></p>
                            	<p id="passion_id"><?php the_field('pass_sub_heading','options');?></p>
                            	
                            </div>
<?php

if($_GET['um_action']=="edit1"){

							?>
							<div style="display: none">
							 <div class="main-container" id="passions">
								<div class="um-shadow passions_section">
									<div class="um-shadow-header">
										<h6>Passions</h6>
									</div>
									<div class="um-shadow-body">
										<div class="gallery-section">
											<?php echo do_shortcode('[my_acf_user_form field_group="431"]'); ?>
										</div>
										
									</div>

								</div>
							</div> 
						</div>
								<?php

							
						} ?>

	<div class="um-form wrapper-with-sidebar">



		<?php

		/**

		 * UM hook

		 *

		 * @type action

		 * @title um_profile_before_header

		 * @description Some actions before profile form header

		 * @input_vars

		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

		 * @change_log

		 * ["Since: 2.0"]

		 * @usage add_action( 'um_profile_before_header', 'function_name', 10, 1 );

		 * @example

		 * <?php

		 * add_action( 'um_profile_before_header', 'my_profile_before_header', 10, 1 );

		 * function my_profile_before_header( $args ) {

		 *     // your code here

		 * }

		 * ?>

		 */

		do_action( 'um_profile_before_header', $args );



		if ( um_is_on_edit_profile() ) { ?>

			<form method="post" action="" class="cs-profile-form">

		<?php }


		/**

		 * UM hook

		 *

		 * @type action

		 * @title um_profile_header_cover_area

		 * @description Profile header cover area

		 * @input_vars

		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

		 * @change_log

		 * ["Since: 2.0"]

		 * @usage add_action( 'um_profile_header_cover_area', 'function_name', 10, 1 );

		 * @example

		 * <?php

		 * add_action( 'um_profile_header_cover_area', 'my_profile_header_cover_area', 10, 1 );

		 * function my_profile_header_cover_area( $args ) {

		 *     // your code here

		 * }

		 * ?>

		 */

		do_action( 'um_profile_header_cover_area', $args );



		/**

		 * UM hook

		 *

		 * @type action

		 * @title um_profile_header

		 * @description Profile header area

		 * @input_vars

		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

		 * @change_log

		 * ["Since: 2.0"]

		 * @usage add_action( 'um_profile_header', 'function_name', 10, 1 );

		 * @example

		 * <?php

		 * add_action( 'um_profile_header', 'my_profile_header', 10, 1 );

		 * function my_profile_header( $args ) {

		 *     // your code here

		 * }

		 * ?>

		 */
   if(isset($_GET['um_action']) && $_GET['um_action']=='edit'){
   		do_action( 'um_profile_header', $args );
   }
 
     
	



		/**

		 * UM hook

		 *

		 * @type filter

		 * @title um_profile_navbar_classes

		 * @description Additional classes for profile navbar

		 * @input_vars

		 * [{"var":"$classes","type":"string","desc":"UM Posts Tab query"}]

		 * @change_log

		 * ["Since: 2.0"]

		 * @usage

		 * <?php add_filter( 'um_profile_navbar_classes', 'function_name', 10, 1 ); ?>

		 * @example

		 * <?php

		 * add_filter( 'um_profile_navbar_classes', 'my_profile_navbar_classes', 10, 1 );

		 * function my_profile_navbar_classes( $classes ) {

		 *     // your code here

		 *     return $classes;

		 * }

		 * ?>

		 */

		$classes = apply_filters( 'um_profile_navbar_classes', '' ); ?>

<div class="um-profile-container right-section">
<?php

if(isset($_GET['experienceId'])){
	$url =  get_permalink( $_GET['experienceId'] ); 
                  ?>
                  <a class="um-button um-custom-book-now" href="<?php echo $url ; ?>">Book Now</a>
                  <?php
                }
                ?>
		<div class="um-profile-navbar <?php echo esc_attr( $classes ); ?>">

			<?php

			/**

			 * UM hook

			 *

			 * @type action

			 * @title um_profile_navbar

			 * @description Profile navigation bar

			 * @input_vars

			 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

			 * @change_log

			 * ["Since: 2.0"]

			 * @usage add_action( 'um_profile_navbar', 'function_name', 10, 1 );

			 * @example

			 * <?php

			 * add_action( 'um_profile_navbar', 'my_profile_navbar', 10, 1 );

			 * function my_profile_navbar( $args ) {

			 *     // your code here

			 * }

			 * ?>

			 */

			do_action( 'um_profile_navbar', $args ); ?>

			<div class="um-clear"></div>

		</div>



		<?php

		/**

		 * UM hook

		 *

		 * @type action

		 * @title um_profile_menu

		 * @description Profile menu

		 * @input_vars

		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

		 * @change_log

		 * ["Since: 2.0"]

		 * @usage add_action( 'um_profile_menu', 'function_name', 10, 1 );

		 * @example

		 * <?php

		 * add_action( 'um_profile_menu', 'my_profile_navbar', 10, 1 );

		 * function my_profile_navbar( $args ) {

		 *     // your code here

		 * }

		 * ?>

		 */

		do_action( 'um_profile_menu', $args );



		if ( um_is_on_edit_profile() ) {



			$nav = 'main';

			$subnav = UM()->profile()->active_subnav();

			$subnav = ! empty( $subnav ) ? $subnav : 'default'; ?>



			<div class="um-profile-body <?php echo esc_attr( $nav . ' ' . $nav . '-' . $subnav ); ?>">



				<?php

				/**

				 * UM hook

				 *

				 * @type action

				 * @title um_profile_content_{$nav}

				 * @description Custom hook to display tabbed content

				 * @input_vars

				 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

				 * @change_log

				 * ["Since: 2.0"]

				 * @usage add_action( 'um_profile_content_{$nav}', 'function_name', 10, 1 );

				 * @example

				 * <?php

				 * add_action( 'um_profile_content_{$nav}', 'my_profile_content', 10, 1 );

				 * function my_profile_content( $args ) {

				 *     // your code here

				 * }

				 * ?>

				 */

				do_action("um_profile_content_{$nav}", $args);



				/**

				 * UM hook

				 *

				 * @type action

				 * @title um_profile_content_{$nav}_{$subnav}

				 * @description Custom hook to display tabbed content

				 * @input_vars

				 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

				 * @change_log

				 * ["Since: 2.0"]

				 * @usage add_action( 'um_profile_content_{$nav}_{$subnav}', 'function_name', 10, 1 );

				 * @example

				 * <?php

				 * add_action( 'um_profile_content_{$nav}_{$subnav}', 'my_profile_content', 10, 1 );

				 * function my_profile_content( $args ) {

				 *     // your code here

				 * }

				 * ?>

				 */

				do_action( "um_profile_content_{$nav}_{$subnav}", $args ); ?>



				<div class="clear"></div>

			</div>


</div>
</div>
		</form>

<?php 


						 } else {

			$menu_enabled = UM()->options()->get( 'profile_menu' );

			$tabs = UM()->profile()->tabs_active();



			$nav = UM()->profile()->active_tab();

			$subnav = UM()->profile()->active_subnav();

			$subnav = ! empty( $subnav ) ? $subnav : 'default';



			if ( $menu_enabled || ! empty( $tabs[ $nav ]['hidden'] ) ) { ?>



				<div class="um-profile-body um-view-section  <?php echo esc_attr( $nav . ' ' . $nav . '-' . $subnav ); ?>">



					<?php

					// Custom hook to display tabbed content

					/**

					 * UM hook

					 *

					 * @type action

					 * @title um_profile_content_{$nav}

					 * @description Custom hook to display tabbed content

					 * @input_vars

					 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

					 * @change_log

					 * ["Since: 2.0"]

					 * @usage add_action( 'um_profile_content_{$nav}', 'function_name', 10, 1 );

					 * @example

					 * <?php

					 * add_action( 'um_profile_content_{$nav}', 'my_profile_content', 10, 1 );

					 * function my_profile_content( $args ) {

					 *     // your code here

					 * }

					 * ?>

					 */
                    if($nav=="main"){
                    	$profile_id = um_profile_id();
                        ?>
                        <style>
                        	.about-information{
                        		display: none;
                        	}
                        	.about-passion{
                        		display: none;
                        	}
                        </style>
						<div class="main-container">
							<div class="subnavigation">
								<ul class="um-sub-menu">
									<li class='active' ><a href="#informations" class="scroll">Information</a>
									</li>
									<li >
										<a href="#being-a-fan" class="scroll">Being a Fan</a>
									</li>
									<li  >
										<a href="#biography" class="scroll">Biography</a>
									</li>
									<!-- <li  > <a href="#photo-gallery" class="scroll">Photo Gallery</a>
									</li> -->
								</ul>
							</div>
							</div>

							<div class="main-container" id="informations">
							   <div class="um-shadow">
								 	<div class="um-shadow-header">
										 <h6>Information</h6>
										 <?php
													if($_GET['um_action']=="edit1"){ ?>
										 <p class="sub-heading"><?php the_field('about_information_heading','options');?></p>
													<?php } ?>
								 </div>
								 	<div class="um-shadow-body">
								 		<div class="included-services">
								 			<div class="single-information-section">
								 				 <div class="single-icon">
								 				 	<i class="fa fa-user"></i>
								 				 </div>
								 				 <div class="single-details">
								 				 	<h6>Full Name</h6>
								 				 	<p class="where-we-meet-details">
								 				 		<?php echo  get_user_meta( $profile_id , "full_name" ,true); ?>
								 				 	</p>
								 				 	
								 				 </div>
								 			</div>
								 			<div class="single-information-section">
								 				 <div class="single-icon">
								 				 	<i class="fa fa-map-marker "></i>
								 				 </div>
								 				 <div class="single-details">
								 				 	<h6 class="where-we-meet-details">Where I Live</h6>
								 				 	<p class="where-we-meet-details">
								 				 		<?php echo  get_user_meta( $profile_id , "street_address" ,true) .' '. get_user_meta( $profile_id , "city" ,true); ?>
								 				 	</p>
								 				 </div>
								 			</div>
								 			<div class="single-information-section">
								 				 <div class="single-icon">
								 				 	<i class="fa fa-calendar"></i>
								 				 </div>
								 				 <div class="single-details">
								 				 	<h6 class="where-we-meet-details">Ticket Subscription</h6>
								 				 	<p class="where-we-meet-details">
								 				 		<?php echo  get_user_meta( $profile_id , "season-ticket-holder" ,true); ?></p>
								 				 </div>
								 			</div>
								 			<div class="single-information-section">
								 				 <div class="single-icon">
								 				 	<i class="fa fa-life-ring "></i>
								 				 </div>
								 				 <div class="single-details"> 
								 				 	<h6 class="where-we-meet-details "  >Stadium Position</h6>
								 				 	<?php 
								 				 	$stadium_position =  get_user_meta( $profile_id, 'stadium-position', 'true');
								 				 	?>
								 				 	<p class="where-we-meet-details">
								 				 		<?php foreach ($stadium_position as $key => $value) {
										                 echo $value . " , ";
										             } ?>
								 				 	</p>
								 				 </div>
								 			</div>
								 		</div>
								 		 		
								 	</div>
							      </div>
							</div>
						<?php
						
					}

					do_action("um_profile_content_{$nav}", $args);

                  

					/**

					 * UM hook

					 *

					 * @type action

					 * @title um_profile_content_{$nav}_{$subnav}

					 * @description Custom hook to display tabbed content

					 * @input_vars

					 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]

					 * @change_log

					 * ["Since: 2.0"]

					 * @usage add_action( 'um_profile_content_{$nav}_{$subnav}', 'function_name', 10, 1 );

					 * @example

					 * <?php

					 * add_action( 'um_profile_content_{$nav}_{$subnav}', 'my_profile_content', 10, 1 );

					 * function my_profile_content( $args ) {

					 *     // your code here

					 * }

					 * ?>

					 */

					do_action( "um_profile_content_{$nav}_{$subnav}", $args ); 

if($_GET['profiletab']=="main"){
		?>
<!-- <div class="main-container" id="photo-gallery">
<div class="um-shadow">
	<div class="um-shadow-header">
		<h6>Photo gallery</h6>
		<?php 
		   if($profile_id==get_current_user_id()){
			 ?>
		<a href="?profiletab=gallery" class="pull-right edit-profile-btn">Edit Images</a>
	<?php } ?>
		
	</div>
	<div class="um-shadow-body">
		<div class="gallery-section">
			<?php echo do_shortcode('[um_gallery_recent_photos_grid ]'); ?>
		</div>
		
	</div>

</div>
</div> -->

<?php
		}
		?>

					<div class="clear"></div>

				</div>



			<?php }

		}

		
		
		do_action( 'um_profile_footer', $args ); ?>

	</div>

</div>