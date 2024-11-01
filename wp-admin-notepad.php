<?php
/*
 * Plugin Name: Wordpress Admin Notepad
 * Plugin URI: http://zenverse.net/wordpress-admin-notepad/
 * Description: Add a Notepad to your admin panel so that you can take note at anywhere.
 * Author: Zen
 * Author URI: http://zenverse.net/
 * Version: 1.2.4
*/

$zv_wpan_plugin_ver = '1.2.4';
$zv_wpan_plugin_name = 'Wordpress Admin Notepad';
$zv_wpan_plugin_shortname = 'WP Admin Notepad';
$zv_wpan_plugin_url = 'http://zenverse.net/wordpress-admin-notepad/';
$zv_wpan_author_url = 'http://zenverse.net/';
$zv_wpan_wp_url = get_bloginfo('url');
$zv_wpan_shorttag = 'wpadminnotepad';
$zv_wpan_settings_name = $zv_wpan_shorttag.'_settings';
$zv_wpan_notes_name = $zv_wpan_shorttag.'_savednote';
$zv_wpan_mainfile_path = 'options-general.php?page=wordpress-admin-notepad/wp-admin-notepad.php';
$zv_wpan_plugin_path = WP_CONTENT_URL.'/plugins/wordpress-admin-notepad/';


/* admin head (only for wp 2.7 and above) */
$zv_wpan_adminhead_display = true;

$zv_wpan_settings = get_option($zv_wpan_settings_name);
if (!empty($zv_wpan_settings)) { 
if (in_array('toggle',$zv_wpan_settings['hide'])) {
$zv_wpan_adminhead_display = false;
} else {
$zv_wpan_adminhead_display = true;
}
}

require_once(dirname(__FILE__).'/wpan_functions.php');

$wp_version_2digits = wp_version_2digits();
if ($wp_version_2digits >= 27 && $zv_wpan_adminhead_display) {
add_action('admin_head', 'zv_wpan_admin_head');
}

function zv_wpan_admin_head() {
global $zv_wpan_plugin_name,$zv_wpan_wp_url,$zv_wpan_shorttag,$zv_wpan_notes_name,$zv_wpan_plugin_url,$zv_wpan_mainfile_path,$zv_wpan_plugin_path,$zv_wpan_settings;

//check for permission to view based on user level
$view_allowed_levelid = zv_wpan_get_permission($zv_wpan_settings,'view');
global $current_user, $wp_version_2digits;
get_currentuserinfo();

if (!in_array($current_user->user_level,$view_allowed_levelid)) {
return;
}

$zv_wpan_textarea_content = get_option($zv_wpan_notes_name);
$zv_wpan_textarea_content = str_replace("\r",'',$zv_wpan_textarea_content);
$zv_wpan_textarea_content = htmlspecialchars(str_replace("\n",'\n',$zv_wpan_textarea_content));

$zv_wpan_notes_lastupdate = get_option($zv_wpan_shorttag.'_lastupdate');
if ($zv_wpan_notes_lastupdate) {
$lastupdate_value = zv_wpan_gettimediff($zv_wpan_notes_lastupdate);
} else {
$lastupdate_value = 'N/A';
}

$buttonstyle = 'padding-top:4px;padding-bottom:4px;';
$br = strtolower($_SERVER['HTTP_USER_AGENT']);
if(ereg("msie", $br)) {
$buttonstyle = '';
}

$notepadstate_style = 'display:none;';
$savestate_open = ''; $savestate_close = '';
if (!empty($zv_wpan_settings['save'])) {
if (in_array('state',$zv_wpan_settings['save'])) {//save state = true
$savestate_open = 'zv_wpan_ajax(\'3\');';
$savestate_close = 'zv_wpan_ajax(\'4\');';

$notepadstate = get_option($zv_wpan_shorttag.'_state');
$notepadstate_style = 'display:none;';
if ($notepadstate == '1') {
$notepadstate_style = 'display:block;';
}
}
}

$edit_allowed_levelid = zv_wpan_get_permission($zv_wpan_settings,'edit');
if (in_array($current_user->user_level,$edit_allowed_levelid)) {//allowed to edit
$note_submit_button = '<p><input type="submit" class="button-primary" style="'.$buttonstyle.'" value="Save Note" name="'.$zv_wpan_shorttag.'_notesubmit" onclick="zv_wpan_ajax(\\\'1\\\');" id="'.$zv_wpan_shorttag.'_submitbutton" />&nbsp;<span id="'.$zv_wpan_shorttag.'_status"></span></p>';
$note_clear_button = '<input type="submit" onclick="zv_wpan_confirm_clear()" class="button" style="'.$buttonstyle.'" value="Clear Note" name="'.$zv_wpan_shorttag.'_noteclear" id="'.$zv_wpan_shorttag.'_submitbutton" /> ';
$return_function = '';
$readonly_textarea = '';
} else {
$note_submit_button = '<p><span id="'.$zv_wpan_shorttag.'_status"></span></p>';
$note_clear_button = '';
$readonly_textarea = ' readonly="readonly"';
$return_function = 'return;';
}

$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser they use.
if(ereg("msie", $br)) { 
$smaller_row = 6;
} else {
$smaller_row = 5;
}

$textarea_rows = 10;
if (isset($_COOKIE['zv_wpan_cookie_noterow']) && is_numeric($_COOKIE['zv_wpan_cookie_noterow'])) {
$textarea_rows = $_COOKIE['zv_wpan_cookie_noterow'];
}

$notesize_code = '<div style="display:inline;font-weight:bold;background:#f1f1f1;-moz-border-radius:8px;font-size:11px;font-family:tahoma;padding:5px;border:1px solid #dddddd;font-style:normal">&nbsp;<a href="javascript:void(0)" onclick="document.getElementById(\\\''.$zv_wpan_shorttag.'_textarea\\\').rows=\\\''.$smaller_row.'\\\';zv_wpan_createCookie(\\\'zv_wpan_cookie_noterow\\\',\\\''.$smaller_row.'\\\',100);">Slim</a> <span style="color:#bbbbbb;font-weight:normal">&nbsp;|&nbsp;</span> <a href="javascript:void(0)" onclick="document.getElementById(\\\''.$zv_wpan_shorttag.'_textarea\\\').rows=\\\'10\\\';zv_wpan_createCookie(\\\'zv_wpan_cookie_noterow\\\',\\\'10\\\',100);">Normal</a></div>';

echo '<script type="text/javascript">
<!--

var $jq = jQuery.noConflict();

function zv_wpan_createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function zv_wpan_isIE() {
return /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent);
}

function zv_wpan_js_init() {
if ('.$wp_version_2digits.' >= 33) {
	$jq("#wp-admin-bar-top-secondary").append(\'<li><a onclick="zv_wpan_showhide_notepad();" href="javascript:void(0)" title="Edit my Wordpress Admin Note">Note</a></li>\');
} else if ('.$wp_version_2digits.' >= 32) {
	$jq("#user_info").before(\'<p style="padding-top:8px;margin:0px;float:right;" id="'.$zv_wpan_shorttag.'_span">&nbsp;| <a onclick="zv_wpan_showhide_notepad();" href="javascript:void(0)" title="Edit my Wordpress Admin Note">Note</a></p>\');
} else {
	var original = document.getElementById(\'user_info\').innerHTML;
	original = original.replace("</p>","");
	original = original.replace("</P>","");
	document.getElementById(\'user_info\').innerHTML = original+\'<span id="'.$zv_wpan_shorttag.'_span"> | <a onclick="zv_wpan_showhide_notepad();" href="javascript:void(0)" title="Edit my Wordpress Admin Note">Note</a></span></p>\';
}

var xdiv = document.createElement("div");
xdiv.innerHTML = \'<div class="wrap" style="width:70%;padding-right:0px;position:relative;"><div id="icon-edit-pages" class="icon32" style="margin-top:0px;"><br /></div><h2 style="padding-top:0px;margin-top:0px;">'.$zv_wpan_plugin_name.'<div style="position:absolute;top:2px;right:5px;">'.$notesize_code.'</div></h2></div><form method="post" action="'.$zv_wpan_mainfile_path.'" onsubmit="return false;"><textarea name="'.$zv_wpan_shorttag.'_textarea" id="'.$zv_wpan_shorttag.'_textarea" rows="'.$textarea_rows.'" style="width:70%;float:left;margin-right:10px;"'.$readonly_textarea.'>'.$zv_wpan_textarea_content.'</textarea>'.$note_submit_button.'<p>'.$note_clear_button.'<input type="button" onclick="zv_wpan_showhide_notepad();" class="button" style="'.$buttonstyle.'" value="Hide Notepad" /></p><p style="margin-top:0px"><small>Last Update : <span id="'.$zv_wpan_shorttag.'_lastupdate_span">'.$lastupdate_value.'</span></small></p><p></p><div style="clear:both"></div></form><hr style="height:1px;border:0px;font-size:1px;background-color:transparent;color:transparent" />\';

xdiv.setAttribute("id","'.$zv_wpan_shorttag.'_div");
xdiv.setAttribute("style","line-height:16px;'.$notepadstate_style.'margin-top:15px;clear:both;margin-bottom:10px;");

try {//for msie
xdiv.style.setAttribute("cssText", "line-height:16px;'.$notepadstate_style.'margin-top:15px;clear:both;margin-bottom:10px;", 0);
} catch (e) {}

if ('.$wp_version_2digits.' >= 33) {
	document.getElementById(\'wpbody-content\').insertBefore(xdiv, document.getElementById(\'screen-meta\'));
} else {
	document.getElementById(\'screen-meta\').appendChild(xdiv);
}

} //end function

function zv_wpan_confirm_clear() {
'.$return_function.'
var r = confirm(\'Do you really want to clear your current note?\');
if (r) {
zv_wpan_ajax(\'2\');
} else {
return false;
}
}

function urlencode(str) {
return escape(str).replace(/\+/g,\'%2B\').replace(/%20/g, \'+\').replace(/\*/g, \'%2A\').replace(/\//g, \'%2F\').replace(/@/g, \'%40\');
}

var zv_wpan_js_timestamp;
var zv_wpan_js_timer;

function zv_wpan_ajax(action){
'.$return_function.'
	var ajaxRequest;
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				//alert("Your browser broke!");
				return false;
			}
		}
	}
	
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
		var updatelastupdate = true;
		//alert(ajaxRequest.responseText);
		  if (ajaxRequest.responseText == "1") {//submit note (success)
			  document.getElementById("'.$zv_wpan_shorttag.'_status").innerHTML = "<img src=\"'.$zv_wpan_plugin_path.'/tick.gif\" /> Saved.";     
      } else if (ajaxRequest.responseText == "2") {//clear note (success)
			  document.getElementById("'.$zv_wpan_shorttag.'_status").innerHTML = "<img src=\"'.$zv_wpan_plugin_path.'/tick.gif\" /> Cleared.";  
			  document.getElementById("'.$zv_wpan_shorttag.'_textarea").value = ""; 
      } else if (ajaxRequest.responseText == "3") { //state saved
      	updatelastupdate = false;
			  document.getElementById("'.$zv_wpan_shorttag.'_status").innerHTML = "";
      } else if (ajaxRequest.responseText == "99" || ajaxRequest.responseText == "-1") {//no permission
			  document.getElementById("'.$zv_wpan_shorttag.'_status").innerHTML = "<img src=\"'.$zv_wpan_plugin_path.'/error.gif\" /> Action denied.";
			  updatelastupdate = false;
      } else if (ajaxRequest.responseText == "100") {//do nothing
			  updatelastupdate = false;
      } else {
			  document.getElementById("'.$zv_wpan_shorttag.'_status").innerHTML = "<img src=\"'.$zv_wpan_plugin_path.'/error.gif\" /> An error occured.";
			  updatelastupdate = false;
      }
      
        if (updatelastupdate) {
        var newtime = new Date().getTime();
        var diff = (newtime-zv_wpan_js_timestamp)/1000;
        document.getElementById("'.$zv_wpan_shorttag.'_lastupdate_span").innerHTML = diff+" seconds ago"; 
			  }
			  updatelastupdate = true;
			  document.getElementById("'.$zv_wpan_shorttag.'_submitbutton").disabled = false;
        zv_wpan_js_timer = setTimeout("document.getElementById(\''.$zv_wpan_shorttag.'_status\').innerHTML = \'\'; ",2000);
        
		} else {
			document.getElementById("'.$zv_wpan_shorttag.'_submitbutton").disabled = true;
			document.getElementById("'.$zv_wpan_shorttag.'_status").innerHTML = "&nbsp;<img src=\"'.$zv_wpan_plugin_path.'/loading.gif\" /> Working..";
    }
	}
	
if (action == "1") {
  var params = "wpadminnotepad_notesubmit=1&wpadminnotepad_textarea=";
  var filtered = document.getElementById("wpadminnotepad_textarea").value;
  filtered = filtered.replace(/\+/g,"%wpan_plus%")
  filtered = utf8_encode(filtered);
  filtered = urlencode(filtered);
  params = params+filtered;
} else if (action == "2") {
  var params = "wpadminnotepad_noteclear=1";
} else if (action == "3") {
  var params = "wpadminnotepad_savestate=1";
} else if (action == "4") {
  var params = "wpadminnotepad_savestate=0";
}

	ajaxRequest.open("POST", "'.$zv_wpan_plugin_path.'ajax.php", true);
	
	//Send the proper header information along with the request
  ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxRequest.setRequestHeader("Content-length", params.length);
  ajaxRequest.setRequestHeader("Connection", "close");
	
  ajaxRequest.send(params);
  zv_wpan_js_timestamp = new Date().getTime();
  clearTimeout(zv_wpan_js_timer)
}

function utf8_encode ( argString ) {
// http://kevin.vanzonneveld.net

    var string = (argString+\'\').replace(/\r\n/g, "\n").replace(/\r/g, "\n");
 
    var utftext = "";
    var start, end;
    var stringl = 0;
 
    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;
 
        if (c1 < 128) {
            end++;
        } else if((c1 > 127) && (c1 < 2048)) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }
 
    if (end > start) {
        utftext += string.substring(start, string.length);
    }
 
    return utftext;
}

function zv_wpan_showhide_notepad() {
var getelement = document.getElementById(\''.$zv_wpan_shorttag.'_div\');
if (getelement.style.display != "block") {
getelement.style.display = "block";
'.$savestate_open.'
} else {
getelement.style.display = "none"
'.$savestate_close.'
}
}

function zv_wpan_addLoadEvent(func) {
    var oldonload = window.onload;
    if (typeof window.onload != \'function\') {
        window.onload = func;
    } else {
        window.onload = function() {
            if (oldonload) {
                oldonload();
            }
            func();
        }
    }
}
zv_wpan_addLoadEvent(zv_wpan_js_init);

//-->
</script>';

}

/* admin menu */
add_action('admin_menu', 'zv_wpan_admin_menu');

function zv_wpan_admin_menu() {
  global $zv_wpan_plugin_shortname;
  add_options_page($zv_wpan_plugin_shortname, $zv_wpan_plugin_shortname, 8, __FILE__, 'zv_wpan_admin_options');
}

function zv_wpan_admin_options() {
global $zv_wpan_shorttag,$zv_wpan_plugin_name,$zv_wpan_author_url,$zv_wpan_plugin_url,$zv_wpan_plugin_ver,$zv_wpan_settings_name,$zv_wpan_notes_name,$zv_wpan_plugin_path,$zv_wpan_mainfile_path,$table_prefix,$zv_wpan_settings;

// check for note submission
if (isset($_POST[$zv_wpan_shorttag.'_notesubmit'])) {

//check for permission to edit based on user level
$edit_allowed_levelid = zv_wpan_get_permission($zv_wpan_settings,'edit');
global $current_user;
get_currentuserinfo();
if (in_array($current_user->user_level,$edit_allowed_levelid)) {

//if (get_option($zv_wpan_notes_name)) {
update_option($zv_wpan_notes_name,$_POST['wpadminnotepad_textarea']);
//} else {
//add_option($zv_wpan_notes_name,$_POST['wpadminnotepad_textarea']);
//}

//if (get_option($zv_wpan_shorttag.'_lastupdate')) {
update_option($zv_wpan_shorttag.'_lastupdate',time());
//} else {
//add_option($zv_wpan_shorttag.'_lastupdate',time());
//}

echo '<div class="updated" style="padding:5px;"><b>Your notes has been saved.</b></div>';
} else {//no permission
echo '<div class="updated" style="padding:5px;"><b>Your have no permission to save notes.</b></div>';
}
}

// clearing note
if (isset($_POST[$zv_wpan_shorttag.'_noteclear'])) {

//check for permission to edit based on user level
$edit_allowed_levelid = zv_wpan_get_permission($zv_wpan_settings,'edit');
global $current_user;
get_currentuserinfo();
if (in_array($current_user->user_level,$edit_allowed_levelid)) {

if (get_option($zv_wpan_notes_name)) {
delete_option($zv_wpan_notes_name);
}
echo '<div class="updated" style="padding:5px;"><b>Your notes has been cleared.</b></div>';
} else {//no permission
echo '<div class="updated" style="padding:5px;"><b>Your have no permission to clear notes.</b></div>';
}
}

// emergency rescue
if (isset($_POST[$zv_wpan_shorttag.'_rescue'])) {

//check for permission to edit based on user level
$edit_allowed_levelid = zv_wpan_get_permission($zv_wpan_settings,'edit');
global $current_user;
get_currentuserinfo();
if (in_array($current_user->user_level,$edit_allowed_levelid)) {

$zv_wpan_query = mysql_query("DELETE FROM `".$table_prefix."options` WHERE `option_name` = 'wpadminnotepad_savednote' LIMIT 1");
if (mysql_affected_rows() != 1) {
echo '<div class="updated" style="padding:5px;"><b>Emergency Rescue cannot be performed. 
<br />It might be due to Emergency rescue has been performed or your note is empty.
<br />The error is from mySQL is: '.mysql_error().'</b></div>';
} else {
echo '<div class="updated" style="padding:5px;"><b>Emergency Rescue has been sucessfully performed. <a href="'.$zv_wpan_mainfile_path.'">Click here to totally refresh the page</a>.</b></div>';
}

} else {//no permission
echo '<div class="updated" style="padding:5px;"><b>Your have no permission to perform Emergency Rescue.</b></div>';
}
}


// update plugin options
if (isset($_POST['updateoptions'])) {

if (!isset($_POST[$zv_wpan_shorttag.'_hide'])) {
$_POST[$zv_wpan_shorttag.'_hide'] = array();
}
if (!isset($_POST[$zv_wpan_shorttag.'_save'])) {
$_POST[$zv_wpan_shorttag.'_save'] = array();
}
if (!isset($_POST[$zv_wpan_shorttag.'_edit'])) {
$_POST[$zv_wpan_shorttag.'_edit'] = array();
}
if (!isset($_POST[$zv_wpan_shorttag.'_view'])) {
$_POST[$zv_wpan_shorttag.'_view'] = array();
}

$temp_options_for_save['editpermission'] = $_POST[$zv_wpan_shorttag.'_edit'];
$temp_options_for_save['viewpermission'] = $_POST[$zv_wpan_shorttag.'_view'];
$temp_options_for_save['save'] = $_POST[$zv_wpan_shorttag.'_save'];
$temp_options_for_save['hide'] = $_POST[$zv_wpan_shorttag.'_hide'];

//var_dump($temp_options_for_save);

//if (get_option($zv_wpan_settings_name)) {
update_option($zv_wpan_settings_name,$temp_options_for_save);
//} else {
//add_option($zv_wpan_settings_name,$temp_options_for_save);
//}

if (!isset($_POST[$zv_wpan_shorttag.'_state'])) { $statex = '0'; } else { $statex = '1'; }
update_option($zv_wpan_shorttag.'_state',$statex);


echo '<div class="updated" style="padding:5px;"><b>Settings has been updated.</b></div>';
}

// reset plugin options
if (isset($_POST['reset_options'])) {
delete_option($zv_wpan_settings_name);
echo '<div class="updated" style="padding:5px;"><b>Settings has been resetted.</b></div>';
}

// start output
echo '<div class="wrap">';
screen_icon();
echo '<h2>'.wp_specialchars($zv_wpan_plugin_name).'</h2>

<div style="padding:10px;border:2px solid #dddddd;background-color:#fff;-moz-border-radius:10px;margin-top:20px;margin-bottom:20px;">
Version '.$zv_wpan_plugin_ver.' &nbsp;|&nbsp; <a target="_blank" href="'.$zv_wpan_plugin_url.'">Plugin FAQs, Change Log & Info</a> &nbsp;|&nbsp; <a target="_blank" href="http://zenverse.net/support/">Support or Donate via Paypal</a> &nbsp;|&nbsp; <a target="_blank" href="'.$zv_wpan_author_url.'">Zen</a>
</div>

<form method="post" action="">
<textarea name="'.$zv_wpan_shorttag.'_textarea" id="'.$zv_wpan_shorttag.'_textarea" rows="10" style="width:80%;margin-bottom:10px;">';

$zv_wpan_textarea_content = get_option($zv_wpan_notes_name);
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8");
if ($zv_wpan_textarea_content) { echo htmlspecialchars(stripslashes($zv_wpan_textarea_content)); }

echo '</textarea><br />
<input type="submit" class="button-primary" style="'.$buttonstyle.'" name="'.$zv_wpan_shorttag.'_notesubmit" value="Save Note" /> <input type="submit" class="button" style="'.$buttonstyle.'" name="'.$zv_wpan_shorttag.'_noteclear" value="Clear Note" onclick="return confirm(\'Do you really want to clear your current note?\');" />';

$zv_wpan_notes_lastupdate = get_option($zv_wpan_shorttag.'_lastupdate');
//echo $zv_wpan_notes_lastupdate;
if ($zv_wpan_textarea_content && $zv_wpan_notes_lastupdate != '') {
echo ' &nbsp;<small>Last Update : '.zv_wpan_gettimediff($zv_wpan_notes_lastupdate).'</small>';
}

echo '</form>
<p>&nbsp;</p>
<h3>Options</h3>';
$pstyle = 'style="padding:5px;border-bottom:1px solid #cccccc;margin-bottom:7px;"';

//get plugin options/settings
$zv_wpan_settings = get_option($zv_wpan_settings_name);
?>

<!-- plugin options -->
<form method="post" action="">

<p <?php echo $pstyle; ?>><strong>Disable Notepad Toggle at top right corner?</strong> <input type="checkbox" value="toggle" name="<?php echo $zv_wpan_shorttag; ?>_hide[]" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['hide'])) { if (in_array('toggle',$zv_wpan_settings['hide'])) { echo 'checked="checked"'; } } ?> />
<br /><small>Check this checkbox to hide the Notepad Toggle in all admin pages. i.e: Notepad can only be accessed at this page. <br />
Please note that if you disabled this, only administrators can see or edit the notes, even if you allowed other roles to view/edit. Because they cannot access the this page (plugin settings page).</small></p>

<p <?php echo $pstyle; ?>><strong>Save the state of Notepad?</strong> <input type="checkbox" value="state" name="<?php echo $zv_wpan_shorttag; ?>_save[]" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['save'])) { if (in_array('state',$zv_wpan_settings['save'])) { echo 'checked="checked"'; } } ?> />
<br /><small>Check this checkbox to keep the notepad visible in all pages after it showed up. It will be always visible unless anyone press to hide it.<br /> (This can be useful if you want to leave message to other admins / editors)</small></p>

<p <?php echo $pstyle; ?>><strong>Current Saved State of Notepad?</strong>
<select name="<?php echo $zv_wpan_shorttag; ?>_state">
<option value="">Hidden or State not saved</option>
<option value="1"<?php if (get_option($zv_wpan_shorttag.'_state') == 1) { echo ' selected="selected"'; } ?>>Visible</option>
</select>
<br /><br /></p>

<div <?php echo $pstyle; ?>><strong>Permission to Notepad?</strong> 
<br /><small>Decide who can see and edit the notepad here. (<a href="http://codex.wordpress.org/Roles_and_Capabilities#Summary_of_Roles" target="_blank">learn more about user level</a>)</small>
<table style="margin-top:5px;text-align:center;width:380px" class="widefat tag fixed">
<thead><tr><th width="120">User Level</th><th width="150">Permission to View</th><th width="150">Permission to Edit</th></tr></thead>
<tr><td>Administrator</td><td> <input type="checkbox" checked="checked" disabled="disabled" /> </td><td> <input type="checkbox" checked="checked" disabled="disabled" /></td></tr>

<tr><td>Editor</td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_view[]" value="editor" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['viewpermission'])) { if (in_array('editor',$zv_wpan_settings['viewpermission'])) { echo 'checked="checked"'; } } ?> /> </td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_edit[]" value="editor" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['editpermission'])) { if (in_array('editor',$zv_wpan_settings['editpermission'])) { echo 'checked="checked"'; } } ?> /></td></tr>
<tr><td>Author</td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_view[]" value="author" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['viewpermission'])) { if (in_array('author',$zv_wpan_settings['viewpermission'])) { echo 'checked="checked"'; } } ?> /> </td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_edit[]" value="author" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['editpermission'])) { if (in_array('author',$zv_wpan_settings['editpermission'])) { echo 'checked="checked"'; } } ?> /></td></tr>
<tr><td>Contributor</td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_view[]" value="contributor" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['viewpermission'])) { if (in_array('contributor',$zv_wpan_settings['viewpermission'])) { echo 'checked="checked"'; } } ?> /> </td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_edit[]" value="contributor" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['editpermission'])) { if (in_array('contributor',$zv_wpan_settings['editpermission'])) { echo 'checked="checked"'; } } ?> /></td></tr>
<tr><td>Subscriber</td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_view[]" value="subscriber" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['viewpermission'])) { if (in_array('subscriber',$zv_wpan_settings['viewpermission'])) { echo 'checked="checked"'; } } ?> /> </td><td> <input type="checkbox" name="<?php echo $zv_wpan_shorttag; ?>_edit[]" value="subscriber" <?php if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings['editpermission'])) { if (in_array('subscriber',$zv_wpan_settings['editpermission'])) { echo 'checked="checked"'; } } ?> /></td></tr>
</table>

</div>

<p class="submit">
	<input type="submit" name="updateoptions" value="<?php _e('Update Options'); ?> &raquo;" />
	<input type="submit" name="reset_options" onclick="return confirm('<?php _e('Do you really want to reset your current configuration?'); ?>');" value="<?php _e('Reset Options'); ?>" />
</p>
</form>


<p>&nbsp;</p>
<h3>Emergency Rescue</h3>
Wordpress Admin Notepad might fail sometimes, and I am not able to figure out the reason, yet.
<br />
If it failed to save notes, press the "Emergency Rescue" button below.
<p>
<form method="post" action="">
<input type="submit" name="<?php echo $zv_wpan_shorttag; ?>_rescue" class="button" onclick="return confirm('Please use this only when you can\'t save your notes. Are you sure you want to continue?')" value="Emergency Rescue" />
</form>
</p>

<br /><br />

<h3>Author's Message</h3>
The development of this plugin took a lot of time and effort, so please don't forget to <a href="http://zenverse.net/support/">donate via PayPal</a> if you found this plugin useful to ensure continued development.


<br /><br />
<hr style="border:0px;height:1px;font-size:1px;margin-bottom:5px;background:#dddddd;color:#dddddd" />
<small style="color:#999999">
<a target="_blank" href="http://zenverse.net/category/wordpress-plugins/">More plugins by me</a> &nbsp; | &nbsp; <a target="_blank" href="http://zenverse.net/category/wpthemes/">Free Wordpress Themes</a> &nbsp; | &nbsp; <a target="_blank" href="http://themes.zenverse.net/">Premium Wordpress Themes</a> &nbsp; | &nbsp; <a target="_blank" href="http://tools.zenverse.net/">Web Tools</a> &nbsp; | &nbsp; Thank you for using my plugin.
</small>
<br /><br /><br />



<?php
echo '</div>
<div style="clear:both"></div>';//close div class = wrap


// javascript part
echo '<script type="text/javascript">
<!--

function zv_wpan_addLoadEvent2(func) {
    var oldonload = window.onload;
    if (typeof window.onload != \'function\') {
        window.onload = func;
    } else {
        window.onload = function() {
            if (oldonload) {
                oldonload();
            }
            func();
        }
    }
}

function zv_wpan_removenotepad() {
try {
document.getElementById(\''.$zv_wpan_shorttag.'_span\').innerHTML = "";
document.getElementById(\''.$zv_wpan_shorttag.'_div\').innerHTML = "";
} catch(e) {}
}

zv_wpan_addLoadEvent2(zv_wpan_removenotepad);
//-->
</script>';

}

/* delete saved notes during deactivation of this plugin */
register_deactivation_hook(__FILE__,'zv_wpan_unset');


function zv_wpan_unset() {
global $zv_wpan_notes_name,$zv_wpan_shorttag,$zv_wpan_settings_name;
if (get_option($zv_wpan_notes_name)) {
delete_option($zv_wpan_notes_name);
}

if (get_option($zv_wpan_shorttag.'_state')) {
delete_option($zv_wpan_shorttag.'_state');
}

//should we delete the settings too?

}

?>
