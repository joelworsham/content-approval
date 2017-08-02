<?php
/**
 * Handles post limitation based on workflows.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_PostLimitations
 *
 * Manages the administrative side of the plugin.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin
 */
class WorkflowManager_PostLimitations {

	/**
	 * WorkflowManager_PostLimitations constructor.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		if ( wfm_get_current_user_limitations() ) {

			add_filter( 'wp_insert_post_empty_content', array( $this, 'create_pending' ), 10, 2 );
			add_filter( 'wp_insert_post_data', array( $this, 'handle_pending_save' ), 10, 2 );
			add_action( 'current_screen', array( $this, 'screen_actions' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_revision_meta_boxes' ) );
		}

		if ( wfm_current_post_is_revision( isset( $_GET['post'] ) ? $_GET['post'] : false ) ) {

			add_action( 'admin_notices', array( $this, 'notice_post_revision' ) );
			$this->redirect_from_restricted_pending();
		}

		add_action( 'admin_notices', array( $this, 'notice_restricted_revision' ) );
	}

	/**
	 * Adds screen-specific action.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param WP_Screen $screen
	 */
	function screen_actions( $screen ) {

		if ( $screen->base === 'post' && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'edit' ) {

			$this->redirect_to_pending();

		} elseif ( $screen->base === 'edit' ) {

			add_filter( 'the_title', array( $this, 'list_table_title' ), 10, 2 );
			add_filter( 'post_class', array( $this, 'list_table_post_class' ), 10, 3 );
		}
	}

	/**
	 * Redirects to pending change if exists.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function redirect_to_pending() {

		$post_status = get_post_status( $_REQUEST['post'] );

		if ( $post_status === 'workflow_pending' ) {

			return;
		}

		$pending_post = wfm_get_pending_post( $_REQUEST['post'] );

		if ( $pending_post ) {

			wp_redirect( get_edit_post_link( $pending_post, 'redirect' ) );
			exit();
		}
	}

	/**
	 * If not current user's pending, redirect away.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function redirect_from_restricted_pending() {

		$revision_user = get_post_meta( $_REQUEST['post'], 'workflow_user', get_current_user_id() );
		$post_type     = get_post_type( $_REQUEST['post'] );

		if ( (int) $revision_user !== get_current_user_id() ) {

			set_transient( 'wfm_restricted_revision_' . get_current_user_id(), 1, 30 );
			wp_redirect( admin_url( "edit.php?post_type=$post_type" ) );
			exit();
		}
	}

	/**
	 * Filters the title, adding "(Pending Changes)" if there are changes pending.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param $title
	 * @param $post_ID
	 *
	 * @return string
	 */
	function list_table_title( $title, $post_ID ) {

		if ( wfm_get_pending_post( $post_ID ) ) {

			return $title . ' (' . __( 'Pending Changes', 'workflow-manager' ) . ')';
		}

		return $title;
	}

	/**
	 * Filters the post class for the table row.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $classes An array of post classes.
	 * @param array $class An array of additional classes added to the post.
	 * @param int $post_id The post ID.
	 *
	 * @return array
	 */
	function list_table_post_class( $classes, $class, $post_ID ) {

		if ( wfm_get_pending_post( $post_ID ) || wfm_get_original_post( $post_ID ) ) {

			$classes[] = 'workflow-pending';
		}

		return $classes;
	}

	/**
	 * Handles saving post.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 */
	function create_pending( $maybe_empty, $postarr ) {

		// TODO actually check user approval workflow stuff
		$approval = $postarr['post_type'] === 'page';

		if ( $approval !== true ||
		     $postarr['post_status'] === 'auto-draft' ||
		     $postarr['original_post_status'] === 'workflow_pending' ||
		     $_REQUEST['action'] !== 'editpost'
		) {

			return $maybe_empty;
		}

		remove_filter( 'wp_insert_post_empty_content', array( $this, 'create_pending' ), 10 );

		$post_ID = 0;
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'editpost' && isset( $_REQUEST['post_ID'] ) ) {

			$post_ID = $_REQUEST['post_ID'];
		}

		$pending_post_ID = $this->save_pending_post( $postarr, $post_ID );

		// Redirect to new post
		wp_redirect( get_edit_post_link( $pending_post_ID, 'redirect' ) );
		exit();
	}

	/**
	 * Saves a new, pending post.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $postarr Post args.
	 * @param int $post_ID Post ID.
	 *
	 * @return int ID of newly created pending post.
	 */
	private function save_pending_post( $postarr, $post_ID = 0 ) {

		$postarr['post_status'] = 'workflow_pending';
		unset( $postarr['ID'] );

		$pending_post_ID = wp_insert_post( $postarr );

		update_post_meta( $pending_post_ID, 'workflow_is_pending', '1' );
		update_post_meta( $pending_post_ID, 'workflow_user', get_current_user_id() );
		update_post_meta( $pending_post_ID, 'workflow_original', $post_ID );

		if ( $post_ID !== 0 ) {

			add_post_meta( $post_ID, 'workflow_pending_posts', $pending_post_ID );
		}

		add_user_meta( get_current_user_id(), 'workflow_pending_posts', $pending_post_ID );

		return $pending_post_ID;
	}

	/**
	 * Wwen saving a pending post, handle the data.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $data An array of slashed post data.
	 * @param array $postarr An array of sanitized, but otherwise unmodified post data.
	 */
	function handle_pending_save( $data, $postarr ) {

		if ( $postarr['original_post_status'] === 'workflow_pending' ) {

			$data['post_status'] = 'workflow_pending';
		}

		return $data;
	}

	/**
	 * Adds meta boxes for the revision posts.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_revision_meta_boxes() {

		$post_type = get_post_type( get_the_ID() );

		add_meta_box(
			'wfm-revision-publish-actions',
			__( 'Revision', 'workflow-manager' ),
			array( $this, 'metabox_revision_publish_actions' ),
			$post_type,
			'side',
			'high'
		);

		remove_meta_box(
			'submitdiv',
			$post_type,
			'side'
		);

		remove_meta_box(
			'pageparentdiv',
			$post_type,
			'side'
		);
	}

	/**
	 * Outputs the metabox for revision actions.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function metabox_revision_publish_actions() {

		global $action;

		$post = get_post();

		$post_type        = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$can_publish      = current_user_can( $post_type_object->cap->publish_posts );

		if ( wfm_current_post_is_revision() ) {

			$submit_text = __( 'Update Revision', 'workflow-manager' );

		} else {

			$submit_text = __( 'Submit for Review', 'workflow-manager' );
		}
		?>
        <div class="submitbox" id="submitpost">

            <div id="minor-publishing">

                <div style="display:none;">
					<?php submit_button( __( 'Save', 'workflow-manager' ), '', 'save' ); ?>
                </div>

                <div id="minor-publishing-actions">
					<?php if ( is_post_type_viewable( $post_type_object ) ) : ?>
                        <div id="preview-action">
							<?php
							$preview_link        = esc_url( get_preview_post_link( $post ) );
							$preview_button_text = __( 'Preview', 'workflow-manager' );

							$preview_button = sprintf( '%1$s<span class="screen-reader-text"> %2$s</span>',
								$preview_button_text,
								/* translators: accessibility text */
								__( '(opens in a new window)', 'workflow-manager' )
							);
							?>
                            <a class="preview button" href="<?php echo $preview_link; ?>"
                               target="wp-preview-<?php echo (int) $post->ID; ?>"
                               id="post-preview"><?php echo $preview_button; ?></a>
                            <input type="hidden" name="wp-preview" id="wp-preview" value=""/>
                        </div>
					<?php endif; ?>
                    <div class="clear"></div>
                </div>
            </div>

            <div id="major-publishing-actions">

				<?php if ( wfm_current_post_is_revision() ) : ?>
                    <div id="delete-action">
                        <a class="submitdelete deletion"
                           href="<?php echo get_delete_post_link( $post->ID, '', true ); ?>">
							<?php _e( 'Delete', 'workflow-manager' ); ?>
                        </a>
                    </div>
				<?php endif; ?>

                <div id="publishing-action">
                    <span class="spinner"></span>
                    <input name="original_publish" type="hidden" id="original_publish"
                           value="<?php esc_attr_e( 'Submit for Review', 'workflow-manager' ) ?>"/>
					<?php submit_button( $submit_text, 'primary large', 'publish', false ); ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
		<?php
	}

	/**
	 * Outputs admin notices if current post is a revision.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function notice_post_revision() {

		$original         = get_post_meta( get_the_ID(), 'workflow_original', true );
		$post_type        = get_post_type( $original );
		$post_type_object = get_post_type_object( $post_type );
		?>
        <div class="notice notice-info">
            <p>
                <strong>
					<?php
					printf(
						__( 'This is a pending revision of the %1$s "%2$s".', 'workflow-manager' ),
						$post_type_object->labels->singular_name,
						get_the_title( $original )
					);
					?>
                </strong>
            </p>
        </div>
		<?php
	}

	/**
	 * Notice for trying to edit someone else's revision.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function notice_restricted_revision() {

		if ( ! get_transient( 'wfm_restricted_revision_' . get_current_user_id() ) ) {

			return;
		}

		delete_transient( 'wfm_restricted_revision_' . get_current_user_id() );

		?>
        <div class="notice notice-error">
            <p>
				<?php _e( 'Sorry, cannot edit someone else\'s revision.', 'workflow-manager' ); ?>
            </p>
        </div>
		<?php
	}
}

/**
 * Returns the administrative instance of the plugin.
 *
 * @since {{VERSION}}
 *
 * @return WorkflowManager_PostLimitations
 */
function WorkflowManager_PostLimitations() {

	static $instance = null;

	if ( $instance === null ) {

		$instance = new WorkflowManager_PostLimitations();
	}

	return $instance;
}

// Instantiate admin
WorkflowManager_PostLimitations();