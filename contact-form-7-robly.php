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

    // API settings
    add_settings_section(
        'cf7_robly_options_keys_section',
        __( 'Add your API Keys', 'cf7_robly' ),
        'cf7_robly_api_settings_section_callback',
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

    // alternate email settings
    add_settings_section(
        'cf7_robly_options_alternate_email_section',
        __( 'Alternate Email', 'cf7_robly' ),
        'cf7_robly_alternate_email_settings_section_callback',
        'cf7_robly_options'
    );

    add_settings_field(
        'cf7_robly_alternate_email',
        __( 'Alternate Email Address', 'cf7_robly' ),
        'cf7_robly_alternate_email_render',
        'cf7_robly_options',
        'cf7_robly_options_alternate_email_section'
    );


}

// print API ID field
function cf7_robly_api_id_render() {
    $options = get_option( 'cf7_robly_settings' ); ?>
    <input type="text" name="cf7_robly_settings[cf7_robly_api_id]" placeholder="8c5cc6b52e139888c3a3eb2cc7dacd9b" size="40" value="<?php echo $options['cf7_robly_api_id']; ?>">
    <?php
}

// print API Key field
function cf7_robly_api_key_render() {
    $options = get_option( 'cf7_robly_settings' ); ?>
    <input type="text" name="cf7_robly_settings[cf7_robly_api_key]" placeholder="f1a80ae1cb0c73d4f4d341" size="40" value="<?php echo $options['cf7_robly_api_key']; ?>">
    <?php
}

// print API Key field
function cf7_robly_alternate_email_render() {
    $options = get_option( 'cf7_robly_settings' ); ?>
    <input type="email" name="cf7_robly_settings[cf7_robly_alternate_email]" placeholder="john.doe@example.com" value="<?php echo $options['cf7_robly_alternate_email']; ?>">
    <?php
}

// print API settings description
function cf7_robly_api_settings_section_callback(  ) {
    echo __( 'Enter your API Keys below. Don’t have any? <a href="mailto:support@robly.com?subject=API access">Request them here</a>.', 'cf7_robly' );
}

// print alteranet email settings description
function cf7_robly_alternate_email_settings_section_callback(  ) {
    echo __( 'By default, failed API results will be emailed to the site administrator. To send to a different email address, enter it below; separate multiple addresses with commas.', 'cf7_robly' );
}

// print form
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

// TODO: get all Robly lists and CF7 forms and match them up

/* hook into CF7 submission */
add_action( 'wpcf7_before_send_mail', 'submit_to_robly', 10, 1 );
function submit_to_robly( $form ) {
    global $wpdb;

    // get API keys
    $options = get_option( 'cf7_robly_settings' );
    $robly_API_id = $options['cf7_robly_api_id'];
    $robly_API_key = $options['cf7_robly_api_key'];

    // set notification email address
    if ( $options['alternate_email'] ) {
        $notification_email = $options['alternate_email'];
    } else {
        $notification_email = get_option( 'admin_email' );
    }

    // get posted data
    $submission = WPCF7_Submission::get_instance();
    if ( $submission ) {
        $posted_data = $submission->get_posted_data();
    }

    // get array keys for form data
    $email_field = filter_array_keys( 'email', $posted_data );
    $first_name_field = filter_array_keys( 'first-name', $posted_data );
    $last_name_field = filter_array_keys( 'last-name', $posted_data );
    $name_field = filter_array_keys( 'name', $posted_data );

    // get form data
    $email = esc_attr( $posted_data[$email_field] );
    if ( isset( $first_name_field) || isset( $last_name_field ) ) {
        $first_name = esc_attr( $posted_data[$first_name_field] );
        $last_name = esc_attr( $posted_data[$last_name_field] );
    } else {
        $first_name = esc_attr( $posted_data[$name_field] );
    }

    // set up data for the request
    $post_url_first_run = 'https://api.robly.com/api/v1/sign_up/generate?api_id=' . $robly_API_id . '&api_key=' . $robly_API_key;
    $post_url_subsequent_runs = 'https://api.robly.com/api/v1/contacts/update_full_contact?api_id=' . $robly_API_id . '&api_key=' . $robly_API_key;
    $post_request_data = array(
        'email'     => $email,
        'fname'     => $first_name,
        'lname'     => $last_name
    );

    // send request via cUrl
    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_URL, $post_url_first_run );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_request_data );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    $first_curl_response = curl_exec( $ch );

    // get sublist(s) and run cUrl for each since PHP won’t allow duplicate array keys and Robly requires sub_lists[] => each list ID
    foreach ( explode( ',', esc_attr( $posted_data['robly-lists'] ) ) as $this_sublist ) {

        // add this sublist to the request
        $post_request_data['sub_lists'] = $this_sublist;

        curl_setopt( $ch, CURLOPT_URL, $post_url_subsequent_runs );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_request_data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $post_result = curl_exec( $ch );

        // check for cUrl errors and send email if needed
        $post_result_array = json_decode( $post_result );
        if ( $post_result_array->successful == 'false' ) {
            $send_email = 'true';
            $notification_content .= $post_result;
        }
    } // end sublist loop

    // close cUrl connection
    curl_close( $ch );

    // send notification email if necessary
    mail( $notification_email, 'Contact to manually add to Robly', $notification_content );
}

function filter_array_keys( $needle, array $haystack ) {
    foreach ( $haystack as $key => $value ) {
        if ( stripos( $key, $needle ) !== false ) {
            return $key;
        }
    }
}
