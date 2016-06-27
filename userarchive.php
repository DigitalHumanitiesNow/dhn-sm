<?php

function getStartAndEndDate($year, $week)
{
   return [
      (new DateTime())->setISODate($year, $week)->format('Y-m-d'), //start date
      (new DateTime())->setISODate($year, $week, 7)->format('Y-m-d') //end date
   ];
}

  global $wpdb; // this is how you get access to the database
  $userlist = '';
     	// WP_User_Query arguments. Search the database for the values from the pie checkbox.
		//dhno this value is pie_checkbox_6
		$args = array ('meta_query'=> array(array('key'=>$GLOBALS['db_pie_field'],),),);

  	//Get the current week number. TO DO: change this so that we can use same code to find all users for the week before and the week after.
		$current_week = 1;
		$prev_week = date("W") - 1;
		$next_week = date("W") + 1;

  	// Query users based on the above arguments
		$user_query = new WP_User_Query( $args );

  	// Create an empty array to save emails to.
		// The User Loop
		while ($current_week <= 54) {
		if ( ! empty( $user_query->results ) ) {
		$username;
			foreach ( $user_query->results as $user ) {
				//echo '<p>found a user</p><br>';
				$allmeta = get_user_meta($user->ID);
				$checkbox = get_user_meta($user->ID, $GLOBALS['db_pie_field'] , true);

        if (is_array($checkbox) && in_array($current_week, $checkbox)) {
					$current_count = $current_count + 1;
					$userinfo = get_userdata($user->ID);
					$twitter = get_user_meta( $user->ID, $GLOBALS['twitter_db_field'], true);
					$user_name = $userinfo->display_name;
          $printdates = getStartAndEndDate(date("Y"), $current_week);
					$userlist .= '<tr><td>' . $userinfo->display_name . '</td><td>' . $userinfo->user_email . '</td><td>'. $current_week . '</td><td>' . $printdates[1] . '</td><td>' . $printdates[2] . '</td></tr>';
				}


			} //end for each
      $current_week = $current_week + 1;
	} //end if
} //end while
	 //endif
	wp_reset_query();

  $returnstring = '<h2>Editor-at-Large Info</h2>
		<table class="table table-striped" style="width: 60%;"><th>Name</th><th>Email</th><th>week number</th><th>week start date</th><th>week end date</th>' . $userlist . '</table>';
echo $returnstring
