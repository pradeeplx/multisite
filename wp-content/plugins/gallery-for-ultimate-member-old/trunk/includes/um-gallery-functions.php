<?php
/**
 * Get the recent photos uploaded by user
 *
 * @param  array  $args [description]
 * @return array
 */
function um_gallery_recent_photos( $args = array() ) {
	/**
	 * Define the array of defaults
	 */
	$defaults = array(
		'user_id' => '',
		'id' => "",
		'offset' => "0",
		'amount' => "10",
	);

	/**
	 * Parse incoming $args into an array and merge it with $defaults
	 */
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	$sql_where = array();
	$sql_where[] = ' 1=1';
	if( !empty($args['user_id']) ) {
		$user_id_lists = explode(',', $args['user_id']);
		if (count($user_id_lists) > 1) {
			$sql_where[] = ' a.user_id IN ('.implode(',', $user_id_lists).')';
		}else{
			$sql_where[] = ' a.user_id = "'.$user_id_lists[0].'" ';
		}
	}
	if( !empty($args['id']) ) {
		$id_lists = explode(',', $args['id']);
		if (count($id_lists) > 1) {
			$sql_where[] = ' a.id IN (' . implode( ',', $id_lists ) . ')';
		}else{
			$sql_where[] = ' a.id = "' . $id_lists[0] . '" ';
		}
	}

	global $wpdb;
	$query = "SELECT a.* FROM {$wpdb->prefix}um_gallery AS a WHERE ".implode(' AND ', $sql_where). " ORDER BY a.id DESC LIMIT {$offset}, {$amount}";
	$items = $wpdb->get_results($query);
	return $items;
}

/**
 * Get user albums and 1 photo for a user
 *
 * @param  integer $user_id [description]
 * @return array
 */
function um_gallery_by_userid( $user_id = 0 ) {
	global $wpdb;
	$query = "SELECT a.*,d.file_name, COUNT(d.id) AS total_photos, d.type FROM {$wpdb->prefix}um_gallery_album AS a LEFT JOIN {$wpdb->prefix}um_gallery AS d ON a.id=d.album_id WHERE a.user_id='{$user_id}' GROUP BY a.id ORDER BY a.id DESC";
	$albums = $wpdb->get_results($query);
	return $albums;
}

/**
 * Get photos by album_id
 *
 * @param  integer $album_id
 *
 * @return array
 */
function um_gallery_photos_by_album( $album_id = 0, $include_taxonomies = false ) {
	global $wpdb;
	
	if ( ! $include_taxonomies ) {
		$query = "SELECT a.* FROM {$wpdb->prefix}um_gallery AS a WHERE a.album_id='{$album_id}' ORDER BY a.id DESC";
	} else {
		$query = "SELECT DISTINCT a.*, 
			FROM {$wpdb->prefix}um_gallery AS a
			LEFT JOIN $wpdb->term_relationships AS tag ON (a.id = tag.object_id)
			LEFT JOIN $wpdb->term_taxonomy AS tag_tax ON (tag.term_taxonomy_id = tag_tax.term_taxonomy_id)
			LEFT JOIN $wpdb->term_relationships AS category ON (a.id = category.object_id)
			LEFT JOIN $wpdb->term_taxonomy AS category_tax ON (category.term_taxonomy_id = category_tax.term_taxonomy_id)
			WHERE category_tax.taxonomy = 'um_gallery_category' 
			AND tag_tax.taxonomy = 'um_gallery_tag' 
			AND a.album_id = '{$album_id}'";
		$query = "SELECT a.* FROM {$wpdb->prefix}um_gallery AS a WHERE a.album_id='{$album_id}' ORDER BY a.id DESC";
	}
	$photos = $wpdb->get_results($query);
	return $photos;
}

/**
 * Get photos by ID.
 *
 * @param  integer $photo_id
 *
 * @return array
 */
function um_gallery_photo_by_id( $photo_id = 0) {
	global $wpdb, $photo;
	$query = "SELECT p.* FROM {$wpdb->prefix}um_gallery AS p WHERE p.id='{$photo_id}'";
	$item  = $wpdb->get_row( $query );
	$photo = um_gallery_setup_photo($item);
	return $photo;
}

/**
 * Get default thumbnail
 *
 * @return [type] [description]
 */
function um_gallery_default_thumb() {
	return apply_filters('um_gallery_default_image', um_gallery()->plugin_url . 'assets/images/default.jpg');
}

/**
 * Setup User data.
 *
 * @return array Return array of users.
 */
function um_gallery_setup_user( $users = array(), $photo = array() ) {
	if ( empty( $users[ $photo->user_id ] ) ) {
		um_fetch_user( $photo->user_id );
		$users[ $photo->user_id ]  = array(
			'id'     => $photo->user_id,
			'name'   => um_user( 'display_name' ),
			'link'   => um_user_profile_url(),
			'avatar' => um_get_user_avatar_data( null, 50),
		);
		um_reset_user();
	}
	return $users;
}

function um_gallery_get_user_details( $detail = '', $user_id = 0, $users = array() ) {
	global $photo;
	if ( ! $user_id && ! empty( $photo->user_id ) ) {
		$user_id = $photo->user_id;
	}

	if ( ! empty( $users[ $user_id ][ $detail ] ) ) {
		return $users[ $user_id ][ $detail ];
	}

	return;
}
/**
*	Setup photo data
*
*	Setup array for photo data to e used in loop
*
* @return array
**/
function um_gallery_setup_photo( $photo = array(), $include_taxonomies = false ) {
	//global $photo;
	$photo->caption = ! empty( $photo->caption ) ? $photo->caption : um_gallery_safe_name( $photo->file_name );
	if ( empty( $photo->type ) ) {
		
		if ( strpos( $photo->file_name, 'youtube' ) > 0 ) {
			$photo->type = 'youtube';
		} elseif( strpos( $photo->file_name, 'vimeo') > 0 ) {
			$photo->type = 'vimeo';
		} else {
			$photo->type = 'photo';
		}
	}

	
	$photo->thumbnail_url = um_gallery()->get_user_image_src( $photo );
	$photo->full_url      = um_gallery()->get_user_image_src( $photo, 'full' );

	if ( $include_taxonomies ) {
		if ( um_gallery_pro_addon_enabled( 'category' ) ) {
			$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all' );
			$category = wp_get_object_terms( $photo->id,  'um_gallery_category', $args );
			$photo->category = wp_list_pluck( $category, 'name' );
			$photo->category_ids = wp_list_pluck( $category, 'term_id' );
		}
		if ( um_gallery_pro_addon_enabled( 'tags' ) ) {
			$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names' );
			$photo->tags = wp_get_object_terms( $photo->id,  'um_gallery_tag', $args );
		}
	}

	$photo->media_url     = um_gallery()->um_gallery_get_media_url( $photo );
	$photo->current_user  = get_current_user_id();
	$photo->user_id       = absint( $photo->user_id );

	return $photo;
}

/**
 * Display the photo ID
 *
 * @since  1.0.4.2
 */
function um_gallery_the_id() {
	echo um_gallery_get_id();
}

/**
 * Get the photo ID
 *
 * @since  1.0.4.2
 * 
 * @return integer
 */
function um_gallery_get_id() {
	global $photo;
	return ! empty( $photo->id ) ? absint( $photo->id ) : 0;
}

/**
 * Display the photo ID
 *
 * @since  1.0.4.2
 */
function um_gallery_the_image_url( $id = 0, $size = 'thumbnail' ) {
	echo um_gallery_get_image_url( $id, $size );
}

/**
 * Get the photo ID
 *
 * @since  1.0.4.2
 * 
 * @return integer
 */
function um_gallery_get_image_url( $id = 0, $size = 'thumbnail' ) {
	global $photo;
	if ( ! $id ) {
		if ( ! empty( $photo->id ) ) {
			$id = absint( $photo->id );		
		} else {
			// Bail.
			return;
		}
	}
	if ( 'thumbnail' == $size ) {
		return esc_url( $photo->thumbnail_url );
	}
	return esc_url( $photo->full_url );
}

/**
 * Display the Media URL.
 *
 * @since  1.0.4.2
 */
function um_gallery_the_media_url() {
	echo um_gallery_get_media_url();
}

/**
 * Get the Media URL.
 *
 * @since  1.0.4.2
 * 
 * @return string
 */
function um_gallery_get_media_url() {
	global $photo;
	return ! empty( $photo->media_url ) ? esc_attr( $photo->media_url ) : '';
}

function um_gallery_get_album_feature_media( $album_id = 0 ) {
	global $wpdb;
	if ( ! $album_id ) {
		return;
	}

	$query = "SELECT a.* FROM {$wpdb->prefix}um_gallery AS a WHERE a.album_id='{$album_id}' ORDER BY a.id DESC LIMIT 0, 1";
	$photo = $wpdb->get_row( $query );
	$photo = um_gallery_setup_photo( $photo );
	return $photo;
}

function um_gallery_get_album_feature_media_url( $album_id = 0, $size = 'thumbnail' ) {
	$photo = um_gallery_get_album_feature_media( $album_id );
	if ( 'thumbnail' == $size ) {
		$url = um_gallery()->get_user_image_src( $photo );
	} else {
		$url = um_gallery()->get_user_image_src( $photo, 'full' );
	}
	return $url;
}
function um_gallery_data( $image ) {
	return array(
		    'id' 			=> $image->id,
			'user_id' 		=> $image->user_id,
			'caption' 		=> $image->caption,
            'type'          => $image->type,
			'description' 	=> esc_html($image->description),
		);
}

/**
 * Get number of photos in album
 *
 * @return integer
 */
function um_gallery_photos_count() {
	global $album;
	return (int)$album->total_photos;
}

/**
 * Get number of photos text
 *
 * @return string
 */
function um_gallery_photos_count_text() {
	$count = um_gallery_photos_count();
	$text = sprintf( _n( '%s photo', '%s photos', $count, 'um-gallery-pro' ), number_format_i18n( $count ) );
	return $text;
}

/**
 * Make file name ready database
 * @param  string $file_name [description]
 * @return string
 */
function um_gallery_safe_name( $file_name = '' ) {
	$filetype = wp_check_filetype( $file_name );
	$file_name = basename( $file_name, "." .$filetype['ext'] );
	return $file_name;
}

/**
 * Gallery URL
 *
 * @return string
 */
function um_gallery_profile_url() {
	$url = um_user_profile_url();
	$url = remove_query_arg('profiletab', $url);
	$url = remove_query_arg('subnav', $url);
	$url = add_query_arg( 'profiletab', um_gallery()->template->gallery, $url );
	return $url;
}

/**
 * Gets an Album URL
 *
 * @return string
 */
function um_gallery_album_url() {
	global $album;
	um_fetch_user( $album->user_id );
	$url = um_user_profile_url();
	$url = remove_query_arg('profiletab', $url);
	$url = remove_query_arg('subnav', $url);
	$url = add_query_arg( 'profiletab', um_gallery()->template->gallery, $url );
	if ( ! empty( $album->id ) ) {
    	$url = add_query_arg( 'album_id',  $album->id, $url );
	}
	um_reset_user();
    return $url;
}

/**
 * Get ALbum ID from address bar
 *
 * @return integer
 */
function um_galllery_get_album_id() {
	$album_id = 0;
	if( isset($_GET) && !empty($_GET['album_id']) ) {
		$album_id = (int)$_GET['album_id'];
	}
	return $album_id;
}

/**
 * Get album data by ID
 *
 * @param  integer $album_id Album ID to query
 * @return array
 */
function um_gallery_album_by_id( $album_id = 0 ) {
	global $wpdb;
	$query = "SELECT a.*,d.file_name, COUNT(d.id) AS total_photos, d.type FROM {$wpdb->prefix}um_gallery_album AS a LEFT JOIN {$wpdb->prefix}um_gallery AS d ON a.id=d.album_id WHERE a.id='{$album_id}'";
	$album = $wpdb->get_row($query);
	return $album;
}

/**
 * Get album data by ID
 *
 * @param  integer $photo_id Album ID to query.
 *
 * @return array
 */
function um_gallery_album_by_photo_id( $photo_id = 0 ) {
	global $wpdb;
	$query = "SELECT a.*,d.file_name, d.type FROM {$wpdb->prefix}um_gallery_album AS a LEFT JOIN {$wpdb->prefix}um_gallery AS d ON a.id=d.album_id WHERE d.id='{$photo_id}'";
	$album = $wpdb->get_row($query);
	return $album;
}

/**
 * Perform photo delete from database and removes file
 *
 * @param  integer $photo_id ID to delete
 * @return void
 */
function um_gallery_delete_photo( $photo_id = 0 ) {
	global $wpdb;
	$file = $wpdb->get_row( $wpdb->prepare( "SELECT file_name, user_id, type FROM {$wpdb->prefix}um_gallery WHERE id ='%d'", $photo_id ) );
	$wpdb->delete( $wpdb->prefix . 'um_gallery', array( 'id' => $photo_id ) );
	if( 'youtube' != $file->type && 'vimeo' != $file->type ) {
		$file_url = um_gallery()->get_user_image_path( $file->user_id, $file->file_name );
		unlink( $file_url );
		$file_url = um_gallery()->get_user_image_path( $file->user_id, $file->file_name, 'none' );
		unlink( $file_url );
	}
	do_action( 'um_gallery_photo_deleted', $photo_id );
}
/**
* Delete album
*
* Remove album from database and all photos under album
*
* @param string $album_id
*
* @return void
*/
function um_gallery_delete_album( $album_id = 0 ) {
	global $wpdb;

	// make sure logged in user can delete album
	if ( ! is_user_logged_in() ) {
		return;
	}

	// get album data
	$album = um_gallery_album_by_id( $album_id );

	// find all photos for this album
	$images = um_gallery_photos_by_album( $album_id );

	// loop through each image for deleting
	if ( ! empty($images) ) {
		foreach ($images as $item) {
			// delete photo.
			um_gallery_delete_photo( $item->id );
		}
	}

	// delete album :(
	$wpdb->delete( $wpdb->prefix.'um_gallery_album', array( 'id' => $album_id ) );

	// action for developers
	do_action('um_gallery_album_deleted', $album_id);
}

/**
* Get all users with album
*
* @return array User IDs
**/
function um_gallery_get_users() {
	global $wpdb;
	$query = "SELECT a.user_id FROM {$wpdb->prefix}um_gallery_album AS a LEFT JOIN {$wpdb->users} AS d ON a.user_id=d.ID GROUP BY a.user_id ORDER BY d.display_name DESC";
	$users = $wpdb->get_col($query);
	return $users;
}
/**
 * Get images uploaded by user id
 *
 * @param  integer $user_id
 * @return array
 */
function get_images_by_user_id( $user_id = 0 ) {
	global $wpdb;
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}um_gallery WHERE user_id=%d", $user_id));
	return $results;
}
/**
 * Get link to user gallery on profile
 *
 * @param  string $id
 *
 * @return string
 */
function um_get_gallery_link( $id='' ) {
	$slug = 'gallery';
    $url = um_user_profile_url();
    $url = remove_query_arg('profiletab', $url);
    $url = remove_query_arg('subnav', $url);
    $url = add_query_arg( 'profiletab', $slug, $url );
    //$url = add_query_arg( 'view',  'edit_doc', $url );
	if($id) {
    	$url = add_query_arg( 'view',  $id, $url );
	}
    return $url;
}

/**
 * Enable the skipping of creating an album
 *
 * @return boolean
 *
 * @since 1.0.6
 */
function um_gallery_allow_albums() {

	$option = um_gallery_pro_get_option( 'um_gallery_single_album', 0 );
	
	return true;
}

/**
 * Display cropped images
 *
 * @return boolean
 *
 * @since 1.0.6
 */
function um_gallery_use_cropped_images() {
	$option = um_gallery_pro_get_option( 'um_gallery_cropped_images' );

	//if nothing has been set then true
	if ( empty( $option ) ) {
		return true;
	}
	//if something is set and it true then return true
	if (  $option && 'off' == $option  ) {
		return false;
	}
	return true;
}
/**
 * Get the first album created by a user
 *
 * @param  integer $user_id [description]
 * @return integer
 */
function um_gallery_get_default_album( $user_id = 0 ) {
	global $wpdb;
	$query = "SELECT a.id FROM {$wpdb->prefix}um_gallery_album AS a WHERE a.user_id = '{$user_id}' ORDER BY a.id ASC LIMIT 0, 1 ";
	$album_id = $wpdb->get_var($query);
	return $album_id;
}

function um_gallery_can_moderate() {
	//get role setting
	$allowed_roles = um_gallery_pro_get_option( 'allowed_roles' );
	//if empty then it's
	if ( empty( $allowed_roles ) ) {
		return true;
	}
	//get profile ID
	$profile_id = um_get_requested_user();

	if ( function_exists( 'UM' ) ) {
		// get user Role.
		$role = UM()->roles()->get_all_user_roles( $profile_id );
		$can_add= false;
		if ( ! empty( $role ) ) {
			foreach ( $role as $r ) {
				if ( in_array( $r, $allowed_roles ) ) {
					$can_add = true;
					break;
				}
			}
		}
	} else {
		// get user Role.
		$role = get_user_meta( $profile_id, 'role', true );
		$can_add = in_array( $role, $allowed_roles );
	}
	//check if profle is in array
	if ( $can_add ) {
		return true;
	}
	//return false
	return false;
}


function um_gallery_allowed_on_profile() {
	//get role setting
	$allowed_roles = um_gallery_pro_get_option( 'allowed_roles' );
	//if empty thenbail.
	if ( empty( $allowed_roles ) ) {
		return true;
	}
	//get profile ID
	$profile_id = um_get_requested_user();

	if ( function_exists( 'UM' ) ) {
		// get user Role.
		$role = UM()->roles()->get_all_user_roles( $profile_id );
		$can_add= false;
		if ( ! empty( $role ) ) {
			foreach ( $role as $r ) {
				if ( in_array( $r, $allowed_roles ) ) {
					$can_add = true;
					break;
				}
			}
		}
	} else {
		// get user Role.
		$role = get_user_meta( $profile_id, 'role', true );
		$can_add = in_array( $role, $allowed_roles );
	}
	//check if profle is in array
	if ( $can_add ) {
		return true;
	}
	//return false
	return false;
}
/**
 * Enable uploading photos from main profile
 *
 * @return boolean
 *
 * @since 1.0.6
 */
function um_gallery_allow_quick_upload() {
	return um_gallery_allow_albums();
	// TODO: Remove lines below since 1.0.6
	return false;
}

/**
 * [um_gallery_exif description]
 * @param  string $file [description]
 * @return [type]       [description]
 */
function um_gallery_exif( $file = '' ) {
	//This line reads the EXIF data and passes it into an array
	$exif = read_exif_data($file['file']);
	//We're only interested in the orientation
	$exif_orient = isset($exif['Orientation'])?$exif['Orientation']:0;
	$rotateImage = 0;
	//We convert the exif rotation to degrees for further use
	if (6 == $exif_orient) {
		$rotateImage = 90;
		$imageOrientation = 1;
	} elseif (3 == $exif_orient) {
		$rotateImage = 180;
		$imageOrientation = 1;
	} elseif (8 == $exif_orient) {
		$rotateImage = 270;
		$imageOrientation = 1;
	}
	//if the image is rotated
	if ($rotateImage) {
		//WordPress 3.5+ have started using Imagick, if it is available since there is a noticeable difference in quality
		//Why spoil beautiful images by rotating them with GD, if the user has Imagick
		if (class_exists('Imagick')) {
			$imagick = new Imagick();
			$imagick->readImage($file['file']);
			$imagick->rotateImage(new ImagickPixel(), $rotateImage);
			$imagick->setImageOrientation($imageOrientation);
			$imagick->writeImage($file['file']);
			$imagick->clear();
			$imagick->destroy();
		} else {
			//if no Imagick, fallback to GD
			//GD needs negative degrees
			$rotateImage = -$rotateImage;
			switch ($file['type']) {
			    case 'image/jpeg':
			    $source = imagecreatefromjpeg($file['file']);
			    $rotate = imagerotate($source, $rotateImage, 0);
			    imagejpeg($rotate, $file['file']);
			        break;
			    case 'image/png':
			    $source = imagecreatefrompng($file['file']);
			    $rotate = imagerotate($source, $rotateImage, 0);
			    imagepng($rotate, $file['file']);
			    break;
			    case 'image/gif':
			    $source = imagecreatefromgif($file['file']);
			    $rotate = imagerotate($source, $rotateImage, 0);
			    imagegif($rotate, $file['file']);
			        break;
			    default:
			        break;
			}
		}
	}
	// The image orientation is fixed, pass it back for further processing
	return $file;
}


/**
* Check if the EXIF orientation flag matches one of the values we're looking for
* http://www.impulseadventure.com/photo/exif-orientation.html
*
* If it does, this means we need to rotate the image based on the orientation flag and then remove the flag.
* This will ensure the image has the correct orientation, regardless of where it's displayed.
*
* Whilst most browsers and applications will read this flag to perform the rotation on displaying just the image, it's
* not possible to do this in some situations e.g. displaying an image within a lightbox, or when the image is
* within HTML markup.
*
* Orientation flags we're looking for:
* 8: We need to rotate the image 90 degrees counter-clockwise
* 3: We need to rotate the image 180 degrees
* 6: We need to rotate the image 90 degrees clockwise (270 degrees counter-clockwise)
*/
function um_gallery_fix_image_orientation( $file = array() ) {

	// Check we have a file
	if ( ! empty( $file['file'] ) && ! file_exists( $file['file'] ) ) {
		return $file;
	}

	// Attempt to read EXIF data from the image
	$exif_data = wp_read_image_metadata( $file['file'] );
	if ( ! $exif_data ) {
		return $file;
	}

	// Check if an orientation flag exists
	if ( ! isset( $exif_data['orientation'] ) ) {
		return $file;
	}

	// Check if the orientation flag matches one we're looking for
	$required_orientations = array( 8, 3, 6 );
	if ( ! in_array( $exif_data['orientation'], $required_orientations ) ) {
		return $file;
	}

	// If here, the orientation flag matches one we're looking for
	// Load the WordPress Image Editor class
	$image = wp_get_image_editor( $file['file'] );
	if ( is_wp_error( $image ) ) {
		// Something went wrong - abort
		return $file;
	}

	// Store the source image EXIF and IPTC data in a variable, which we'll write
	// back to the image once its orientation has changed
	// This is required because when we save an image, it'll lose its metadata.
	$source_size = getimagesize( $file['file'], $image_info );

	// Depending on the orientation flag, rotate the image
	switch ( $exif_data['orientation'] ) {

		/**
		* Rotate 90 degrees counter-clockwise
		*/
		case 8:
			$image->rotate( 90 );
			break;

		/**
		* Rotate 180 degrees
		*/
		case 3:
			$image->rotate( 180 );
			break;

		/**
		* Rotate 270 degrees counter-clockwise ($image->rotate always works counter-clockwise)
		*/
		case 6:
			$image->rotate( 270 );
			break;

	}

	// Save the image, overwriting the existing image
	// This will discard the EXIF and IPTC data
	$image->save( $file['file'] );

	// Drop the EXIF orientation flag, otherwise applications will try to rotate the image
	// before display it, and we don't need that to happen as we've corrected the orientation

	// Write the EXIF and IPTC metadata to the revised image
	$result = um_gallery_transfer_iptc_exif_to_image( $image_info, $file['file'], $exif_data['orientation'] );
	if ( ! $result ) {
		return $file;
	}

	// Finally, return the data that's expected
	return $file;

}

/**
* Transfers IPTC and EXIF data from a source image which contains either/both,
* and saves it into a destination image's headers that might not have this IPTC
* or EXIF data
*
* Useful for when you edit an image through PHP and need to preserve IPTC and EXIF
* data
*
* @since 1.0.0
*
* @source http://php.net/iptcembed - ebashkoff at gmail dot com
*
* @param string $image_info 			EXIF and IPTC image information from the source image, using getimagesize()
* @param string $destination_image 		Path and File of Destination Image, which needs IPTC and EXIF data
* @param int 	$original_orientation 	The image's original orientation, before we changed it.
*										Used when we replace this orientation in the EXIF data
*/
function um_gallery_transfer_iptc_exif_to_image( $image_info, $destination_image, $original_orientation ) {

    // Check destination exists
    if ( ! file_exists( $destination_image ) ) {
    	return false;
    }

    // Get EXIF data from the image info, and create the IPTC segment
    $exif_data = ( ( is_array( $image_info ) && key_exists( 'APP1', $image_info ) ) ? $image_info['APP1'] : null );
    if ( $exif_data ) {
    	// Find the image's original orientation flag, and change it to 1
    	// This prevents applications and browsers re-rotating the image, when we've already performed that function
        // @TODO I'm not sure this is the best way of changing the EXIF orientation flag, and could potentially affect
        // other EXIF data
    	$exif_data = str_replace( chr( dechex( $original_orientation ) ) , chr( 0x1 ), $exif_data );

        $exif_length = strlen( $exif_data ) + 2;
        if ( $exif_length > 0xFFFF ) {
        	return false;
        }

        // Construct EXIF segment
        $exif_data = chr(0xFF) . chr(0xE1) . chr( ( $exif_length >> 8 ) & 0xFF) . chr( $exif_length & 0xFF ) . $exif_data;
    }

    // Get IPTC data from the source image, and create the IPTC segment
    $iptc_data = ( ( is_array( $image_info ) && key_exists( 'APP13', $image_info ) ) ? $image_info['APP13'] : null );
    if ( $iptc_data ) {
        $iptc_length = strlen( $iptc_data ) + 2;
        if ( $iptc_length > 0xFFFF ) {
        	return false;
        }

        // Construct IPTC segment
        $iptc_data = chr(0xFF) . chr(0xED) . chr( ( $iptc_length >> 8) & 0xFF) . chr( $iptc_length & 0xFF ) . $iptc_data;
    }

    // Get the contents of the destination image
    $destination_image_contents = file_get_contents( $destination_image );
    if ( ! $destination_image_contents ) {
    	return false;
    }
    if ( strlen( $destination_image_contents ) == 0 ) {
    	return false;
    }

    // Build the EXIF and IPTC data headers
    $destination_image_contents = substr( $destination_image_contents, 2 );
    $portion_to_add = chr(0xFF) . chr(0xD8); // Variable accumulates new & original IPTC application segments
    $exif_added = ! $exif_data;
    $iptc_added = ! $iptc_data;

    while ( ( substr( $destination_image_contents, 0, 2 ) & 0xFFF0 ) === 0xFFE0 ) {
        $segment_length = ( substr( $destination_image_contents, 2, 2 ) & 0xFFFF );
        $iptc_segment_number = ( substr( $destination_image_contents, 1, 1 ) & 0x0F );   // Last 4 bits of second byte is IPTC segment #
        if ( $segment_length <= 2 ) {
        	return false;
        }

        $thisexistingsegment = substr( $destination_image_contents, 0, $segment_length + 2 );
        if ( ( 1 <= $iptc_segment_number) && ( ! $exif_added ) ) {
            $portion_to_add .= $exif_data;
            $exif_added = true;
            if ( 1 === $iptc_segment_number ) {
                $thisexistingsegment = '';
            }
        }

        if ( ( 13 <= $iptc_segment_number ) && ( ! $iptc_added ) ) {
            $portion_to_add .= $iptc_data;
            $iptc_added = true;
            if ( 13 === $iptc_segment_number ) {
                $thisexistingsegment = '';
            }
        }

        $portion_to_add .= $thisexistingsegment;
        $destination_image_contents = substr( $destination_image_contents, $segment_length + 2 );
    }

    // Write the EXIF and IPTC data to the new file
    if ( ! $exif_added ) {
        $portion_to_add .= $exif_data;
    }
    if ( ! $iptc_added ) {
        $portion_to_add .= $iptc_data;
    }

    $output_file = fopen( $destination_image, 'w' );
    if ( $output_file ) {
    	return fwrite( $output_file, $portion_to_add . $destination_image_contents );
    }

    return false;

}

/**
 * Add modal structure.
 *
 * @return void
 */
function um_gallery_form_modal() {
	?>
    <div id="um-gallery-modal" class="um-gallery-popup mfp-hide"></div>
    <?php
}
add_action('wp_footer', 'um_gallery_form_modal');

/**
 * A custom sanitization function that will take the incoming input, and sanitize
 * the input before handing it back to WordPress to save to the database.
 *
 * @since    1.0.6
 *
 * @param    array    $input        The address input.
 * @return   array    $new_input    The sanitized input.
 */
function um_gallery_pro_sanitize_array( $input ) {

	// Initialize the new array that will hold the sanitize values
	$new_input = array();

	// Loop through the input and sanitize each of the values
	foreach ( $input as $key => $val ) {
		$new_input[ $key ] = sanitize_text_field( $val );
	}

	return $new_input;

}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed		Option value
 */
function um_gallery_pro_get_option( $key = '', $default = '' ) {
	$options = get_option( 'um_gallery_pro' );
	$value = '';
	if ( ! empty( $options[ $key ] ) ) {
		if( is_array( $options[ $key ] ) ) {
			$value = um_gallery_pro_sanitize_array( $options[ $key ] );
		}else{
			$value = sanitize_text_field( $options[ $key ] );
		}
	}
	if ( empty( $value ) && ! empty( $default ) ) {
		$value = $default;
	}
	return $value;
	//return um_gallery()->admin->
	return cmb2_get_option( um_gallery_pro_admin()->key, $key );
	return ( function_exists( 'cmb2_get_option' ) ? cmb2_get_option( um_gallery_pro_admin()->key, $key ) : '' );
}

/**
 * Check if addon is enabled
 * @return boolean
 */
function um_gallery_pro_addon_enabled( $addon = '' ) {
	global $gallery_enabled_addons;
	if ( empty( $gallery_enabled_addons ) ) {
		$gallery_enabled_addons = get_option( 'um_gallery_pro_addons', array() );
	}
	if ( ! empty( $gallery_enabled_addons ) && in_array( $addon, $gallery_enabled_addons ) ) {
		return true;
	}
	return false;
}

/**
 * Get the video type based on URL
 *
 * @since  1.0.7.2
 * @param  string $url
 *
 * @return string|false
 */
function um_gallery_get_video_type( $url = '' ) {
	$type = false;
	if ( empty( $url ) ) {
		return $type;
	}
	if ( strpos( $url, 'youtu' ) > 0 ) {
		$type = 'youtube';
	} elseif ( strpos( $url, 'vimeo' ) > 0 ) {
		$type = 'vimeo';
	} elseif ( strpos( $url, 'hudl' ) > 0 ) {
		$type = 'hudl';
	}
	return apply_filters( 'um_gallery_get_video_type', $type );
}

function um_get_gallery_siblings( $photo_id = 0 ) {
	global $wpdb;

	// Get the album ID.
	$album_id = $wpdb->get_var( $wpdb->prepare( "SELECT album_id FROM {$wpdb->prefix}um_gallery WHERE id='%d'", $photo_id ) );
	if ( empty( $album_id ) ) {
		return;
	}
	// Find siblings
	$photos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}um_gallery WHERE album_id='%d'", $album_id ) );
	if ( ! empty( $photos ) ) {
		return $photos;
	}

	return;
}

/**
 * Get remote media types
 *
 * @since  1.0.8.3.1
 *
 * @return array
 */
function um_gallery_get_remote_media_types() {
	$media_types = array(
		'youtube',
		'vimeo',
		'hudl',
	);
	return apply_filters( 'um_gallery_get_remote_media_types', $media_types );
}

/**
 * Verify if user is owner.
 *
 * @return boolean
 */
function um_gallery_is_owner() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( um_get_requested_user() == get_current_user_id() ) {
		return true;
	}

	return false;
}