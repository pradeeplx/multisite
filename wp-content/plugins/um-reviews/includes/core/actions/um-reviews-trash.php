<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Move to Trash Review
 *
 * @param $postid
 */
function trash_um_review( $postid ) {
	if ( ! did_action( 'trash_post' ) ) {
		UM()->Reviews_API()->api()->undo_review( $postid );
		UM()->Reviews_API()->api()->adjust_rating( $postid );// update rating
	}
}
add_action( 'trash_um_review', 'trash_um_review' );


/**
 * Restore from Trash Review
 *
 * @param $postid
 */
function untrash_um_review( $postid ){
	if ( get_post_type( $postid ) != 'um_review' ) {
		return;
	}

	UM()->Reviews_API()->api()->publish_review( $postid );
}
add_action( 'untrash_post', 'untrash_um_review' );