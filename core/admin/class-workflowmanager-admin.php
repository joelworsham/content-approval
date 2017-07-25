<?php
/**
 * Manages the administrative side of the plugin.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_Admin
 *
 * Manages the administrative side of the plugin.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */
class WorkflowManager_Admin {

	/**
	 * Instance for the admin page.
	 *
	 * @since {{VERSION}}
	 *
	 * @var WorkflowManager_AdminPage
	 */
	public $admin_page;

	/**
	 * Instance for the manage workflows.
	 *
	 * @since {{VERSION}}
	 *
	 * @var WorkflowManager_ManageWorkflows
	 */
	public $manage_workflows;

	/**
	 * WorkflowManager_Admin constructor.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		$this->includes();

		add_action( 'admin_init', array( __CLASS__, 'get_pages' ) );
	}

	/**
	 * Returns all admin pages.
	 *
	 * @since {{VERSION}}
	 *
	 * @return mixed|array
	 */
	public static function get_pages() {

		/**
		 * Filters the admin pages.
		 *
		 * @since {{VERSION}}
		 */
		$pages = apply_filters( 'wfm_admin_pages', array(
			array(
				'id'        => 'manage_workflows',
				'callback' => array( 'WorkflowManager_AdminPage', 'page_body_workflows' ),
				/* translators: Page title for the main admin page. */
				'title'     => __( 'Manage Workflows', 'workflow-manager' ),
				/* translators: Tab title for the main admin page. */
				'tab_title' => __( 'Workflows', 'workflow-manager' ),
			)
		) );

		return $pages;
	}

	/**
	 * Includes all required files.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function includes() {

		require_once WORKFLOWMANAGER_DIR . 'core/admin/class-workflowmanager-admin-page.php';
		$this->admin_page = new WorkflowManager_AdminPage();

		require_once WORKFLOWMANAGER_DIR . 'core/admin/class-workflowmanager-manage-workflows.php';
		$this->manage_workflows = new WorkflowManager_ManageWorkflows();
	}
}

/**
 * Returns the administrative instance of the plugin.
 *
 * @since {{VERSION}}
 *
 * @return WorkflowManager_Admin
 */
function WorkflowManager_Admin() {

	static $instance = null;

	if ( $instance === null ) {

		$instance = new WorkflowManager_Admin();
	}

	return $instance;
}

// Instantiate admin
WorkflowManager_Admin();