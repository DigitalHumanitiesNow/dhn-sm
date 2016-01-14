<?php



$options = get_option('dhnsm_settings'); 
$db_pie_field = $options['dhnsm_text_field_0'];

function dhn_sm_log($message = '', $reset = false) {
	$file = 'sm_log.txt';
	$file = WP_PLUGIN_DIR . "/dhn-sm/sm_log.txt";
	$current = file_get_contents($file);
	$current .= $message . PHP_EOL;
	file_put_contents($file, $current);
	//echo '<ul>' . file_get_contents($file) . '</ul>';
}


function tailFile($file, $lines = 1) {
		$logcontents = trim(implode("", array_slice(file($file), -$lines)));
		return $logcontents;
	}


function Log_callback() {
	$file = 'sm_log.txt';
	

	$file = WP_PLUGIN_DIR . "/dhn-sm/sm_log.txt";
	$data = file($file);
	$lines = implode("\r\n",array_slice($data,count($data)-25,25));


	$logcontents = '<ul>' . $lines . '</ul>';
	
	$EL_data_trigger = $_GET['EL_displaylog_trigger'];
     	if (isset($_GET['EL_displaylog_trigger'])){
     	echo $logcontents; 
     }
     	if ($EL_data_trigger == 'true') {
        //echo 'the values match';
     	} else {
     	echo 'error the values dont match'; }
    // check to see if log should reset
     	reset_log();
    exit(); 
}

//$testfile = WP_PLUGIN_DIR . "/dhn-sm/sm_log.txt";
//$testlines = count(file($testfile));
//echo '<script>console.log(' . $testlines . ');</script>';

function reset_log() {
	$current_week = date("W");
	if ($current_week == 16 || 33 || 53) {
		$file = WP_PLUGIN_DIR . "/dhn-sm/sm_log.txt";
		$cleared_message = '<li>' . date(DATE_RSS) . ' The log file has been automatically reset.</li>';
		file_put_contents($file, $cleared_message);
	}
}

function EL_week_data_callback() {
     global $wpdb; // this is how you get access to the database
   		$userlist = '';
     	// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhno this value is pie_checkbox_6
		$args = array ('meta_query'=> array(array('key'=>$GLOBALS['db_pie_field'],),),);
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
				$checkbox = get_user_meta($user->ID, $GLOBALS['db_pie_field'] , true);
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
    

		// $prev_count = 0;
		// $next_count = 0;
		// $current_count = 0;
    exit(); 


    // this is required to return a proper result & exit is faster than die();
} 



function EL_Log_Generator() { ?>
	<script type="text/javascript" >
		
		jQuery(document).ready(function($) {

    		$('.logbutton').click(function(){
    		
        		var data = {
            		'action': 'EL_log_data',
            		'EL_displaylog_trigger': true,
            	};
            	$('.logbutton').attr('disabled','disabled');
     
        	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.get(ajaxurl, data, function(response) {
        		//alert('test');
        		console.log('the actionhistory button has been clicked.');
            	$('.actionhistory').append('<h3>Action History</h3>')
            	$('.actionhistory').append(response);
            	  }); 
    		})
    		}); 
			
		
	</script>
<?php }

add_action('wp_ajax_EL_log_data', 'Log_callback');
//need to append this to an ajax button

/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */
//to get value do: 
//$options = get_option('dhnsm_settings'); 
//echo $options['dhnsm_text_field_0'];
add_action( 'admin_menu', 'dhnsm_add_admin_menu' );
add_action( 'admin_init', 'dhnsm_settings_init' );


function dhnsm_add_admin_menu(  ) { 

	add_submenu_page( 'dhn-usermanagement', 'DHNow User Management Plugin Settings', 'Settings', 'manage_options', 'dhn-sm', 'dhnsm_options_page' );

}


function dhnsm_settings_init(  ) { 

	register_setting( 'pluginPage', 'dhnsm_settings' );

	add_settings_section(
		'dhnsm_pluginPage_section', 
		__( '', 'wordpress' ), 
		'dhnsm_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'dhnsm_text_field_0', 
		__( 'Database Field Name', 'wordpress' ), 
		'dhnsm_text_field_0_render', 
		'pluginPage', 
		'dhnsm_pluginPage_section' 
	);


}


function dhnsm_text_field_0_render(  ) { 

	$options = get_option( 'dhnsm_settings' );
	?>
	<input type='text' name='dhnsm_settings[dhnsm_text_field_0]' value='<?php echo $options['dhnsm_text_field_0']; ?>'>
	<?php

}


function dhnsm_settings_section_callback(  ) { 

	echo __( '', 'wordpress' );

}


function dhnsm_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h1>Digital Humanities Now User Management</h1>
		<h2>Plugin Settings</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}


  














































?>