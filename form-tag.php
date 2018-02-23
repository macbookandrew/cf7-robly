<?php // Robly Form Tag Add On

// Add Robly Opt In Tag 

add_action( 'wpcf7_init', 'custom_add_form_tag_robly' );
 
function custom_add_form_tag_robly() {
    wpcf7_add_form_tag( 'robly', 'custom_robly_form_tag_handler' ); 
}
 
function custom_robly_form_tag_handler( $tag ) {
    
    $output = '<input type="checkbox" name="roblyOptIn" id="roblyOptIn"><label for="roblyOptIn"> Sign up for our mailing list</label>';
    
    return $output;
}

/*
* Add Robly Tag to CF7 Form Generator
*/

add_action( 'admin_init', 'init_tag_generator', 99 );
function init_tag_generator() {
		if ( ! class_exists( 'WPCF7_TagGenerator' ) ) {
			return;
		}
		WPCF7_TagGenerator::get_instance()->add( 'robly', __( 'Robly Opt In', 'robly' ), 'robly_tag_generator', array(
				'id'    => 'wpcf7-tg-pane-robly',
				'title' => __( 'Robly Opt In', 'robly' ),
		) );
	}

function robly_tag_generator($args){
   ?>

<div id="wpcf7-tg-pane-ctct">
		<form action="#">
			<h4><?php _e('This will add a conditional checkbox to the contact form to allow users to opt in or not to the selected Robly Mailing list from the Robly Settings Tab.', 'cf7_robly' ); ?></h4>
			<div>
				<input type="hidden" name="name" class="tg-name" value="" />
			</div>
		</form>
        <div class="insert-box" style="padding-left: 15px; padding-right: 15px;">
			<div class="tg-tag clear"><?php echo __( "Insert this tag into the Form. There should only be one of these tags per form.", 'cf7_robly' ); ?><br /><input type="text" name="robly" class="tag code" readonly="readonly" onfocus="this.select();" onmouseup="return false;" /></div>

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>
		</div>
    <?php 
}