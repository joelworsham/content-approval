<?php
/**
 * Creates the post type for Workflows.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/postTypes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_PostType_Workflow
 *
 * Creates the post type for Workflows.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/postTypes
 */
class WorkflowManager_PostType_Workflow {

	/**
	 * WFM_PostType_Workflow constructor.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'post_action_approve', array( $this, 'action_approve_post' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_custom_fields' ) );
		add_action( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		if ( isset( $_GET['approved'] ) ) {

			add_action( 'wfm_page_header', array( $this, 'approved_admin_notice' ), 10 );
		}
	}

	/**
	 * Registers this post type.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function register_post_type() {

		/**
		 * Labels for the workflow post type.
		 *
		 * @since {{VERSION}}
		 */
		$post_labels = apply_filters( 'wfm_posttype_labels_workflow', array(
			/* translators: Label "name" for the post type "workflow" */
			'name'               => __( 'Workflows', 'workflow-manager' ),
			/* translators: Label "singular_name" for the post type "workflow" */
			'singular_name'      => __( 'Workflow', 'workflow-manager' ),
			/* translators: Label "menu_name" for the post type "workflow" */
			'menu_name'          => __( 'Workflows', 'workflow-manager' ),
			/* translators: Label "name_admin_bar" for the post type "workflow" */
			'name_admin_bar'     => __( 'Workflow', 'workflow-manager' ),
			/* translators: Label "add_new" for the post type "workflow" */
			'add_new'            => __( "Add New", 'workflow-manager' ),
			/* translators: Label "add_new_item" for the post type "workflow" */
			'add_new_item'       => __( "Add New Workflow", 'workflow-manager' ),
			/* translators: Label "new_item" for the post type "workflow" */
			'new_item'           => __( "New Workflow", 'workflow-manager' ),
			/* translators: Label "edit_item" for the post type "workflow" */
			'edit_item'          => __( "Edit Workflow", 'workflow-manager' ),
			/* translators: Label "view_item" for the post type "workflow" */
			'view_item'          => __( "View Workflow", 'workflow-manager' ),
			/* translators: Label "all_items" for the post type "workflow" */
			'all_items'          => __( "All Workflows", 'workflow-manager' ),
			/* translators: Label "search_items" for the post type "workflow" */
			'search_items'       => __( "Search Workflows", 'workflow-manager' ),
			/* translators: Label "parent_item_colon" for the post type "workflow" */
			'parent_item_colon'  => __( "Parent Workflows:", 'workflow-manager' ),
			/* translators: Label "not_found" for the post type "workflow" */
			'not_found'          => __( "No Workflows found.", 'workflow-manager' ),
			/* translators: Label "not_found_in_trash" for the post type "workflow" */
			'not_found_in_trash' => __( "No Workflows found in Trash.", 'workflow-manager' ),
		) );

		/**
		 * Arguments for the workflow post type.
		 */
		$post_args = apply_filters( 'wfm_posttype_args_workflow', array(
			'labels'             => $post_labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'show_in_rest'       => true,
			'query_var'          => true,
			'supports'           => array( 'title', 'custom-fields' ),
			'capability_type'    => 'workflow',
		) );

		register_post_type( 'workflow', $post_args );

		register_post_status( 'workflow_pending', array(
			'label'                     => __( 'Pending', 'workflow-manager' ),
			'internal'                  => true,
			'public'                    => false,
			'private'                   => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
		) );
	}

	/**
	 * Handles action of approving a post.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param int $post_ID Post ID of revision to approve.
	 */
	function action_approve_post( $post_ID ) {

		global $sendback;

		$original_post_ID = wfm_approve_post( $post_ID );

		if ( $original_post_ID === false ) {

			wp_redirect( add_query_arg( 'approved', - 1, $sendback ) );
			exit();

		} else {

			wp_redirect( add_query_arg( 'approved', $original_post_ID, $sendback ) );
			exit();
		}
	}

	/**
	 * Admin notice for after a revision is approved.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function approved_admin_notice() {

		$original_post_ID = $_REQUEST['approved'];

		if ( $original_post_ID !== '-1' ) {

			$message = sprintf(
			/* translators: %s is approved post title */
				__( 'Revision successfully approved for %s.', 'workflow-manager' ),
				'<a href="' . get_permalink( $original_post_ID ) . '">' . get_the_title( $original_post_ID ) . '</a>'
			);

		} else {

			$message = __( 'Could not approve the revision. Permission denied.', 'workflow-manager' );
		}
		?>
        <div class="notice notice-<?php echo $original_post_ID !== '-1' ? 'success' : 'error'; ?>">
            <p>
				<?php echo $message; ?>
            </p>
        </div>
		<?php
	}

	/**
	 * Registers custom fields for the rest api.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function register_rest_custom_fields() {

		/**
		 * Custom fields to register with the rest api.
		 *
		 * @since {{VERSION}}
		 */
		$custom_meta = apply_filters( 'wfm_rest_custom_fields', array(
			'post_types',
			'submission_users',
			'submission_roles',
			'approval_users',
			'approval_roles',
		) );

		foreach ( $custom_meta as $field ) {

			register_rest_field( 'workflow', $field, array(
				'get_callback'    => array( $this, 'rest_get_field' ),
				'update_callback' => array( $this, 'rest_update_field' ),
			) );
		}
	}

	/**
	 * Gets custom fields for the rest api.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param $object
	 * @param $field
	 *
	 * @return mixed
	 */
	function rest_get_field( $object, $field ) {

		$post_id = $object['id'];

		return get_post_meta( $post_id, $field );
	}

	/**
	 * Updates custom fields for the rest api.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param $meta
	 * @param $post
	 * @param $field
	 */
	function rest_update_field( $meta, $post, $field ) {

		if ( is_array( $meta ) ) {

			$current_values = get_post_meta( $post->ID, $field );
			$delete_values  = array_diff( $current_values, $meta );
			$new_values     = array_diff( $meta, $current_values );

			foreach ( $delete_values as $delete_value ) {

				delete_post_meta( $post->ID, $field, $delete_value );
			}

			foreach ( $new_values as $new_value ) {

				add_post_meta( $post->ID, $field, $new_value );
			}

			return;
		}

		update_post_meta( $post->ID, $field, $meta );
	}

	/**
	 * Updates post action messages for revisions.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	function post_updated_messages( $messages ) {

		foreach ( $messages as $post_type => $message_group ) {

			$messages[ $post_type ][6] = __( 'Revision updated.', 'workflow-manager' );
		}

		return $messages;
	}

	/**
	 * Gets workflows.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $args Post arguments.
	 *
	 * @return array|bool Workflows, if any.
	 */
	public static function get_workflows( $args = array() ) {

		$args['post_type'] = 'workflow';

		$workflows = get_posts( $args );

		/**
		 * Filters returned workflows.
		 *
		 * @since {{VERSION}}
		 */
		$workflows = apply_filters( 'wfm_get_workflows', $workflows );

		return $workflows;
	}
}