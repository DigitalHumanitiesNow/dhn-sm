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
        add_menu_page( 'User Management', 'DHNow User Management', 'manage_options', 'custompage', 'user_man_page' );
}

function user_man_page() {
	echo '<h1>Admin Page Test</h1>
		<div class="button-container">
		<button class="instructions_button">Instructional Email</button>
		</div>';
	echo '<div class="alerts"></div>';
	echo '<div class="weeksetup"></div>';
}
 
 

//Ajax code adapted from codex and this question on WordPress Development 
//http://wordpress.stackexchange.com/questions/24235/how-can-i-run-ajax-on-a-button-click-event

add_action('admin_head', 'instructions_action_javascript');

function instructions_action_javascript() { ?>
	<script type="text/javascript" >
		jQuery(document).ready(function($) {

    		$('.instructions_button').click(function(){
        		var data = {
            		action: 'instructional_email',
            		instruction_action_trigger: true };
     
        	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.get(ajaxurl, data, function(response) {
            	$('.alerts').append("I just appended this content.<br>" + response)  }); //end .get
    		}); //end .instructions_button
		}); 
	</script>
<?php } //end instructions_action_javascript

//The first part of this add_action, 'wp_ajax_instructional_email' calls the action defined in the data varaibale in instructions_action_javascript above. See https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)

add_action('wp_ajax_instructional_email', 'instructions_callback');

function instructions_callback() {
     global $wpdb; // this is how you get access to the database

     $instruction_action_trigger = $_GET['instruction_action_trigger'];
     // $weeksinfo = $_GET['weeks_info'];

     if (isset($_GET['instruction_action_trigger'])){
     	echo get_weeks();
     	echo output_week_info();
  		} //end if $_Get[whatever]

     if ($instruction_action_trigger == 'true') {

             echo 'the values match';
     } else {
     	echo 'error the values dont match';
     }

     exit(); // this is required to return a proper result & exit is faster than die();
}
function output_week_info() {
	$current_week = date("W");
	$current_year = date("Y");
	$date = new DateTime();
	$date->setISODate($current_year,$current_week);
	$EL_Start_date = $date->format('d-M-Y');
	return $EL_Start_date;
}

function get_weeks() {
		global $wpdb;

		// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhno this value is pie_checkbox_6
		$args = array (
			'meta_query'     => array(
				array(
					'key'       => 'pie_checkbox_10',
					
				),
			),
		);


		//Get the current week number. TO DO: change this so that we can use same code to find all users for the week before and the week after. 

		$current_week = date("W");
		$prev_week = date("W") - 1;
		$next_week = date("W") + 1;
		

		// Query users based on the above arguments
		$user_query = new WP_User_Query( $args );


		// Create an empty array to save emails to.
		$emails = array();



// The User Loop
if ( ! empty( $user_query->results ) ) {
	
	foreach ( $user_query->results as $user ) {
		echo '<p>found a user</p><br>';
		global $usercount;
		$usercount = $usercount + 1;
		$allmeta = get_user_meta($user->id);
		$checkbox = '<strong>user checkbox data:</strong> ' . get_user_meta($user->id, 'pie_checkbox_10', true) . '<br>';
		//return(get_user_meta($user->id, 'last_name'));
	} //end for each

	} else { 
		echo 'didnt finda  user';
	}
	
} //end get weeks


/******

Generate EL Info Functions

*/////

add_action('admin_footer', 'EL_Info_Generator');

function EL_Info_Generator() { ?>
	<script type="text/javascript" >
		jQuery(document).ready(function($) {

    		
        		var data = {
            		'action': 'EL_week_data',
            		'EL_data_trigger': true
            	};
     
        	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.get(ajaxurl, data, function(response) {
        		//alert('test');
        		console.log('test');
            	$('.weeksetup').append(response)  }); //end .get
    		}); //end .instructions_button
		
	</script>
<?php } //end instructions_action_javascript

//The first part of this add_action, 'wp_ajax_instructional_email' calls the action defined in the data varaibale in instructions_action_javascript above. See https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)

add_action('wp_ajax_EL_week_data', 'EL_week_data_callback');

function EL_week_data_callback() {
     global $wpdb; // this is how you get access to the database

     

     	global $wpdb;
     	// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhno this value is pie_checkbox_6
		$args = array ('meta_query'=> array(array('key'=>'pie_checkbox_10',),),);
		//Get the current week number. TO DO: change this so that we can use same code to find all users for the week before and the week after. 
		$current_week = date("W");
		$prev_week = date("W") - 1;
		$next_week = date("W") + 1;
		// Query users based on the above arguments
		$user_query = new WP_User_Query( $args );
		// Create an empty array to save emails to.
		// The User Loop
		$prev_count = 0;
		$next_count = 0;
		$current_count = 0;
		if ( ! empty( $user_query->results ) ) {
		$username;
			foreach ( $user_query->results as $user ) {
				//echo '<p>found a user</p><br>';
				$allmeta = get_user_meta($user->id);
				$checkbox = get_user_meta($user->id, 'pie_checkbox_10', true);
				if (in_array($prev_week, $checkbox)) {
					$prev_count = $prev_count + 1;
				} elseif (in_array($next_week, $checkbox)) {
					$next_count = $next_count + 1;
				} elseif (in_array($current_week, $checkbox)) {
					$current_count = $current_count + 1;
					$userinfo = get_userdata($user->id);
					$user_name = $userinfo->user_login;
					$userlist .= '<tr><td>' . $userinfo->user_login . '</td><td>' . $userinfo->user_email . '</td></tr>';
				}
			
			}
				//return(get_user_meta($user->id, 'last_name'));
	} //end for each
	 //endif
	
	$returnstring = '<h2>Editor-at-Large Info</h2>
	This week there are ' . $current_count . ' editor(s) signed up. Last week we had '. $prev_count . ' editor(s) signed up. Currently, there are ' . $next_count . ' editor(s) signed up for next week. See the table below for a list of current editor-at-large names and emails.

		<table><th>Name</th><th>Email</th>' . $userlist . '</table>



	'; 
    $EL_data_trigger = $_GET['EL_data_trigger'];
     	if (isset($_GET['EL_data_trigger'])){
     	echo $returnstring; 
     }
     	if ($EL_data_trigger == 'true') {
        //echo 'the values match';
     	} else {
     	echo 'error the values dont match'; }
    exit(); // this is required to return a proper result & exit is faster than die();
} 



?>
