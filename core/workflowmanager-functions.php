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

	static $workflows;

	if ( $workflows !== null ) {

		return $workflows;
	}

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

	return $workflows;
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
function wfm_current_post_is_revision( $post_ID = 0 ) {

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