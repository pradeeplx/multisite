<?php

/**
 * Holds all functions of football receive data into wordpress .
 *
 * @author Dheeraj Tiwari <dheeraj@dheeraj@blsoftware.net>
 * @since 1.0.0
 * @package football data receive
 */

class HaveFun_RestAPI {


    /**
	 * The public constructor.
	 */
    // var $host_username = '3rde';
    // var $host_password = 'Linux@123';
    // var $host_authorization_key = '';
    public function __construct() {
        global $wpdb;
        // add_action( 'rest_api_init', array( $this,'football_get_event_route' ) );
        // $this->$host_authorization_key ='Basic ' . base64_encode( $this->host_username . ':' . $this->host_password);
    }
    public function create_product_wcfm_api( $fields, $user_id ){
	$curl = curl_init();
	 
	$authorization_key = 'Basic ' . base64_encode( '3rde' . ':' . 'Linux@123' );
	curl_setopt_array($curl, array(
  	CURLOPT_URL => site_url()."/wp-json/wcfmmp/v1/products",
  	CURLOPT_RETURNTRANSFER => true,
  	CURLOPT_ENCODING => "",
  	CURLOPT_MAXREDIRS => 10,
  	CURLOPT_TIMEOUT => 30,
  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  	CURLOPT_CUSTOMREQUEST => "POST",
  	CURLOPT_POSTFIELDS => json_encode( $fields ),
  		CURLOPT_HTTPHEADER => array(
    		"Authorization: ".$authorization_key,
    		"Cache-Control: no-cache",
    		"Content-Type: application/json",
  		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	   return $err;
	} else {
	  return $response;
	} 
}

public function create_product_wchf_api( $fields, $userid){
	$curl = curl_init();
	$cs_consumer_key = 'ck_6ad10a7c6eec78988e6caf1aabc926909f9e18e3';
	$cs_secret_key = 'cs_db11a21df88a418f3248061547c62e823cde3124'; 
	 
	$url = site_url().'/wp-json/wc/v3/products?consumer_key='.$cs_consumer_key.'&consumer_secret='.$cs_secret_key;
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => json_encode( $fields ),
	  CURLOPT_HTTPHEADER => array(
	    "Cache-Control: no-cache",
	    "Content-Type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  return  $err;
	} else {
		return $response;
	}
}


public function get_all_product(){
	$curl = curl_init();
	$cs_consumer_key = 'ck_6ad10a7c6eec78988e6caf1aabc926909f9e18e3';
	$cs_secret_key = 'cs_db11a21df88a418f3248061547c62e823cde3124'; 
	 
	$url = site_url().'/wp-json/wc/v3/products?consumer_key='.$cs_consumer_key.'&consumer_secret='.$cs_secret_key;
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "Postman-Token: 5beb9c05-1c22-4c84-b50a-dabed87f34aa"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
}

    public function update_wcfm_paid_services( $data, $user_id, $product_id){
    	$curl = curl_init();
	 	// var_dump(json_encode( $data ));
	 	// die("e");
		$authorization_key = 'Basic ' . base64_encode( '3rde' . ':' . 'Linux@123' );
		curl_setopt_array($curl, array(
	  	CURLOPT_URL => site_url()."/wp-json/wcfmmp/v1/products/".$product_id,
	  	CURLOPT_RETURNTRANSFER => true,
	  	CURLOPT_ENCODING => "",
	  	CURLOPT_MAXREDIRS => 10,
	  	CURLOPT_TIMEOUT => 30,
	  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  	CURLOPT_CUSTOMREQUEST => "PUT",
	  	CURLOPT_POSTFIELDS => json_encode( $data ),
	  		CURLOPT_HTTPHEADER => array(
	    		"Authorization: ".$authorization_key,
	    		"Cache-Control: no-cache",
	    		"Content-Type: application/json",
	  		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		   return $err;
		} else {
		  return $response;
		} 
    }

}



// $dd = $HaveFun_RestAPI->update_wcfm_paid_services( array('regular_price' => '25', 'name' => 'demo'), 2, 240);
// var_dump($dd);

