<?php
/**
 * Plugin Name: Workflow Manager
 * Plugin URI: https://wordpress.org/plugins/workflow-manager
 * Description: Create highly customizable and intuitive content strategy workflows for your WordPress website.
 * Version 0.1.0
 * Author: Joel Worsham
 * Author URI: http://joelworsham.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: workflow-manager
 * Domain Path: /languages
 */

// TODO Approval users must have list of what revisions they can access. What can they access? That's tricky.
// TODO Delete user meta for revisions on post revision delete
// TODO Revision table improvements such as: pagination, search, faster query
// TODO When viewing revision as approval user, either don't allow edit somehow or allow updating revision without publishing
// TODO Preview on revisions not working
// TODO Figure out how to handle/hide other pending post changes
// TODO See if possible to use current_user_can() for "approve_post"
// TODO View revisions contextually for posts

add_action( 'init', function () {

	register_taxonomy( 'page-test2', 'page', array(
		'labels' => array(
			'name' => 'Test 2',
		),
	));

	register_taxonomy( 'page-test', 'page', array(
		'labels' => array(
			'name' => 'Test',
		),
	));
});

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'WorkflowManager' ) ) {

	define( 'WORKFLOWMANAGER_VERSION', '0.1.0' );
	define( 'WORKFLOWMANAGER_DIR', plugin_dir_path( __FILE__ ) );
	define( 'WORKFLOWMANAGER_URI', plugins_url( '', __FILE__ ) );

	/**
	 * Class WorkflowManager
	 *
	 * The main plugin class for Workflow Manager.
	 *
	 * @since {{VERSION}}
	 * @access public
	 *
	 * @package WorkflowManager
	 */
	final class WorkflowManager {

		protected function __clone() {
		}

		protected function __wakeup() {
		}

		/**
		 * Call this method to get singleton
		 *
		 * @since 1.0.0
		 *
		 * @return WorkflowManager()
		 */
		public static function instance() {

			static $instance = null;

			if ( $instance === null ) {

				$instance = new WorkflowManager();
			}

			return $instance;
		}

		/**
		 * WorkflowManager constructor.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		private function __construct() {

			$this->includes();

			add_action( 'init', array( $this, 'register_assets' ) );
		}

		/**
		 * Includes all required files.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		private function includes() {

			require_once WORKFLOWMANAGER_DIR . 'core/workflowmanager-functions.php';

			require_once WORKFLOWMANAGER_DIR . 'core/post-types/class-workflowmanager-post-types.php';
			require_once WORKFLOWMANAGER_DIR . 'core/class-workflowmanager-post-limitations.php';

			if ( is_admin() ) {

				require_once WORKFLOWMANAGER_DIR . 'core/admin/class-workflowmanager-admin.php';
			}
		}

		/**
		 * Registers all plugin assets.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		function register_assets() {

			// Admin
			wp_register_style(
				'wfm-admin',
				WORKFLOWMANAGER_URI . '/assets/dist/css/wfm-admin.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : WORKFLOWMANAGER_VERSION
			);

			// Manage Workflows
			wp_register_style(
				'wfm-manage-workflows',
				WORKFLOWMANAGER_URI . '/assets/dist/css/wfm-manage-workflows.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : WORKFLOWMANAGER_VERSION
			);

			wp_register_script(
				'wfm-manage-workflows',
				WORKFLOWMANAGER_URI . '/assets/dist/js/wfm-manage-workflows.min.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : WORKFLOWMANAGER_VERSION,
				true
			);
		}
	}

	// Bootstrap the plugin
	require_once WORKFLOWMANAGER_DIR . 'workflow-manager-bootstrapper.php';
	new WorkflowManager_Bootstrapper();

	// Plugin installer
	require_once WORKFLOWMANAGER_DIR . 'core/class-workflowmanager-install.php';
	register_activation_hook( __FILE__, array( 'WorkflowManager_Install', 'install' ) );
}