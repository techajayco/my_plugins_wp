<?php

require_once('../wp-config.php');

if('PDF_FILE_ENC_DEC'){
 $key = PDF_FILE_ENC_DEC;

}
  
function my_decrypt($data, $key) {
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

$postid =  $_GET['id'];
 
 /* if ( get_post_type($postid) != 'document' || !is_numeric($postid) ) {
	header("Location: ".site_url());
}  */
// check user auth and file permissions
$get_selected_users = get_post_meta($postid ,'specific_users',true);
 
$current_user = wp_get_current_user();
/* if(!in_array($current_user->ID,$get_selected_users) || empty($current_user->ID)){
	header("Location: ".site_url());
} */

function createSlug($str, $delimiter = '-'){  
		$slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
		return $slug; 
} 


    $file_url = get_post_meta($postid ,'wp_custom_attachment', true);
  
 	$doc_name = get_the_title($postid);
	$dname =  createSlug($doc_name);
    $file_url = $file_url['url'];
     
    
    if (empty($file_url)) {
        die('File Not Found!');
    }
    $msg = file_get_contents($file_url);
    
    $msg_encrypted = my_decrypt($msg, $key);
      
    $file = fopen($dname.'.pdf', 'wb');
    fwrite($file, $msg_encrypted); 
 
  $filename=$dname.'.pdf'; 

  
  $file_url = site_url()."/documents/".$filename; 
  
  
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file_url)); //Absolute URL
 
 
readfile($file_url); //Absolute URL.

unlink($filename=$dname.'.pdf'); // File Delete from Server.

fclose($file);  

 // header("Location: ".site_url()."/reports");
    die();
