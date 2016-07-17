<?php
   /*
   Plugin Name: User Login Form
   */

// user login form
function pippin_login_form() {
 
	if(!is_user_logged_in()) {
 
		global $pippin_load_css;
 
		// set this to true so the CSS is loaded
		$pippin_load_css = true;
 
 		//$output = wp_login_form();
		$output = pippin_login_form_fields();
	} else {
		// could show some logged in user info here
		//$output = 'user info here';
		wp_redirect('http://dev.tncreations.ca/dashboard/my-dashboard'); exit;
	}
	return $output;
}
add_shortcode('login_form', 'pippin_login_form');

// login form fields
function pippin_login_form_fields() {
 
	ob_start(); ?>
		<h1 class="pippin_header text-center site-title"><?php _e('Employee Dashboard'); ?></h1>
 
		<?php
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_login_form"  class="pippin_form form-horizontal" method="post">
			<div class="form-group">
				<label for="pippin_user_Login" class="col-sm-3 control-label">Username</label>
				<div class="col-sm-9">
					<input name="pippin_user_login" class="form-control" id="pippin_user_login" class="required" type="text"/>
				</div>
			</div>
			<div class="form-group">
				<label for="pippin_user_pass" class="col-sm-3 control-label">Password</label>
				<div class="col-sm-9">
					<input name="pippin_user_pass" class="form-control" id="pippin_user_pass" class="required" type="password"/>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<input type="hidden" name="pippin_login_nonce" value="<?php echo wp_create_nonce('pippin-login-nonce'); ?>"/>
					<input id="pippin_login_submit" type="submit" class="btn btn-default" value="Login"/>
				</div>
			</div>
		</form>
	<?php
	return ob_get_clean();
}

// logs a member in after submitting a form
function pippin_login_member() {
 
	if(isset($_POST['pippin_user_login']) && wp_verify_nonce($_POST['pippin_login_nonce'], 'pippin-login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_user_by('login', $_POST['pippin_user_login']);
 
		if(!$user) {
			// if the user name doesn't exist
			pippin_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['pippin_user_pass']) || $_POST['pippin_user_pass'] == '') {
			// if no password was entered
			pippin_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['pippin_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			pippin_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = pippin_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
			$creds = array(
				'user_login'    => $_POST['pippin_user_login'],
        		'user_password' => $_POST['pippin_user_pass'],
        		'remember'      => true
			);
 
			$user = wp_signon($creds, true);
 
			wp_redirect('http://dev.tncreations.ca/dashboard/my-dashboard'); exit;
		}
	}
}
add_action('init', 'pippin_login_member');

// used for tracking error messages
function pippin_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function pippin_show_error_messages() {
	if($codes = pippin_errors()->get_error_codes()) {
		echo '<div class="pippin_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = pippin_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}

// register our form css
function pippin_register_css() {
	wp_register_style('pippin-form-css', plugin_dir_url( __FILE__ ) . '/css/forms.css');
}
add_action('init', 'pippin_register_css');

// load our form css
function pippin_print_css() {
	global $pippin_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $pippin_load_css )
		return; // this means that neither short code is present, so we get out of here
 
	wp_print_styles('pippin-form-css');
}
add_action('wp_footer', 'pippin_print_css');
