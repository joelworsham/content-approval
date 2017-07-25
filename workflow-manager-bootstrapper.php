<?php
/**
 * Bootstrapper for the plugin Workflow Manager.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 */

defined( 'ABSPATH' ) || die();

/**
 * Class WorkflowManager_Bootstrapper
 *
 * Bootstrapper for the plugin Workflow Manager.
 *
 * @since {{VERSION}}
 *
 * @package WorkflowManager
 */
class WorkflowManager_Bootstrapper {

	/**
	 * Notices to show if cannot load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * WorkflowManager_Bootstrapper constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'maybe_load' ), 5 );
	}

	/**
	 * Maybe loads the plugin.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function maybe_load() {

		$php_version = phpversion();
		$wp_version  = get_bloginfo( 'version' );

		// Minimum PHP version
		if ( version_compare( $php_version, '5.3.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum PHP version of 5.3.0 required. Current version is %s. Please contact your system administrator to upgrade PHP to its latest version.', 'workflow-manager' ),
				$php_version
			);
		}

		// Minimum WordPress version
		// TODO Figure out actual minimum version
		if ( version_compare( $wp_version, '4.0.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum WordPress version of 4.0.0 required. Current version is %s. Please contact your system administrator to upgrade WordPress to its latest version.', 'workflow-manager' ),
				$wp_version
			);
		}

		// Don't load and show errors if incompatible environment.
		if ( ! empty( $this->notices ) ) {

			add_action( 'admin_notices', array( $this, 'notices' ) );

			return;
		}

		$this->load();
	}

	/**
	 * Loads the plugin.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function load() {

		WorkflowManager();
	}

	/**
	 * Shows notices on failure to load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function notices() {
		?>
		<div class="notice error">
			<p>
				<?php
				printf(
					__( '%sWorkflow Manager%s could not load because of the following errors:', 'workflow-manager' ),
					'<strong>',
					'</strong>'
				);
				?>
			</p>

			<ul>
				<?php foreach ( $this->notices as $notice ) : ?>
					<li>
						<?php echo $notice; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

/**
 * Gets the main plugin file instance.
 *
 * @since {{VERSION}}
 *
 * @return WorkflowManager
 */
function WorkflowManager() {

	return WorkflowManager::instance();
}