<?php
/**
 * Template Name: Front event list template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 *
 * @package um-theme
 */
global $defaults, $wpdb;
get_header();
$event_Table = $wpdb->prefix.'events';
?>

<main id="primary" class="content-area" tabindex="-1">
<div id="main" class="site-main">
		<?php
			// Elementor `archive` location
			if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) { ?>

				<div class="website-canvas">
				<div class="boot-row">
					<div class="boot-col-3 boot-order-first">
						<form class="event-search-box" method="GET">
							<?php 
							$all_country = $wpdb->get_results( "SELECT DISTINCT(country_name) FROM $event_Table ORDER BY `country_name` " );
							$all_teams = $wpdb->get_results( "SELECT DISTINCT(match_hometeam_name) FROM $event_Table ORDER BY `match_hometeam_name` " );
							?>
							<p>Country</p>

							<p>
							<select name="by_country" id="by_country">
								<option value="">All</option>
							<?php foreach ($all_country as $ev_country) {
								 echo '<option value="'.trim($ev_country->country_name).'">'.trim($ev_country->country_name).'</option>';
							}
							?>
							</select>
							</p>
							<p>City</p>
							<p><input type="text" name="by_city"></p>
							<p>Team</p>
							<p>
								<select name="by_team" id="by_team">
								<option value="">All</option>
									<?php foreach ($all_teams as $ev_team) {
										 echo '<option value="'.trim($ev_team->match_hometeam_name).'">'.trim($ev_team->match_hometeam_name).'</option>';
									}
									?>
							</select>
							</p>
							<p>Date/p>
							<p><input type="date" name="by_date"></p>
							<input type="submit" value="Search Now">
						</form>
					<?php 
					// if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('event-page-sidebar') ) : 
	 
					// endif; 
					?>
					</div>
					<div class="template-blog boot-col-9 boot-order-second">
						<a href="<?php echo get_site_url(); ?>/event">Calendar View</a>
						<div class="boot-row">

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