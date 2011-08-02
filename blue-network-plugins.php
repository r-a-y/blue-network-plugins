<?php
/*
Plugin Name: Blue Network Plugins!
Description: Separates active network plugins from inactive ones on the Wordpress network plugins page. Requires Wordpress Multisite.
Author: r-a-y
Version: 1.1
Network: true
Author URI: http://buddypress.org/community/members/r-a-y

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6U36PVEZB4BAE
*/

/**
 * Blue Network Plugins
 *
 * @package BNP
 * @subpackage Core
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wp_version;

// let's stop this plugin from running if multisite isn't enabled and WP < 3.0
if( !is_multisite() && version_compare( $wp_version, '3.0', '<' ) )
	return false;

/**
 * Class: Blue Network Plugins
 *
 * @package BNP
 * @subpackage Classes
 * @since 1.1
 */
class Blue_Network_Plugins {

	/**
	 * Initializes the class when called upon.
	 * Yeah, I know it's named funny. Why?  See line 177 ;)
	 */
	function blue() {

		/* filters **************************************************************/

		// filter network plugins from entire plugins list
		add_filter( 'all_plugins', 			array( &$this, 'exclude_network_plugins' ) );

		// add extra action links for our plugin
		add_filter( 'network_admin_plugin_action_links',array( &$this, 'add_plugin_action_links' ), 10, 2 );

		/* actions **************************************************************/

		// localization
		add_action( 'admin_init', 			array( &$this, 'translate' ) );

		// that funky pale blue color you love from WPMU
		// let's only add the CSS on the plugins page
		add_action( 'admin_head-plugins.php',		array( &$this, 'css' ), 99 );

		// add custom network plugins table before the regular plugins table
		add_action( 'pre_current_active_plugins', 	array( &$this, 'display' ) );
	}

	/**
	 * Filter network plugins from entire plugins list
	 */
	function exclude_network_plugins() {
		$all_plugins = get_plugins();

		$network_plugins = ray_get_network_plugins_only();
		return array_diff_assoc( $all_plugins, $network_plugins );
	}

	/**
	 * CSS on the plugins page
	 */
	function css() {
	?>
		<style type="text/css">
		#network-plugins tr.active th, #network-plugins tr.active td {background-color:#EEF2FF !important;}
		#network-plugins .check-column input, h2 .subtitle {display:none;}
		p.search-box {margin-top:5px;}
		</style>
	<?php
	}

	/**
	 * Display our modified plugins table above the regular plugin table.
	 * Accomodates both WP 3.0 and 3.1+.
	 *
	 * @uses Network_Plugins_List_Table
	 */
	function display() {
		global $wp_version, $s;

		if ( current_user_can( 'manage_network_plugins' ) ) :

			echo '<h3>' . __( 'Currently Active Network Plugins', 'bnp' ) . '</h3>';

			echo '<div id="network-plugins">';

			// WP 3.1+
			if ( version_compare( $wp_version, '3.1', '>=' ) ) :

				$screen = get_current_screen();

				// display this on the network admin plugins page
				if ( $screen->is_network ) :
					echo '<p>' . __( 'Plugins that appear in the list below are activated on all sites across this installation.', 'bnp' ) .'</p>';

					// WP 3.1 introduces a new class to output plugins
					// Network_Plugins_List_Table extends WP_Plugins_List_Table
					$wp_list_table = new Network_Plugins_List_Table;
					$wp_list_table->prepare_items();
					$wp_list_table->display();

				// display this on the site admin plugins page
				else :
					echo '<p>' . sprintf( __( 'You can <a href="%s">manage your network plugins here</a>.', 'bnp' ), network_admin_url( 'plugins.php' ) ) . '</p>';
					echo '<h3>' . sprintf( __( 'Plugins on %s', 'bnp' ), get_bloginfo( 'name' ) ) . '</h3>';
				endif;

			// WP 3.0 branch
			// print_plugins_table() doesn't exist in WP 3.1+
			else :
				echo '<p>' . __( 'Plugins that appear in the list below are activated on all sites across this installation.', 'bnp' ) .'</p>';
				print_plugins_table( ray_get_network_plugins_only() );
				echo '<p>' . __( 'Plugins that are enabled on the entire network can only be disabled by a super administrator.', 'bnp' ) .'</p>';
			endif;

			echo '</div>';

			// if a search is being made, let's reposition the search heading here
			if ( $s )
				printf( '<h4>' . __( 'Search results for &#8220;%s&#8221;', 'bnp' ) . '</h3>', esc_html( $s ) );

		endif;
	}

	/**
	 * This method works even if we shove the plugin in /mu-plugins/.
	 */
	function translate() {
		$locale = get_locale();

		$mofile = dirname(__FILE__) . "/lang/bnp-$locale.mo";

		if ( file_exists( $mofile ) )
			load_textdomain( 'bnp', $mofile );
	}

	/**
	 * Add extra links for BNP on the WP Admin plugins page
	 *
	 * @param array $links Plugin action links
	 * @param string $file A plugin's loader base filename
	 */
	function add_plugin_action_links( $links, $file ) {

		// Do not do anything for other plugins
		if ( strrpos( 'blue-network-plugins.php', $file ) === false )
			return $links;

		// Donate link
		$donate = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6U36PVEZB4BAE" target="_blank" title="So you\'re using this plugin, help support this plugin by donating any amount you wish. Thanks for reading! :)" style="font-weight:bold; color:#D54E21">Donate!</a>';

		array_unshift( $links, $donate );

		return $links;
	}
}

// I'm blue, dah bah dee, dah bah dah!
$eiffel_65 = new Blue_Network_Plugins;
$eiffel_65->blue();


/* FUNCTIONS *************************************************************/

/**
 * Helper function to get network plugins only
 *
 * @since 1.1
 */
function ray_get_network_plugins_only() {
	$all_plugins = get_plugins();

	$network_plugins = array();
	foreach ( (array) $all_plugins as $plugin_file => $plugin_data) {
		if ( is_multisite() && is_network_only_plugin( $plugin_file ) && !current_user_can( 'manage_network_plugins' ) ) {
			unset( $all_plugins[ $plugin_file ] );
			continue;
		} elseif ( is_plugin_active_for_network($plugin_file) ) {
			$network_plugins[ $plugin_file ] = $plugin_data;
		}
	}

	return apply_filters( 'network_plugins_only', $network_plugins );
}


/* CLASSES **************************************************************/

// WP 3.1+ support
if ( version_compare( $wp_version, '3.1', '>=' ) ) :

require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-plugins-list-table.php' );

/**
 * Class: Network_Plugins_List_Table
 *
 * Extends and manipulates {@link WP_Plugins_List_Table class} to
 * display only network plugins.
 *
 * @package BNP
 * @subpackage Classes
 * @see Blue_Network_Plugins::display()
 * @since 1.1
 */
class Network_Plugins_List_Table extends WP_Plugins_List_Table {

	/**
	 * Overrides {@link WP_Plugins_List_Table::prepare_items()}
	 */
	function prepare_items() {
		global $page, $orderby, $order;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		$plugins = array(
			'all' => ray_get_network_plugins_only(),
			'search' => array(),
			'active' => array(),
			'inactive' => array(),
			'recently_activated' => array(),
			'upgrade' => array(),
			'mustuse' => array(),
			'dropins' => array()
		);

		$screen = get_current_screen();

		if ( ! is_multisite() || ( $screen->is_network && current_user_can('manage_network_plugins') ) ) {
			$current = get_site_transient( 'update_plugins' );
			foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
				if ( isset( $current->response[ $plugin_file ] ) )
					$plugins['upgrade'][ $plugin_file ] = $plugin_data;
			}
		}

		set_transient( 'plugin_slugs', array_keys( $plugins['all'] ), 86400 );

		$recently_activated = get_option( 'recently_activated', array() );

		$one_week = 7*24*60*60;
		foreach ( $recently_activated as $key => $time )
			if ( $time + $one_week < time() )
				unset( $recently_activated[$key] );
		update_option( 'recently_activated', $recently_activated );

		if ( !current_user_can( 'update_plugins' ) )
			$plugins['upgrade'] = array();

		$totals = array();
		foreach ( $plugins as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $plugins[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = array();
		foreach ( $plugins[ $status ] as $plugin_file => $plugin_data ) {
			// Translate, Don't Apply Markup, Sanitize HTML
			$this->items[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			uasort( $this->items, array( &$this, '_order_callback' ) );
		}

		$plugins_per_page = $this->get_items_per_page( str_replace( '-', '_', $screen->id . '_per_page' ) );

		$start = ( $page - 1 ) * $plugins_per_page;

		if ( $total_this_page > $plugins_per_page )
			$this->items = array_slice( $this->items, $start, $plugins_per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $plugins_per_page,
		) );
	}

	/**
	 * Overrides {@link WP_Plugins_List_Table::bulk_actions()}
	 * to return nothing since this isn't applicable for our needs
	 */
	function bulk_actions() {
		return;
	}

	/**
	 * Overrides {@link WP_Plugins_List_Table::display_tablenav()}
	 * to return nothing since this isn't applicable for our needs
	 */
	function display_tablenav( $which ) {
		return;
	}
}
endif;

?>