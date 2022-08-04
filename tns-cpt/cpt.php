<?php

/*

Plugin Name: TNS CPT
Plugin URI:  https://www.webmasterserviceshawaii.com
Description: TNS CPT
Version:     1.0
Author:      Gary Wells, Sourav Sobti
Author URI:  https://www.webmasterserviceshawaii.com
License:     GPL2 etc

Copyright 2021 Sourav Sobti (email : souravsobti@gmail.com).	

*/ 

if ( ! defined( 'NOTIFICATION_VERSION' ) ) {

	define( 'NOTIFICATION_VERSION', '1.0' );

}


include( trailingslashit( dirname( __FILE__ ) ) . '/inc/tns_cpt.php' );
/* require_once plugin_dir_path( __FILE__ ) . 'pdf_pp/fpdf_protection.php';
require_once plugin_dir_path( __FILE__ ) . 'pdf_pp/fpdf.php';
require_once plugin_dir_path( __FILE__ ) . 'pdf_pp/helvetica.php';
require_once plugin_dir_path( __FILE__ ) . 'pdf_pp/pdf.php';
require_once plugin_dir_path( __FILE__ ) . 'pdf_pp/doc.pdf';
 
  */