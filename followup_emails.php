<?php

function followup_callback() {
     global $wpdb; // this is how you get access to the database

     $followup_action_trigger = $_GET['followup_action_trigger'];

     // $weeksinfo = $_GET['weeks_info'];

     if (isset($_GET['followup_action_trigger'])){
     	echo send_followup();
     	//echo output_week_info();
  		} //end if $_Get[whatever]

     if ($followup_action_trigger == 'true') {
     	$output = "success";

     } else {
     	$output = "<script>console.log('There was not an error calling the followup email function.');</script>";
     	 echo $output;
     }

     exit(); // this is required to return a proper result & exit is faster than die();
}


function send_followup() {
		global $wpdb;
    $options = get_option('dhnsm_settings');
    $db_followup_email_text= $options['dhnsm_followup_email_field'];
    $returnemail = $options['dhnsm_return_email_2'];
		// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhnow this value is pie_checkbox_6, imac test site 10, laptop 3.
		$args = array (
			'meta_query'     => array(
				array(
					'key'       => $GLOBALS['db_pie_field'],

				),
			),
		);

		$subj_pw = $options['dhnsm_subject_field_2'];

    $body_pw = $db_followup_email_text;
		//$body_pw = "Dear Editors-at-Large,\n\nWe would love to hear back from you about your experience as an Editor-at-Large.\n\nYou can find our feedback form at http://digitalhumanitiesnow.org/editors-corner/feedback/\nWe always welcome submissions from former Editors-at-Large using the Nominate This bookmarklet and your WordPress login. You can find instructions for using the bookmarklet here: https://github.com/PressForward/pressforward/wiki/User-Manual#installing-and-using-the-nominate-this-bookmarklet\n\nSincerely\n\nThe Editors.";

		//Get the current week number. TO DO: change this so that we can use same code to find all users for the week before and the week after.

		$current_week = date("W");
		$prev_week = date("W") - 1;
		$next_week = date("W") + 1;
    $current_year = date("Y");
		echo '<script>console.log(' . $previous_week . ');</script>';

		// Query users based on the above arguments
		$user_query = new WP_User_Query( $args );


		// Create an empty array to save emails to.

		global $userdetails;
		if ( ! empty($user_query->results)) {
			foreach ($user_query->results as $user) {
				$allmeta = get_user_meta( $user->ID );
				$checkbox = get_user_meta($user->ID, $GLOBALS['db_pie_field'], true);
					if (is_array($checkbox) && in_array($prev_week, $checkbox)){
						$userinfo = get_userdata($user->ID);
						$userdetails .= '<tr><td>' . $userinfo->user_login . '</td><td>' . $userinfo->user_email . '</td></tr>';
						$headers[] = 'Bcc:' . $userinfo->display_name . ' <' . $userinfo->user_email . '>';
						$emails_pw[] = $userinfo->user_email;
					}
			} //end foreach

			$headers[] .= 'From: Digital Humanities Now <' . $returnemail . '>';
			$to = 'dhnow@pressforward.org';
			wp_mail($to, $subj_pw, $body_pw, $headers);
		}//end if
		$weekstart = new DateTime();
		$weekstart->setISODate($current_year, $prev_week);
		if( empty( $emails_pw ) ) {
		echo '<div style="margin-top: 10px;" class="alert alert-danger" role="alert">The follow up emails for the week of ' . $weekstart->format('d-M-Y') . ' were not sent. There are no users registered.</div>';
		$failurelogmsg = '<li>' . date(DATE_RSS) . ' An attempt was made to send the follow up emails but there are no users signed up. Emails did not send.</li>';
		dhn_sm_log($failurelogmsg);
		} else {
		echo '<div style="margin-top: 10px;" class="alert alert-success" role="alert">The follow up emails for the week of ' . $weekstart->format('d-M-Y') . ' were sent successfully.</div>' .
		'<br>Emails were sent to: <br>
		<table class="table table-striped">' . $userdetails . '</table>';
		$logemails = implode(" ,", $emails_pw);
		$logmessage = '<li>' . date(DATE_RSS) . ' sent the follow up email to: ' .  $logemails . '</li>' . $db_followup_email_text;
		dhn_sm_log($logmessage);
		}
		//unset($userdetails);
		//unset($emails_nw);
	}


?>
