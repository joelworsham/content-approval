<?php
/**
 * The list table for Revisions.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin/includes
 */

defined( 'ABSPATH' ) || die();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class WFM_Revisions_ListTable
 *
 * The list table for Revisions.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 * @subpackage WorkflowManager/core/admin/includes
 */
class WFM_Revisions_ListTable extends WP_List_Table {

	/**
	 * Number of items to show per page.
	 *
	 * @since {{VERSION}}
	 *
	 * @var int
	 */
	public $per_page = 30;

	/**
	 * Hidden user rows.
	 *
	 * @since {{VERSION}}
	 *
	 * @var int
	 */
	public $hidden = 0;

	/**
	 * User rows that can be hidden.
	 *
	 * @since {{VERSION}}
	 *
	 * @var int
	 */
	public $hideable = 0;

	/**
	 * WFM_Revisions_ListTable constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'admin_print_footer_scripts', array( $this, 'print_data' ) );

		parent::__construct( array(
			'singular' => __( 'Revision', 'workflow-manager' ),
			'plural'   => __( 'Revisions', 'workflow-manager' ),
		) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since {{VERSION}}
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'title'     => __( 'Title', 'workflow-manager' ),
			'original'  => __( 'Original', 'workflow-manager' ),
			'user'      => __( 'User', 'workflow-manager' ),
			'submitted' => __( 'Submitted On', 'workflow-manager' ),
		);

		/**
		 * Filters the Revision List columns.
		 *
		 * @since {{VERSION}}
		 */
		$columns = apply_filters( 'wfm_revision_list_columns', $columns );

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since {{VERSION}}
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {

		$columns = array(
			'title'     => array( 'title', false ),
			'original'  => array( 'original', false ),
			'user'      => array( 'user', false ),
			'submitted' => array( 'submitted', false ),
		);

		/**
		 * Filters the Revision List sortable columns.
		 *
		 * @since {{VERSION}}
		 */
		$columns = apply_filters( 'wfm_revision_list_sortable_columns', $columns );

		return $columns;
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since {{VERSION}}
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {

		return 'title';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {

		return $item[ $column_name ];
	}

	/**
	 * Renders the title column.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $item Contains all the data of the keys
	 *
	 * @return string Column Name
	 */
	public function column_title( $item ) {

		$link = '<a href="' . get_edit_post_link( $item['ID'] ) . '">';
		$link .= $item['title'];
		$link .= '</a>';

		return $link;
	}

	/**
	 * Renders the original title column.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $item Contains all the data of the keys
	 *
	 * @return string Column Name
	 */
	public function column_original( $item ) {

		$link = '<a href="' . get_edit_post_link( $item['original_ID'] ) . '">';
		$link .= $item['original_title'];
		$link .= '</a>';

		return $link;
	}

	/**
	 *
	 * Get a list of CSS classes for the WP_List_Table table tag.
	 *
	 * @since {{VERSION}}
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {

		$classes = parent::get_table_classes();

		$classes[] = 'wfm-revision-lists';

		return $classes;
	}

	/**
	 * Generates and display row actions links for the list table.
	 *
	 * @since {{VERSION}}
	 *
	 * @param object $item The item being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary Primary column name.
	 *
	 * @return string The row actions HTML, or an empty string if the current column is the primary column.
	 */
	function handle_row_actions( $item, $column_name, $primary ) {

		if ( $primary !== $column_name ) {

			return '';
		}

		$post = get_post( $item['ID'] );

		$can_edit_post = current_user_can( 'edit_post', $post->ID );
		$actions       = array();
		$title         = $item['title'];

		if ( $can_edit_post && 'trash' != $post->post_status ) {

			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				get_edit_post_link( $post->ID ),
				/* translators: %s: post title */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
				__( 'Edit' )
			);
		}

		$actions['approve'] = sprintf(
			'<a href="%s" aria-label="%s" onclick="return confirm(\'%s\')">%s</a>',
			wfm_get_approve_post_link( $post->ID ),
			/* translators: %s: post title */
			esc_attr( sprintf( __( 'Approve &#8220;%s&#8221;' ), $title ) ),
			esc_attr( sprintf( __( 'Are you sure you want to approve &#8220;%s&#8221;? This will be permament.' ), $title ) ),
			__( 'Approve' )
		);

		if ( current_user_can( 'delete_post', $post->ID ) ) {

			$actions['delete'] = sprintf(
				'<a href="%s" class="submitdelete" aria-label="%s" onclick="return confirm(\'%s\')">%s</a>',
				get_delete_post_link( $post->ID, '', true ),
				/* translators: %s: post title */
				esc_attr( sprintf( __( 'Delete &#8220;%s&#8221;' ), $title ) ),
				esc_attr( sprintf( __( 'Are you sure you want to delete &#8220;%s&#8221;? This will be permament.' ), $title ) ),

				__( 'Delete' )
			);
		}

		return $this->row_actions( $actions );
	}

	/**
	 * Performs the query to get the data, in this case users.
	 *
	 * @since {{VERSION}}
	 */
	public function query() {

		$user_args = array(
			'number' => $this->per_page,
			'offset' => $this->per_page * ( $this->get_paged() - 1 ),
			'order'  => isset( $_GET['order'] ) ? $_GET['order'] : 'asc',
		);

		$revisions = wfm_get_current_user_approval_revisions();

		$data = array();

		foreach ( $revisions as $revision ) {

			$user     = new WP_User( get_post_meta( $revision, 'workflow_user', true ) );
			$original = get_post_meta( $revision, 'workflow_original', true );

			$data[] = array(
				'ID'             => $revision,
				'original_ID'    => $original,
				'title'          => get_the_title( $revision ),
				'original_title' => get_the_title( $original ),
				'user'           => $user->display_name,
				'submitted'      => get_the_date( '', $revision ),
			);
		}

		/**
		 * Filters the Gradebook list table data.
		 *
		 * @since {{VERSION}}
		 */
		$data = apply_filters( 'wfm_revision_list_list_table_data', $data );

		return $data;
	}

	/**
	 * Retrieve count of total users with keys
	 *
	 * @since {{VERSION}}
	 */
	public function total_items() {

		if ( ! ( $users = get_users() ) ) {

			return false;
		}

		return count( $users );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since {{VERSION}}
	 */
	public function no_items() {

		_e( 'No revisions to show.', 'workflow-manager' );
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since {{VERSION}}
	 */
	public function prepare_items() {

		// Get and set columns
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable, 'title' );

		$data = $this->query();

		$total_items = count( $data );

		$this->items = $data;

//		$this->set_pagination_args( array(
//				'total_items' => $total_items,
//				'per_page'    => $this->per_page,
//				'total_pages' => ceil( $total_items / $this->per_page ),
//			)
//		);
	}
}