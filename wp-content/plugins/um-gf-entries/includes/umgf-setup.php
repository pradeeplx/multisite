<?php
class UMGF_Entries_Setup {

	public $gf_tabs = array();
	public $ctab = null;
	public function __construct() {

		$this->setup_gf_tabs();

		// Load admin style sheet and JavaScript.
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		//
		add_action( 'init', array( $this, 'register_cpt' ) );
		//
		add_action( 'add_meta_boxes', array( $this, 'setup_metabox' ) );
		//
		add_action( 'save_post', array( $this, 'save_meta_data'), 12, 2 );
		//
		add_filter( 'um_profile_tabs', array( $this, 'add_profile_tabs'), 12, 1 );
		add_filter( 'um_account_page_default_tabs_hook', array( $this, 'add__account_tabs' ), 12, 1 );
		add_action( 'init', array( $this, 'setup_tab_content' ) );
		//
		//
	}

	public function setup_gf_tabs() {
		$this->gf_tabs = umgf_get_tabs();
	}
	/**
	 * Register and enqueue admin-specific JavaScript/CSS.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_assets() {
		/*wp_enqueue_style( umgf_entries()->slug .'-admin-styles', umgf_entries()->plugin_url . 'admin/assets/css/cg-styles.css' );*/
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-draggable');
		/*wp_register_script( umgf_entries()->slug . '-admin-script', umgf_entries()->plugin_url . 'admin/assets/js/cg-script.js', array( 'jquery' ) );
   		wp_localize_script(
			umgf_entries()->slug . '-admin-script',
			'myAjax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php'),
				'loader' => plugins_url( '/images/loading.gif', __FILE__ ),
				'mcebutton' => plugins_url( 'assets/images/tinymce_button.png', __FILE__ )
			)
		);
		wp_enqueue_script( umgf_entries()->slug . '-admin-script');*/
	}
	/*
	*	Register post type for profile tab
	*/
	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'GF Tabs', 'post type general name', 'um-gf-entries' ),
			'singular_name'      => _x( 'GF Tab', 'post type singular name', 'um-gf-entries' ),
			'menu_name'          => _x( 'GF Tabs', 'admin menu', 'um-gf-entries' ),
			'name_admin_bar'     => _x( 'GF Tab', 'add new on admin bar', 'um-gf-entries' ),
			'add_new'            => _x( 'Add New', 'event', 'um-gf-entries' ),
			'add_new_item'       => __( 'Add New GF Tab', 'um-gf-entries' ),
			'new_item'           => __( 'New GF Tab', 'um-gf-entries' ),
			'edit_item'          => __( 'Edit GF Tab', 'um-gf-entries' ),
			'view_item'          => __( 'View GF Tab', 'um-gf-entries' ),
			'all_items'          => __( 'All GF Tabs', 'um-gf-entries' ),
			'search_items'       => __( 'Search GF Tabs', 'um-gf-entries' ),
			'parent_item_colon'  => __( 'Parent GF Tabs:', 'um-gf-entries' ),
			'not_found'          => __( 'No gf tabs found.', 'um-gf-entries' ),
			'not_found_in_trash' => __( 'No gf tabs found in Trash.', 'um-gf-entries' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Gravity Form Entry tabs', 'um-gf-entries' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => umgf_entries()->post_type ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
		);

		register_post_type( umgf_entries()->post_type, $args );
	}
	public function setup_metabox() {
		add_meta_box(
			'umgf_meta',
			__( 'Tab Setup','um-gf-entries' ),
			array($this, 'umgf_metabox_display'),
			umgf_entries()->post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'umgf_meta_settings',
			__('Tab Settings','um-gf-entries'),
			array($this, 'umgf_metabox_settings_display'),
			umgf_entries()->post_type,
			'side'
		);
	}
	/*
	*
	*/
	public function umgf_metabox_display(){
		global $post;
		$post_id = $post->ID;
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-draggable');
		// Add an nonce field so we can check for it later.
        wp_nonce_field( 'umgf_meta_box', 'umgf_meta_box_nonce' );

		$forms = RGFormsModel::get_forms( null, 'title' );
		$tab_name = get_post_meta($post_id, '__umgf_tab_name', true);
		$tab_slug = get_post_meta($post_id, '__umgf_tab_slug', true);
		$tab_fields = get_post_meta($post_id, '__umgf_tab_fields', true);
		$umgf_field_id = get_post_meta($post_id, '__umgf_tab_field_id', true);
		$fields = array();
		if( !empty($umgf_field_id) ){
		$form = RGFormsModel::get_form_meta($umgf_field_id);
			if(is_array($form["fields"])){
				foreach($form["fields"] as $field){
					if(isset($field["inputs"]) && is_array($field["inputs"])){

						foreach($field["inputs"] as $input)
							$fields[$input["id"]] =  array('ID' => $input["id"], 'name' => GFCommon::get_label($field, $input["id"]));
					}
					else if(!rgar($field, 'displayOnly')){
						$fields[$field["id"]] =  array('ID' => $field["id"], 'name' => GFCommon::get_label($field));
					}
				}
			}
		}
		?>
        <style type="text/css">
            #umgf_form_fields span.dashicons.dashicons-menu.umgf-movie-handle{display:none}
            #umgf_form_fields .hide--td{opacity:0}
            #umgf_form_list span.dashicons.dashicons-menu.umgf-movie-handle + label {
                display: inline-block;
            }
			div#umgf_form_fields {
				padding-right: 2px;
			}

			div#umgf_form_list {
				padding-left: 2px;
			}
			.umgf-empty-row{

			}
			.umgf-block{
				display: block;
			}

			.um-admin-field {
				display: block;
				width: 100%;
				margin-top: 10px;
			}

			.um-admin-field a.button {
				position: relative;
				top: 1px;
				height: 30px !important;
			}

			.um-admin-half {
				float: left;
				width: 48%;
			}

			.um-admin-tri {
				float: left;
				width: 33%;
			}

			.um-admin-left{float: left}
			.um-admin-right {float: right}
			.um-admin-clear {clear: both}
        </style>
        <div class="um-admin-metabox">
        <div class="umfg-meta-entry">
        	<label class="um-admin-half"><?php _e('Tab Name','um-gf-entries'); ?></label>
            <span class="um-admin-half">
            <input type="text" name="tab_name" id="tab_name" value="<?php echo $tab_name; ?>" required />
            </span>
            <div class="um-admin-clear"></div>
        </div>
        <div class="umfg-meta-entry">
        	<label class="um-admin-half"><?php _e('Tab Slug','um-gf-entries'); ?></label>
            <span class="um-admin-half">
            <input type="text" name="tab_slug" id="tab_slug" value="<?php echo $tab_slug; ?>" required />
            </span>
            <div class="um-admin-clear"></div>
        </div>
        <div class="umfg-meta-entry">
        	<label class="um-admin-half"><?php _e('Form','um-gf-entries'); ?></label>
            <span class="um-admin-half">
            <select name="umgf_field_id" id="umgf_gravity_forms">
            <option value=""><?php _e('-Gravity Form-','um-gf-entries'); ?></option>
            <?php
			if(!empty($forms)):
			foreach( $forms as $form ):
			?>
  			<option value="<?php echo $form->id; ?>" <?php echo ($umgf_field_id == $form->id ? ' selected="selected" ' : ''); ?>><?php echo $form->title; ?></option>
			<?php
            endforeach;
            endif; ?>
            </select>
            </span>
            <div class="um-admin-clear"></div>
        </div>
        <div class="umfg-meta-entry">
        	<div class="um-admin-half"><h3><?php _e('Gravity Form Fields','um-gf-entries'); ?></h3></div>
        	<div class="um-admin-half"><h3><?php _e('Fields for entries','um-gf-entries'); ?></h3></div>
            <div class="um-admin-clear"></div>
        </div>
        <div class="umfg-meta-entry">
        	<div class="um-admin-half" id="umgf_form_fields">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th colspan="2">Field</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
						if(!empty($fields)):
						$counted = false;
						foreach($fields as $field):
							if(!empty($tab_fields[$field['ID']])){
								continue;
							}
							$counted = true;
						?>
                        <tr class="" id="umgf-tr-<?php echo $field['ID']; ?>"><td><span class="dashicons dashicons-menu umgf-movie-handle"></span><label><input type="checkbox" class="umgf-field" value="<?php echo $field['ID']; ?>"><?php echo $field['name']; ?></label></td><td class="hide--td"><input type="text" class="umgf-label" placeholder="<?php echo __('Field Label','um-gf-entries'); ?>" /></td></tr>
                        <?php
						endforeach;
						else:
						?>
                        <tr class="umgf-empty-row"><td colspan="2"><?php echo __('No fields listed','um-gf-entries'); ?></td></tr>
                        <?php endif; ?>
                        <?php if ( !empty($fields) && false === ($counted)){
							?>
                            <tr class="umgf-empty-row"><td colspan="2"><?php echo __('No fields listed','um-gf-entries'); ?></td></tr>
                            <?php
						}
						?>
                    </tbody>
                </table>
            </div>
            <div class="um-admin-half" id="umgf_form_list">
                <table class="wp-list-table widefat fixed striped gf-entries--table">
                    <thead>
                        <tr>
                            <th><?php _e('Field','um-gf-entries'); ?></th>
                            <th><?php _e('Label','um-gf-entries'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
						if(!empty($tab_fields)):
						foreach($tab_fields as $field):
						?>
                        <tr class="" id="umgf-tr-<?php echo $field['field_id']; ?>"><td><span class="dashicons dashicons-menu umgf-movie-handle"></span><label><input type="checkbox" checked class="umgf-field" value="<?php echo $field['field_id']; ?>" name="umgf_field[<?php echo $field['field_id']; ?>][field_id]"><?php echo $fields[$field['field_id']]['name'] ;?></label></td><td class="hide--td"><input type="text" class="umgf-label" placeholder="<?php _e('Field Label','um-gf-entries'); ?>" name="umgf_field[<?php echo $field['field_id']; ?>][label]" value="<?php echo $field['label']; ?>" /></td></tr>
                        <?php
						endforeach;
						else:
						?>
                        <tr class="umgf-empty-row"><td colspan="2"><?php echo __('No fields listed','um-gf-entries'); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="um-admin-clear"></div>
        </div>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function($){
            var umgf_table = jQuery( ".gf-entries--table tbody");
            var default_tables = "#umgf_form_fields tbody";
            umgf_table.sortable({
                handle: ".umgf-movie-handle"
            });
            umgf_table.disableSelection();
             function umgf_isEmpty( el ){
				  return !$.trim(el.html())
			  }
			function umgf_adjust_table(){
				if( umgf_isEmpty(jQuery( "#umgf_form_fields tbody")) ){
					jQuery( "#umgf_form_fields tbody").append('<tr class="umgf-empty-row"><td colspan="2"><?php echo __('No fields listed','um-gf-entries'); ?></td></tr>');
				}else{
					jQuery( "#umgf_form_fields tbody").find('.umgf-empty-row').slideUp().remove();
				}
				if( umgf_isEmpty(jQuery( ".gf-entries--table tbody")) ){
					jQuery( ".gf-entries--table tbody").append('<tr class="umgf-empty-row"><td colspan="2"><?php echo __('No fields listed','um-gf-entries'); ?></td></tr>');
				}else{
					jQuery( ".gf-entries--table tbody").find('.umgf-empty-row').slideUp().remove();
				}
			}
            jQuery(document).on('click', '#umgf_form_fields input[type="checkbox"],#umgf_form_list input[type="checkbox"]', function(){
                var id = $(this).val();

                if ( $(this).is(':checked') ) {
                    $("#umgf-tr-" + id).prependTo(".gf-entries--table tbody");
                }
                else {
                    $("#umgf-tr-" + id).prependTo(default_tables);
                }
                jQuery('#umgf_form_fields .umgf-label,#umgf_form_fields .umgf-field').removeAttr('name');

				jQuery("#umgf_form_list tr").each(function() {
				  	var value = $(this).find(".umgf-field").val();
					jQuery(this).find('.umgf-label').attr('name', 'umgf_field['+ value +'][label]');
					jQuery(this).find('.umgf-field').attr('name', 'umgf_field['+ value +'][field_id]');
				});


                umgf_table.sortable({
                    handle: ".umgf-movie-handle"
                });
				umgf_adjust_table();
            });
			jQuery(document).on('change', '#umgf_gravity_forms', function(event){
				var option = $(this).val();
				jQuery('#umgf_form_fields tbody,#umgf_form_list tbody').html('');
				jQuery.ajax({
					  type: "GET",
					  url: ajaxurl,
					  data: {
					  	'action': "umgf_get_fields",
					  	'form_id'	: option
					  },
					  success: function(response){

						jQuery.each(response, function(index, obj) {
							jQuery(default_tables).append('<tr class="" id="umgf-tr-'+ obj.ID +'"><td><span class="dashicons dashicons-menu umgf-movie-handle"></span><label><input type="checkbox" class="umgf-field" value="'+obj.ID+'">'+obj.name+'</label></td><td class="hide--td"><input type="text" class="umgf-label" placeholder="Field Label" /></td></tr>');
						});
						umgf_adjust_table();
					  }
					});
			});
		});
        </script>
        <?php
	}
	/*
	*	Tab Settings
	*/
	public function umgf_metabox_settings_display(){
		global $post;
		$post_id = $post->ID;
		$tab_icon = get_post_meta($post_id, '__umgf_tab_icons', true );

		//field data
		$tab_privacy = get_post_meta($post_id, '__umgf_tab_privacy', true );
		$roles = UM()->roles()->get_roles();
		$allowed_roles = get_post_meta($post_id, '__umgf_allowed_roles', true);
		if( ! $allowed_roles ) {
			$allowed_roles = array();
		}
		$section =  get_post_meta($post_id, '__umgf_tab_section', true);
		?>
        <p>
        	<label class="umgf-block"><?php _e('Tab Section', 'um-gf-entries'); ?></label>
        	<select name="umgf_section" id="umgf_section">
            	<option value="profile" <?php echo ($section == 'profile' ? 'selected="selected"' : ''); ?>><?php _e('Profile','um-gf-entries'); ?></option>
                <option value="account" <?php echo ($section == 'account' ? 'selected="selected"' : ''); ?>><?php _e('Account','um-gf-entries'); ?></option>
            </select>
        </p>
        <p>
        	<label><input type="checkbox" name="umgf_privacy" id="umgf_privacy" <?php checked($tab_privacy, 1, true); ?> value="1" /> <?php _e('Make Private', 'um-gf-entries'); ?></label>

        </p>
        <p>
        	<label><?php _e('Tab Icon', 'um-gf-entries'); ?></label>
            <input type="text" name="umgf_icon" id="umgf_icon" value="<?php echo $tab_icon; ?>" placeholder="um-faicon-user" />
        </p>
        <div class="umgf-checkbox-list">
        	<label><?php _e('Users with this role can view this tab', 'um-gf-entries'); ?></label>
        	<?php
			if ( ! empty( $roles ) ) {
				foreach ( $roles as $k => $role ) {
					?>
                    <label class="umgf-block"><input type="checkbox" name="umgf_allowed_roles[]" id="umgf_privacy" <?php echo in_array( $k, $allowed_roles ) ? ' checked ' : ''; ?> value="<?php echo esc_attr( $k ); ?>" /> <?php echo $role; ?></label>
                    <?php
				}
			}
			?>
        </div>
        <?php
	}
	/*
	*	Save photos meta data
	*/
	public function save_meta_data( $post_id, $post ) {
		/*
		* We need to verify this came from the our screen and with proper authorization,
		* because save_post can be triggered at other times.
		*/

        // Check if our nonce is set.
        if ( ! isset( $_POST['umgf_meta_box_nonce'] ) ) {
            return $post_id;
        }

        $nonce = $_POST['umgf_meta_box_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'umgf_meta_box' ) ) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		//tab name
		if ( isset( $_POST['tab_name'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_name', sanitize_text_field( $_POST['tab_name'] ) );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_name' );
		}
		//table slug
		if ( isset( $_POST['tab_slug'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_slug', sanitize_title( $_POST['tab_slug'] ) );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_slug' );
		}
		//field data
		if ( isset( $_POST['umgf_field'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_fields', $_POST['umgf_field'] );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_fields' );
		}
		//field data
		if ( isset( $_POST['umgf_field_id'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_field_id', absint( $_POST['umgf_field_id'] ) );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_field_id' );
		}
		//field data
		if ( isset( $_POST['umgf_icon'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_icons', sanitize_text_field( $_POST['umgf_icon'] ) );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_icons' );
		}

		//field data
		if ( isset( $_POST['umgf_privacy'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_privacy', sanitize_text_field( $_POST['umgf_privacy'] ) );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_privacy' );
		}

		//field data
		if ( isset( $_POST['umgf_section'] ) ) {
			update_post_meta( $post_id, '__umgf_tab_section', sanitize_text_field( $_POST['umgf_section'] ) );
		} else {
			delete_post_meta( $post_id, '__umgf_tab_section' );
		}
		//field data
		if ( isset( $_POST['umgf_allowed_roles'] ) ) {
			update_post_meta( $post_id, '__umgf_allowed_roles', $_POST['umgf_allowed_roles'] );
		} else {
			delete_post_meta( $post_id, '__umgf_allowed_roles' );
		}

		delete_option( 'umgf_tabs_cache' );
	}
	public function add__account_tabs( $tabs = array() ) {
		$gf_tabs = $this->gf_tabs;

		$defaults = array(
			'section'       => '',
			'name'          => '',
			'slug'          => '',
			'icon'          => '',
			'private'       => '',
			'allowed_roles' => '',
		);
		if ( ! empty( $gf_tabs ) ) {
			foreach ( $gf_tabs as $gf_tab ) {

				$gf_tab        = wp_parse_args( $gf_tab, $defaults );
				$section       =  $gf_tab['section'];
				if ( $section != 'account' ) {
					continue;
				}
				$tab_name      = $gf_tab['name'];
				$tab_slug      = $gf_tab['slug'];
				$tab_icon      = $gf_tab['icon'];
				$tab_icon      = $tab_icon ? $tab_icon : 'um-faicon-user';
				$private_tab   = $gf_tab['private'];
				$allowed_roles = $gf_tab['allowed_roles'];

				$position = 101;

				$tabs[ $position ][ $tab_slug ]['icon']   = $tab_icon;
				$tabs[ $position ][ $tab_slug ]['title']  = $tab_name;
				$tabs[ $position ][ $tab_slug ]['custom'] = true;
				$tabs[ $position ][ $tab_slug ]['show_button'] = false;

				$position++;
			}
		}
		return $tabs;
	}
	public function add_profile_tabs( $tabs = array() ){

		$user_role    = '';
		if ( is_user_logged_in() ) {
			um_fetch_user( get_current_user_id() );
			$user_role    = UM()->user()->get_role();
			um_reset_user();
		}
		
		
		$profile_role = UM()->user()->get_role();

		$gf_tabs = $this->gf_tabs;

		$defaults = array(
			'section'       => '',
			'name'          => '',
			'slug'          => '',
			'icon'          => '',
			'private'       => '',
			'allowed_roles' => '',
		);

		if ( ! empty( $gf_tabs ) ) {
			foreach( $gf_tabs as $gf_tab ) {

				$gf_tab = wp_parse_args( $gf_tab, $defaults );
				$section     =  $gf_tab['section'];
				if( $section != 'profile' ) {
					continue;
				}
				$tab_name      = $gf_tab['name'];
				$tab_slug      = $gf_tab['slug'];
				$tab_icon      = $gf_tab['icon'];
				$tab_icon      = $tab_icon ? $tab_icon : 'um-faicon-user';
				$private_tab   = $gf_tab['private'];
				$allowed_roles = $gf_tab['allowed_roles'];

				$view_roles    = array();

				$current_user_roles = um_user( 'roles' );

				if ( ! $tab_slug ) {
					continue;
				}
				
				// private tab - user on another profile but can not view
				if ( $private_tab && ! um_is_user_himself() ) {
					continue;
				}

				$can_view = false;

				// public tab - user can not view tab
				if ( ! empty( $allowed_roles ) ) {
					if ( ! empty( $current_user_roles ) ) {
						foreach ( $current_user_roles as $current_role ) {
							if ( in_array( $current_role, $allowed_roles, true ) ){
								$can_view = true;
							}
						}
					}
				} else {
					$can_view = true;
				}
				
				if ( ! $can_view ) {
					continue;
				}

				$tabs[ $tab_slug ] = array(
					'name'   => $tab_name,
					'icon'   => $tab_icon,
					'custom' => true,
				);
			}

		}

		return $tabs;
	}
	function setup_tab_content(){
		$gf_tabs = $this->gf_tabs;
		$defaults = array(
			'section'       => '',
			'name'          => '',
			'slug'          => '',
			'icon'          => '',
			'private'       => '',
			'allowed_roles' => '',
			'show_button'   => false,
			'post_id'       => '',
		);
		if ( ! empty ( $gf_tabs ) ) {
			foreach ( $gf_tabs as $gf_tab ) {
				$gf_tab = wp_parse_args( $gf_tab, $defaults );
				$tab_slug       = $gf_tab['slug'];

				add_action( 'um_profile_content_' . $tab_slug, array( $this, 'display_tab_content' ) );
				add_action( 'um_before_account_' . $tab_slug, array( $this, 'umgf_account_tab' ) );
				add_filter( 'um_account_content_hook_' . $tab_slug, array( $this, 'um_account_content_hook_tab' ), 12, 2 );
			}
		}
	}

	public function um_account_content_hook_tab( $output, $shortcode_args ) {
		ob_start();
		?>
		<div class="um-field"></div>		
		<?php
		$output .= ob_get_contents();
		ob_end_clean();
		return $output;
	}
	public function umgf_account_tab() {
		$defaults = array(
			'section'       => '',
			'name'          => '',
			'slug'          => '',
			'icon'          => '',
			'private'       => '',
			'allowed_roles' => '',
			'show_button'   => false,
			'post_id'       => '',
		);
		// Find the slug from the current filter.
		$slug = str_replace( 'um_before_account_', '', current_filter() );
		if ( ! empty( $this->gf_tabs ) ) {
			foreach ( $this->gf_tabs as $tab ) {
				$tab = wp_parse_args( $tab, $defaults );
				if ( ! empty( $tab['slug'] ) && $tab['slug'] == $slug ) {
					if ( $tab['post_id'] ) {
						$this->entries_tables( $tab['post_id'] );
						return;
					}
				}
			}
		}
	}
	function display_tab_content(){
		$post_id = umgf_get_tab_post_ID();
		$this->entries_tables( $post_id );
	}
	public function entries_tables( $post_id = 0 ){
		$umgf_field_id = get_post_meta( $post_id, '__umgf_tab_field_id', true );
		$tab_fields = get_post_meta( $post_id, '__umgf_tab_fields', true );
		$profile_id = um_profile_id();
		$fields = array();
		if ( ! empty( $umgf_field_id ) ){
		$form = RGFormsModel::get_form_meta( $umgf_field_id );
			if(is_array($form["fields"])){
				foreach($form["fields"] as $field){
					if(isset($field["inputs"]) && is_array($field["inputs"])){

						foreach($field["inputs"] as $input)
							$fields[$input["id"]] =  array('ID' => $input["id"], 'name' => GFCommon::get_label($field, $input["id"]));
					}
					else if(!rgar($field, 'displayOnly')){
						$fields[$field["id"]] =  array('ID' => $field["id"], 'name' => GFCommon::get_label($field));
					}
				}
			}
		}
		//entries
		$search_criteria['field_filters'][] = array( 'key' => 'created_by', 'value' => $profile_id );
		$entries = GFAPI::get_entries($umgf_field_id, $search_criteria);
		//setup table layout
		?>
        <table>
        	<thead>
            	<tr>
                	<?php
					if(!empty($tab_fields)):
						foreach($tab_fields as $field):
						if( empty($fields[$field['field_id']])){
							continue;
						}
						$name = (!empty($field['label']) ? sanitize_text_field($field['label']) : $fields[$field['field_id']]['name'] );
					?>
                    <th id="umgf-col-<?php echo $field['field_id']; ?>"><?php echo $name; ?></th>
                    <?php
						endforeach;
					endif;
					?>
                </tr>
            </thead>
            <tbody>
            	<?php
				if(!empty($entries)):
					foreach($entries as $entry):
					/*if( empty($fields[$field['field_id']])){
						continue;
					}*/
				?>
            	<tr>
                	<?php
					if(!empty($tab_fields)):
						foreach($tab_fields as $field):
						if( empty($fields[$field['field_id']])){
							continue;
						}
						$name = (!empty($field['label']) ? sanitize_text_field($field['label']) : $fields[$field['field_id']]['name'] );
					?>
                	<td><?php echo $entry[$field['field_id']]; ?></td>
						<?php
                        endforeach;
                    endif;
                    ?>
                </tr>
                 <?php
					endforeach;
				else:
				?>
                	<tr>
                    	<td colspan="<?php echo count($tab_fields); ?>"><?php _e('No entries added', 'um-gf-entries'); ?></td>
                    </tr>
                <?php
				endif;
				?>
            </tbody>
        </table>
        <?php
	}
}
new UMGF_Entries_Setup();