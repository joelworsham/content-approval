<?php
/**
 * Manage Revisions page body.
 *
 * @since {{VERSION}}
 *
 * @var WFM_Revisions_ListTable $revision_list_table
 */

defined( 'ABSPATH' ) || die();
?>

<div id="wfm-manage-revisions">
	<?php $revision_list_table->display(); ?>
</div>