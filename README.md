# Contact Form 7 to Robly #
**Contributors:** [macbookandrew](https://profiles.wordpress.org/macbookandrew)  
**Donate link:** https://cash.me/$AndrewRMinionDesign  
**Tags:** contact form 7, contact form, cf7, form, forms, submission, submissions, robly, email, automation, customer, marketing  
**Requires at least:** 4.3  
**Tested up to:** 4.8.2  
**Stable tag:** 1.2.5  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Adds Contact Form 7 submission information to one or more Robly lists, including custom fields.

## Description ##

[Robly](https://www.robly.com/) is a paid email marketing service provider that helps you send emails to large numbers of people at once and allows contacts to be in multiple lists for targeted marketing purposes. Using Robly’s API, this plugin adds Contact Form 7 submissions to one or more Robly lists, including custom fields.

This plugin requires an active Robly account as well as Contact Form 7. You’ll also need to <a href="mailto:support@robly.com?subject=API access">contact Robly support</a> to get an API ID and key for your account.

## Installation ##

1. Upload this folder to the `/wp-content/plugins/` directory or install from the Plugins menu in WordPress.
1. Activate the plugin through the Plugins menu in WordPress.
1. Go to Settings > CF7 to Robly in WordPress, enter your Robly API ID and key, and save the settings.
1. On each of your Contact Form 7 forms, go to the “Robly Settings” tab and specify the list(s) you would like to add submissions to, as well as specifying the form fields and Robly data fields. Save the contact form.
1. If you need advanced list handling (adding contacts to specific lists based on which checkboxes are active, etc.):
	1. Add a hidden field named `robly_lists`: `[hidden robly-lists]`
	1. Get the ID numbers of the appropriate lists from Robly: go to <a href="https://app.robly.com/email/lists/summary" target="_blank">lists summary</a> and click on a list. The URL will have a `value` paramater with the ID field you need (example: https://app.robly.com/email/contacts?lookup_type=sub_list&value=<strong>11111</strong>)
	1. Use Javascript on the frontend to watch for changes and dynamically set the `robly_lists` field value to a comma-separated list of the Robly list IDs
	1. For an example, look in the plugin folder at <a href="https://github.com/macbookandrew/cf7-robly/blob/master/js/advanced-checkboxes.js">js/advanced-checkboxes.js</a>

## Frequently asked questions ##

### What is Robly? ###

[Robly](https://www.robly.com/) is a paid email marketing service provider that helps you send emails to large numbers of people at once.

### What do I need to use this plugin? ###

This plugin requires an active Robly account as well as Contact Form 7. You’ll also need to <a href="mailto:support@robly.com?subject=API access">contact Robly support</a> to get an API ID and key for your account.

### API-what? ###

API stands for “Application Programming Interface,” which basically means computer code that is able to talk to other computer systems and get or send information. Most API providers require an API key of some sort (similar to a username and password) to ensure that only authorized people are able to use their services.

### What info is sent or received? ###

1. When you install the plugin and enter your API ID and key, your WordPress site will contact the Robly API, asking for all the lists you have set up in your account. You are then able to choose certain lists to which contact form submissions are added, and those choices are saved in your WordPress options.
1. When somebody submits a contact form, WordPress will contact the Robly API and search for that email address in your Robly account. If found, it will update their information according to the settings you specified; otherwise, it will create a new contact with the contact information and add them to the list(s) you selected.

### I have a hard-coded HTML field and it doesn’t show up in the list of fields; what do I do? ###

1. Click the “Add a custom field” button at the bottom of the Robly settings section.
1. In the “Custom Field Name” field, enter the `name` attribute of your custom field.
1. Choose the Robly field(s) for the custom field and save the contact form.

## Screenshots ##

### 1. Settings screen ###
![Settings screen](http://ps.w.org/cf7-robly/assets/screenshot-1.png)

### 2. Per-form settings ###
![Per-form settings](http://ps.w.org/cf7-robly/assets/screenshot-2.png)

### 3. Custom fields ###
![Custom fields](http://ps.w.org/cf7-robly/assets/screenshot-3.png)


## Changelog ##

### 1.2.5 ###
 * Update documentation

### 1.2.4 ###
 * Fix a bunch of PHP undefined index issues for new installations

### 1.2.3 ###
 * Fix issue preventing form submission if no sublists are set for that form

### 1.2.2 ###
 * Update readme

### 1.2.1 ###
 * Fix some sublist and field caching issues if credentials not set on first run

### 1.2 ###
 * Add support for custom fields

### 1.1.1 ###
 * Fix some array bugs
 * Ignore more field types that don’t make sense for Robly data fields

### 1.1 ###
 * Major upgrade
 * Add individual form and field settings to capture all data
 * Add option to ignore a form

### 1.0.3 ###
 * Check for email address in submitted data

### 1.0.2 ###

 * Improve debugging

### 1.0.1 ###

 * Add GitHub Plugin Updater URL

### 1.0 ###

 * Initial plugin
