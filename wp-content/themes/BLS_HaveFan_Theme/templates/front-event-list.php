<?php
/**
 * Template Name: Front event list template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 *
 * @package um-theme
 */
global $defaults;
get_header();?>

<main id="primary" class="content-area" tabindex="-1">
<div id="main" class="site-main">
		<?php
			// Elementor `archive` location
			if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) { ?>

				<div class="website-canvas">
				<div class="boot-row">
					 
					<div class="template-blog boot-col-11" style="margin: auto;">
						<div class="boot-row">
                         <div class="main-container">
							<div class="subnavigation">
								<ul class="um-sub-menu">
									<li class=""><a href="<?php echo get_site_url(); ?>/event">Calendar View</a>
									</li>
									<li class="active">
										<a href="<?php echo get_site_url(); ?>/event/event-list/" class="scroll">List View</a>
									</li>
									<!-- <li  > <a href="#photo-gallery" class="scroll">Photo Gallery</a>
									</li> -->
								</ul>
							</div>
							</div>
						<?php
						 //echo do_shortcode('[fooevents_calendar]'); 
						 echo do_shortcode('[fooevents_events_list]'); 
                           
						?>
						</div>
					</div>
					
				</div>
				</div>
			<?php }?>

</div><!-- #primary -->
</main><!-- #main -->
<?php
get_footer();

