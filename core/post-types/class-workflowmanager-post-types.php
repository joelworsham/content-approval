<?php
/**
 * Manages the post types for the plugin
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/postTypes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_PostTypes
 *
 * Manages the administrative side of the plugin.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */
class WorkflowManager_PostTypes {

	/**
	 * All plugin post type instances.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array
	 */
	public $post_types = array();

	/**
	 * WorkflowManager_PostTypes constructor.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		$this->includes();
	}

	/**
	 * Includes all required files.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function includes() {

		require_once WORKFLOWMANAGER_DIR . 'core/post-types/class-workflowmanager-post-type-workflow.php';

		$this->post_types['workflow'] = new WorkflowManager_PostType_Workflow();
	}
}

/**
 * Returns the post types instance of the plugin.
 *
 * @since {{VERSION}}
 *
 * @return WorkflowManager_PostTypes
 */
function WorkflowManager_PostTypes() {

	static $instance = null;

	if ( $instance === null ) {

		$instance = new WorkflowManager_PostTypes();
	}

	return $instance;
}

// Instantiate post types
WorkflowManager_PostTypes();