<?php
/**
 * CV & Blog theme setup.
 *
 * @package cv-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'post-thumbnails' );
		// Load the theme stylesheet (including dark mode) inside the block editor too.
		add_editor_style( 'style.css' );
	}
);

add_action(
	'init',
	function () {
		register_block_pattern_category(
			'cv-blog',
			array( 'label' => __( 'CV', 'cv-blog' ) )
		);
	}
);

// Use the logo as the favicon until a Site Icon is set in Customizer / Settings.
add_action(
	'wp_head',
	function () {
		if ( ! has_site_icon() ) {
			printf(
				'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
				esc_url( get_template_directory_uri() . '/assets/logo.svg' )
			);
		}
	}
);

/**
 * Contact pop-up.
 *
 * The "Let's talk Workday" buttons (class ov-contact-open) open a <dialog> containing a Fluent Forms form.
 * If the plugin or form is missing, or JavaScript is off, the buttons simply go to LinkedIn.
 */
const CV_BLOG_LINKEDIN = 'https://www.linkedin.com/in/oliverholdsworth';

function cv_blog_contact_form_id() {
	$id = (int) apply_filters( 'cv_blog_contact_form_id', 0 ); // Force an ID: add_filter( 'cv_blog_contact_form_id', fn() => 3 );
	if ( $id ) {
		return $id;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'fluentform_forms';
	$wpdb->suppress_errors( true );
	// Prefer a form titled "Contact...", otherwise the oldest form.
	$id = (int) $wpdb->get_var( "SELECT id FROM {$table} WHERE title LIKE 'Contact%' ORDER BY id ASC LIMIT 1" );
	if ( ! $id ) {
		$id = (int) $wpdb->get_var( "SELECT id FROM {$table} ORDER BY id ASC LIMIT 1" );
	}
	$wpdb->suppress_errors( false );
	return $id;
}

add_action(
	'wp_footer',
	function () {
		if ( is_admin() || ! ( is_front_page() || is_page() ) || ! shortcode_exists( 'fluentform' ) ) {
			return;
		}
		$form_id = cv_blog_contact_form_id();
		if ( ! $form_id ) {
			return;
		}
		?>
		<dialog id="ov-contact" class="ov-dialog" aria-labelledby="ov-contact-title">
			<div class="ov-dialog-inner">
				<div class="ov-dialog-head">
					<h2 id="ov-contact-title"><?php esc_html_e( "Let's talk Workday", 'cv-blog' ); ?></h2>
					<button type="button" class="ov-dialog-close" aria-label="<?php esc_attr_e( 'Close', 'cv-blog' ); ?>">&times;</button>
				</div>
				<p class="ov-body2"><?php esc_html_e( "Send me a message and I'll get back to you.", 'cv-blog' ); ?></p>
				<?php echo do_shortcode( sprintf( '[fluentform id="%d"]', $form_id ) ); ?>
				<p class="ov-body2">
					<?php esc_html_e( 'Prefer LinkedIn?', 'cv-blog' ); ?>
					<a href="<?php echo esc_url( CV_BLOG_LINKEDIN ); ?>" rel="noopener"><?php esc_html_e( 'Connect there instead.', 'cv-blog' ); ?></a>
				</p>
			</div>
		</dialog>
		<?php
	},
	5
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$version = wp_get_theme()->get( 'Version' );
		$uri     = get_template_directory_uri();

		wp_enqueue_style( 'cv-blog-style', get_stylesheet_uri(), array(), $version );
		// Print rules load last and only for print, so the CV saves cleanly to PDF.
		wp_enqueue_style( 'cv-blog-print', $uri . '/assets/print.css', array( 'cv-blog-style' ), $version, 'print' );
		wp_enqueue_script(
			'cv-blog',
			$uri . '/assets/theme.js',
			array(),
			$version,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
);
