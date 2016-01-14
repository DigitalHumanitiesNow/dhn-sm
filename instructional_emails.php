<?php

function instructions_callback() {
     global $wpdb; // this is how you get access to the database

     $instruction_action_trigger = $_GET['instruction_action_trigger'];
     // $weeksinfo = $_GET['weeks_info'];

     if (isset($_GET['instruction_action_trigger'])){
     	echo send_instructions();
     	//echo output_week_info();
  		} //end if $_Get[whatever]

     if ($instruction_action_trigger == 'true') {
     	$output = "success";
     
     } else {
     	$output = "<script>console.log('There was not an error calling the instructional email function.');</script>";
     	 echo $output;
     }

     exit(); // this is required to return a proper result & exit is faster than die();
}


function send_instructions() {
		global $wpdb;

		// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhnow this value is pie_checkbox_6, imac test site 10, laptop 3.
		$args = array (
			'meta_query'     => array(
				array(
					'key'       => $GLOBALS['db_pie_field'],
					
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
				$checkbox = get_user_meta($user->ID, $GLOBALS['db_pie_field'], true);
					if (in_array($next_week, $checkbox)){
						$userinfo = get_userdata($user->ID);
						$userdetails .= '<tr><td>' . $userinfo->user_login . '</td><td>' . $userinfo->user_email . '</td></tr>';
						$headers[] = 'Bcc:' . $userinfo->display_name . ' <' . $userinfo->user_email . '>';
						$emails_nw[] = $userinfo->user_email;
					}
			} //end foreach
			$headers[] .= 'From: Digital Humanities Now <dhnow@pressforward.org>';
			$to = 'dhnow@pressforward.org';
			wp_mail($to, $subj_nw, $body_nw, $headers);
		}//end if
		$weekstart = new DateTime();
		$weekstart->setISODate(2015, $next_week);
		if( empty( $emails_nw ) ) {
		echo '<div style="margin-top: 10px;" class="alert alert-danger" role="alert">The instructional emails for the week of ' . $weekstart->format('d-M-Y') . ' were not sent successfully. There are no users registered.</div>';
		$failurelogmsg = '<li>' . date(DATE_RSS) . ' An attempt was made to send the instructional emails but there are no users signed up. Emails did not send.</li>';
		dhn_sm_log($failurelogmsg);
		} else {
		echo '<div style="margin-top: 10px;" class="alert alert-success" role="alert">The instructional emails for the week of ' . $weekstart->format('d-M-Y') . ' were sent successfully.</div>' .
		'<br>Emails were sent to: <br>
		<table class="table table-striped">' . $userdetails . '</table>';
		$logemails = implode(" ,", $emails_nw);
		$logmessage = '<li>' . date(DATE_RSS) . ' sent the instructional email to: ' .  $logemails . '</li>';
		dhn_sm_log($logmessage);
		}
		//unset($userdetails);
		//unset($emails_nw);
	}


?>