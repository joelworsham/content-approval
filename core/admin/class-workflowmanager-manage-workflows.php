<?php
/**
 * The workflow manager instance.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_ManageWorkflows
 *
 * The workflow manager instance.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */
class WorkflowManager_ManageWorkflows {

	/**
	 * WorkflowManager_ManageWorkflows constructor.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		add_action( 'current_screen', array( $this, 'page_load' ) );
	}

	/**
	 * Fires actions only on this page.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param WP_Screen $current_screen
	 */
	function page_load( $current_screen ) {

		$page = WorkflowManager_AdminPage::get_active_page();

		if ( $current_screen->id != 'tools_page_workflows' && $page['id'] != 'manage_workflows' ) {

			return;
		}

		remove_action( 'wfm_page_header', array( WorkflowManager_Admin()->admin_page, 'page_title' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'page_assets' ) );
	}

	/**
	 * Gets available workflow fields.
	 *
	 * @since {{VERSION}}
	 *
	 * @return array|mixed
	 */
	public static function get_workflow_fields() {

		/**
		 * Args for getting the post type objects for the workflow field "post_types".
		 *
		 * @since {{VERSION}}
		 */
		$post_type_args = apply_filters( 'wfm_workflow_field_post_types_args', array(
			'public' => true,
		) );

		$post_types        = get_post_types( $post_type_args, 'objects' );
		$post_type_options = array();

		foreach ( $post_types as $post_type ) {

			$post_type_options[] = array(
				'label' => $post_type->label,
				'value' => $post_type->name,
			);
		}

		$roles        = get_editable_roles();
		$role_options = array();

		foreach ( $roles as $role_ID => $role ) {

			$role_options[] = array(
				'label' => $role['name'],
				'value' => $role_ID,
			);
		}

		/**
		 * Available workflow fields.
		 *
		 * @since {{VERSION}}
		 */
		$fields = apply_filters( 'wfm_workflow_fields', array(
			array(
				'name'     => 'title',
				/* translators: Label for the workflow fields "title" */
				'label'    => __( 'Title', 'workflow-manager' ),
				'type'     => 'text',
				'required' => true,
			),
			array(
				'name'     => 'post_types',
				/* translators: Label for the workflow fields "post_types" */
				'label'    => __( 'Post Types', 'workflow-manager' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => isset( $post_type_options ) ? $post_type_options : array(),
				'required' => true,
			),
			array(
				'name'        => 'submission_description',
				'type'        => 'html',
				/* translators: Label for the workflow fields "submission_description" */
				'label'       => __( 'Submission', 'workflow-manager' ),
				'description' => __( 'Determines who can submit changes to content. Use "Users", "Roles", or a combination of both.', 'workflow-manager' ),
			),
			array(
				'name'        => 'submission_users',
				/* translators: Label for the workflow fields "submission_users" */
				'label'       => __( 'Users', 'workflow-manager' ),
				'type'        => 'select',
				'multiple'    => true,
				'loadOptions' => array(
					'type' => 'users',
				),
				'required'    => array(
					'relation' => 'OR',
					'fields'   => array(
						'submission_roles',
					),
				),
			),
			array(
				'name'     => 'submission_roles',
				/* translators: Label for the workflow fields "submission_roles" */
				'label'    => __( 'Roles', 'workflow-manager' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => isset( $role_options ) ? $role_options : array(),
				'required' => array(
					'relation' => 'OR',
					'fields'   => array(
						'submission_users',
					),
				),
			),
			array(
				'name'        => 'approval_description',
				'type'        => 'html',
				/* translators: Label for the workflow fields "submission_description" */
				'label'       => __( 'Approval', 'workflow-manager' ),
				'description' => __( 'Determines who can approve changes to content. Use "Users", "Roles", or a combination of both.', 'workflow-manager' ),
			),
			array(
				'name'        => 'approval_users',
				/* translators: Label for the workflow fields "approval_users" */
				'label'       => __( 'Users', 'workflow-manager' ),
				'type'        => 'select',
				'multiple'    => true,
				'loadOptions' => array(
					'type' => 'users',
				),
				'required'    => array(
					'relation' => 'OR',
					'fields'   => array(
						'approval_roles',
					),
				),
			),
			array(
				'name'     => 'approval_roles',
				/* translators: Label for the workflow fields "approval_roles" */
				'label'    => __( 'Roles', 'workflow-manager' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => isset( $role_options ) ? $role_options : array(),
				'required' => array(
					'relation' => 'OR',
					'fields'   => array(
						'approval_users',
					),
				),
			),
			array(
				'name' => 'id',
				'type' => 'hidden',
			),
			array(
				'name'    => 'status',
				'default' => 'publish',
				'type'    => 'hidden',
			),
		) );

		return $fields;
	}

	/**
	 * Loads assets for specific pages.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function page_assets() {

		wp_localize_script( 'wfm-manage-workflows', 'WFM_ManageWorkflows', array(
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'workflowSchema' => self::get_workflow_fields(),
			'l10n'           => array(
				/* translators: Page title for the "Manage Workflows" page */
				'manage_workflows'                     => __( 'Manage Workflows', 'workflow-manager' ),
				'title'                                => __( 'Title', 'workflow-manager' ),
				'loading'                              => __( 'Loading...', 'workflow-manager' ),
				'something_went_wrong'                 => __( 'Something went wrong.', 'workflow-manager' ),
				'retry'                                => __( 'Retry', 'workflow-manager' ),
				'no_workflows'                         => __( 'No workflows', 'workflow-manager' ),
				'add_workflow'                         => __( 'Add Workflow', 'workflow-manager' ),
				'edit_workflow'                        => __( 'Edit Workflow', 'workflow-manager' ),
				/* translators: %s is the type of field for a workflow */
				'field_type_not_found'                 => __( 'Error: Field type %s not found', 'workflow-manager' ),
				'save_workflow'                        => __( 'Save Workflow', 'workflow-manager' ),
				'update_workflow'                      => __( 'Update Workflow', 'workflow-manager' ),
				'delete'                               => __( 'Delete', 'workflow-manager' ),
				'confirm_delete_workflow'              => __( 'Are you sure you want to delete this? This cannot be undone.', 'workflow-manager' ),
				'field_required_message'               => __( 'This field is required.', 'workflow-manager' ),
				/* translators: %s is list of required fields */
				'field_required_and_message'           => __( 'Following fields required: %s.', 'workflow-manager' ),
				/* translators: %s is list of required fields */
				'field_required_or_message'            => __( 'At least one of the following fields required: %s.', 'workflow-manager' ),
				/* translators: %s is the error message returned from the API */
				'api_error_fetch_workflows'            => __( 'Could not get workflows. %s', 'workflow-manager' ),
				'server_returned_code'                 => __( 'Server returned error code: %s', 'workflow-manager' ),
				'reason_unknown'                       => __( 'Reason unknown.', 'workflow-manager' ),
				//
				// React Select translations
				'reactSelect_placeholder'              => __( 'Select...', 'workflow-manager' ),
				'reactSelect_noResultsText'            => __( 'No results found', 'workflow-manager' ),
				'reactSelect_searchPromptText'         => __( 'Type to search', 'workflow-manager' ),
				'reactSelect_loadingPlaceholder'       => __( 'Loading...', 'workflow-manager' ),
				'reactSelect_clearValueText'           => __( 'Clear value', 'workflow-manager' ),
				'reactSelect_clearAllText'             => __( 'Clear all', 'workflow-manager' ),
				/* translators: {last label} should be left as is; do NOT translate it. It will be replaced */
				'reactSelect_backspaceToRemoveMessage' => __( 'Press backspace to remove {last label}', 'workflow-manager' ),
				/* translators: {label} should be left as is; do NOT translate it. It will be replaced */
				'reactSelect_addLabelText'             => __( 'Add "{label}"?', 'workflow-manager' ),
			),
		) );

		wp_enqueue_style( 'wfm-manage-workflows' );
		wp_enqueue_script( 'wfm-manage-workflows' );
	}
}