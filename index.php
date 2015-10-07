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
	echo '<div class="container">
	<div class="col-md-12">
	<h1>Admin Page Test</h1>
	</div>
	<div class="col-md-8 weeksetup"></div>
	
	</div>';
	echo '<div class="col-md-6">
	<h2>Instructional Emails</h2>
	<p>Each week instructional emails get sent to the editors-at-large for the following week.</p>
	<button class="instructions_button btn btn-default">Instructional Email</button>
	<div class="instructional_response"></div>
	</div>
	<div class="col-md-6 button-container">
	<h2>Follow-Up Emails</h2>
	<p>Each week a follow-up email gets sent to the editors-at-large for the previous week.</p>
		<button class="btn btn-default">Follow Up Email</button>
		</div>';
}
 
//Load up bootstrap for easy layout on admin page
function load_custom_wp_admin_style() {

        // wp_register_style( 'custom_wp_admin_css', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', false, '1.0.0' );
wp_register_style( 'custom_wp_admin_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );


//log function. This function accepts two arguments: 1. message: the text to copy into the log. 2. a reset variable that if set to true will wipe the log. This currently doesn't do anything but eventually we'll want to have a reset log button in the dashboard. 
function dhn_sm_log($message = '', $reset = false) {
	$file = 'sm_log.txt';
	$file = WP_PLUGIN_DIR . "/dhn-sm/sm_log.txt";
	$current = file_get_contents($file);
	echo $current;
	$current .= $message;
	file_put_contents($file, $current);

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
            	$('.instructional_response').append(response)  }); //end .get
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
     	//echo output_week_info();
  		} //end if $_Get[whatever]

     if ($instruction_action_trigger == 'true') {
     	$output = "<script>console.log('There was not an error calling the instructional email function.');</script>";
     	 echo $output;
     
     } else {
     	 $output = "<script>console.log('There was an error');</script>";
     	 echo $output;
     }

     exit(); // this is required to return a proper result & exit is faster than die();
}
// function output_week_info() {
// 	$current_week = date("W");
// 	$current_year = date("Y");
// 	$date = new DateTime();
// 	$date->setISODate($current_year,$current_week);
// 	$EL_Start_date = $date->format('d-M-Y');
// 	return $EL_Start_date;
// }

function get_weeks() {
		global $wpdb;

		// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhnow this value is pie_checkbox_6, imac test site 10, laptop 3.
		$args = array (
			'meta_query'     => array(
				array(
					'key'       => 'pie_checkbox_10',
					
				),
			),
		);

		$subj_nw = "Editor-at-Large Instructions";
		
		$body_nw = "Dear Editors-at-Large,
		Thank you for volunteering to help Digital Humanities Now. You have signed up to be an Editor-at-Large next week, from Saturday through Friday. You may review additional material, but please make sure to cover these particular days.
		You should have already received an email from our WordPress installation with login information for digitalhumanitiesnow.org. If you don't see it, please check your spam filter first, and then email us if you need your credentials sent again.
		Detailed instructions for nominating content can be found at http://digitalhumanitiesnow.org/editors-corner/instructions/.
		Please email us at dhnow@pressforward.org with any questions or concerns during this process.
		Sincerely,
		The Editors.";

		//Get the current week number. TO DO: change this so that we can use same code to find all users for the week before and the week after. 

		$current_week = date("W");
		$prev_week = date("W") - 1;
		$next_week = date("W") + 1;
		

		// Query users based on the above arguments
		$user_query = new WP_User_Query( $args );


		// Create an empty array to save emails to.

		global $userdetails;
		if ( ! empty($user_query->results)) {
			foreach ($user_query->results as $user) {
				$allmeta = get_user_meta( $user->ID );
				$checkbox = get_user_meta($user->ID, 'pie_checkbox_10', true);
					if (in_array($next_week, $checkbox)){
						$userinfo = get_userdata($user->ID);
						$userdetails .= '<tr><td>' . $userinfo->user_login . '</td><td>' . $userinfo->user_email . '</td></tr>';
						$emails_nw[] = $userinfo->user_email;
					}
			} //end foreach
			wp_mail( $emails_nw, $subj_nw, $body_nw);
		}//end if

		echo '<br>Emails were sent to: <br>
		<table class="table table-striped">' . $userdetails . '</table>';
	
		//unset($userdetails);
		//unset($emails_nw);
	}


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
   		$userlist = '';
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
				$allmeta = get_user_meta($user->ID);
				$checkbox = get_user_meta($user->ID, 'pie_checkbox_10', true);
				if (in_array($prev_week, $checkbox)) {
					$prev_count = $prev_count + 1;
				} if (in_array($next_week, $checkbox)) {
					$next_count = $next_count + 1;
				} if (in_array($current_week, $checkbox)) {
					$current_count = $current_count + 1;
					$userinfo = get_userdata($user->ID);
					$user_name = $userinfo->user_login;
					$userlist .= '<tr><td>' . $userinfo->user_login . '</td><td>' . $userinfo->user_email . '</td></tr>';
				}
			
			}
				//return(get_user_meta($user->ID, 'last_name'));
	} //end for each
	 //endif
	wp_reset_query();
	$returnstring = '<h2>Editor-at-Large Info</h2>
	This week there are ' . $current_count . ' editor(s) signed up. Last week we had '. $prev_count . ' editor(s) signed up. Currently, there are ' . $next_count . ' editor(s) signed up for next week. See the table below for a list of current editor-at-large names and emails.

		<table class="table table-striped" style="width: 60%;"><th>Name</th><th>Email</th>' . $userlist . '</table>

	'; 

    $EL_data_trigger = $_GET['EL_data_trigger'];
     	if (isset($_GET['EL_data_trigger'])){
     	echo $returnstring; 
     }
     	if ($EL_data_trigger == 'true') {
        //echo 'the values match';
     	} else {
     	echo 'error the values dont match'; }
    
    dhn_sm_log('testings', false);

		$prev_count = 0;
		$next_count = 0;
		$current_count = 0;
    exit(); // this is required to return a proper result & exit is faster than die();
} 



?>
