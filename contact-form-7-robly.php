<?php
/**
 * Plugin Name: Contact Form 7 to Robly
 * Plugin URI: http://code.andrewrminion.com/contact-form-7-to-robly
 * Description: Adds Contact Form 7 submissions to Robly using their API
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * License: GPL2
 */

/* prevent this file from being accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* add settings page */
add_action( 'admin_menu', 'cf7_robly_add_admin_menu' );
add_action( 'admin_init', 'cf7_robly_settings_init' );

// add to menu
function cf7_robly_add_admin_menu() {
    add_options_page( 'Contact Form 7 to Robly', 'CF7 to Robly', 'manage_options', 'contact-form-7-robly', 'cf7_robly_options_page' );
}

// add settings section and fields
function cf7_robly_settings_init() {
    register_setting( 'cf7_robly_options', 'cf7_robly_settings' );

    add_settings_section(
        'cf7_robly_options_keys_section',
        __( 'Add your API Keys', 'cf7_robly' ),
        'cf7_robly_settings_section_callback',
        'cf7_robly_options'
    );

    add_settings_field(
        'cf7_robly_api_id',
        __( 'API ID', 'cf7_robly' ),
        'cf7_robly_api_id_render',
        'cf7_robly_options',
        'cf7_robly_options_keys_section'
    );

    add_settings_field(
        'cf7_robly_api_key',
        __( 'API Key', 'cf7_robly' ),
        'cf7_robly_api_key_render',
        'cf7_robly_options',
        'cf7_robly_options_keys_section'
    );

}


function cf7_robly_api_id_render() {
    $options = get_option( 'cf7_robly_settings' ); ?>
    <input type="text" name="cf7_robly_settings[cf7_robly_api_id]" placeholder="8c5cc6b52e139888c3a3eb2cc7dacd9b" size="40" value="<?php echo $options['cf7_robly_api_id']; ?>">
    <?php
}


function cf7_robly_api_key_render(  ) {
    $options = get_option( 'cf7_robly_settings' ); ?>
    <input type="text" name="cf7_robly_settings[cf7_robly_api_key]" placeholder="f1a80ae1cb0c73d4f4d341" size="40" value="<?php echo $options['cf7_robly_api_key']; ?>">
    <?php
}


function cf7_robly_settings_section_callback(  ) {
    echo __( 'Enter your API Keys below. Don’t have any? <a href="mailto:support@robly.com?subject=API access">Request them here</a>.', 'cf7_robly' );
}


function cf7_robly_options_page(  ) { ?>
    <div class="wrap">
       <h2>Contact Form 7 to Robly</h2>
        <form action="options.php" method="post">

            <?php
            settings_fields( 'cf7_robly_options' );
            do_settings_sections( 'cf7_robly_options' );
            submit_button();
            ?>

        </form>
    </div>
    <?php
}
