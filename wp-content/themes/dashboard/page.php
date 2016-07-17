<!DOCTYPE html>
<html>
<head>
	<title>TNCreations | My Dashboard</title>
	<?php wp_head(); ?>
	
</head>
<body>
<div class="apps-page-wrapper">
<img src="http://www.dev.tncreations.ca/dashboard/wp-content/themes/dashboard/img/tncreations-logo.png" class="tnc-logo">
	<a href="<?php echo wp_logout_url('http://dev.tncreations.ca/dashboard'); ?>" class="btn btn-default pull-right">Logout</a>
	<?php
		$current_user = wp_get_current_user();
		echo "<h1>Hello, " . $current_user->display_name . "!</h1>";
		
		$roles = $current_user->roles;
		$flattened_roles = implode("','", $roles);
		global $wpdb;
		$sql = "SELECT DISTINCT kp_dashboard_apps.app_name, kp_dashboard_apps.app_logo_path, kp_dashboard_apps.app_link FROM kp_dashboard_apps INNER JOIN kp_dashboard_roles ON kp_dashboard_roles.app_id = kp_dashboard_apps.app_id WHERE kp_dashboard_roles.role_name IN ('" . $flattened_roles . "')";
		$app_details = $wpdb->get_results($sql);
		echo "<div class='apps-wrapper'>";

		foreach($app_details as $app_detail){
			echo "<div class='panel panel-default app-wrapper'>
				<figure class='panel-body'>
<a href='" . $app_detail->app_link . "' target='_blank'>
<div class='parent'>
<div class='child'>
					<img src='" . $app_detail->app_logo_path . "' alt=''>
					<figcaption>" . $app_detail->app_name . "</figcaption></div></div>

</a>
				</figure></div>";
		}
		echo "</div>";
	?>
	
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Debugging tool - User data array</a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      	<?php
        	echo "<pre>";
		print_r(var_dump($current_user));
		echo "</pre>";
	?>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Debugging tool - User Roles Array</a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
      <?php
      	$roles = $current_user->roles;
	foreach ($roles as $role) {
		echo $role . '<br>';
	}
      ?>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Debugging tool - User App Data</a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      <?php
      	echo "<pre>";
	print_r(var_dump($app_details));
	echo "</pre>";
      ?>
      </div>
    </div>
  </div>
</div>
</div>
<?php
wp_footer();
?>
</body>
</html>