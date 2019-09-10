<?php

/**
 * Plugin Name: BuddyForms Collaborative Publishing
 * Plugin URI: https://themekraft.com/products/buddyforms-collaborative-publishing/
 * Description: BuddyForms Collaborative Publishing
 * Version: 1.0.0
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/
 * License: GPLv2 or later
 * Network: false
 * Text Domain: buddyforms
 *
 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */


class BuddyFormsCPublishing {
	/**
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Initiate the class
	 *
	 * @package buddyforms CPUBLISHING
	 * @since 0.1
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ), 4, 1 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'buddyforms_admin_js_css_enqueue', array( $this, 'admin_js_css_enqueue' ) );
		add_action( 'buddyforms_front_js_css_after_enqueue', array( $this, 'buddyforms_front_js_css_enqueue' ) );

		$this->load_constants();
	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package buddyforms_CPUBLISHING
	 * @since 1.0
	 */
	public function load_constants() {
		if ( ! defined( 'BUDDYFORMS_CPUBLISHING_PLUGIN_URL' ) ) {
			define( 'BUDDYFORMS_CPUBLISHING_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
		}
		if ( ! defined( 'BUDDYFORMS_CPUBLISHING_INSTALL_PATH' ) ) {
			define( 'BUDDYFORMS_CPUBLISHING_INSTALL_PATH', dirname( __FILE__ ) . '/' );
		}
		if ( ! defined( 'BUDDYFORMS_CPUBLISHING_INCLUDES_PATH' ) ) {
			define( 'BUDDYFORMS_CPUBLISHING_INCLUDES_PATH', BUDDYFORMS_CPUBLISHING_INSTALL_PATH . 'includes/' );
		}

	}

	/**
	 * Include files needed by BuddyForms
	 *
	 * @package buddyforms_CPUBLISHING
	 * @since 1.0
	 */
	public function includes() {

		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'form-elements.php';
		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'functions.php';
		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'invite-new-user.php';
		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'shortcodes.php';
		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'user-taxonomy.php';
		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'become-an-editor.php';
		require_once BUDDYFORMS_CPUBLISHING_INCLUDES_PATH . 'delete-post.php';

	}

	/**
	 * Load the textdomain for the plugin
	 *
	 * @package buddyforms_CPUBLISHING
	 * @since 1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'buddyforms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function admin_js_css_enqueue() {
		wp_enqueue_script( 'buddyforms-cpublishing-form-builder-js', plugins_url( 'assets/admin/js/form-builder.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'buddyforms-cpublishing-form-builder-css', plugins_url( 'assets/admin/css/form-builder.css', __FILE__ ) );
	}

	function buddyforms_front_js_css_enqueue() {
		wp_enqueue_script( 'buddyforms-collaborative-js', plugins_url( 'assets/js/collaborative.js', __FILE__ ), array( 'jquery' ) );
	}

}

$GLOBALS['BuddyFormsCPublishing'] = new BuddyFormsCPublishing();
