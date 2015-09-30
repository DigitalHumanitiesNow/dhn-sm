<?php
/*
Plugin Name: Test plugin
Description: A test plugin to demonstrate wordpress functionality
Author: Simon Lissack
Version: 0.1
*/
add_action('admin_menu', 'test_plugin_setup_menu');
 
function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}
 
function test_init(){
        echo "<h1>Hello World!</h1>";
        echo "<button id='test' type='button'>Click Me!</button>";
}


// Register Script
function custom_scripts() {

	wp_register_script( 'customjs', 'http://local.wordpress.dev/wp-content/plugins/dhn-sm/myscript.js', array( 'jquery' ), false, false );
	wp_enqueue_script( 'customjs' );

}
add_action( 'admin_enqueue_scripts', 'custom_scripts' );


function test_ajax_load_scripts() {
	// load our jquery file that sends the $.post request
	wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/ajax-test.js', array( 'jquery' ) );
 
	// make the ajaxurl var available to the above script
	wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
}
add_action('wp_print_scripts', 'test_ajax_load_scripts');






?>