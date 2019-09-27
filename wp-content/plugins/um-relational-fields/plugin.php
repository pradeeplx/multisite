<?php
/**
 * Plugin Name: UM Relational Fields
 * Plugin URI:  https://plusplugins.com
 * Description: Add relationships between users, post types and taxonomies.
 * Author:      PlusPlugins
 * Version:     1.0
 * Author URI:  https://plusplugins.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PP_FIELDS_REQUIRES', '1.3.28' );

class PP_Fields {
	public $notice_messge = '';
	function __construct() {

		//add_filter( 'redux/options/um_options/sections', array( $this, 'add_field_relationships_tab' ), 9005 );
		add_action( 'init', array( $this, 'init' ), 100 );
		add_action( 'admin_menu', array( $this, 'um_relat_options_page' ) );
	}

	function um_relat_options_page(){


		add_submenu_page( 'ultimatemember', 'UM Relational', 'UM Relational', 'manage_options', 'ultimatemember-relation', array( $this,'um_relat_callback_fun') );
	}
	function um_relat_callback_fun(){
		 ob_start();
		 ?>
		 <h2>Enter a relationship in the following format</h2>
		 <?php
		 
		if(isset($_POST['save_urur_save'])){
		 
			if(isset($_POST['field_name'])){
				$temp = array();
				foreach ( $_POST['field_name'] as $field_value) {
					if(trim($field_value) != '' ){
						array_push( $temp, trim($field_value) );
					}
				}
				um_update_option( 'utur_field_relationships', $temp);
			}else{
				um_remove_option( 'utur_field_relationships');
			}
		}
		$utur_relationships = um_get_option( 'utur_field_relationships' );
	 
		?>
		
		<form method="post" action="#" id="utur_form">	
		<div class="field_wrapper">
			<div id="reqs">
				<h3 align="left"> UM Relations </h3>
					
					<input type="hidden" name="field_name_id" value="1" />
					<?php if( is_array($utur_relationships)){
						$i = 1;
						foreach ($utur_relationships as $value) {
							if(trim($value) != '' ){
							?>
								<input type="text" class="w3-input w3-border" name="field_name[]" required="required" id="reqs<?php echo $i;?>" value="<?php echo trim($value);?>">
								<button class="reqsr_remove_btn" id="reqsr<?php echo $i;?>" type="button" onclick="deleteOldFields(<?php echo $i;?>)">Remove</button>
							<?php
							$i++;
							}
						}
					}?>
		     
		</div>
	</div>
	<button type="button" class="utur_add_btn" value="Add" onclick="javascript:add();"> Add</button>
	<input type="submit" class="save_urur_save" name="save_urur_save" value="Save" class="btn btn-primary">

	</form>
		<pre>meta_key|type|slug|link</pre>
		<h4>meta_key</h4>
		<p>This is the meta key of the field generated in the UM Form Builder.<br>
			<strong>Important:</strong> The field type should be either <code>dropdown</code> or
			<code>multiselect</code>, and "relationship" should be added in the first line of the options field of the
			Form Builder.</p>
		<h4>type</h4>
		<p>This is type of item you are relating to - either <code>user</code>, <code>tax</code> or <code>post</code>.
		</p>
		<h4>slug</h4>
		<p>If your <code>type</code> is <code>user</code>, this value is the user role slug that will filter the options
			in the select area. Multiple user roles should be defined as a comma-seperated list eg.
			<code>admin,member</code>. Leave empty to add all user roles.<br>
			If your <code>type</code> is <code>tax</code>, this value is the taxonomy slug that will filter the options
			in the select area. A single taxonomy slug must be entered.<br>
			If your <code>type</code> is <code>post</code>, this value is the comma seperated list of post types slugs
			that filter the options in the select area.</p>
		<h4>link</h4>
		<p>Set this value to <code>true</code> if you want to hyperlink the users or posts in display mode on the
			profile, or <code>false</code> to disable hyperlinks. If your type is <code>user</code>, you can also use
			<code>avatar</code> here to display an avatar.</p>
		<h4>Examples</h4>
		<p><code>my_coach|user|coach|true</code></p>
		<p><code>fav_meal|post|recipes|true</code></p>
		<p><code>recommended_content|post|post,page|true</code></p>
		<p><code>friends|user||false</code></p>
		<p><code>category|tax|category|true</code></p>
		<h4>Questions?</h4>
		<p>Shoot us an email: <strong>info@plusplugins.com</strong></p>
		 
		
	<style type="text/css">
		form#utur_form input[type=text] {
		    width: 450px;
		    display: block;
		    float: left;
		    clear: left;
		    background: none !important;
		}
		button.reqsr_remove_btn {
		    color: red;
		    font-weight: 600;
		    display: block;
		    float: left;
		    margin-left: 15px;
		    margin-bottom: 10px;
		     
		}
		button.utur_add_btn{
			color: #fff;
		    background: #308ec5;
		    border: none;
		    padding: 10px;
		    width: 100px;
		    font-size: 16px;
		    font-weight: 600;
		    border-radius: 10px;
		     margin-top: 20px;
			margin-right: 10px;
		}
		button.save_urur_save{
			color: #fff;
		    background: #308ec5;
		    border: none;
		    padding: 10px;
		    width: 100px;
		    font-size: 16px;
		    font-weight: 600;
		    border-radius: 10px;
		}
		input.save_urur_save {
		    color: #fff;
		    background: #4ec530;
		    border: none;
		    padding: 10px;
		    width: 100px;
		    font-size: 16px;
		    font-weight: 600;
		    border-radius: 10px;
		}
		div#reqs {
		    overflow: hidden;
		}
	</style>
	<script>
	  	var reqs_id = 0;
	  	reqs_id = $("input.w3-input").length;
		 function removeElement(ev) {
		    var button = ev.target;
		    var field = button.previousSibling;
		    var div = button.parentElement;
		    div.removeChild(button);
		    div.removeChild(field);
		}
		function deleteOldFields(e){
			jQuery('input#reqs'+e).remove();
			jQuery('button#reqsr'+e).remove();
		}
	  	function add() {
		    reqs_id++; // increment reqs_id to get a unique ID for the new element

		    //create textbox
		    var input = document.createElement('input');
		    input.type = "text";
		    input.setAttribute("class", "w3-input w3-border");
		    input.setAttribute("name", "field_name[]");
		     input.setAttribute("required", "required");
		    input.setAttribute('id', 'reqs' + reqs_id);
		    input.setAttribute('value', '');
		    var reqs = document.getElementById("reqs");
		    //create remove button
		    var remove = document.createElement('button');
		    remove.setAttribute('id', 'reqsr' + reqs_id);
		     remove.setAttribute('class', 'reqsr_remove_btn');
		    remove.onclick = function(e) {
		      	removeElement(e)
		    };
		    remove.setAttribute("type", "button");
		    remove.innerHTML = "Remove";
		    //append elements
		    reqs.appendChild(input);
		    reqs.appendChild(remove);
	  	}
  		 
	</script>	 
	<?php
	}

	function add_field_relationships_tab( $sections ) {
		ob_start();
		?>

		<h2>Enter a relationship in the following format</h2>

		<pre>meta_key|type|slug|link</pre>
		<h4>meta_key</h4>
		<p>This is the meta key of the field generated in the UM Form Builder.<br>
			<strong>Important:</strong> The field type should be either <code>dropdown</code> or
			<code>multiselect</code>, and "relationship" should be added in the first line of the options field of the
			Form Builder.</p>
		<h4>type</h4>
		<p>This is type of item you are relating to - either <code>user</code>, <code>tax</code> or <code>post</code>.
		</p>
		<h4>slug</h4>
		<p>If your <code>type</code> is <code>user</code>, this value is the user role slug that will filter the options
			in the select area. Multiple user roles should be defined as a comma-seperated list eg.
			<code>admin,member</code>. Leave empty to add all user roles.<br>
			If your <code>type</code> is <code>tax</code>, this value is the taxonomy slug that will filter the options
			in the select area. A single taxonomy slug must be entered.<br>
			If your <code>type</code> is <code>post</code>, this value is the comma seperated list of post types slugs
			that filter the options in the select area.</p>
		<h4>link</h4>
		<p>Set this value to <code>true</code> if you want to hyperlink the users or posts in display mode on the
			profile, or <code>false</code> to disable hyperlinks. If your type is <code>user</code>, you can also use
			<code>avatar</code> here to display an avatar.</p>
		<h4>Examples</h4>
		<p><code>my_coach|user|coach|true</code></p>
		<p><code>fav_meal|post|recipes|true</code></p>
		<p><code>recommended_content|post|post,page|true</code></p>
		<p><code>friends|user||false</code></p>
		<p><code>category|tax|category|true</code></p>
		<h4>Questions?</h4>
		<p>Shoot us an email: <strong>info@plusplugins.com</strong></p>

		<?php
		$desc = ob_get_contents();
		ob_end_clean();

		$fields = array();

		$fields[] = array(
			'id'       => 'field_relationships',
			'type'     => 'multi_text',
			'default'  => array(),
			'add_text' => __( 'Add New Relationship', 'ultimatemember' ),
			'title'    => 'Field Relationships',
			'desc'     => $desc,
		);

		$sections[] = array(

			'icon'       => 'um-faicon-exchange',
			'title'      => __( 'Field Relationships', 'pp-fields' ),
			'fields'     => $fields,
			'subsection' => false,

		);

		return $sections;
	}

	function init() {
		 

		global $ultimatemember;

		$relationships = um_get_option( 'utur_field_relationships' );
		 
		if ( empty( $relationships ) ) {
			return;
		}

		foreach ( $relationships as $relationship ) {
			$args = array_map( 'trim', explode( "|", trim( $relationship ) ) );

			$key  = isset( $args[0] ) ? $args[0] : '';
			$type = isset( $args[1] ) ? $args[1] : '';
			$slug = isset( $args[2] ) ? $args[2] : '';
			$link = isset( $args[3] ) ? $args[3] : '';

			if ( ! empty( $key ) ) {
				add_action( 'um_before_form', function ( $args ) use ( $type, $key, $slug, $link ) {
					$this->profile_form( $type, $key, $slug, $link );
				}, 10, 1 );

				$this->search_form( $type, $key, $slug, $link );
				$this->filtered_value( $type, $key, $slug, $link );
			}
		}
	}

	function items( $type, $slug ) {
		$items = array();

		if ( $type == 'user' ) {
			$args = array();

			if ( ! empty( $slug ) ) {
				$roles = explode( ",", $slug );

				// $args['meta_key']     = 'role';
				// $args['meta_value']   = $roles;
				// $args['meta_compare'] = 'IN';
				$args['role__in'] =  $roles;

			}
			 
			$items = get_users( $args );
			 
		}

		if ( $type == 'tax' ) {
			$items = get_terms( array( $slug ), array( 'hide_empty' => 0 ) );
		}

		if ( $type == 'post' ) {
			$post_types = array();

			if ( empty( $slug ) ) {
				$post_types[] = 'post';
			} else {
				$post_types = explode( ",", $slug );
			}

			$items = get_posts( array( "post_type" => $post_types, 'posts_per_page' => 999 ) );
		}

		return $items;
	}

	function options( $items, $type ) {
		$options = array();
	 
		 
		if ( $type == 'tax' ) {
			foreach ( $items as $item ) {
				$options[ $item->term_id ] = apply_filters( 'pp-fields-tax-option-field', $item->name, $item );
			}
		}

		if ( $type == 'user' ) {
		 
			foreach ( $items as $item ) {
				$options[ $item->ID ] = apply_filters( 'pp-fields-user-option-field', $item->display_name, $item );
			}
		}

		if ( $type == 'post' ) {
			foreach ( $items as $item ) {
				$options[ $item->ID ] = apply_filters( 'pp-fields-post-option-field', $item->post_title, $item );
			}
		}

		asort( $options );

		return $options;
	}

	function search_form( $type, $key, $slug, $link ) {
		add_filter( "um_search_field_{$key}", function ( $attrs ) use ( $type, $key, $slug ) {
			$items   = $this->items( $type, $slug );
			$options = $this->options( $items, $type );

			$attrs['options'] = $options;
			$attrs['custom']  = true;

			return $attrs;
		}, 100, 1 );
	}

	function profile_form( $type, $key, $slug, $link ) {
		
		$profile_id = um_profile_id();
		

		add_filter( "um_{$key}_form_edit_field", function ( $output, $mode ) use ( $type, $key, $slug, $profile_id ) {
			$ids     = get_user_meta( $profile_id, $key, true );
			$replace = '<option value=""></option>';
			$items   = $this->items( $type, $slug );
			$options = $this->options( $items, $type );

			foreach ( $options as $k => $v ) {
				$selected = '';

				if ( is_array( $ids ) ) {
					if ( $type == 'tax' ) {
						if ( in_array( $k, $ids ) ) {
							$selected = 'selected';
						}
					} elseif ( in_array( $k, $ids ) ) {
						$selected = 'selected';
					}
				} else {
					if ( $type == 'tax' ) {
						if ( $k == $ids ) {
							$selected = 'selected';
						}
					} elseif ( $k == $ids ) {
						$selected = 'selected';
					}
				}
               
				$replace .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
			}
			if($key==='team-names'){
				global $wpdb;
				$results = $wpdb->get_results( "SELECT `id`, `teamtitle`, `team_key` FROM {$wpdb->prefix}teams");
				if(!empty($results))                       
                     {    
                  foreach($results as $row){ 
					  if($ids==$row->teamtitle){
						$replace .= '<option value="'.$row->teamtitle.'" selected >'.$row->teamtitle.'</option>'; 
					  }else{
						$replace .= '<option value="'.$row->teamtitle.'" >'.$row->teamtitle.'</option>';
					  }
					
					
				  }
				} 
				
			}

			if($key==='user-citys'){
				//require_once("city.php");
				//$cityArray= json_decode($cityjson); 
				//print_r($cityArray);
				//foreach($cityArray as $keys =>$value ){ 
					//$i=0;
				//	foreach ($value as $key ) {
					//	$replace .= '<option  value="'.$key.'" >'.$key.'</option>';
					//}
					// while ($value) {
					// 	$replace .= '<option  value="'.$value[$i].'" >'.$value[$i].'</option>';
					// 	$i++;
					// }
                 // } 
				if($ids){
				  $replace .= '<option selected  value="'.$ids.'" >'.$ids.'</option>';
				}else{
					//$replace .= '<option value="Genova" >Genova</option>';
				}
				
			}
			$output = preg_replace( '%<option .*</option>%', $replace, $output );

			return $output;
		}, 10, 2 );

		add_filter( "um_{$key}_form_show_field", function ( $output, $mode ) use ( $type, $key, $slug, $link, $profile_id ) {
			$ids = get_user_meta( $profile_id, $key, true );

			if ( ! is_array( $ids ) ) {
				$ids = array( $ids );
			}

			$replace = '<div class="um-field-value">';
			$items   = array();

			if ( $type == 'user' ) {
				$items = get_users( array( 'include' => $ids ) );
			}

			if ( $type == 'tax' ) {
				$items = get_terms( array( $slug ), array( 'include' => $ids, 'hide_empty' => 0 ) );
			}

			if ( $type == 'post' ) {
				$post_types = explode( ",", $slug );
				$items      = get_posts( array(
					'include'        => $ids,
					'post_type'      => $post_types,
					'posts_per_page' => 999
				) );
			}

			$replace = '<div class="um-field-value">';

			$names = array();

			foreach ( $items as $item ) {
				if ( $type == 'user' ) {
					um_fetch_user( $item->ID );

					$result = $item->display_name;

					if ( $link == 'true' ) {
						$result = '<a href="' . um_user_profile_url() . '">' . $item->display_name . '</a>';
					} elseif ( $link == 'avatar' ) {
						$result = '<a style="display:inline-block" href="' . um_user_profile_url() . '" class="um-tip-s" title="' . um_user( 'display_name' ) . '">' . get_avatar( $item->ID, 40 ) . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-user-display-field', $result, $item );
				}

				if ( $type == 'tax' ) {
					$result = $item->name;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_term_link( $item ) . '">' . $item->name . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-tax-value-field', $result, $item );
				}

				if ( $type == 'post' ) {
					$result = $item->post_title;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_permalink( $item ) . '">' . $item->post_title . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-post-value-field', $result, $item );
				}
			}

			// return to profile user data
			um_fetch_user( $profile_id );

			if ( $link == "avatar" ) {
				$replace .= implode( " ", $names );
			} else {
				$replace .= implode( ", ", $names );
			}

			$replace .= '</div>';

			$find = '%<div class="um-field-value">([0-9,\s]+)*</div>%';

			$output = preg_replace( $find, $replace, $output );

			return $output;
		}, 10, 2 );
	}

	/**
	 * Show relation on Profile Card in Members Directory
	 *
	 * @param $type
	 * @param $key
	 * @param $slug
	 * @param $link
	 */
	function filtered_value( $type, $key, $slug, $link ) {

		add_filter( "um_profile_field_filter_hook__{$key}", function ( $value, $data ) use ( $type, $key, $slug, $link ) {

			$profile_id = um_user( 'ID' );

			$ids = get_user_meta( $profile_id, $key, true );

			if ( ! is_array( $ids ) ) {
				$ids = array( $ids );
			}

			$items = array();

			if ( $type == 'user' ) {
				$items = get_users( array( 'include' => $ids ) );
			}

			if ( $type == 'tax' ) {
				$items = get_terms( array( $slug ), array( 'include' => $ids, 'hide_empty' => 0 ) );
			}

			if ( $type == 'post' ) {
				$post_types = explode( ",", $slug );
				$items      = get_posts( array(
					'include'        => $ids,
					'post_type'      => $post_types,
					'posts_per_page' => 999
				) );
			}

			$modified_value = '<div class="um-field-value">';

			$names = array();

			foreach ( $items as $item ) {
				if ( $type == 'user' ) {
					um_fetch_user( $item->ID );

					$result = $item->display_name;

					if ( $link == 'true' ) {
						$result = '<a href="' . um_user_profile_url() . '">' . $item->display_name . '</a>';
					} elseif ( $link == 'avatar' ) {
						$result = '<a style="display:inline-block" href="' . um_user_profile_url() . '" class="um-tip-s" title="' . um_user( 'display_name' ) . '">' . get_avatar( $item->ID, 40 ) . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-user-display-field', $result, $item );
				}

				if ( $type == 'tax' ) {
					$result = $item->name;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_term_link( $item ) . '">' . $item->name . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-tax-value-field', $result, $item );
				}

				if ( $type == 'post' ) {
					$result = $item->post_title;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_permalink( $item ) . '">' . $item->post_title . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-post-value-field', $result, $item );
				}
			}

			// return to profile user data
			um_fetch_user( $profile_id );

			if ( $link == "avatar" ) {
				$modified_value .= implode( " ", $names );
			} else {
				$modified_value .= implode( ", ", $names );
			}

			$modified_value .= '</div>';

			return $modified_value;

		}, 10, 2 );
	}
}

new PP_Fields();