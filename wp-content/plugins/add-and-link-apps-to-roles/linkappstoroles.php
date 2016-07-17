<?php

/*
Plugin Name: Add and Link Apps to Roles
*/

add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_menu() {
	add_menu_page( 'Add and Link Apps to Roles', 'Add, Link Apps to Roles', 'manage_options', 'add-and-link-apps-to-roles/linkappstoroles.php', 'link_apps_to_roles_admin_page', 'dashicons-tickets-alt', 6  );
}

function addApp($appName, $appLogo, $appLink){

	global $wpdb;

		$wpdb->insert('kp_dashboard_apps', 
			array(
				'app_id' => null,
				'app_name' => $appName,
				'app_logo_path' => $appLogo,
				'app_link' => $appLink),
			array(
				'%d',
				'%s',
				'%s',
				'%s') 
		);

}

function linkAppWithRoles($app_id, $roles){

	foreach ($roles as $role) {
		global $wpdb;
		$wpdb->insert('kp_dashboard_roles', 
		array(
			'app_id' => $app_id,
			'role_name' => $role),
		array(
			'%d',
			'%s')
		);
	}
}

function updateAppInformation($appToUpdateID, $updatedAppName, $updatedAppLogo, $updatedAppLink){

	global $wpdb;

		$wpdb->update('kp_dashboard_apps', 
			array(
				'app_name' => $updatedAppName,
				'app_logo_path' => $updatedAppLogo,
				'app_link' => $updatedAppLink),
			array('app_id' => $appToUpdateID),
			array(
				'%s',
				'%s',
				'%s'),
			array('%d')
		);
}

function link_apps_to_roles_admin_page(){

	if(!empty($_POST['addApp'])){
		$appName = filter_var($_POST['app-name'], FILTER_SANITIZE_STRIPPED);
		$appLogo = filter_var($_POST['app-logo'], FILTER_SANITIZE_URL);
		$appLink = filter_var($_POST['app-link'], FILTER_SANITIZE_URL);
		addApp($appName, $appLogo, $appLink);
	}

	if(!empty($_POST['linkAppAndRoles'])){
		$app_id = filter_var($_POST['appSelection'], FILTER_SANITIZE_NUMBER_INT);
		$roles = $_POST['roles'];
		global $wpdb;
		$deleteSQL = "DELETE FROM kp_dashboard_roles WHERE app_id = %d";
		$wpdb->query(
			$wpdb->prepare($deleteSQL, $app_id)
		);
		linkAppWithRoles($app_id, $roles);
	}

	if(!empty($_POST['updateApp'])){
		$appToUpdateID = filter_var($_POST['updateAppSelection'], FILTER_SANITIZE_NUMBER_INT);
		$updatedAppName = filter_var($_POST['update-app-name'], FILTER_SANITIZE_STRIPPED);
		$updatedAppLogo = filter_var($_POST['update-app-logo'], FILTER_SANITIZE_URL);
		$updatedAppLink = filter_var($_POST['update-app-link'], FILTER_SANITIZE_URL);
		if(isset($_POST['delete-app'])){
			global $wpdb;
			$deleteAppFromAppsTable = "DELETE FROM kp_dashboard_apps WHERE app_id = %d";
			$wpdb->query(
				$wpdb->prepare($deleteAppFromAppsTable, $appToUpdateID)
			);
			$deleteAppFromRolesTable = "DELETE FROM kp_dashboard_roles WHERE app_id = %d";
			$wpdb->query(
				$wpdb->prepare($deleteAppFromRolesTable, $appToUpdateID)
			);
		} else {
			updateAppInformation($appToUpdateID, $updatedAppName, $updatedAppLogo, $updatedAppLink);
		}
	}
	
	?>
	<div class="wrap">
		<h2>Add Apps</h2>
		<form method="post">
			<label for="app-name">App Name
				<input type="text" name="app-name" id="app-name" required>
			</label>
			<label for="app-logo">App Logo
				<input type="text" name="app-logo" id="app-logo" required>
			</label>
			<label for="app-link">App Link
				<input type="text" name="app-link" id="app-link" required>
			</label>
			<input type="submit" name="addApp" value="Submit">
		</form>
	<hr>
	<h2>Update App Information</h2>
	<form method="post">
		<label for="UpdateAppSelection">Select App:
		<select name="updateAppSelection" id="updateAppSelection">
			<option value="0">-- Select an App --</option>
			<?php 
				global $wpdb;
				$appsToUpdate = $wpdb->get_results('SELECT * FROM kp_dashboard_apps', ARRAY_A);
				foreach($appsToUpdate as $appToUpdate){
					print '<option value="' . $appToUpdate['app_id'] . '">' . $appToUpdate['app_name'] . '</option>';
				}
			?>
		</select>
		</label>
		<fieldset>
		<label for="update-app-name">App Name
			<input type="text" name="update-app-name" id="update-app-name" required>
		</label>
		<label for="update-app-logo">App Logo
			<input type="text" name="update-app-logo" id="update-app-logo" required>
		</label>
		<label for="update-app-link">App Link
			<input type="text" name="update-app-link" id="update-app-link" required>
		</label>
		<label for="delete-app">Delete App
			<input type="checkbox" name="delete-app" id="delete-app">
		</label>
		</fieldset>
		<input type="submit" name="updateApp" value="Submit">
	</form>
	<hr>
	<h2>Apply Apps to Roles</h2>
		<form method="post">
			<label for="appSelection">Select App:
				<select name="appSelection" id="appSelection">
					<option value="0">-- Select an App --</option>
					<?php 
						global $wpdb;
						$apps = $wpdb->get_results('SELECT * FROM kp_dashboard_apps', ARRAY_A);
						foreach($apps as $app){
							print '<option value="' . $app['app_id'] . '">' . $app['app_name'] . '</option>';
						}
					?>
				</select>
				<?php 
					global $wp_roles;
					$roles = $wp_roles->get_names();
					$searchword = 'role';
					$customRoles = array();
					foreach ($roles as $roleName => $displayName) {
						if(preg_match("/([a-z]*_role)/", $roleName)) {
							$customRoles[$roleName] = $displayName;
						}
						
					}
					foreach ($customRoles as $roleName => $displayName) {
						print '<input id="' . htmlentities($roleName) . '" type="checkbox" name="roles[]" value="' . htmlentities($roleName) . '">' . $displayName;
					}
				?>
			</label>
			<input type="submit" name="linkAppAndRoles" value="Submit">
		</form>
	</div>
	<!-- script -->
	<script>
	jQuery(document).ready(function(){
		//jQuery to AJAX for app roles
		function update_app_selection(appID){
			jQuery.get("http://dev.tncreations.ca/dashboard/wp-json/kp_linkappstoroles/v1/app/" + appID, function(roles){
					for (var role in roles) {
						jQuery("input[id=" + roles[role]['role_name'] + "]").attr('checked', 'checked'); 
					}
			});
		}

		jQuery('#appSelection').change(function(){
    			var appID = jQuery(this).find('option:selected').val()
			jQuery('input').removeAttr('checked');
			if(appID > 0){ 
        			update_app_selection(appID);
    			}
		});
		
		//jQuery to AJAX for app info
		function show_current_app_info(appIDinfo){
			jQuery.get("http://dev.tncreations.ca/dashboard/wp-json/kp_linkappstoroles/v1/currentappinfo/" + appIDinfo, function(info){
			console.log(info);
					jQuery("input[id='update-app-name']").val(info[0].app_name);
					console.log(info[0].app_name);
					jQuery("input[id='update-app-logo']").val(info[0].app_logo_path);
					jQuery("input[id='update-app-link']").val(info[0].app_link);
			});
		}
		
		jQuery('#updateAppSelection').change(function(){
    			var appIDinfo = jQuery(this).find('option:selected').val()
			jQuery('fieldset > input[type=text]').val("");
			if(appIDinfo > 0){ 
        			show_current_app_info(appIDinfo);
    			}
		});
	});
	</script>

	<?php 
}

//AJAX to populate textfields with current values in Update App Information form
add_action( 'rest_api_init', function () {

	register_rest_route( 'kp_linkappstoroles/v1', '/currentappinfo/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'get_current_app_info',
	));
});

function get_current_app_info( $info ) {

	global $wpdb;
	$currentAppInfoSQL = "SELECT app_name, app_logo_path, app_link FROM kp_dashboard_apps WHERE app_id = '" . $info['id'] . "'"; 
	$currentAppInfo = $wpdb->get_results($currentAppInfoSQL);

	if ( empty( $currentAppInfo ) ) {
		return null;
	}

	return $currentAppInfo;
}

//AJAX to check which roles are currently assigned to an app
add_action( 'rest_api_init', function () {

	register_rest_route( 'kp_linkappstoroles/v1', '/app/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'get_selected_roles',
	));
});

function get_selected_roles( $data ) {

	global $wpdb;
	$sql = "SELECT role_name FROM kp_dashboard_roles WHERE app_id = '" . $data['id'] . "'"; 
	$selectedRoles = $wpdb->get_results($sql);

	if ( empty( $selectedRoles ) ) {
		return null;
	}

	return $selectedRoles;
}

?>