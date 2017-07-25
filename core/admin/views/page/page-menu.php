<?php
/**
 * Page menu.
 *
 * @since {{VERSION}}
 *
 * @var array $pages
 * @var string $active_page
 */

defined( 'ABSPATH' ) || die();
?>

<nav class="nav-tab-wrapper">
	<?php foreach ( $pages as $page ) : ?>
        <a href="<?php echo admin_url( "tools.php?page=workflows&tab=$page[id]" ); ?>"
           class="nav-tab <?php echo $page['id'] == $active_page ? 'nav-tab-active' : ''; ?>">
			<?php echo esc_html( $page['tab_title'] ); ?>
        </a>
	<?php endforeach; ?>
</nav>