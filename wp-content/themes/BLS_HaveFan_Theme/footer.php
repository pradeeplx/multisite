<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package um-theme
 */

global $defaults;
?>
</div>
</div><!-- site-content -->
</div><!-- Row -->
<?php
if( isset($_GET['profiletab'] ) && $_GET['profiletab'] == 'experience' ){
	$curret_date = date('Y-m-d');
	$um_profile_id = um_profile_id();
 	$userInfo = get_user_by('ID', $um_profile_id);   
   
    // The Query
    $guest_arg1 = array(
        'post_type' => 'product',
        'status' => array('publish'),
        'author' => $um_profile_id,
        'posts_per_page' => 1,
        'paged' => 1,
        'meta_key' => 'MatchDate',
        'orderby'   => 'meta_value',
        'order'     => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'MatchDate',
                'value' => $curret_date,
                'compare' => '>=',
                'type' => 'DATE',
                )
            )
        );
    $the_query1 = new WP_Query( $guest_arg1 );
    if ( $the_query1->have_posts() ) {
    	?>
    	<div class="single-experience-bar">
    	<?php
    	 while ( $the_query1->have_posts() ) {
            $the_query1->the_post();
            $prod_id = get_the_ID();
            $product_data = new WC_Product( $prod_id );
            $MatchDate = get_post_meta( $prod_id, 'MatchDate', true);
            $host_product_url = site_url('user/'.$userInfo->user_login.'/?profiletab=next-matches');
            ?>
            <div class="single-experience-one single-experience-box">
            	<?php the_title();?>
            </div>
            <div class="single-experience-two single-experience-box">
            	<?php echo date('d F Y', strtotime($MatchDate));?>
            </div>
            <div class="single-experience-three single-experience-box">
            	<span class="price-heading">Price</span>
                   
                  <?php echo $product_data->get_price_html();?>                  
                </div>
            
            <div class="single-experience-four">
            	<a href="<?php echo $host_product_url;?>">Check Availability</a>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
    }
    wp_reset_postdata();
}

 ?>

<?php do_action( 'um_theme_before_footer' ); ?>
	<footer id="colophon" class="site-footer">
		<?php
			// Elementor `footer` location
			if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
				/**
				* Functions hooked in to um_theme_footer action
				*
				* @hooked um_theme_footer_widgets - 10
				* @hooked um_theme_footer_bottom_content - 20
				*/
				do_action( 'um_theme_footer' );
			}
		?>
	</footer>
<?php do_action( 'um_theme_after_footer' ); ?>
<a href="#0" class="scrollToTop"><p class="fas fa-chevron-up" style="display: inline-block;"></p></a>
</div><!-- site-content -->
<?php wp_footer(); ?>
</body>
</html>
