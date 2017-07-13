<?php
/*
Plugin Name: DHNow User Management
Description: Plugin to manage users in DHNow.
Author: Amanda Regan
Version: 0.1
*/

//Calls the function that creates a new menu page -- `user_management_menu_page`. That function then calls user_man_page which generates the content on the page.
add_action('admin_menu', 'user_management_menu_page');

function user_management_menu_page(){
       global $dhn_sm_page;
       $dhn_sm_page = add_menu_page( 'User Management', 'DHNow User Management', 'manage_options', 'dhn-usermanagement', 'user_man_page' );

}


function user_man_page() {
echo '<div class="container">
	<div class="row">
		<div class="col-md-12">
        <h1>User Management Dashboard</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-md-8 weeksetup"></div>
	</div>

	<div class="row">

		<div class="col-md-6">
			<h2>Instructional Emails</h2>
			<p>Each week instructional emails get sent to the editors-at-large for the following week.</p>

			<button class="instructions_button btn btn-default">Instructional Email</button>

			<div class="instructional_response"></div>
		</div>

		<div class="col-md-6 button-container">
			<h2>Follow-Up Emails</h2>
			<p>Each week a follow-up email gets sent to the editors-at-large for the previous week.</p>

			<button class="followup_button btn btn-default">Follow Up Email</button>
			<div class="followup_response"></div>
		</div>

	</div>


	<div class="row" style="margin-top: 30px;">

		<div class="log col-md-12">
			<button class="logbutton btn btn-primary">View Action History</button>
			<div class="actionhistory"></div>
		</div>


	</div>

</div>';
}



//Load up bootstrap for easy layout on admin page
function load_custom_wp_admin_style() {
global $dhn_sm_page;
global $dhnarchivepage;
	$screen = get_current_screen();
	if($screen->id == $dhn_sm_page | $screen->id == $dhnarchivepage) {

    wp_register_style( 'custom_wp_admin_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_css' );
    wp_register_script( 'datatables', 'https://cdn.datatables.net/1.10.12/js/jquery.dataTables.js', true );
    wp_enqueue_script('datatables');
    wp_register_style('datatables_css', 'https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css', false );
    wp_enqueue_style('datatables_css');



} }

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );



require_once(WP_PLUGIN_DIR . '/dhn-sm/functions.php');
require_once(WP_PLUGIN_DIR . '/dhn-sm/instructional_emails.php');
require_once(WP_PLUGIN_DIR . '/dhn-sm/followup_emails.php');
require_once(WP_PLUGIN_DIR . '/dhn-sm/userarchive.php');




//Ajax code adapted from codex and this question on WordPress Development
//http://wordpress.stackexchange.com/questions/24235/how-can-i-run-ajax-on-a-button-click-event

add_action('admin_head', 'instructions_action_javascript');

function instructions_action_javascript() {
	global $dhn_sm_page;
	$screen = get_current_screen();
	if($screen->id == $dhn_sm_page) {
	?>

	<script type="text/javascript" >
		jQuery(document).ready(function($) {

    		$('.instructions_button').click(function(){
        		var data = {
            		action: 'instructional_email',
            		instruction_action_trigger: true };
            	 $('.instructions_button').attr('disabled','disabled');

        	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.get(ajaxurl, data, function(response) {
        		console.log("instructional function executed");
            	$('.instructional_response').append(response)  }); //end .get
    		}); //end .instructions_button
		});
	</script>

<?php } } //end instructions_action_javascript

//The first part of this add_action, 'wp_ajax_instructional_email' calls the action defined in the data varaibale in instructions_action_javascript above. See https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)

add_action('wp_ajax_instructional_email', 'instructions_callback');

add_action('admin_head', 'followup_action_javascript');
function followup_action_javascript() {
	global $dhn_sm_page;
	$screen = get_current_screen();
	if($screen->id == $dhn_sm_page) {
	?>

	<script type="text/javascript" >
		jQuery(document).ready(function($) {

    		$('.followup_button').click(function(){
        		var data = {
            		action: 'followup_email',
            		followup_action_trigger: true };
            	 $('.followup_button').attr('disabled','disabled');

        	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.get(ajaxurl, data, function(response) {
        		console.log('followup function executed');
            	$('.followup_response').append(response)  }); //end .get
    		}); //end .instructions_button
		});
	</script>
<?php } }//end instructions_action_javascript

//The first part of this add_action, 'wp_ajax_instructional_email' calls the action defined in the data varaibale in instructions_action_javascript above. See https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)

add_action('wp_ajax_followup_email', 'followup_callback');







/******

Generate EL Info Functions

*/////

add_action('admin_footer', 'EL_Info_Generator');

function EL_Info_Generator() {
	global $dhn_sm_page;
	$screen = get_current_screen();
	if($screen->id == $dhn_sm_page) {
	?>
	<script type="text/javascript" >
		jQuery(document).ready(function($) {


        		var data = {
            		'action': 'EL_week_data',
            		'EL_data_trigger': true,
            	};

        	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.get(ajaxurl, data, function(response) {
        		//alert('test');
        		console.log('week data function loaded');
            	$('.weeksetup').append(response)  }); //end .get
              
    		}); //end .instructions_button


	</script>
<?php } } //end instructions_action_javascript

//The first part of this add_action, 'wp_ajax_instructional_email' calls the action defined in the data varaibale in instructions_action_javascript above. See https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)

add_action('wp_ajax_EL_week_data', 'EL_week_data_callback');
//need to append this to an ajax button


add_action('admin_footer', 'EL_Log_Generator');






?>
