<?php

function fca_lpc_upgrade_menu() {
	$page_hook = add_submenu_page(
		'edit.php?post_type=landingpage',
		__('Upgrade to Premium', 'landing-page-cat'),
		__('Upgrade to Premium', 'landing-page-cat'),
		'manage_options',
		'landing-page-cat-upgrade',
		'fca_lpc_upgrade_ob_start'
	);
	add_action('load-' . $page_hook , 'fca_lpc_upgrade_page');
}
add_action( 'admin_menu', 'fca_lpc_upgrade_menu', 99 );

function fca_lpc_upgrade_ob_start() {
    ob_start();
}

function fca_lpc_upgrade_page() {
    wp_redirect('https://fatcatapps.com/landingpagecat/', 301);
    exit();
}

function fca_lpc_upgrade_to_premium_menu_js() {
    ?>
    <script type="text/javascript">
    	jQuery(document).ready(function ($) {
            $('a[href="edit.php?post_type=landingpage&page=landing-page-cat-upgrade"]').on('click', function () {
        		$(this).attr('target', '_blank')
            })
        })
    </script>
    <style>
        a[href="edit.php?post_type=landingpage&page=landing-page-cat-upgrade"] {
            color: #6bbc5b !important;
        }
        a[href="edit.php?post_type=landingpage&page=landing-page-cat-upgrade"]:hover {
            color: #7ad368 !important;
        }
    </style>
    <?php 
}
add_action( 'admin_footer', 'fca_lpc_upgrade_to_premium_menu_js');
