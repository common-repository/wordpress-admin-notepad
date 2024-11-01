<?php
define('DOING_AJAX', true);
define('WP_ADMIN', true);

$zv_wpan_shorttag = 'wpadminnotepad';
$zv_wpan_notes_name = $zv_wpan_shorttag.'_savednote';
$zv_wpan_lastupdate_name = $zv_wpan_shorttag.'_lastupdate';
$zv_wpan_savestate_name = $zv_wpan_shorttag.'_state';
$zv_wpan_settings_name = $zv_wpan_shorttag.'_settings';

require_once('../../../wp-load.php');
//require_once('../../../wp-admin/includes/admin.php');

if ( !is_user_logged_in() ) { echo '-1'; die(); }

require_once(dirname(__FILE__).'/wpan_functions.php');
$zv_wpan_settings = get_option($zv_wpan_settings_name);
$allowed_levelid = zv_wpan_get_permission($zv_wpan_settings,'edit');

//check for permission based on user level
global $current_user;
get_currentuserinfo();

if (!in_array($current_user->user_level,$allowed_levelid)) {
  if (isset($_POST[$zv_wpan_shorttag.'_savestate'])) {
  echo '100'; die(); // do nothing
  } else {
  echo '99'; die();
  }
} else {

  if (isset($_POST[$zv_wpan_shorttag.'_notesubmit'])) {
  
  $_POST[$zv_wpan_shorttag.'_textarea'] = (urldecode($_POST[$zv_wpan_shorttag.'_textarea']));
  //echo ($_POST[$zv_wpan_shorttag.'_textarea']);
  $_POST[$zv_wpan_shorttag.'_textarea'] = str_replace("%wpan_plus%",'+',$_POST[$zv_wpan_shorttag.'_textarea']);
  
    //if (get_option($zv_wpan_notes_name)) {
    update_option($zv_wpan_notes_name,$_POST[$zv_wpan_shorttag.'_textarea']);
    //} else {
    //add_option($zv_wpan_notes_name,$_POST[$zv_wpan_shorttag.'_textarea']);
    //}
    //if (get_option($zv_wpan_lastupdate_name)) {
    update_option($zv_wpan_lastupdate_name,time());
    //} else {
    //add_option($zv_wpan_lastupdate_name,time());
    //}
    echo '1'; 
  }
  
  if (isset($_POST[$zv_wpan_shorttag.'_noteclear'])) {
    if (get_option($zv_wpan_notes_name)) {
    delete_option($zv_wpan_notes_name);
    }
    //if (get_option($zv_wpan_lastupdate_name)) {
    update_option($zv_wpan_lastupdate_name,time());
    //} else {
    //add_option($zv_wpan_lastupdate_name,time());
    //}
    echo '2';
  }
  
  if (isset($_POST[$zv_wpan_shorttag.'_savestate'])) {
    if ($_POST[$zv_wpan_shorttag.'_savestate'] == '1') {//save opened
      //if (get_option($zv_wpan_savestate_name)) {
      update_option($zv_wpan_savestate_name,'1');
      //} else {
      //add_option($zv_wpan_savestate_name,'1');
      //}
    } else {
      //if (get_option($zv_wpan_savestate_name)) {
      update_option($zv_wpan_savestate_name,'0');
      //} else {
      //add_option($zv_wpan_savestate_name,'0');
      //}
    }
    echo '3';
  }
  
}

?>
