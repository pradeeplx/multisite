<?php
global $album;
$album_id = ! empty( $album ) ? (int) $album->id : 0;
?>
<div class="um-gallery-form-wrapper" id="um-gallery-album">
	<div class="um-modal-header">
		<?php echo um_gallery_pro_get_option( 'um_gallery_modal_title', __( 'Manage Album', 'um-gallery-pro' ) ); ?>
	</div>
	<div class="um-modal-body">
		<form>
			<div class="um-gallery-form-header">
				<?php if ( ! um_gallery()->template->quick_upload ) : ?>
				<div class="um-gallery-form-field">
					<input type="text" name="album_name" id="album_name" placeholder="<?php _e( 'Enter Album Name', 'um-gallery-pro' ); ?>" value="<?php echo ! empty( $album ) ? esc_attr( $album->album_name ) : ''; ?>" />
				</div>
				<div class="um-gallery-form-field">
					<textarea name="album_description" id="album_description" placeholder="<?php _e( 'About this album', 'um-gallery-pro' ); ?>"><?php echo ! empty( $album ) ? esc_attr( $album->album_description ) : ''; ?></textarea>
				</div>
				<?php else : ?>
						<input type="hidden" name="album_name" id="album_name" placeholder="<?php _e( 'Enter Album Name', 'um-gallery-pro' ); ?>" value="<?php echo @$album->album_name; ?>" />
						<input type="hidden" name="album_description" id="album_description" placeholder="<?php _e( 'Enter Album Name', 'um-gallery-pro' ); ?>" value="<?php echo ! empty( $album ) ? esc_attr( $album->album_name ) : ''; ?>" />
				<?php endif; ?>
			</div>
			<div >
				<?php /*?><input type="hidden" name="ug_upload" value="1" /><?php */?>
				<div class="um-clear"></div>
			</div>
			<div class="um-gallery-pro-action-buttons">
			<ul>
				<li class="active"><a href="#photo" data-type="photo"><i class="um-faicon-camera"></i> <?php echo esc_html( um_gallery_pro_get_option( 'um_gallery_add_photos_tab', __( 'Add Photos', 'um-gallery-pro' ) ) ); ?></a></li>
				<?php if ( um_gallery_pro_addon_enabled( 'videos' ) ) { ?>
				<li><a href="#video" data-type="video"><i class="um-faicon-video-camera"></i> <?php echo esc_html( um_gallery_pro_get_option( 'um_gallery_add_videos_tab', __( 'Add Videos', 'um-gallery-pro' ) ) ); ?></a></li>
				<?php } ?>
			</ul>
			</div>
			<div class="um-gallery-form-tabs">
				<div id="um-gallery-form-tab-photo">
					<div id="dropzone" class="dropzone um-gallery-upload"> </div>
				</div>
				<div id="um-gallery-form-tab-video">
				<div class="um-gallery-pro-video-wrapper">
						<div id="um-gallery-pro-video-insert">
							<div class="um-gallery-form-field">
								<input type="text" name="video_url" id="video_url" placeholder="<?php echo esc_attr( um_gallery_pro_get_option( 'um_gallery_video_placeholder_text', __( 'Video URL', 'um-gallery-pro' ) ) ); ?>" value="" />
								<input type="button" class="um-gallery-add-video" value="<?php echo esc_attr( um_gallery_pro_get_option( 'um_gallery_add_video_button', __( 'Add Video', 'um-gallery-pro' ) ) ); ?>" />
							</div>
						</div>
						<div class="um-gallery-pro-video-list"></div>
					</div>
				</div>
			</div>
			<input type="hidden" name="album_id" value="<?php echo $album_id; ?>" />
		</form>
		<div class="um-modal-footer">
			<div class="um-modal-left">
				<div class="um-modal-progress">
						<div class="um-gallery-spinner">
							<div class="rect1"></div>
							<div class="rect2"></div>
							<div class="rect3"></div>
							<div class="rect4"></div>
							<div class="rect5"></div>
						</div>
						<!--<progress id="um-gallery--progress-bar" value="0" max="100"></progress>-->
				</div>
				<div class="um-gallery-form-field">
					<?php /*
			TBD
			?><label>
						<?php _e('Privacy'); ?>
					</label>
					<select name="album_privacy" id="album_privacy">
						<option value="public">
						<?php _e('Public'); ?>
						</option>
						<option value="private">
						<?php _e('Private'); ?>
						</option>
						<option value="followers">
						<?php _e('Followers'); ?>
						</option>
					</select><?php */?>
					<input type="hidden" name="album_privacy" id="album_privacy" value="public" />
				</div>
			</div>
			<div class="um-modal-right"> <a href="#" class="um-modal-btn image" id="um-gallery-save" data-id="<?php echo absint( $album_id ); ?>" data-type="album">
				<?php echo esc_html( um_gallery_pro_get_option( 'um_gallery_save_button', __( 'Save', 'um-gallery-pro' ) ) ); ?>
		</a> <a href="#" class="um-modal-btn um-gallery-close alt" id="um-gallery-cancel">  <?php echo um_gallery_pro_get_option( 'um_gallery_cancel_button', __( 'Cancel', 'um-gallery-pro' ) ); ?></a> </div>
			<div class="um-clear"></div>
		</div>
	</div>
</div>
