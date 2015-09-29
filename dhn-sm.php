<?php
/**
 * Plugin Name: Digital Humanities Now Site Management
 * Plugin URI: http://danielpataki.com
 * Description: This plugin creates a site manager dashboard where users can be emailed and managed. 
 * Version: 0.1
 * Author: Amanda Regan
 * Author URI: http://amanda-regan.com
 * License: GPL2
 */

add_action('admin_menu', 'dhn_sm_admin_menu');

function dhn_sm_admin_menu() {

	add_menu_page( 'Site Management', 'Site Management', 'manage_options', 'dhn-sm/dhn-sm-admin.php', 'dhn_sm_admin_page', 'dashicons-welcome-learn-more', 6);
}


function dhn_sm_admin_page(){
	?>
	<div class="wrap">
		<h2>Welcome To My Plugin</h2>
	</div>
	<?php
}
?>