<?php

/**
 * Generated by the WordPress Meta Box generator
 * at http://jeremyhixon.com/tool/wordpress-meta-box-generator/
 */

function stocker_sponsor_metabox_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function stocker_sponsor_metabox_add_meta_box() {
	add_meta_box(
		'stocker_sponsor_metabox-stocker-sponsor-metabox',
		__( 'Sponsor Metabox', 'stocker_sponsor_metabox' ),
		'stocker_sponsor_metabox_html',
		'sponsor',
		'side',
		'core'
	);
}
add_action( 'add_meta_boxes', 'stocker_sponsor_metabox_add_meta_box' );

function stocker_sponsor_metabox_html( $post) {
	wp_nonce_field( '_stocker_sponsor_metabox_nonce', 'stocker_sponsor_metabox_nonce' ); ?>

	<p>This meta-box is for the Lorain County Community College Sponsors custom post type.</p>

	<p>
		<label for="stocker_sponsor_metabox_associated_link"><?php _e( 'Associated Link', 'stocker_sponsor_metabox' ); ?></label><br>
		<input type="text" name="stocker_sponsor_metabox_associated_link" id="stocker_sponsor_metabox_associated_link" value="<?php echo stocker_sponsor_metabox_get_meta( 'stocker_sponsor_metabox_associated_link' ); ?>">
	</p><?php
}

function stocker_sponsor_metabox_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['stocker_sponsor_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['stocker_sponsor_metabox_nonce'], '_stocker_sponsor_metabox_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['stocker_sponsor_metabox_associated_link'] ) )
		update_post_meta( $post_id, 'stocker_sponsor_metabox_associated_link', esc_attr( $_POST['stocker_sponsor_metabox_associated_link'] ) );
}
add_action( 'save_post', 'stocker_sponsor_metabox_save' );

/*
	Usage: stocker_sponsor_metabox_get_meta( 'stocker_sponsor_metabox_associated_link' )
*/

?>