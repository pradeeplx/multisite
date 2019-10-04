<?php 
 add_shortcode( 'experence-tabs', 'experence_view_with_tab' );
 function experence_view_with_tab(){
  $profile_id = um_profile_id();
  // var_dump($profile_id);
  // var_dump("expression");
  // var_dump(get_current_user_id());
  $tabName='information';
  if($_GET['subtab']){
    $tabName=$_GET['subtab'];
  }
    ?>
     <div class="main-container followMeBar">
      <div class="subnavigation">
        <ul class="um-sub-menu">
          <li class='active' ><a class="scroll" href="#information">Information</a>
          </li>
          <li >
            <a href="#where-to-go" class="scroll where-to-go-tab">Where to go</a>
          </li>
          <li  >
            <a href="#services" class="scroll services-tab">Services</a>
          </li>
          <li  > <a href="#photo-gallery" class="scroll photo-gallery-tab">Photo Gallery</a>
          </li>
          <li >
            <a href="#where-we-meet" class="scroll where-we-meet-tab">Where We Meet</a>
          </li>
          <li >
            <a href="#guest-requests" class="scroll guest-requests-tab">Guest Requests</a>
          </li>
           
            </ul>
      </div>
     </div>
    <?php
    if(isset($_GET['profiletab'])){
        $tabName=$_GET['subtab'];
        require_once("experience-template/information.php");
        require_once("experience-template/where-to-go.php");
        require_once("experience-template/service.php");
        require_once("experience-template/experience-banner.php");
        require_once("experience-template/gallery.php");
        require_once("experience-template/where-we-meet.php");
        require_once("experience-template/guest-request.php");

       
        
          switch ($tabName) {
            case 'information':
                //  require_once("experience-template/information.php");
                break;
            case 'where-to-go':
                //  require_once("experience-template/where-to-go.php");
              break;
            case 'services':
                //require_once("experience-template/service.php");
              break;
            case 'photo-gallery':
                    //  require_once("experience-template/gallery.php");
            break;
            case 'guest-requests':
               // require_once("experience-template/guest-request.php");
              break;
            case 'extra-tours':
               // echo "Extra Tours";
              break;
            default:
           // echo $_GET['subtab'];
             // require_once("experience-template/information.php");
              break;
          }
    }

 }



function add_acf_form_head(){
    global $post;
    
  if ( !empty($post) && has_shortcode( $post->post_content, 'my_acf_user_form' ) ) {
        acf_form_head();
    }
}
add_action( 'wp_head', 'add_acf_form_head', 7 );



 function my_acf_user_form_func( $atts ) {
 
  $a = shortcode_atts( array(
    'field_group' => ''
  ), $atts );
 
  $uid = get_current_user_id();
  
  if ( ! empty ( $a['field_group'] ) && ! empty ( $uid ) ) {
    $user_info = get_userdata($uid);
    $fornname='information';
    switch ($a['field_group']) {
      case '133':
          $fornname='where-we-meet';
        break;
      case '141':
          $fornname='guest-requests';
        break;
      case '89':
          $fornname='information';
        break;
      case '338':
          $fornname='services';
        break;


      default:
       $fornname='information';
        break;
    }
    if($a['field_group']=='284'){
        $redirect_url=get_site_url().'/account';
    }else{
        $redirect_url=get_site_url().'/user/'.$user_info->user_login.'/?profiletab=experience&subtab='.$fornname;
    }
    
    
    $options = array(
      'post_id' => 'user_'.$uid,
      'id' =>'form_id_'.$a['field_group'],
      'field_groups' => array( intval( $a['field_group'] ) ),
      'return' => add_query_arg( 'updated', 'true', $redirect_url )
    );
    
    ob_start();
    
    acf_form( $options );
    $form = ob_get_contents();
    
    ob_end_clean();
  }
  
    return $form;
}
 
add_shortcode( 'my_acf_user_form', 'my_acf_user_form_func' );






?>