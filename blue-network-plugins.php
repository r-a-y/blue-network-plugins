<?php
/*
Plugin Name: Blue Network Plugins!
Description: Miss how network plugins were separated from regular plugins like in Wordpress MU? Miss the ol' blue network plugins color as well? Check this out!
Author: r-a-y
Version: 1.0
Author URI: http://buddypress.org/community/members/r-a-y

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6U36PVEZB4BAE
*/

global $wp_version;

// let's stop this plugin from running if multisite isn't enabled and WP < 3.0
if( !is_multisite() && version_compare( $wp_version, '3.0', '<' ) )
	return false;

// that funky pale blue color you love from WPMU
// let's only add the CSS on the plugins page
add_action('admin_head-plugins.php', 'bnp_active_plugin_colors', 99);
function bnp_active_plugin_colors() {
?>
	<style type="text/css">	
	#network-plugins tr.active th, #network-plugins tr.active td {background-color:#EEF2FF !important;}
	#network-plugins .check-column input {display:none;}
	</style>
<?php	
}

// filter network plugins from entire plugins list
add_filter( 'all_plugins', 'ray_exclude_network_plugins' );
function ray_exclude_network_plugins() {
	$all_plugins = get_plugins();

	$network_plugins = array();
	foreach ( (array) $all_plugins as $plugin_file => $plugin_data) {
		// Filter into individual sections
		if ( is_multisite() && is_network_only_plugin( $plugin_file ) && !current_user_can( 'manage_network_plugins' ) ) {
			unset( $all_plugins[ $plugin_file ] );
			continue;
		} elseif ( is_plugin_active_for_network($plugin_file) ) {
			$network_plugins[ $plugin_file ] = $plugin_data;
		}
	}
	return array_diff_assoc($all_plugins, $network_plugins);
}

// add custom network plugins table before the regular plugins table
// todo: l10n?
add_action( 'pre_current_active_plugins', 'ray_blue_network_plugins' );
function ray_blue_network_plugins() {
	$all_plugins = get_plugins();

	$network_plugins = array();
	foreach ( (array) $all_plugins as $plugin_file => $plugin_data) {
		// Filter into individual sections
		if ( is_multisite() && is_network_only_plugin( $plugin_file ) && !current_user_can( 'manage_network_plugins' ) ) {
			unset( $all_plugins[ $plugin_file ] );
			continue;
		} elseif ( is_plugin_active_for_network($plugin_file) ) {
			$network_plugins[ $plugin_file ] = $plugin_data;
		}
	}
?>
	<h3>Currently Active Network Plugins</h3>
	<p>Plugins that appear in the list below are activated on all sites across this installation.</p>

	<div id="network-plugins">
		<?php print_plugins_table($network_plugins); ?>
	</div>

	<p>Plugins that are enabled on the entire network can only be disabled by a super administrator.</p>
<?php
}

?>