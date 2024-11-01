=== Wordpress Admin Notepad ===
Contributors: Zen
Donate link: http://zenverse.net/support/
Tags: admin, notepad, note
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 1.2.4

Add a Notepad to your admin panel so that you can view or edit note at anywhere.

== Description ==

Wordpress Admin Notepad is a plugin for wordpress 3.0+ that allows selected user group to view or edit notes as long as they are logged into admin panel.

Wordpress Admin Notepad automatically creates a show/hide notepad toggle at top right corner (beside log out link) so that users can access their notes at anywhere.

Wordpress Admin Notepad supports ONLY Wordpress 3.0 and above, that is after wordpress added Roles & Capability.


**What's New**

* You can now configure the permission to view or edit the notes for the 4 user levels (editors, author, contributor and subscriber).
* You can now save the state of notepad (visible or hidden)
* Notepad now supports cyrilic texts

**Features**

* Allows selected user groups (roles) to edit/view notes at anywhere in admin panel
* The toggle at top right corner can be disable
* Save the state of notepad (visible or hidden)
* Configure the permission to view or edit the notes for the 4 user levels (editors, author, contributor and subscriber)
* Size of notepad can be personalized (slim or normal) and will be saved using cookie


[Plugin Info & Homepage](http://zenverse.net/wordpress-admin-notepad/) | [Plugin Author](http://zenverse.net/)

== Installation ==

1. Download the plugin package
2. Extract and upload the "wordpress-admin-notepad" folder to your-wordpress-directory/wp-content/plugins/
3. Activate the plugin and its ready
4. Go to Admin Panel > Settings > WP Admin Notepad and customise it to suit your needs.

== Frequently Asked Questions ==
= Notes failed to save =
This happened to me without reason and I haven't figure out why. However the fix is here. Use the Emergency Rescue feature at the plugin option page. It will delete the **notes' option row** from your wordpress database like a fresh install. 

== Screenshots ==
1. A live Admin Notepad
2. Settings Page

== Changelog ==
= 1.2.4=
* Now supports Wordpress 3.3

= 1.2.3 =
* Fixed an error while saving note.

= 1.2.2 =
* Fixed a file include conflict with other plugins that did not include their own functions.php file using the correct way

= 1.2.1 =
* Updated toggle button position. for wordpress 3.2 and above.

= 1.2 =
* Updated the form element and description in the settings page.

= 1.1.1 =  
* Wordpress Admin Notepad now supports cyrilic texts

= 1.1 =  
* User can now change size of notepad (this personal setting will be saved using cookie)
* Saving the state of notepad (visible or invisible) is available for you to leave note for other admins or editors.
* Added permission settings for the 5 user levels to view or edit the notes
* Added function htmlspecialchars to textarea content

= 1.0.4 =  
* Fixed some javascript errors in MSIE and Firefox

= 1.0.3 =  
* Fixed an error in hiding the notepad toggle link at top right corner

= 1.0.2 =  
* Added Emergency Rescue feature in case the note failed to save (it might happen, I haven't figure out the reason yet, but the fix is here, see FAQ)

= 1.0.1 =  
* Fixed a bug during note submission using ajax

= 1.0 =  
* First version of Wordpress Admin Notepad
