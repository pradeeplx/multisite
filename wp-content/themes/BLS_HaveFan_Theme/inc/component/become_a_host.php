<?php 

global $wpdb;
$event_team = $wpdb->prefix.'teams';
$postmeta_table = $wpdb->prefix.'postmeta';
$query="SELECT * FROM $event_team ";
$all_teams = $wpdb->get_results($query);
$teamArray=[];
foreach ($all_teams as $key ) {
	$teamArray[$key->teamtitle]=$key->teamtitle;
}
$all_country = $wpdb->get_results( "SELECT * FROM $postmeta_table where `meta_key`='match_country'  group by `meta_value`" );
$countryArray=[];
foreach ($all_country  as $key ) {
	$countryArray[$key->meta_value]=$key->meta_value;
}
acf_add_local_field_group(array (
	'key' => 'team-names',
	'title' => 'Team',
	'fields' => array (
		array (
			'key' => 'team-names',
			'label' => 'Team',
			'name' => 'langue',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => $teamArray,
			'default_value' => array (
				'francais' => 'francais',
			),
			'allow_null' => 1,
			'multiple' => 0,
			'ui' => 1,
			'ajax' => 1,
			'placeholder' => '',
			'disabled' => 0,
			'readonly' => 0,
		),
		array (
			'key' => 'country',
			'label' => 'Country',
			'name' => 'country',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => $countryArray,
			'default_value' => '',
			'allow_null' => 1,
			'multiple' => 0,
			'ui' => 1,
			'ajax' => 1,
			'placeholder' => '',
			'disabled' => 0,
			'readonly' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'role-parmission',
			),
		),
	),
	'menu_order' => 1,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));




add_shortcode('become-a-host', 'become_a_host');

function become_a_host( $atts ){

acf_form_head();

get_header();

?>
<div  class="main-container">
	<div class="um-shadow">
	<?php
	
	acf_form(array(
		'post_id'		=> 'new_post',
		'post_title'	=> true,
		'new_post'		=> array(
			'post_type'		=> 'role-parmission',
			'post_status'	=> 'publish'
		)
	));
	
	?>
  </div> 
	
</div>

<?php get_footer(); 
}
?>