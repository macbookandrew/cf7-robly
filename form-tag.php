<?php // Robly Form Tag Add On


// Add Robly Opt In Tag 

add_action( 'wpcf7_init', 'custom_add_form_tag_robly' );
 
function custom_add_form_tag_robly() {
    wpcf7_add_form_tag( 'robly', 'custom_robly_form_tag_handler', true ); 
}
 
function custom_robly_form_tag_handler( $tag ) {
    new WPCF7_FormTag( $tag );
    
    if ( empty( $tag->name ) )
		return '';

	$atts = array();
	$atts['class'] = $tag->get_option( 'class' )['0'];
	$atts['id'] = $tag->get_option( 'id', 'id', true );
	$atts['type'] = $tag->type;
    $atts['message'] = ( empty ($tag->values[0] ) ) ? 'Sign me up for your mailing list' : $tag->values[0] ;
    $atts['checked'] = $tag->get_option('checked')['0'];
	$inputid = (!empty($atts['id'])) ? 'id="'.$atts['id'].'" ' : '';
	$inputid_for = ($inputid) ? 'for="'.$atts['id'].'" ' : '';

        $html = '<span id="Robly-form" class="wpcf7-form-control-wrap ' . $atts['type'] . '-wrap">';
        $html .= '<span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last">';
        $html .= '<label ' . $inputid_for . ' class="robly-optin-label">';
        $html .= '<input type="hidden" name="'.$atts['type'].'" value="0" />';
        $html .= '<input ' . $inputid . 'class="' . $atts['class'] . '"  type="checkbox" value="1" name="' . $atts['type'] . '"';
        $html .= ($atts['checked'] === 'true') ? 'checked' : '';
        $html .= '/><span class="wpcf7-list-item-label">' .$atts['message'] . '</label>';
        $html .= '</label></span></span></span>';
    
    return $html;
}

/*
* Add Robly Tag to CF7 Form Generator
*/

add_action( 'admin_init', 'init_tag_generator', 99 );
function init_tag_generator() {
		if ( ! class_exists( 'WPCF7_TagGenerator' ) ) {
			return;
		}
		$tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add( 'robly', __( 'Robly', 'cf7_robly' ), 'wpcf7_tg_pane_robly');
	}

function wpcf7_tg_pane_robly($contact_form, $args=''){
    
        // Get the Robly Lists
    
        $robly_sublists = maybe_unserialize( get_option( 'robly_sublists' ) );
        $sublists_options = '';

        // cache sublists if needed
        if ( ! $robly_sublists ) {
            cf7_robly_cache_robly_lists();
            $robly_sublists = maybe_unserialize( get_option( 'robly_sublists' ) );
            if ( ! $robly_sublists ) {
                echo '<h2>Please <a href="' . get_admin_url() . '/options-general.php?page=contact-form-7-robly">save your API credentials</a> first.</h2>';
            }
        }

        // get all Robly fields
        $robly_fields = maybe_unserialize( get_option( 'robly_fields' ) );
        $fields_options = NULL;

        // cache fields if needed
        if ( ! $robly_fields ) {
            cf7_robly_cache_robly_fields();
            $robly_fields = maybe_unserialize( get_option( 'robly_fields' ) );
        }

        $args = wp_parse_args( $args, array() );
		$description = __( 'This will add a conditional checkbox to the contact form to allow users to opt in or not to the selected Robly Mailing list from the Robly Settings Tab. %s', 'cf7_robly' );
        $desc_link = '<a href="#">Link Desc</a>';
		?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

				<table class="form-table"><tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'cf7_robly' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
							<em><?php echo esc_html( __( 'This is the name of the tag.  The number isn&rsquo;t important', 'cf7_robly' ) ); ?></em>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'ID (optional)', 'cf7_robly' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class (optional)', 'cf7_robly' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
						</td>
					</tr>
                    <tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Message (optional)', 'cf7_robly' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" />
						</td>
					</tr>
    				<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-checked' ); ?>"><?php echo esc_html( __( 'Make Checkbox Pre-Checked', 'cf7_robly' ) ); ?></label>
						</th>
						<td>
							<input type="checkbox" name="checked:true" id="<?php echo esc_attr( $args['content'] . '-checked' ); ?>" class="checkedvalue option" /><br />
							<em><?php echo __('If checked, This will make the opt-in pre-checked','cf7_robly'); ?></em>
						</td>
					</tr>
				</tbody></table>
			</fieldset>
		</div>

		<div class="insert-box" style="width: 99%">
			<input type="text" name="robly" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'cf7_robly' ) ); ?>" />
			</div>

			<br class="clear" />
		</div>
<?php } 