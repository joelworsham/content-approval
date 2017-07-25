<?php
/**
 * Adds the management page.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_AdminPage
 *
 * Manages the administrative side of the plugin.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */
class WorkflowManager_AdminPage {

	/**
	 * The currently active admin page, if any.
	 *
	 * @since {{VERSION}}
	 *
	 * @var null|array
	 */
	public $active_page = null;

	/**
	 * WorkflowManager_AdminPage constructor.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_menu_items' ) );

		$this->active_page = self::get_active_page();

		add_action( 'wfm_page_header', array( $this, 'page_menu' ), 5 );
		add_action( 'wfm_page_header', array( $this, 'page_title' ), 10 );
		add_action( 'wfm_page_body', array( $this, 'page_body' ), 10 );
	}

	/**
	 * Adds all menu items.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_menu_items() {

		add_management_page(
		/* translators: Menu title for main plugin page, Workflows */
			__( 'Workflows', 'workflow-manager' ),
			/* translators: Page title for main plugin page, Workflows */
			__( 'Workflows', 'workflow-manager' ),
			'manage_workflows',
			'workflows',
			array( $this, 'load_page' )
		);
	}

	/**
	 * Gets the currently active page.
	 *
	 * @since {{VERSION}}
	 *
	 * @return array
	 */
	public static function get_active_page() {

		$pages = WorkflowManager_Admin::get_pages();

		if ( isset( $_GET['tab'] ) && isset( $pages[ $_GET['tab'] ] ) ) {

			$active_page = $pages[ $_GET['tab'] ];

		} else {

			$active_page = array_shift( $pages );
		}

		return $active_page;
	}

	/**
	 * Outputs the page menu.
	 *
	 * @since {{VERSION}}
	 */
	public function page_menu() {

		$pages       = WorkflowManager_Admin::get_pages();
		$active_page = $this->active_page['id'];

		include WORKFLOWMANAGER_DIR . 'core/admin/views/page/page-menu.php';
	}

	/**
	 * Outputs the page title.
	 *
	 * @since {{VERSION}}
	 */
	public function page_title() {

		$page = $this->active_page;

		include WORKFLOWMANAGER_DIR . 'core/admin/views/page/page-title.php';
	}

	/**
	 * Outputs the page body.
	 *
	 * @since {{VERSION}}
	 */
	public function page_body() {

		if ( is_callable( $this->active_page['callback'] ) ) {

			call_user_func( $this->active_page['callback'] );
		}
	}

	/**
	 * Outputs the Workflows admin page.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function load_page() {

		include WORKFLOWMANAGER_DIR . 'core/admin/views/page/page.php';
	}

	/**
	 * Outputs the page body for Workflows.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function page_body_workflows() {

		$workflows = wfm_get_workflows();

		include WORKFLOWMANAGER_DIR . 'core/admin/views/pages/manage-workflows.php';
	}
}