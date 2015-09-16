# Contact Form 7 to Robly #
- Contributors: [macbookandrew](https://profiles.wordpress.org/macbookandrew/)
- Tags: button
- Donate link: [https://cash.me/$AndrewRMinionDesign](https://cash.me/$AndrewRMinionDesign)
- Tested up to: 4.3
- Stable tag: 1.0.0
- License: GPL2

A simple plugin to add email submissions to Robly using their API.

## Description ##
A simple plugin to add email submissions to Robly using [their API](http://support.robly.com/api-overview/).

## Installation ##
1. Install this plugin
1. Enter your API keys on the plugin settings screen
1. Create your Contact 7 Forms using these specifications:
    - Email field must be either the `email` type or have the word “email” in the field name
    - First and last name fields must have the words “first-name” or “last-name” in the field name
    - A single name field must have “name” in the name field, and will be added to the first-name field in Robly
    - In each contact form, add a “hidden field” named “robly-lists” with the IDs of the sub-list for the contact to be added to (separate multiple sub-lists with commas)
        - Example: `[hidden robly-lists "11162,11159"]`
