<?php

//allow redirection, even if my theme starts to send output to the browser
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 10);
function my_jquery_enqueue() {
   wp_deregister_script('jquery');
   wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js", false, null);
   wp_enqueue_script('jquery');
}

if(!function_exists('load_styles_and_scripts')){
	function load_styles_and_scripts(){
		wp_enqueue_style('bootstrap.min.css', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
		wp_enqueue_style('bootstrap-theme.min.css', get_stylesheet_directory_uri() . '/css/bootstrap-theme.min.css');
		//wp_enqueue_script('jQueryv3.0.0.js', get_stylesheet_directory_uri() . '/js/jQueryv3.0.0.js');
		wp_enqueue_style('style.css', get_stylesheet_directory_uri() . '/style.css');
		wp_enqueue_script('bootstrap.min.js', get_stylesheet_directory_uri() . '/js/bootstrap.min.js');
	}
}

add_action('wp_enqueue_scripts', 'load_styles_and_scripts');

add_action( 'admin_bar_menu', function( \WP_Admin_Bar $bar )
{
    $bar->add_menu( array(
        'id'     => 'manage_apps',
        'title'  => '<span class="ab-icon"></span>'.__( 'Manage Apps' ),
        'href'   => 'http://dev.tncreations.ca/dashboard/wp-admin/admin.php?page=add-and-link-apps-to-roles%2Flinkappstoroles.php',
        'meta'   => array(
            'target'   => '_self'
        ),
    ) );
}, 210 ); // <-- THIS INTEGER DECIDES WHERE THE ITEM GETS ADDED (Low = left, High = right)



?>