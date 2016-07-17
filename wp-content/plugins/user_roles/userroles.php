<?php
   /*
   Plugin Name: User Roles
   */

function add_roles_on_plugin_activation() {
	add_role( 'general_role', 'General', array( 'read' => true, 'level_0' => true ) );
       add_role( 'finance_role', 'Finance', array( 'read' => true, 'level_0' => true ) );
       add_role( 'design_role', 'Design', array( 'read' => true, 'level_0' => true ) );
       add_role( 'development_role', 'Development', array( 'read' => true, 'level_0' => true ) );
       add_role( 'client_management_role', 'Client Communication + Management', array( 'read' => true, 'level_0' => true ) );
}

function delete_roles_on_deactivation(){
	remove_role('custom_role');
	remove_role('basic_contributor');
}

register_activation_hook( __FILE__, 'add_roles_on_plugin_activation' );
register_deactivation_hook(__FILE__, 'delete_roles_on_deactivation');
?>