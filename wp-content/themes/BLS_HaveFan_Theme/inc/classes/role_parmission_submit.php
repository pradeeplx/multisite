
<?php
//add_filter('um_account_page_default_tabs_hook', 'my_custom_tab_in_um_for_request_parmission', 10 );
// function my_custom_tab_in_um_for_request_parmission( $tabs ) {
// 	$tabs[200]['request_host']['icon'] = 'um-faicon-user';
// 	$tabs[200]['request_host']['title'] = 'Want to Host';
// 	$tabs[200]['request_host']['custom'] = true;
// 	unset($tabs['220']['shipping']);
// 	unset($tabs['210']['billing']);
// 	unset($tabs['230']['orders']);
// 	return $tabs;
// }
	
/* make our new tab hookable */

// add_action('um_account_tab__request_host', 'um_account_tab__information_host');
// function um_account_tab__information_host( $info ) {
// 	global $ultimatemember;
// 	extract( $info );
//     $output = $ultimatemember->account->get_tab_output('request_host');
// 	if ( $output ) { echo $output; }
// }

/* Finally we add some content in the tab */

function new_host_request_methods( $contactmethods ) {
    $contactmethods['host_request'] = 'Host Request';
    return $contactmethods;
}
add_filter( 'user_contactmethods', 'new_host_request_methods', 10, 1 );


function new_modify_user_table( $column ) {
    $column['host_request'] = 'Host Request';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'host_request' :
        	$request_status = trim(get_user_meta( $user_id, 'send-request', true ));
        	$userData = get_userdata( $user_id );
        	$user_roles = $userData->roles;
        	if( "Yes" == $request_status && in_array('customer', $user_roles) ){
        		$become_data = array();
        		$f_name = get_user_meta( $user_id, 'first_name', true);
        		$l_name = get_user_meta( $user_id, 'last_name' , true );
        		 $fullName = $f_name .' '. $l_name;
        		$become_data['fullname'] = $fullName;
        		$become_data['mobile'] = get_user_meta( $user_id, 'mobile' , true );
        		$become_data['biography'] = get_user_meta( $user_id, 'biography' , true );
        		$become_data['become_message'] = get_user_meta( $user_id, 'become_message' , true );
        		$become_data['staduim'] = implode(', ',  get_user_meta( $user_id, 'stadium-position' , true ));
        		$become_data['teams'] = get_user_meta( $user_id, 'team-names' , true );
        		$become_data['country'] = get_user_meta( $user_id, 'country', true);
        		$become_data['city'] = get_user_meta( $user_id, 'city', true);
        		
        		 
        		return "<span class='view_become_profile' style='color:green;    cursor: pointer;' data-user='".json_encode($become_data)."'>Awating for host<span>";
        	}
            //return get_the_author_meta( 'phone', $user_id );
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );
?>