<?php
/**
 * Plugin Name: Contact Form 7 to Robly
 * Plugin URI: http://code.andrewrminion.com/contact-form-7-to-robly
 * Description: Adds Contact Form 7 submissions to Robly using their API
 * Version: 1.1.1
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * License: GPL2
 * GitHub Plugin URI: https://github.com/macbookandrew/cf7-robly
 */

/* prevent this file from being accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* register scripts */
add_action( 'admin_enqueue_scripts', 'cf7_robly_scripts' );
function cf7_robly_scripts() {
    wp_register_script( 'chosen', plugins_url( 'js/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ) );
    wp_register_style( 'chosen', plugins_url( 'css/chosen.min.css', __FILE__ ) );
    wp_register_script( 'cf7-robly-backend', plugins_url( 'js/backend.min.js', __FILE__ ), array( 'jquery', 'chosen' ) );
    wp_register_style( 'cf7-robly-backend', plugins_url( 'css/cf7-robly.min.css', __FILE__ ), array( 'chosen' ) );
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
    // cache lists and fields if valid keys
    if ( $options['cf7_robly_api_id'] && $options['cf7_robly_api_key'] ) {
        cache_robly_lists( $options['cf7_robly_api_id'], $options['cf7_robly_api_key'] );
        cache_robly_fields( $options['cf7_robly_api_id'], $options['cf7_robly_api_key'] );
    }
}

// print alternate email field
function cf7_robly_alternate_email_render() {
    $options = get_option( 'cf7_robly_settings' ); ?>
    <input type="email" name="cf7_robly_settings[cf7_robly_alternate_email]" placeholder="john.doe@example.com" value="<?php echo $options['cf7_robly_alternate_email']; ?>">
    <?php
}

// print API settings description
function cf7_robly_api_settings_section_callback() {
    echo __( 'Enter your API Keys below. Don’t have any? <a href="mailto:support@robly.com?subject=API access">Request them here</a>.', 'cf7_robly' );
}

// print alternate email settings description
function cf7_robly_alternate_email_settings_section_callback() {
    echo __( 'By default, failed API results will be emailed to the site administrator. To send to a different email address, enter it below; separate multiple addresses with commas.', 'cf7_robly' );
}

// print form
function cf7_robly_options_page() { ?>
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

// cache Robly lists
function cache_robly_lists( $robly_API_id = NULL, $robly_API_key = NULL ) {
    if ( $robly_API_id && $robly_API_key ) {
        // get all sublists from API
        $sublists_ch = curl_init();
        curl_setopt( $sublists_ch, CURLOPT_URL, 'https://api.robly.com/api/v1/sub_lists/show?api_id=' . $robly_API_id . '&api_key=' . $robly_API_key . '&include_all=true' );
        curl_setopt( $sublists_ch, CURLOPT_RETURNTRANSFER, true );
        $sublists_ch_response = curl_exec( $sublists_ch );
        curl_close( $sublists_ch );

        // decode JSON return
        $all_sublists = json_decode( $sublists_ch_response );

        // save to options
        if ( $all_sublists ) {
            $sublists = array();
            foreach ( $all_sublists as $list ) {
                $sublists[$list->sub_list->id] = $list->sub_list->name;
            }
            update_option( 'robly_sublists', maybe_serialize( $sublists ) );
        }
    }
}

// cache Robly fields
function cache_robly_fields( $robly_API_id = NULL, $robly_API_key = NULL ) {
    if ( $robly_API_id && $robly_API_key ) {
        // get all fields from API
        $fields_ch = curl_init();
        curl_setopt( $fields_ch, CURLOPT_URL, 'https://api.robly.com/api/v1/fields/show?api_id=' . $robly_API_id . '&api_key=' . $robly_API_key . '&include_all=true' );
        curl_setopt( $fields_ch, CURLOPT_RETURNTRANSFER, true );
        $fields_ch_response = curl_exec( $fields_ch );
        curl_close( $fields_ch );

        // decode JSON return
        $all_fields = json_decode( $fields_ch_response );

        // save to options
        if ( $all_fields ) {
            $fields = array();
            foreach ( $all_fields as $field ) {
                $fields[$field->field_tag->user_tag] = $field->field_tag->label;
            }
            update_option( 'robly_fields', maybe_serialize( $fields ) );
        }
    }
}

// add WPCF7 metabox
add_action( 'wpcf7_add_meta_boxes', 'cf7_robly_wpcf7_add_meta_boxes' );
function cf7_robly_wpcf7_add_meta_boxes() {
    add_meta_box(
        'cf7s-subject',
        'Robly Settings',
        'cf7_robly_wpcf7_metabox',
        NULL,
        'form',
        'low'
    );
}

// print WPCF7 metabox
function cf7_robly_wpcf7_metabox( $cf7 ) {
    $post_id = $cf7->id();
    $settings = cf7_robly_get_form_settings( $post_id );

    wp_enqueue_script( 'chosen' );
    wp_enqueue_style( 'chosen' );
    wp_enqueue_script( 'cf7-robly-backend' );
    wp_enqueue_style( 'cf7-robly-backend' );

    // get all Robly sublists
    $robly_sublists = maybe_unserialize( get_option( 'robly_sublists' ) );
    $all_submissions = $settings['all-submissions'];
    $sublists_options = NULL;

    // generate list of sublist options
    foreach ( $robly_sublists as $id => $list ) {
        $sublists_options .= '<option value="' . $id . '"';
        if ( $all_submissions ) {
            $sublists_options .= in_array( $id, $settings['all-submissions'] ) ? ' selected="selected"' : '';
        }
        $sublists_options .= '>' . $list . '</option>';
    }

    // get all Robly fields
    $robly_fields = maybe_unserialize( get_option( 'robly_fields' ) );
    $fields_options = NULL;

    // get all WPCF7 fields
    $wpcf7_shortcodes = WPCF7_ShortcodeManager::get_instance();
    $field_types_to_ignore = array( 'recaptcha', 'clear', 'submit' );
    $form_fields = array();
    foreach ( $wpcf7_shortcodes->get_scanned_tags() as $this_field ) {
        if ( ! in_array( $this_field['type'], $field_types_to_ignore ) ) {
            $form_fields[] = $this_field['name'];
        }
    }

    // get saved fields and combine with WPCF7
    $saved_fields = $settings['fields'];
    if ( $saved_fields ) {
        $all_fields = array_merge( $form_fields, array_keys( $saved_fields ) );
    } else {
        $all_fields = $form_fields;
    }

    // start setting up Robly settings fields
    $fields = array(
        'ignore-field' => array(
            'label'     => 'Ignore this Contact Form',
            'docs_url'  => 'http://code.andrewrminion.com/contact-form-7-to-robly/',
            'field'     => sprintf(
                '<input id="ignore-form" name="cf7-robly[ignore-form]" value="1" %s type="checkbox" />
                <p class="desc"><label for="ignore-form">%s</ignore></p>',
                checked( $settings[ 'ignore-form' ], true, false ),
                'Don&rsquo;t send anything from this form to Robly'
            ),
        ),
        'all-submissions' => array(
            'label'     => 'Robly Lists',
            'docs_url'  => 'http://code.andrewrminion.com/contact-form-7-to-robly/',
            'field'     => sprintf(
                '<label>
                    <select name="cf7-robly[all-submissions][]" multiple %1$s>
                    ' . $sublists_options .  '
                    </select>
                </label>
                <p class="desc">%2$s</p>',
                $settings['ignore-form'] ? 'disabled' : '',
                'Add all submissions to these lists'
            ),
        ),
    );

    // add all CF7 fields to Robly settings fields
    foreach ( $all_fields as $this_field ) {
        $fields_options = NULL;
        foreach ( $robly_fields as $id => $label ) {
            $fields_options .= '<option value="' . $id . '"';
            if ( $settings['fields'] && $settings['fields'][$this_field] ) {
                $fields_options .= in_array( $id, $settings['fields'][$this_field] ) ? ' selected="selected"' : '';
            }
            $fields_options .= '>' . $label . '</option>';
        }

        $fields[$this_field] = array(
            'label'     => '<code>' . esc_html( $this_field ) . '</code> Field',
            'docs_url'  => 'http://code.andrewrminion.com/contact-form-7-to-robly/',
            'field'     => sprintf(
                '<label>
                    <select name="cf7-robly[fields][%1$s][]" multiple %3$s>
                        %2$s
                    </select>
                </label>
                <p class="desc">Add contents of the <code>%1$s</code> field to these Robly field(s)</p>',
                $this_field,
                $fields_options,
                $settings['ignore-form'] ? 'disabled' : ''
            )
        );
    }

    // add a hidden row to use for cloning
    $fields['custom-field-template'] = array(
        'label'     => '<input type="text" placeholder="Custom Field" name="custom-field-name" /> Field',
        'docs_url'  => 'http://code.andrewrminion.com/contact-form-7-to-robly/',
        'field'     => sprintf(
            '<label>
                <select name="cf7-robly[fields][%1$s][]" multiple>
                    %2$s
                </select>
            </label>
            <p class="desc">Add contents of the <code><span class="name">%1$s</span></code> field to these Robly field(s)</p>',
            'custom-field-template-name',
            $fields_options
        )
    );

    $rows = array();

    foreach ( $fields as $field_id => $field )
        $rows[] = sprintf(
            '<tr class="cf7-robly-field-%1$s">
                <th>
                    <label for="%1$s">%2$s</label><br/>
                </th>
                <td>%3$s</td>
            </tr>',
            esc_attr( $field_id ),
            $field['label'],
            $field['field']
        );

    printf(
        '<p class="cf7-robly-message"></p>
        <table class="form-table cf7-robly-table">
            %1$s
        </table>
        <p><button class="cf7-robly-add-custom-field button-secondary" %2$s>Add a custom field</button></p>',
        implode( '', $rows ),
        $settings['ignore-form'] ? 'disabled' : ''
    );

}

// register WPCF7 Robly Settings panel
add_filter( 'wpcf7_editor_panels', 'cf7_robly_register_wpcf7_panel' );
function cf7_robly_register_wpcf7_panel( $panels ) {
    $form = WPCF7_ContactForm::get_current();
    $post_id = $form->id();

    $panels['cf7-robly-panel'] = array(
        'title' => 'Robly Settings',
        'callback' => 'cf7_robly_wpcf7_metabox',
    );

    return $panels;
}

// save WPCF7 Robly settings
add_action( 'wpcf7_save_contact_form', 'cf7_robly_wpcf7_save_contact_form' );
function cf7_robly_wpcf7_save_contact_form( $cf7 ) {
    if ( ! isset( $_POST ) || empty( $_POST ) || ! isset( $_POST['cf7-robly'] ) || ! is_array( $_POST['cf7-robly'] ) ) {
        return;
    }

    $post_id = $cf7->id();

    if ( ! $post_id ) {
        return;
    }

    if ( $_POST['cf7-robly'] ) {
        update_post_meta( $post_id, '_cf7_robly', $_POST['cf7-robly'] );
    }
}

// retrieve WPCF7 Robly settings
function cf7_robly_get_form_settings( $form_id, $field = null, $fresh = false ) {
    $form_settings = array();

    if ( isset( $form_settings[ $form_id ] ) && ! $fresh ) {
        $settings = $form_settings[ $form_id ];
    } else {
        $settings = get_post_meta( $form_id, '_cf7_robly', true );
    }

    $settings = wp_parse_args(
        $settings,
        array(
            '_cf7_robly' => NULL,
        )
    );

    // Cache it for re-use
    $form_settings[ $form_id ] = $settings;

    // Return a specific field value
    if ( isset( $field ) ) {
        if ( isset( $settings[ $field ] ) ) {
            return $settings[ $field ];
        } else {
            return null;
        }
    }

    return $settings;
}

/* hook into WPCF7 submission */
add_action( 'wpcf7_before_send_mail', 'submit_to_robly', 10, 1 );
function submit_to_robly( $form ) {
    global $wpdb;

    // get API keys
    $options = get_option( 'cf7_robly_settings' );
    $robly_API_id = $options['cf7_robly_api_id'];
    $robly_API_key = $options['cf7_robly_api_key'];
    $API_base = 'https://api.robly.com/api/v1/';
    $API_credentials = '?api_id=' . $robly_API_id . '&api_key=' . $robly_API_key;

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
    $settings = cf7_robly_get_form_settings( $posted_data['_wpcf7'], NULL, true );

    // get Robly lists
    $hidden_fields_sublists = explode( ',', esc_attr( $posted_data['robly-lists'] ) );
    $cf7_form_settings_sublists = $settings['all-submissions'];
    $robly_sublists = array_unique( array_merge( $hidden_fields_sublists, $cf7_form_settings_sublists ) );

    // get array keys for form data
    if ( $settings['fields'] ) {
        $field_matches = array();
        foreach( $settings['fields'] as $id => $field ) {
            foreach ( $field as $this_field ) {
                $field_matches[$this_field] = urlencode( $id );
            }
        }
        $email_field = $field_matches['email'];
    }

    // check for email address
    if ( ! $settings['ignore-form'] && isset( $field_matches['email'] ) && $posted_data[$email_field] != NULL && $posted_data[$email_field] != '' ) {
        // search Robly for customer by email
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $API_base . 'contacts/search' . $API_credentials . '&email=' . $posted_data[$email_field] );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $curl_search = curl_exec( $ch );
        $curl_search_response = json_decode( $curl_search );

        // set API method for subsequent call
        if ( isset( $curl_search_response->member ) ) {
            // handle deleted/unsubscribed members
            if ( $curl_search_response->member->is_subscribed == false || $curl_search_response->member->is_deleted == true ) {
                curl_setopt( $ch, CURLOPT_URL, $API_base . 'contacts/resubscribe' . $API_credentials . '&email=' . $email );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

                // run the request and check to see if manual email is needed
                $resubscribe_curl_response = curl_exec( $ch );
                $json_response = json_decode( $resubscribe_curl_response );
                if ( $json_response->successful != true ) {
                    $send_email = true;
                    $error_message .= 'Resubscribe: ' . json_decode( $resubscribe_curl_response )->message;
                } else {
                    $send_email = false;
                }
            }
            // continue with updating contact info
            $API_method = 'contacts/update_full_contact';
        // handle new members
        } else {
            $API_method = 'sign_up/generate';
        }
        $post_url = $API_method . $API_credentials;

        // set up user data for the request
        $user_parameters = array();
        foreach( $field_matches as $key => $label ) {
            $user_parameters[$key] = $posted_data[$label];
        }
        $user_parameters = http_build_query( $user_parameters );

        // add sublist IDs
        $post_data = NULL;
        if ( $robly_sublists ) {
            foreach ( $robly_sublists as $this_list ) {
                $post_data .= 'sub_lists[]=' . $this_list . '&';
            }
        }
        $post_data = rtrim( $post_data, '&' );

        // set up the rest of the request
        curl_setopt( $ch, CURLOPT_URL, $API_base . $API_method . $API_credentials . '&' . $user_parameters );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        // run the request and check to see if manual email is needed
        $user_curl_response = curl_exec( $ch );
        $json_response = json_decode( $user_curl_response );
        if ( $json_response->successful != true || ( $json_response->successful == true && strpos( $json_response->message, 'already exists' ) !== false ) ) {
            $send_email = true;
            $error_message .= 'Update Contact: ' . json_decode( $user_curl_response )->message;
        } else {
            $send_email = false;
        }

        // close cUrl connection
        curl_close( $ch );

        // send notification email if necessary
        if ( $send_email ) {
            $email_sent = mail( $notification_email, 'Contact to manually add to Robly', "API failure\n\nAPI call:\n" . $API_base . $API_method . '?api_id=XXX&api_key=XXX&' . $user_parameters . "\nLists: " . $post_data . "\n\nDetails:\n" . $error_message . "\n\nSent by the Contact Form 7 to Robly plugin on " . home_url() );
        }
    }
}
