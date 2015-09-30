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
    echo '<button class="myajax">Test</button>';
}

add_action('admin_head', 'my_action_javascript');

function my_action_javascript() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {

    $('.myajax').click(function(){
        var data = {
            action: 'my_action',
            whatever: 1233
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

add_action('wp_ajax_my_action', 'my_action_callback');

function my_action_callback() {
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
