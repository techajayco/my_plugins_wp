<?php

class TNS_CPT { 

	public function __construct()
	{
		add_shortcode( 'users_documents', array($this,'tnsCPT' )); 
		add_shortcode( 'LoginBtn', array($this,'tnsLoginBtn' )); 
 		add_action('int_my_decrypt', array($this,'my_decrypt')); 
  }

	  function my_decrypt($data, $key)
  {
		// Remove the base64 encoding from our key
		$encryption_key = base64_decode($key);
		// To decrypt, split the encrypted data from our IV - our unique separator used was "::"
		list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
		return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
	}

	function tnsLoginBtn( $atts )
  {    
	    if ( is_user_logged_in() ) {
            $data ='<a href='.get_site_url().'/report/" class="login-btn my_account elementor-button-link elementor-button elementor-size-md" role="button">
						<span class="elementor-button-text">My Account</span>
					</a>';
        } else {
            $data ='<a href="'.get_site_url().'/login/" class="login-btn elementor-button-link elementor-button elementor-size-md" role="button">
						<span class="elementor-button-text">Login</span>
					</a>';
    }
      
		return $data;
	}

	function createSlug($str, $delimiter = '-')
  {

		$slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
		return $slug;
	
	} 

	function tnsCPT ( $atts )
  {
		extract( shortcode_atts( array(
			'number' => '30',
			'orderby' => 'none',
 		), $atts ) );

		return $this->getCPT( $number, $orderby, $category );
	}

	function getCPT($posts_per_page, $orderby)
  {
	      
        if(isset($_GET['f'])){
            $args = array( 
            	'post_type' => 'document',
            	'posts_per_page' => -1,
            	'orderby' => 'date',
              'post_status' => 'publish',
            	'order' => 'ASC' 
            );
    
    		$loop = new WP_Query( $args );	 
            if ( $loop->have_posts() ) {
        		$data.= '<div class="tnsCPT"><a href="'.site_url().'/reports"><button id="back-btn">back</button></a>';
        		$current_user = wp_get_current_user();
        		$current_user_id = $current_user->data->ID;
        		$data .= "
        		<style>
        		ul.reports_list{display:inline-block;width:100%;margin:auto;padding:0}ul.reports_list li{padding:2px;list-style:none;width:46%;float:left;margin:2%}img.pdf-imgage{height:50px;width:60px}p.doc_name{display:flex;justify-content:center;margin-top:-45px}li.file{background-color:transparent;border:1px solid #131313e8;border-radius:3px}.doc_inner{display:flex;align-items:center}.doc_inner .elementor-icon{width:12%;font-size:28px;padding:5px;color:#0d72ed}.document_name{width:82%;font-size:13px }
                button#back-btn {
                    background: #0d72ed;
                    border: 2px solid #0d72ed;
                    border-radius: 50px;
                    position: absolute;
                    bottom: 15px;
                    color: #fff;
                    left: 43%;
                    padding: 8px 42px;
                }  

              }
        		</style>
        		<ul class='reports_list'>"; 
         		foreach($loop->posts as $val){   
 
            			$postid = $val->ID;
            			$document_type = get_field( 'document_type',$postid );

            			$doc_count = 0;

            			if($document_type[0] == $_GET['f'] || $document_type == $_GET['f']){
            			     $doc_name = $val->post_title;  
                      
                   		  $specific_users = get_field("specific_users", $postid); 

                   		 $user_own_doc_upload = get_field("user_value", $postid);  
                			 $show_to_all_users = get_field("show_to_all_users", $postid);   
                 			 $file = get_post_meta($postid ,'wp_custom_attachment', true);
                      
                            if (isset($file['url'])) {
                                $url = $file['url'];     
                               $user_meta = get_userdata($current_user_id); 
                                $user_roles = $user_meta->roles;

                                    if (!empty($specific_users)) {
                                          if (in_array($current_user_id, $specific_users)) { 
                                           $data .= "<li class='file doc-".$postid."'><a href=".site_url()."/documents/download.php?id=".$postid."><div class='doc_inner'> <div class=elementor-icon>
                			                <i aria-hidden=true class='far fa-file-alt'></i></div> <div class='document_name' >".$doc_name."</div></div> </a></li><br><br>"; 
                                        $doc_count++;
                                       } 
                                }    
                                    
                                    if($show_to_all_users[0] == "Show All"){ 
                                        $data .= "<li class='file doc-".$postid."'><a href=".site_url()."/documents/download.php?id=".$postid."><div class='doc_inner'> <div class=elementor-icon>
                                        <i aria-hidden=true class='far fa-file-alt'></i></div> <div class='document_name' >".$doc_name."</div></div> </a></li>";
                                        $doc_count++;
                                    }  
                                    //$doc_count++;
  
                            } 
            			}
        
        		}
            //$doc_count++;   
        		if($doc_count==0){
    			    //$data.= '<div class="no-results">Empty!</div>';
    			}
        		$data .= "</ul>"; 

        		$data .= '</div>';
            }else{
                $data.= '<div class="no-results">Empty!</div>';
            }
        }else{
             $field = get_field_object('field_627cbe8410f83'); 
                if( $field ) {
                   $data.='<ul class="doc_folders">';
                        foreach( $field['choices'] as $value ) {
                            $data.= '<li><a href="'.get_site_url().'/reports/?f='. $value .'">' . $value . '</a></li>';
                        }
                    $data.= '</ul>';
                }
        }

		wp_reset_query();		
		
		return $data;
		
	}
}

new TNS_CPT();

add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
  }
}
 
function add_custom_meta_boxes()
 { 
	// Define the custom attachment for posts
	add_meta_box(
		'wp_custom_attachment',
		'Custom Attachment',
		'wp_custom_attachment',
		'document',
		'normal'
	);
 
} // end add_custom_meta_boxes
add_action('add_meta_boxes', 'add_custom_meta_boxes');

function wp_custom_attachment()
  {
    wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');
	  $file = get_post_meta(get_the_ID(), 'wp_custom_attachment', true); //get_post_meta(get_the_ID(), 'wp_custom_attachment', true);
  	//var_dump(($file));
	  $html = ($file)? '<a href="'.$file['url'].'">Encrypt Dcoument</a>':'';
    $html.= '<p class="description">';
        $html .= 'Upload your PDF here.';
    $html .= '</p>';
    $html .= '<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25" />';
    echo $html;
 
  } // end wp_custom_attachment


function my_encrypt($data, $key)
 {
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
    return base64_encode($encrypted . '::' . $iv);
 }
 
 function save_custom_meta_data($id)
 {
	
    /* --- security verification --- */
    if(!wp_verify_nonce($_POST['wp_custom_attachment_nonce'], plugin_basename(__FILE__))) {
      return $id;
    } // end if
       
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $id;
    } // end if
	
	
    if('document' == $_POST['post_type']) {
      if(!current_user_can('edit_page', $id)) {
        return $id;
      } // end if
    } else {
        if(!current_user_can('edit_page', $id)) {
            return $id;
        } // end if
    } // end if
    /* - end security verification - */
	//die('sfsdf');
    // Make sure the file array isn't empty
    if(!empty($_FILES['wp_custom_attachment']['name'])) { 
		$filename   = uniqid() . "-" . time(); 
		$key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';
         
        // Setup the array of supported file types. In this case, it's just PDF.
        $supported_types = array('application/pdf');
         
        // Get the file type of the upload

		//var_dump($_FILES['wp_custom_attachment']['name']); die();
        $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
        $uploaded_type = $arr_file_type['type'];
          
        // Check if the type is supported. If not, throw an error.
        if(in_array($uploaded_type, $supported_types)) {

	   $msg = file_get_contents($_FILES["wp_custom_attachment"]["tmp_name"]); 
	 	$msg_encrypted = my_encrypt($msg, $key); 

		$file = fopen($_FILES["wp_custom_attachment"]["tmp_name"], 'wb');
		fwrite($file, $msg_encrypted);
		fclose($file);  
        $upload = wp_upload_bits($filename.'.enc', null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));
           
            if(isset($upload['error']) && $upload['error'] != 0) {
                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
            } else {
                add_post_meta($id, 'wp_custom_attachment', $upload);
                update_post_meta($id, 'wp_custom_attachment', $upload);     
            } // end if/else 

        } else {
            wp_die("The file type that you've uploaded is not a PDF.");
        } // end if/else
     } // end if
     
 } // end save_custom_meta_data

add_action('save_post', 'save_custom_meta_data');

function update_edit_form()
 {
    echo ' enctype="multipart/form-data"';
 } // end update_edit_form
add_action('post_edit_form_tag', 'update_edit_form');


add_filter( 'upload_mimes', 'my_myme_types', 1, 1 );
function my_myme_types( $mime_types ) {
  $mime_types['enc'] = 'application/enc';   
  return $mime_types;
}


function company_data()
{ 
  $message = ''; 
  $message.="<table>
                <tbody>
                    <tr>
                        <th style='width:100px'> Company Name</th>
                    </tr>";
                    $user_ID = get_current_user_id(); 
                    $companyData = get_field('company_info', 'user_'.$user_ID);

                    if(isset($companyData)){
                        foreach($companyData as $val){
                            $message.="
                                    <tr>
                                        <td style='width:100px'><a href='".$val['company_url']."' target='_blank'>".$val['company_name']."</a></td>
                                    </tr>";
                        } 
                    }
    $message.="</tbody>
            </table>";

    return $message;

}

add_shortcode('companydetail', 'company_data');
 
add_shortcode('DocumentsUpload', 'fronend_upload_doc'); 
function fronend_upload_doc(){
    $user = wp_get_current_user(); 
    $user_id = $user->ID;
    $user_name = $user->display_name;
    $user_email = $user->user_email;
    $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

  
  if(isset($_POST['submit_file'])){
  $doc_name = $_POST['wp_custom_doc_name']; //Document Name

  $file_uploads = $_FILES['wp_custom_attachment'];  // file Uploads
  $filename   = uniqid() . "-" . time();  
  $supported_types = array('application/pdf');
  $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
  $uploaded_type = $arr_file_type['type'];

 
  $msg = file_get_contents($_FILES["wp_custom_attachment"]["tmp_name"]); 
 
  $msg_encrypted = my_encrypt($msg, $key); 
 
  $file = fopen($_FILES["wp_custom_attachment"]["tmp_name"], 'wb');
 
  fwrite($file, $msg_encrypted);

  fclose($file);   
  $upload = wp_upload_bits($filename.'.enc', null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));  

   

  if(isset($upload['error']) && $upload['error'] != 0) {
    wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
} else {
    add_post_meta($post_id, 'wp_custom_attachment', $upload);
    update_post_meta($post_id, 'wp_custom_attachment', $upload);     
}
 $my_post = array(
  'post_author' =>  $user_id,
  'post_type' => 'document', 
  //'post_title'    => wp_strip_all_tags( date("Y-m-d")." ".$user_name." ".'Upload Doc' ), 
  'post_title'    =>  $doc_name . " - " . "$user_name " . date('Y-m-d, g:i a'), 
  'post_content'   => '',
  'post_status'   => 'publish',  
); 
// Insert the post into the database
$post_id = wp_insert_post( $my_post, true ); 

//  update_field('user_value', $user_id, $post_id); // ACF File User Update 
  update_field('specific_users', $user_id, $post_id); // ACF File User Update 
  $value = array("MyUploads");
  update_field('document_type', $value, $post_id); // ACF Select Fields Update 
   update_post_meta($post_id, 'wp_custom_attachment', $upload); 
  }

    $html = ''; 
    if(is_user_logged_in() == true){ 

    $html .= '<form action="" method="post" enctype="multipart/form-data"> 
    <input type="text" name="wp_custom_doc_name" id="wp_custom_doc_name" placeholder=" Document Name"><br><br>
    <input type="file" name="wp_custom_attachment" id="wp_custom_attachment"><br><br>
    <input type="submit" value="Upload PDF" name="submit_file">
  </form> ';
}
    return $html;

}



// Add the custom columns to the document post type:
add_filter( 'manage_document_posts_columns', 'set_custom_edit_document_columns' );
function set_custom_edit_document_columns($columns) {
     
    $columns['document_author'] = __( 'Owner', '' );
      
    return $columns;
}

// Add the data to the custom columns for the document post type:
add_action( 'manage_document_posts_custom_column' , 'custom_document_column', 10, 2 );
function custom_document_column( $column) { 
 
    if($column){ 
      $owner_name = get_field("specific_users", get_the_ID());  
      $user_data = get_userdata( $owner_name[0]); 
      echo $user_data->data->display_name; 
       
    }
  
}

