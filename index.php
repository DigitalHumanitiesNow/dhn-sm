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
}
 
 

//Ajax code adapted from codex and this question on WordPress Development 
//http://wordpress.stackexchange.com/questions/24235/how-can-i-run-ajax-on-a-button-click-event

add_action('admin_head', 'instructions_action_javascript');

function instructions_action_javascript() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {

    $('.instructions_button').click(function(){
        var data = {
            action: 'instructional_email',
            whatever: 1234
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.get(ajaxurl, data, function(response) {
            alert('Got this from the server: ' + response);
        });
    });


});
</script>
<?php
}

//The first part of this add_action, 'wp_ajax_instructional_email' calls the action defined in the data varaibale in instructions_action_javascript above. See https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)

add_action('wp_ajax_instructional_email', 'instructions_callback');

function instructions_callback() {
     global $wpdb; // this is how you get access to the database

     $whatever = $_GET['whatever'];

     if (isset($_GET['whatever'])){
     	echo get_weeks();
  		} //end if $_Get[whatever]

     if ($whatever == '1234') {

             echo 'the values match';
     } else {
     	echo 'error the values dont match';
     }

     exit(); // this is required to return a proper result & exit is faster than die();
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
		$prev_week_string = 'the previous week is: ' . $prev_week;

		// Query users based on the above arguments
		$user_query = new WP_User_Query( $args );


		// Create an empty array to save emails to.
		$emails = array();



// The User Loop
if ( ! empty( $user_query->results ) ) {
	
	foreach ( $user_query->results as $user ) {
		echo '<p>found a user</p><br>';
		$allmeta = get_user_meta($user->id);
		$checkbox = '<strong>user checkbox data:</strong> ' . get_user_meta($user->id, 'pie_checkbox_10', true) . '<br>';
		//return(get_user_meta($user->id, 'last_name'));
	} //end for each

	} else { 
		echo 'didnt finda  user';
	}
	
} //end get weeks





?>
