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
 
 add_action('in_admin_header', 'my_ajax_button');

function my_ajax_button() {
    echo '<button class="instructions_button">Instructional Email</button>';
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

     if ($whatever == '1234') {

             echo 'the values match';
     } else {
     	echo 'error the values dont match';
     }

     exit(); // this is required to return a proper result & exit is faster than die();
}







?>
