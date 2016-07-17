<!DOCTYPE html>
<html>
<head>
	<title>TNCreations | Dashboard Login</title>
	<?php wp_head(); ?>
</head>
<body>
<div class="wrapper">
<img src="http://www.dev.tncreations.ca/dashboard/wp-content/themes/dashboard/img/tncreations-logo.png" class="tnc-logo">

<?php 

$var = do_shortcode('[login_form]');
echo $var;

?>
<p class="beta-notice text-right">- Beta</p>
</div>
</body>
</html>