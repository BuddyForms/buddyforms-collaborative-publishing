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
 * Text Domain: buddyforms-collaborative-publishing
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
	public static $version = '1.0.0';
	public static $include_assets = array();
	public static $slug = 'buddyforms-collaborative-publishing';

	/**
	 * Initiate the class
	 *
	 * @package buddyforms CPUBLISHING
	 * @since 0.1
	 */
	public function __construct() {
		if ( self::is_buddy_form_active() ) {
			add_action( 'init', array( $this, 'includes' ), 4, 1 );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'buddyforms_admin_js_css_enqueue', array( $this, 'admin_js_css_enqueue' ) );

			add_action( 'buddyforms_front_js_css_after_enqueue', array( $this, 'buddyforms_collaborative_publishing_needs_assets' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'buddyforms_front_js_css_enqueue' ) );

			$this->load_constants();
		} else {
			add_action( 'admin_notices', array( $this, 'need_buddyforms' ) );
		}
	}

	public function buddyforms_collaborative_publishing_needs_assets( $content, $form_slug ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}
		if ( empty( $form_slug ) ) {
			return;
		}

		global $buddyforms;

		if ( empty( $buddyforms[ $form_slug ] ) ) {
			return;
		}

		if ( ! function_exists( 'buddyforms_exist_field_type_in_form' ) ) {
			return;
		}

		//check if the field exist
		$exist_field = buddyforms_exist_field_type_in_form( $form_slug, 'collaborative-publishing' );

		$needs_assets = ( $exist_field == true );
		BuddyFormsCPublishing::setNeedAssets( $needs_assets, $form_slug );
	}

	public static function load_plugins_dependency() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	public static function is_buddy_form_active() {
		self::load_plugins_dependency();

		return is_plugin_active( 'buddyforms-premium/BuddyForms.php' );
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

	public function need_buddyforms() {
		?>
		<style>
			.buddyforms-notice label.buddyforms-title {
				background: rgba(0, 0, 0, 0.3);
				color: #fff;
				padding: 2px 10px;
				position: absolute;
				top: 100%;
				bottom: auto;
				right: auto;
				-moz-border-radius: 0 0 3px 3px;
				-webkit-border-radius: 0 0 3px 3px;
				border-radius: 0 0 3px 3px;
				left: 10px;
				font-size: 12px;
				font-weight: bold;
				cursor: auto;
			}

			.buddyforms-notice .buddyforms-notice-body {
				margin: .5em 0;
				padding: 2px;
			}

			.buddyforms-notice.buddyforms-title {
				margin-bottom: 30px !important;
			}

			.buddyforms-notice {
				position: relative;
			}
		</style>
		<div class="error buddyforms-notice buddyforms-title">
			<label class="buddyforms-title">BuddyForms Collaborative Publishing</label>
			<div class="buddyforms-notice-body">
				<b>Oops...</b> BuddyForms Collaborative Publishing cannot run without <a target="_blank" href="https://themekraft.com/buddyforms/">BuddyForms</a>.
			</div>
		</div>
		<?php
	}

	public static function error_log( $message ) {
		if ( ! empty( $message ) ) {
			error_log( self::getSlug() . ' -- ' . $message );
		}
	}

	/**
	 * @return string
	 */
	public static function getNeedAssets() {
		if ( empty( self::$include_assets ) ) {
			return false;
		}

		return in_array( true, self::$include_assets, true );
	}

	/**
	 * @param string $include_assets
	 * @param string $form_slug
	 */
	public static function setNeedAssets( $include_assets, $form_slug ) {
		self::$include_assets[ $form_slug ] = $include_assets;
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	static function getVersion() {
		return self::$version;
	}

	/**
	 * Get plugins slug
	 *
	 * @return string
	 */
	static function getSlug() {
		return self::$slug;
	}

	/**
	 * Load the textdomain for the plugin
	 *
	 * @package buddyforms_CPUBLISHING
	 * @since 1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'buddyforms-collaborative-publishing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function admin_js_css_enqueue() {
		wp_enqueue_script( 'buddyforms-cpublishing-form-builder-js', BUDDYFORMS_CPUBLISHING_PLUGIN_URL . 'assets/admin/js/form-builder.js', array( 'jquery' ), self::getVersion() );
		wp_enqueue_style( 'buddyforms-cpublishing-form-builder-css', BUDDYFORMS_CPUBLISHING_PLUGIN_URL . 'assets/admin/css/form-builder.css', self::getVersion() );
	}

	function buddyforms_front_js_css_enqueue() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}
		if ( BuddyFormsCPublishing::getNeedAssets() ) {
			wp_enqueue_script( 'buddyforms-collaborative-script', BUDDYFORMS_CPUBLISHING_PLUGIN_URL . 'assets/js/script.js', array( 'jquery' ), self::getVersion() );
			wp_enqueue_style( 'buddyforms-collaborative-style', BUDDYFORMS_CPUBLISHING_PLUGIN_URL . 'assets/css/style.css', array(), self::getVersion() );
			wp_localize_script( 'buddyforms-collaborative-script', 'buddyformsCollaborativePublishingObj', array(
				'ajax'     => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( BUDDYFORMS_CPUBLISHING_INSTALL_PATH . 'bf_collaborative_publishing' ),
				'language' => apply_filters( 'bf_collaborative_publishing_language', array(
					'edit_request_in_process' => apply_filters( 'bf_collaborative_publishing_edit_request_process_string', __( 'Edit Request in Process.', 'buddyforms-collaborative-publishing' ) ),
					'remove_as_editor'        => apply_filters( 'bf_collaborative_publishing_remove_as_editor_string', __( 'Are you sure to remove as Editor', 'buddyforms-collaborative-publishing' ) ),
					'remove_post'             => apply_filters( 'bf_collaborative_publishing_remove_post_string', __( 'Are you sure to delete the Post?', 'buddyforms-collaborative-publishing' ) ),
					'invalid_invite_editors'  => apply_filters( 'bf_collaborative_publishing_invalid_invite_editors_string', __( 'You need to select a valid user or type a valid email.', 'buddyforms-collaborative-publishing' ) ),
					'invalid_invite_message'  => apply_filters( 'bf_collaborative_publishing_invalid_invite_message_string', __( 'Message is a required field.', 'buddyforms-collaborative-publishing' ) ),
					'popup_loading'           => apply_filters( 'bf_collaborative_publishing_modal_loading_string', __( 'Loading...', 'buddyforms-collaborative-publishing' ) ),
				) )
			) );
			add_thickbox();
		}
	}

}

$GLOBALS['BuddyFormsCPublishing'] = new BuddyFormsCPublishing();
