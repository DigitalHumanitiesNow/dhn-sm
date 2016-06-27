<?php

/* CREATE SUBPAGE */

//NOTES
///////
// add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
//add_menu_page( 'User Management', 'DHNow User Management', 'manage_options', 'dhn-usermanagement', 'user_man_page' );
//add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
///////

function clivern_plugin_top_menu(){
	global $dhnarchivepage;
	$dhnarchivepage = add_submenu_page('dhn-usermanagement', 'User Archive', 'User Archive', 'manage_options','dhn-archive', 'dhn_archive_page');
}

function dhn_archive_page(){
	?>
	<div class="row">
		<div class="col-md-12">
	 		<h2>User Sign Up Archive</h2>
 		</div>
 	</div>
  <div class="row">
		 <div class="col-md-12">
			  <?php echo getarchivedusers(); ?>
			</div>
	</div>

	<script type="text/javascript" >
	jQuery(document).ready( function ($) {
    $('#archive_table').DataTable( {
			 "pageLength": 50,
			 "order": [[ 2, 'asc' ]],
		});
	} ); </script>

	<?php
}
add_action('admin_menu','clivern_plugin_top_menu');

/* FUNCTION TO GET THE START AND END DATE FOR EACH WEEK */

function getStartAndEndDate($year, $week)
{
   return [
      (new DateTime())->setISODate($year, $week)->format('Y-m-d'), //start date
      (new DateTime())->setISODate($year, $week, 7)->format('Y-m-d') //end date
   ];
}

/* FUCTION TO LOOP THROUGH AND GET TABLE OF USERS */
function getarchivedusers() {
  global $wpdb; // this is how you get access to the database
	$twitter_db_field = $options['dhnsm_twitter_handle_dbfield'];
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
					$userinfo = get_userdata($user->ID);
					$twitter = get_user_meta( $user->ID, $GLOBALS['twitter_db_field'], true);
					$user_name = $userinfo->display_name;
          $printdates = getStartAndEndDate(date("Y"), $current_week);
					$userlist .= '<tr><td>' . $userinfo->display_name . '</td><td>' . $userinfo->user_email . '</td><td>' . $twitter . '</td><td>'. $current_week . '</td><td>' . $printdates[0] . '</td><td>' . $printdates[1] . '</td></tr>';
				}


			} //end for each
      $current_week = $current_week + 1;
	} //end if
} //end while
	 //endif
	wp_reset_query();

  $returnstring = '<table class="table table-striped display" id="archive_table"><thead><th>Name</th><th>Email</th><th>Twitter</th><th>Week Number</th><th>Week Start Date</th><th>Week End Date</th></thead><tbody>' . $userlist . '</tbody></table>';
return $returnstring;
}
