<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add user rating in members directory
 *
 * @param $user_id
 * @param $args
 */
function um_reviews_add_rating( $user_id, $args ) {
	if ( ! UM()->options()->get( 'members_show_rating' ) ) {
		return;
	}

	$args_t = compact( 'args', 'user_id' );
	UM()->get_template( 'member-rating.php', um_reviews_plugin, $args_t, true );

	wp_enqueue_script( 'um_reviews' );
	wp_enqueue_style( 'um_reviews' );
}
add_action( 'um_members_after_user_name', 'um_reviews_add_rating', 50, 2 );


/**
 * Needed for new user signups, make empty avg rating
 *
 * @param $user_id
 */
function um_reviews_sync_new_user( $user_id ) {
	if ( ! get_user_meta( $user_id, '_reviews_avg', true ) ) {
		update_user_meta( $user_id, '_reviews_avg', 0.00 );
	}
}
add_action( 'um_after_user_is_approved', 'um_reviews_sync_new_user' );


/**
 * Undo all reviews from this user and to this user
 *
 * @param int $user_id
 */
function um_reviews_undo_review_on_user_delete( $user_id ) {
	$args = array(
		'post_type'         => 'um_review',
		'posts_per_page'    => -1,
		'post_status'       => array( 'publish' ),
		'meta_query'        => array(
			'relation'  => 'OR',
			array(
				'key'     => '_reviewer_id',
				'value'   => $user_id,
				'compare' => '='
			),
			array(
				'key'     => '_user_id',
				'value'   => $user_id,
				'compare' => '='
			)
		),
		'fields' => 'ids'
	);

	$reviews = get_posts( $args );

	if ( empty( $reviews ) ) {
		return;
	}

	foreach ( $reviews as $review_id ) {
		UM()->Reviews_API()->api()->undo_review( $review_id );
		wp_delete_post( $review_id, true );
		UM()->Reviews_API()->api()->adjust_rating( $review_id );// update rating
	}
}
add_action( 'um_delete_user', 'um_reviews_undo_review_on_user_delete', 10 );