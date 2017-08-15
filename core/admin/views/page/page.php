<?php
/**
 * Workflows base page.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();
?>

<div class="wrap wfm-wrap">

    <header class="wfm-header">
		<?php
		/**
		 * Fires in the header of the page header.
		 *
		 * @since {{VERSION}}
		 *
		 * @hooked WorkflowManager_AdminPage::page_header() 5
		 * @hooked WorkflowManager_AdminPage::page_menu() 10
		 */
		do_action( 'wfm_page_header' );
		?>
    </header>

    <section class="wfm-body">
		<?php
		/**
		 * Fires in the header of the page body.
		 *
		 * @since {{VERSION}}
		 *
		 * @hooked WorkflowManager_AdminPage::page_body() 10
		 */
		do_action( 'wfm_page_body' );
		?>
    </section>

    <footer class="wfm-footer">
		<?php
		/**
		 * Fires in the header of the page footer.
		 *
		 * @since {{VERSION}}
		 */
		do_action( 'wfm_page_footer' );
		?>
    </footer>

</div>