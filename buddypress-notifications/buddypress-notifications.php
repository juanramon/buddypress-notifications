<?php
/*
Plugin Name: Posts & Activities notify
Plugin URI:
Description: Notify all the users when a new post or activity is published
Version: 1.2
Author: Juan Ramon & Christian
Author URI: http://twitter.com/juanradiaz
*/

define('POST_NOTIFY', '1.2') ;

if ( !function_exists('set_contenttype') ) {
    function set_contenttype($content_type) {
        return 'text/html' ;
    }
}

function post_notification($post_ID) {
    global $wpdb ;

    $post = get_post($post_ID) ;
    add_filter('wp_mail_content_type','set_contenttype') ;

    $aUsersID = $wpdb->get_col( $wpdb->prepare( "SELECT $wpdb->users.ID FROM $wpdb->users") ) ;
    foreach($aUsersID as $iUserID) {
        $user     = get_userdata($iUserID) ;
        $to       = $user->user_email ;
        $subject  = '[' . get_bloginfo('name') . '] New post in the blog' ;
        $message  = 'New Post: <a href="' . get_permalink($post_ID) . '">' . $post->post_title . '</a>' ;
        $message .= '<br/><br/>' ;
        $message .= nl2br($post->post_content) ;
        wp_mail( $to, $subject, $message ) ;
    }
}

add_action('publish_post', 'post_notification') ;

function activity_notification($content, $user_id, $activity_id) {
    global $wpdb ;

    add_filter('wp_mail_content_type', 'set_contenttype') ;

    $aUsersID = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users") ) ;
    foreach($aUsersID as $iUserID) {
        $user     = get_userdata($iUserID) ;
        $to       = $user->user_email ;
        $subject  = '[' . get_bloginfo('name') . '] New activity' ;
        $message  = '<a href="' . bp_activity_get_permalink($activity_id) . '">New activity in ' . get_bloginfo('name') . '</a>:' ;
        $message .= '<br/><br/>' ;
        $message .= nl2br($content) ;

        wp_mail( $to, $subject, $message ) ;
    }
}

add_action( 'bp_activity_posted_update', 'activity_notification') ;

?>