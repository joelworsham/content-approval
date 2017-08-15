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

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function enqueue_scripts() {

		wp_enqueue_style( 'wfm-admin' );
	}

	/**
	 * Filters admin body class.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param string $class
	 *
	 * @return string
	 */
	function admin_body_class( $class ) {

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'workflows' ) {

			$class .= ' wfm-page';

			$pages = self::get_pages();

			if ( ! isset( $_GET['tab'] ) ) {

				$class .= " wfm-{$pages[0]['id']}";

			} else {

				$class .= " wfm-$_GET[tab]";
			}
		}

		return $class;
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
				'id'        => 'manage_revisions',
				'callback'  => array( 'WorkflowManager_AdminPage', 'page_body_manage_revisions' ),
				/* translators: Page title for the main admin page. */
				'title'     => __( 'Manage Revisions', 'workflow-manager' ),
				/* translators: Tab title for the main admin page. */
				'tab_title' => __( 'Revisions', 'workflow-manager' ),
			),
			array(
				'id'        => 'manage_workflows',
				'callback'  => array( 'WorkflowManager_AdminPage', 'page_body_manage_workflows' ),
				/* translators: Page title for the main admin page. */
				'title'     => __( 'Manage Workflows', 'workflow-manager' ),
				/* translators: Tab title for the main admin page. */
				'tab_title' => __( 'Workflows', 'workflow-manager' ),
			),
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