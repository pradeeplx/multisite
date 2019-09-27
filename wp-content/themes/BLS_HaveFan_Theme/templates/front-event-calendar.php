<?php
/**
 * Template Name: Front event template
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
              
				
				<div class="website-canvas ">
					<?php if(get_field('event_page_banner_image','options')){ ?>
				<div class="banner-image-evant">
						<img src="<?php the_field('event_page_banner_image','options'); ?>" >
					
				</div>
			<?php } ?>
				<div class="boot-row">
					<div class="boot-col-3  boot-order-first">
						<? echo do_shortcode('[default_hf_search_form layout="vertical"]');?>
					</div>
					<div class="template-blog boot-col-9 boot-order-second ">
						<div class="boot-row">
							 <div class="main-container">
								<div class="event_subnavigation">
									<?php
                                    $getString='?';
                                    foreach ($_GET as $key => $value) {
                                    	$getString.= $key .'=' . $value . '&';
                                    }
									 ?>
									
									<ul class="um-event-list-menu ">
										<li class="calendar-btn event-btn-list-calendar <?php if($_GET['view']=='calendar' || !isset($_GET['view']) ){ echo 'active'; } ?>" ><a onclick="listOutEvant('calendar','<?php echo $getString; ?>')" href="JavaScript:void(0);">Calendar View</a>
										</li>
										<li class="list-btn event-btn-list-calendar <?php if($_GET['view']=='list'){ echo 'active'; } ?>">
											<a onclick="listOutEvant('list','<?php echo $getString; ?>')" href="JavaScript:void(0);" >List View</a>
										</li>
										
									</ul>
								</div>
							</div>
							<div id="event-view" >
								<?php
								$default_date_calendar = date('Y-m-d');
								if(isset($_GET['by_date'])){
									$default_date_calendar = ( trim( $_GET['by_date'] ) != '') ? trim($_GET['by_date']) : date('Y-m-d');
								}
								if($_GET['view']=='list'){
						          echo do_shortcode('[fooevents_events_list]'); 
								}else{
                                  echo do_shortcode('[fooevents_calendar  defaultDate= "'.$default_date_calendar.'"]'); 
                                   
								}
						// echo do_shortcode('[fooevents_events_list]'); 
                           
						?>
							</div>
						
						</div>
					</div>
					
				</div>
				</div>
			<?php }?>

</div><!-- #primary -->
</main><!-- #main -->
<?php
get_footer();

