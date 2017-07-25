<?php
/**
 * Installs the plugin.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class WorkflowManager_Install
 *
 * Installs the plugin.
 *
 * @since {{VERSION}}
 */
class WorkflowManager_Install {

	/**
	 * Loads the install functions.
	 *
	 * @since {{VERSION}}
	 */
	static function install() {

		add_option( 'workflowmanager_db_version', '1.0.0' );

		self::setup_capabilities();
	}

	/**
	 * Sets up custom capabilities
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private static function setup_capabilities() {

		$administrator = get_role( 'administrator' );

		$administrator->add_cap( 'manage_workflows' );

		$administrator->add_cap( 'edit_workflow' );
		$administrator->add_cap( 'read_workflow' );
		$administrator->add_cap( 'delete_workflow' );
		$administrator->add_cap( 'edit_workflows' );
		$administrator->add_cap( 'edit_others_workflows' );
		$administrator->add_cap( 'publish_workflows' );
		$administrator->add_cap( 'read_private_workflows' );
		$administrator->add_cap( 'edit_workflows' );
	}
}