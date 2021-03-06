<?php

// INCLUDE WORDPRESS STUFF
define('WP_USE_THEMES', false);
include_once('../../../wp-load.php');

// @see http://wordpress.stackexchange.com/questions/44802/how-to-create-a-specific-frontend-url-not-a-page-from-a-theme-or-plugin

require_once (TEMPLATEPATH . '/schema.php');

switch ($_POST['action']) {
    case 'confirmation_email':
    
    $headers = 'From: ' . get_bloginfo('name') . ' <'.get_settings('admin_email') . '>' . "\r\n";
    add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
    
    $message .= '<style>h2 { margin-top:0; } p { margin-bottom:15px; }</style>';
    
    $message .= '<div style="background-color:#E7EBF2;margin:20px;font-family:"lucida grande",tahoma,verdana,arial,sans-serif;">';
    
    $message .= '<div style="background:white;border:1px solid #C4CDE0;border-bottom-width:2px;border-radius:3px;padding:20px;margin:auto;">';
    
    $message .= '<p>'.$_POST['message'].'</p>';
    
    $message .= '<p>'.$_POST['order_id'].'</p>';
    
    if ($_POST['link_url'] != '') { $message .= '<p><a href="'.$_POST['link_url'].'">Download Now</a></p>'; }
    
    if ($_POST['video_url'] != '') { $message .= '<p><a href="'.$_POST['video_url'].'">Watch Video</a></p>'; }
    
    $message .= '</div>';
    
    $message .= '</div>';
    
    
    wp_mail( $_POST['email'], $_POST['subject'], $message, $headers, $attachments ); 
    
    
        break;
    
    case 'check_email_duplicate':
    
    $email_address = $_POST['email'];
    
    $orders = $wpdb->get_results( "SELECT details FROM ".$wpdb->prefix."popshop_order" );	
    	
    foreach($orders as $order) { 
	    
	    $details = json_decode($order->details);
	    
	    if ($_POST['email'] == $details->email) { echo 'true'; break; }
	    
    }	
    
    exit;
    
    break;
        
    default:
    
$table    = (isset($_POST['table'])) ? $_POST['table'] : null;
$name     = (isset($_POST['name'])) ? $_POST['name'] : null;
$details  = (isset($_POST['details'])) ? $_POST['details'] : null;

// Security checks.

if (!in_array($table, popshop_all_tables())) {
    echo -1;
    exit;
}

// Strip everything from $name but letters and digits
$name = preg_replace("/[^\w]+/", "", $name);


$details = stripslashes($details);

if ($details && !json_decode($details)) {
    echo -1;
    exit;
}

    	// Returns id of insert row:
        echo popshop_insert_event($table, $name, $details);
        break;
}