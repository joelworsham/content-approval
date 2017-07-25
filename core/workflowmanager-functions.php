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
function wfm_get_workflows( $args = array() ) {

	return WorkflowManager_PostType_Workflow::get_workflows( $args );
}