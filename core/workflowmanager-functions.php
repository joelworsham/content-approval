<?php
/**
 * Helper functions.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core
 */

defined( 'ABSPATH' ) || die();

/**
 * Gets workflows.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 *
 * @return array|bool
 */
function wfm_get_workflow_posts( $args = array() ) {

	return WorkflowManager_PostType_Workflow::get_workflows( $args );
}

/**
 * If the current user has a pending edit for the post, this returns that pending edit post ID.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID Post ID to lookup pending. Leave empty to use current post.
 *
 * @return int|false Pending post ID or false if none
 */
function wfm_get_pending_post( $post_ID = 0 ) {

	if ( $post_ID === 0 ) {

		$post = get_post();

		if ( ! $post ) {

			return false;
		}

		$post_ID = $post->ID;
	}

	$current_post_pending      = get_post_meta( $post_ID, 'workflow_pending_posts' );
	$user_post_pending         = get_user_meta( get_current_user_id(), 'workflow_pending_posts' );
	$current_user_post_pending = array_intersect( $current_post_pending, $user_post_pending );

	if ( empty( $current_user_post_pending ) ) {

		return false;
	}

	$current_user_post_pending = array_shift( $current_user_post_pending );

	if ( get_post( $current_user_post_pending ) ) {

		/**
		 * The pending post ID for the supplied post.
		 *
		 * @since {{VERSION}}
		 */
		$current_user_post_pending = apply_filters(
			'workflow_pending_post',
			$current_user_post_pending,
			$post_ID
		);

		return $current_user_post_pending;

	} else {

		delete_post_meta( $_REQUEST['post'], 'workflow_pending_posts', $current_user_post_pending );
		delete_user_meta( get_current_user_id(), 'workflow_pending_posts', $current_user_post_pending );
	}

	return false;
}

/**
 * If pending post, returns original post ID.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID Post ID to lookup pending. Leave empty to use current post.
 *
 * @return bool
 */
function wfm_get_original_post( $post_ID = 0 ) {

	if ( $post_ID === 0 ) {

		$post = get_post();

		if ( ! $post ) {

			return false;
		}

		$post_ID = $post->ID;
	}

	$original_post_ID = get_post_meta( $post_ID, 'workflow_original', true );

	/**
	 * The original post ID for the current pending post.
	 *
	 * @since {{VERSION}}
	 *
	 * @param int $post_ID Post ID to check for original.
	 */
	$original_post_ID = apply_filters( 'workflow_original_post', $original_post_ID, $post_ID );

	return $original_post_ID;
}

/**
 * True if post is a pending version, false otherwise.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID Post ID to lookup pending. Leave empty to use current post.
 *
 * @return bool
 */
function wfm_is_pending_post( $post_ID = 0 ) {

	return wfm_get_original_post( $post_ID ) || false;
}

/**
 * Return all workflow posts.
 *
 * @since {{VERSION}}
 *
 * @return array
 */
function wfm_get_workflows() {

	static $workflows;

	if ( $workflows !== null ) {

		return $workflows;
	}

	$workflows_posts = wfm_get_workflow_posts();

	foreach ( $workflows_posts as $workflow_post ) {

		$workflows[] = array(
			'title'            => $workflow_post->post_title,
			'post_types'       => get_post_meta( $workflow_post->ID, 'post_types', true ),
			'submission_users' => get_post_meta( $workflow_post->ID, 'submission_users', true ),
			'submission_roles' => get_post_meta( $workflow_post->ID, 'submission_roles', true ),
			'approval_users'   => get_post_meta( $workflow_post->ID, 'approval_users', true ),
			'approval_roles'   => get_post_meta( $workflow_post->ID, 'approval_roles', true ),
		);
	}

	return $workflows;
}

/**
 * Returns current user limitation workflows.
 *
 * @since {{VERSION}}
 *
 * @return array
 */
function wfm_get_current_user_limitations() {

	$current_user = wp_get_current_user();

	$workflows = get_posts( array(
		'post_type'   => 'workflow',
		'numberposts' => - 1,
		'meta_query'  => array(
			'relation' => 'OR',
			array(
				'compare' => 'IN',
				'key'     => 'submission_users',
				'value'   => $current_user->ID,
			),
			array(
				'compare' => 'IN',
				'key'     => 'submission_roles',
				'value'   => $current_user->roles,
			),
		),
	) );

	/**
	 * Current user limitation workflows.
	 *
	 * @since {{VERSION}}
	 */
	$workflows = apply_filters( 'wfm_current_user_limitation_workflows', $workflows );

	return $workflows;
}

/**
 * Revisions accessible for approval by the current user.
 *
 * @since {{VERSION}}
 *
 * @return array
 */
function wfm_get_current_user_approval_revisions() {

	$current_user = wp_get_current_user();

	$workflows = get_posts( array(
		'post_type'   => 'workflow',
		'numberposts' => - 1,
		'meta_query'  => array(
			'relation' => 'OR',
			array(
				'compare' => 'IN',
				'key'     => 'approval_users',
				'value'   => $current_user->ID,
			),
			array(
				'compare' => 'IN',
				'key'     => 'approval_roles',
				'value'   => $current_user->roles,
			),
		),
	) );

	$users = array();

	foreach ( $workflows as $workflow ) {

		$submission_roles = get_post_meta( $workflow->ID, 'submission_roles' );
		$submission_users = get_post_meta( $workflow->ID, 'submission_users' );

		$workflow_users_by_id = get_users( array(
			'number'   => - 1,
			'include'  => $submission_users,
			'meta_key' => 'workflow_pending_posts',
		) );

		$users = array_merge( $users, $workflow_users_by_id );

		$workflow_users_by_role = get_users( array(
			'number'       => - 1,
			'role__in'     => $submission_roles,
			'meta_key'     => 'workflow_pending_posts',
			'meta_compare' => 'EXISTS',
		) );

		$users = array_merge( $users, $workflow_users_by_role );
	}

	$revisions = array();

	foreach ( $users as $user ) {

		$user_revisions = get_user_meta( $user->ID, 'workflow_pending_posts' );

		$revisions = array_merge( $revisions, $user_revisions );
	}

	$revisions = array_unique( $revisions );

	foreach ( $revisions as $i => $revision ) {

		if ( ! get_post( $revision ) ) {

			unset( $revisions[ $i ] );
		}
	}

	$revisions = array_values( $revisions );

	/**
	 * Revisions accessible for approval by the current user.
	 *
	 * @since {{VERSION}}
	 */
	$revisions = apply_filters( 'wfm_current_user_approval_revisions', $revisions );

	return $revisions;
}

/**
 * Tells if post is a revision of another.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID Post ID.
 *
 * return bool
 */
function wfm_post_is_revision( $post_ID = 0 ) {

	if ( $post_ID === 0 ) {

		$post = get_post();

	} else {

		$post = get_post( $post_ID );
	}

	if ( ! $post ) {

		return false;
	}

	$original = get_post_meta( $post->ID, 'workflow_original', true );

	return $original || false;
}

/**
 * Determines if post is revision of current user.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID
 *
 * @return bool
 */
function wfm_post_is_current_user_revision( $post_ID = 0 ) {

	if ( $post_ID === 0 ) {

		$post = get_post();

	} else {

		$post = get_post( $post_ID );
	}

	if ( ! $post ) {

		return false;
	}

	$revision_user = get_post_meta( $post_ID, 'workflow_user', true );

	return (int) $revision_user === get_current_user_id();
}

/**
 * Determines if post is approval revision of current user.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID
 *
 * @return bool
 */
function wfm_post_is_current_user_approval( $post_ID = 0 ) {

	if ( $post_ID === 0 ) {

		$post = get_post();

	} else {

		$post = get_post( $post_ID );
	}

	if ( ! $post ) {

		return false;
	}

	$approvable_revisions = wfm_get_current_user_approval_revisions();

	return in_array( $post->ID, $approvable_revisions );
}

/**
 * Gets the link for approving a post.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID
 *
 * @return string
 */
function wfm_get_approve_post_link( $post_ID = 0 ) {

	if ( $post_ID === 0 ) {

		$post = get_post();

	} else {

		$post = get_post( $post_ID );
	}

	if ( ! $post ) {

		return '';
	}

	$post_type_object = get_post_type_object( $post->post_type );

	if ( ! $post_type_object ) {

		return;
	}

	$approve_link = add_query_arg( 'action', 'approve', admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );

	/**
	 * Filters the revision approve link.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $link The approve link.
	 * @param int $post_id Post ID.
	 */
	return apply_filters(
		'wfm_get_approve_post_link',
		wp_nonce_url( $approve_link, "approve-post_{$post->ID}" ),
		$post->ID
	);
}

/**
 * Approves a post.
 *
 * @since {{VERSION}}
 *
 * @param int $post_ID Post ID of revision to approve.
 *
 * @return int|false The post ID of the original post or false if not verified.
 */
function wfm_approve_post( $post_ID ) {

	// TODO Add verification of custom cap approve_posts
	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], "approve-post_$post_ID" )
//	     ! current_user_can( 'wfm_approve_posts' )
	) {

		return false;
	}

	$post_meta = get_post_meta( $post_ID );

	$workflow_user_ID = array_shift( $post_meta['workflow_user'] );
	$original_post_ID = array_shift( $post_meta['workflow_original'] );

	$post = get_post( $post_ID );

	/**
	 * Fields from the revision post to transfer to the original post during approval.
	 *
	 * @since {{VERSION}}
	 */
	$save_fields = apply_filters( 'wfm_approve_revision_fields', array(
		'post_title'   => $post->post_title,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
	) );

	wp_update_post( array_merge( array(
		'ID' => $original_post_ID,
	), $save_fields ) );

	/**
	 * Meta fields from the revision post to transfer to the original post during approval.
	 *
	 * @since {{VERSION}}
	 */
	$save_meta = apply_filters( 'wfm_approve_revision_meta', array_diff_key( $post_meta, array(
		'_edit_lock',
		'workflow_user',
		'workflow_original',
	) ) );

	foreach ( $save_meta as $meta_field => $meta_value ) {

		update_post_meta( $original_post_ID, $meta_field, $meta_value );
	}

	/**
	 * Terms from the revision post to transfer to the original post during approval.
	 *
	 * @since {{VERSION}}
	 */
	$terms = apply_filters( 'wfm_approve_revision_terms', wp_get_post_terms( $post_ID, get_taxonomies() ) );

	foreach ( $terms as $term ) {

		wp_add_object_terms( $original_post_ID, $term->term_id, $term->taxonomy );
	}

	wp_delete_post( $post_ID, true );

	return $original_post_ID;
}