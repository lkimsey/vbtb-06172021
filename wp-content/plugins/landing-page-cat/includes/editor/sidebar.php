<?php
	
function fca_lpc_add_marketing_metaboxes( $post ) {

	add_meta_box( 
		'fca_lpc_marketing_metabox',
		__( 'Upgrade to Premium', 'landing-page-cat' ),
		'fca_lpc_render_marketing_metabox',
		null,
		'side',
		'default'
	);

}
add_action( 'add_meta_boxes_landingpage', 'fca_lpc_add_marketing_metaboxes', 11, 1 );

function fca_lpc_render_marketing_metabox( $post ) {

	ob_start(); ?>

	<ul style='padding-left: 30px; text-indent: -24px;'>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Particle Effects', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Add a Countdown Timer', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Build Welcome Gates', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Build 404 Pages', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Google Analytics Integration', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Redirect To "Thank You" Page', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Priority Support', 'landing-page-cat' ); ?></li>
	</ul>
	<a class='button button-primary' href='https://fatcatapps.com/landingpagecat/' target='_blank' style="width: 100%; text-align: center;"><?php _e('Learn More', 'landing-page-cat')?></a>
	
	<?php 
		
	echo ob_get_clean();
}
