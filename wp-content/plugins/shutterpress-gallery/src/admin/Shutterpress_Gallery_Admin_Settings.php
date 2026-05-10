<?php
/**
 * Handles the registration and rendering of the admin settings and help pages
 * for the ShutterPress Gallery plugin.
 *
 * This class manages the plugin settings pages, including general options,
 * breakpoints, slug, tracking, and debug controls. It also builds the Help page
 * with resource links, signup forms, and Pro upgrade information. All settings
 * are registered with WordPress using the Settings API.
 *
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @since     1.0.0
 */

namespace Shutterpress\Gallery;

if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Defines the admin settings interface for ShutterPress Gallery.
 *
 * Registers plugin settings, renders settings and help screens, and provides
 * helper methods to output form fields and tabbed interfaces. Also includes
 * debugging integration and tracking identifiers for upgrade prompts.
 *
 * @since   1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Admin
 */
class Shutterpress_Gallery_Admin_Settings {

	private string $distinct_id;
	protected Shutterpress_Gallery_Tracking $tracking;
	protected Shutterpress_Gallery_Debug $debug;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->tracking = new Shutterpress_Gallery_Tracking();
		$this->debug    = new Shutterpress_Gallery_Debug();
	}

	/**
	 * Register the settings page
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_register_settings_page() {
		$this->distinct_id = $this->tracking->get_install_id();
		add_submenu_page(
			'edit.php?post_type=shutterpress-gallery',
			esc_html__( 'Shutterpress Gallery Settings', 'shutterpress-gallery' ),
			esc_html__( 'Settings', 'shutterpress-gallery' ),
			'manage_options',
			'sp-gallery-settings',
			array( $this, 'sp_gallery_settings_page_html' ),
			90
		);

		add_submenu_page(
			'edit.php?post_type=shutterpress-gallery',
			esc_html__( 'Shutterpress Gallery Help', 'shutterpress-gallery' ),
			esc_html__( 'Help', 'shutterpress-gallery' ),
			'manage_options',
			'sp-gallery-help',
			array( $this, 'help_page_html' ),
			95
		);

		if ( ! is_plugin_active( 'shutterpress-gallery-pro/shutterpress-gallery-pro.php' ) ) {
			add_submenu_page(
				'edit.php?post_type=shutterpress-gallery',
				esc_html__( 'Upgrade to Pro', 'shutterpress-gallery' ),
				esc_html__( 'Upgrade to Pro', 'shutterpress-gallery' ),
				'manage_options',
				'https://shutterpress.io/buy-now?utm_source=plugin_upgrade_link&utm_medium=plugin&utm_campaign=shutterpress_plugin&install_id=' . $this->distinct_id,
				'',
				99
			);
		}
		$this->debug->log( 'Settings page registered' );
	}

	/**
	 * Render the html for the settings pags
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_settings_page_html() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tabs = array(
			'general' => array(
				'title'    => esc_html__( 'General', 'shutterpress-gallery' ),
				'callback' => array( $this, 'sp_gallery_render_general_tab_content' ),
			),
		);

		$tabs = apply_filters( 'sp_gallery_settings_tabs', $tabs );

		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
		$this->debug->log( 'Rendering settings page', array( 'active_tab' => $active_tab ) );

		?>
		<div class="wrap sp-gallery-settings-page">
			<h1><?php echo esc_html__( 'Shutterpress Gallery Settings', 'shutterpress-gallery' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $id => $tab ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'tab', esc_attr( $id ), admin_url( 'admin.php?page=sp-gallery-settings' ) ) ); ?>" class="nav-tab<?php echo $id === $active_tab ? ' nav-tab-active' : ''; ?>">
						<?php echo esc_html( $tab['title'] ); ?>
					</a>
				<?php endforeach; ?>
			</h2>
	
			<?php foreach ( $tabs as $id => $tab ) : ?>
				<div id="<?php echo esc_attr( $id ); ?>" class="tab-content" style="<?php echo $id === $active_tab ? '' : 'display:none;'; ?>">
					<?php

					if ( ! is_callable( $tab['callback'] ) ) {
						$this->debug->log( 'Invalid tab callback', array( 'tab' => $id ), 'WARNING' );
						continue;
					}

					call_user_func( $tab['callback'] );
					?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Renders the settings fields for the Shutterpress Gallery plugin.
	 *
	 * This function is responsible for outputting the necessary hidden fields and nonce
	 * for the settings form, ensuring proper form submission and security
	 *
	 * Replace the WordPress settings_fields() function with a custom implementation to return the tab name
	 *
	 * @param string $option_group The option group for the settings.
	 * @param string $tab_name     The name of the current tab.
	 * @param string $form_action  The form action.
	 *
	 * @return void
	 */
	public function sp_gallery_settings_fields( $option_group, $tab_name, $form_action ) {

		$tab_name = sanitize_key( $tab_name );

		echo "<input type='hidden' name='option_page' value='" . esc_attr( $option_group ) . "' />";

		echo "<input type='hidden' name='action' value='" . esc_attr( $form_action ) . "' />";

		wp_nonce_field( "{$option_group}-options", '_wpnonce', false );

		$referer_url = esc_url(
			add_query_arg(
				array(
					'post_type' => 'shutterpress-gallery',
					'page'      => 'sp-gallery-settings',
					'tab'       => $tab_name,
				),
				admin_url( 'edit.php' )
			)
		);

		echo '<input type="hidden" name="_wp_http_referer" value="' . esc_attr( $referer_url ) . '" />';
	}

	/**
	 * Renders the settings form for the Shutterpress Gallery plugin.
	 *
	 * This function is responsible for rendering the settings form, including the necessary
	 * hidden fields, nonce, and submit button. It takes in parameters to customize the
	 * form's behavior, such as the option group, settings section callback, and submit
	 * button text.
	 *
	 * @param string $option_group     The option group for the settings.
	 * @param string $section_callback The callback function to render the settings section.
	 * @param string $submit_text      The text to display on the submit button.
	 * @param string $tab_name         The name of the current tab.
	 * @param string $form_action      The form action.
	 *
	 * @return void
	 */
	public function sp_gallery_render_settings_form( $option_group, $section_callback, $submit_text, $tab_name, $form_action = 'update' ) {
		?>
		<form action="options.php" method="post">
			<?php

			$this->sp_gallery_settings_fields( $option_group, $tab_name, $form_action );

			do_settings_sections( $section_callback );

			submit_button( $submit_text );
			?>
		</form>
		<?php
	}

	/**
	 * Renders the general settings tab content for the Shutterpress Gallery plugin.
	 *
	 * This function is responsible for rendering the settings form for the general tab
	 * of the Shutterpress Gallery plugin. It calls the `sp_gallery_render_settings_form()`
	 * method to generate the form, passing in the appropriate parameters for the general
	 * tab.
	 *
	 * @return void
	 */
	public function sp_gallery_render_general_tab_content() {
		$this->sp_gallery_render_settings_form(
			'sp_gallery_options_group',
			'sp-gallery-settings',
			esc_html__( 'Save Settings', 'shutterpress-gallery' ),
			'general'
		);
	}

	/**
	 * Initializes the settings for the Shutterpress Gallery plugin.
	 *
	 * This function registers the various settings used by the plugin, including the
	 * layout, lightbox, column, and breakpoint settings. It also adds a new settings
	 * section to the WordPress admin settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function sp_gallery_settings_init() {
		register_setting(
			'sp_gallery_options_group',
			'sp_gallery_images_per_page',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'show_in_rest'      => false,
			)
		);

		register_setting(
			'sp_gallery_options_group',
			'sp_gallery_breakpoint_tablet',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'show_in_rest'      => false,
			)
		);

		register_setting(
			'sp_gallery_options_group',
			'sp_gallery_breakpoint_mobile',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'show_in_rest'      => false,
			)
		);

		register_setting(
			'sp_gallery_options_group',
			'sp_gallery_slug',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => false,
			)
		);

		register_setting(
			'sp_gallery_options_group',
			'sp_gallery_tracking_allowed',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => false,
			)
		);

		register_setting(
			'sp_gallery_options_group',
			'sp_gallery_debug',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => false,
			)
		);

		add_settings_section(
			'sp_gallery_settings_section',
			esc_html__( 'ShutterPress General Settings', 'shutterpress-gallery' ),
			array( $this, 'sp_gallery_section_callback' ),
			'sp-gallery-settings'
		);

		$this->sp_gallery_add_settings_fields();
	}

	/**
	 * Adds the various settings fields for the Shutterpress Gallery plugin.
	 *
	 * This method is responsible for registering all the settings fields that are
	 * used to configure the behavior of the Shutterpress Gallery plugin. It adds
	 * fields for the gallery layout, lightbox, number of columns, column gap, and
	 * breakpoints.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function sp_gallery_add_settings_fields() {
		$this->sp_gallery_add_images_per_page_option_field();
		$this->sp_gallery_add_breakpoint_field( 'tablet', esc_html__( 'Tablet Breakpoint (px)', 'shutterpress-gallery' ) );
		$this->sp_gallery_add_breakpoint_field( 'mobile', esc_html__( 'Mobile Breakpoint (px)', 'shutterpress-gallery' ) );
		$this->sp_gallery_add_slug_option_field();
		$this->sp_gallery_add_tracking_option_field();
		if ( ! is_multisite() || is_network_admin() || is_super_admin() ) {
			$this->sp_gallery_add_debug_option_field();
		}
	}

	/**
	 * Adds a breakpoint field to the settings page.
	 *
	 * @param string $device The device name (e.g., 'tablet', 'mobile').
	 * @param string $label  The label for the field.
	 *
	 * @return void
	 */
	private function sp_gallery_add_breakpoint_field( $device, $label ) {

		$device = sanitize_key( $device );

		$callback_method = "sp_gallery_breakpoint_{$device}_field_callback";

		if ( ! method_exists( $this, $callback_method ) ) {
			return;
		}

		$callback = array( $this, $callback_method );
		if ( ! is_callable( $callback ) ) {
			return;
		}

		add_settings_field(
			"sp_gallery_breakpoint_{$device}",
			esc_html( $label ),
			$callback,
			'sp-gallery-settings',
			'sp_gallery_settings_section'
		);
	}

	/**
	 * Adds the settings field for the gallery slug.
	 *
	 * Registers a text input on the Settings page that lets an admin define
	 * the base URL segment used for gallery archives and single gallery pages.
	 * The field is rendered by sp_gallery_slug_callback().
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function sp_gallery_add_slug_option_field() {
		add_settings_field( 'sp_gallery_slug', esc_html__( 'Gallery Slug', 'shutterpress-gallery' ), array( $this, 'sp_gallery_slug_callback' ), 'sp-gallery-settings', 'sp_gallery_settings_section' );
	}

	/**
	 * Adds the settings field for images per page.
	 *
	 * Registers a number input field in the settings page to allow users to set
	 * how many images should display per page or per infinite scroll load.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	private function sp_gallery_add_images_per_page_option_field() {
		add_settings_field(
			'sp_gallery_images_per_page',
			esc_html__( 'Images per Page', 'shutterpress-gallery' ),
			array( $this, 'sp_gallery_pages_field_callback' ),
			'sp-gallery-settings',
			'sp_gallery_settings_section'
		);
	}

	/**
	 * Helper function to render breakpoints field for a device
	 *
	 * @since    1.0.0
	 *
	 * @param string $device The device name (e.g., 'desktop', 'tablet', 'mobile').
	 * @param int    $default_value The default value for the breakpoint.
	 * @return void
	 */
	private function sp_gallery_render_breakpoint_field( $device, $default_value ) {

		$device_sanitized = sanitize_key( $device );
		$breakpoint       = get_option( 'sp_gallery_breakpoint_' . $device_sanitized, $default_value );

		$breakpoint = is_numeric( $breakpoint ) ? intval( $breakpoint ) : $default_value;
		if ( $breakpoint < 320 || $breakpoint > 1920 ) {
			$breakpoint = $default_value;
		}

		printf(
			'<input type="number" name="%s" value="%s" min="320" max="1920">',
			esc_attr( "sp_gallery_breakpoint_{$device_sanitized}" ),
			absint( $breakpoint )
		);

		printf(
			'<p class="description">%s %s %s</p>',
			esc_html__( 'Define the breakpoint for', 'shutterpress-gallery' ),
			esc_html( $device_sanitized ),
			esc_html__( '(in pixels)', 'shutterpress-gallery' )
		);
	}

	/**
	 * Helper function to render tracking option field
	 *
	 * @since    1.3.7
	 * @return void
	 */
	private function sp_gallery_add_tracking_option_field() {
		add_settings_field(
			'sp_gallery_tracking_allowed',
			esc_html__( 'Anonymous Usage Tracking', 'shutterpress-gallery' ),
			array( $this, 'sp_gallery_tracking_allowed_callback' ),
			'sp-gallery-settings',
			'sp_gallery_settings_section'
		);
	}

	/**
	 * Helper function to render debug option field
	 *
	 * @since    1.3.7
	 * @return void
	 */
	private function sp_gallery_add_debug_option_field() {
		add_settings_field( 'sp_gallery_debug', esc_html__( 'Enable Debugging', 'shutterpress-gallery' ), array( $this, 'sp_gallery_debug_callback' ), 'sp-gallery-settings', 'sp_gallery_settings_section' );
	}

	/**
	 * Section callback for description
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_section_callback() {
		esc_html_e( 'Set the general settings for ShutterPress Gallery.', 'shutterpress-gallery' );
	}

	/**
	 * Field callback for breakpoints
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_breakpoint_tablet_field_callback() {
		$this->sp_gallery_render_breakpoint_field( 'tablet', 782 );
	}

	/**
	 * Field callback for the breakpoint for mobile devices.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function sp_gallery_breakpoint_mobile_field_callback() {
		$this->sp_gallery_render_breakpoint_field( 'mobile', 480 );
	}

	/**
	 * Adds the settings field for the infinite scroll option.
	 *
	 * Registers a checkbox field in the settings page to allow users to enable or disable
	 * infinite scroll for gallery images.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function sp_gallery_pages_field_callback() {
		$pages = get_option( 'sp_gallery_images_per_page', 50 );

		$pages = absint( $pages );

		printf(
			'<input type="number" name="%s" value="%s" min="0">',
			esc_attr( 'sp_gallery_images_per_page' ),
			absint( $pages )
		);

		printf( '<p class="description">%s</p>', esc_html__( 'How many images per page or per infinite scroll load', 'shutterpress-gallery' ) );
	}

	/**
	 * Callback function for the gallery slug field.
	 *
	 * This function retrieves the current gallery slug option value, or a default value if not set,
	 * and renders an input field for the user to enter a custom slug. It also outputs a description
	 * for the slug field, including an example of the full gallery URL based on the site URL and the
	 * entered slug.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function sp_gallery_slug_callback() {
		$slug = get_option( 'sp_gallery_slug', 'gallery' );

		printf(
			'<input type="text" name="%s" value="%s" class="sp-gallery-slug" />',
			esc_attr( 'sp_gallery_slug' ),
			esc_attr( $slug )
		);

		$site_url = esc_url( site_url() );

		printf(
			'<p>%s</p><p class="description">%s</p>',
			esc_html__( 'Select a slug for the gallery.', 'shutterpress-gallery' ),
			wp_kses(
				sprintf( __( 'Each gallery URL will be %1$s/<strong>%2$s</strong>/gallery-title', 'shutterpress-gallery' ), esc_url( $site_url ), esc_html( $slug ) ),
				array( 'strong' => array() )
			)
		);
	}
	/**
	 * Field callback for the anonymous usage tracking opt-out setting.
	 *
	 * Renders a checkbox allowing users to turn on or off debugging.
	 *
	 * @since 1.4
	 *
	 * @return void
	 */
	public function sp_gallery_debug_callback() {
		$debug_enabled = absint( get_option( 'sp_gallery_debug', 0 ) );
		?>
		<label>
			<input type="checkbox" name="sp_gallery_debug" value="1" <?php checked( $debug_enabled, 1 ); ?>>
			<?php esc_html_e( 'Turn on debugging to log plugin activity to wp-content/shutterpress-debug.log.', 'shutterpress-gallery' ); ?>
		</label>
		<?php
	}

	/**
	 * Field callback for the anonymous usage tracking opt-out setting.
	 *
	 * Renders a checkbox allowing users to opt-in or opt-out of anonymous usage data collection.
	 * When checked, basic plugin usage metrics are collected to help improve the plugin.
	 * If unchecked, no tracking data is sent.
	 *
	 * @since 1.3.7
	 *
	 * @return void
	 */
	public function sp_gallery_tracking_allowed_callback() {
		$tracking_allowed = get_option( 'sp_gallery_tracking_allowed', 1 );
		?>
	<label>
		<input type="checkbox" name="sp_gallery_tracking_allowed" value="1" <?php checked( $tracking_allowed, 1 ); ?>>
		<?php esc_html_e( 'Allow Shutterpress Gallery to collect anonymous usage data to improve the plugin.', 'shutterpress-gallery' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'You can opt out at any time. No personal data is collected.', 'shutterpress-gallery' ); ?>
		<a href="https://shutterpress.io/data-collection/" target="_blank"><?php esc_html_e( 'Learn more.', 'shutterpress-gallery' ); ?></a>
	</p>
		<?php
	}

	/**
	 * Add meta boxes to the settings page
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_add_meta_boxes() {
		add_meta_box(
			'sp_gallery_meta_box',
			__( 'SP Meta Box', 'shutterpress-gallery' ),
			array( $this, 'sp_gallery_meta_box_callback' ),
			'settings_page_sp-gallery-settings',
			'normal',
			'default'
		);
	}

	/**
	 * Meta box content
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_meta_box_callback() {

		$value = get_option( 'sp_gallery_meta_value', '' );

		printf(
			'<label for="%s">%s</label>',
			esc_attr( 'sp_gallery_meta_field' ),
			esc_html__( 'Meta Field', 'shutterpress-gallery' )
		);

		printf(
			'<input type="text" id="%s" name="%s" value="%s">',
			esc_attr( 'sp_gallery_meta_field' ),
			esc_attr( 'sp_gallery_meta_value' ),
			esc_attr( (string) $value )
		);
	}

	/**
	 * Render the html for the help pags
	 *
	 * @since    1.5.0
	 * @return void
	 */
	public function help_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$current_user = wp_get_current_user();
		$email        = sanitize_email( $current_user ? $current_user->user_email : '' );
		$pro_active   = is_plugin_active( 'shutterpress-gallery-pro/shutterpress-gallery-pro.php' );

		$output  = $this->help_wrapper_open();
		$output .= $this->help_resources_card( $pro_active );
		$output .= $this->help_signup_card( $email );

		if ( ! $pro_active ) {
			$output .= $this->help_pro_card();
		}

		$output .= $this->help_wrapper_close();

		echo $output;
	}



	/**
	 * Opens the HTML wrapper for the Help page layout in the admin area.
	 *
	 * Outputs the opening markup for the ShutterPress Gallery Help screen,
	 * including the main wrapper and grid container.
	 *
	 * @since 1.7.0
	 *
	 * @return string HTML markup for the opening wrapper.
	 */
	private function help_wrapper_open() {
		return '<div class="wrap sp-help">'
			. '<h1>' . esc_html__( 'ShutterPress Gallery Help', 'shutterpress-gallery' ) . '</h1>'
			. '<div class="sp-help-grid">';
	}


	/**
	 * Closes the HTML wrapper for the Help page layout in the admin area.
	 *
	 * Outputs the closing markup that terminates the Help page wrapper and grid container.
	 *
	 * @since 1.7.0
	 *
	 * @return string HTML markup for the closing wrapper.
	 */
	private function help_wrapper_close() {
		return '</div></div>';
	}



	/**
	 * Renders the Resources card for the Help page.
	 *
	 * Displays links to documentation, support, video tutorials, and community channels.
	 * When Pro is active, also includes a contact support card.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $pro_active Whether ShutterPress Gallery Pro is active.
	 *
	 * @return string HTML markup for the Resources help card.
	 */
	private function help_resources_card( $pro_active ) {
		$out = '
<div class="sp-card sp-card-resources">
  <div class="sp-card-header">
    <h2>' . esc_html__( 'Resources', 'shutterpress-gallery' ) . '</h2>
  </div>
  <div class="sp-card-content">
    <div>
      <h3>Documentation</h3>
      <p>' . esc_html__( 'Find step-by-step guides covering every feature in ShutterPress Gallery, from creating your first gallery to advanced setup and Pro features. The documentation is always kept up to date with new releases.', 'shutterpress-gallery' ) . '</p>
      <p><a class="button button-primary" href="' . esc_url( 'https://shutterpress.io/docs' ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open Documentation', 'shutterpress-gallery' ) . '</a></p>
    </div>';

		if ( $pro_active ) {
			$out .= '
    <div>
      <h3>Contact Us</h3>
      <p>' . esc_html__( 'If you need help or have a question, get in touch with the ShutterPress support team. We’re photographers ourselves and usually reply within 24 hours on weekdays.', 'shutterpress-gallery' ) . '</p>
      <a class="button button-primary" href="' . esc_url( 'https://shutterpress.io/contact-us' ) . '" target="_blank" rel="noopener">' . esc_html__( 'Contact Support', 'shutterpress-gallery' ) . '</a>
    </div>';
		}

		$out .= '
    <div>
      <h3>Video Tutorials</h3>
      <p>' . esc_html__( 'Watch quick video walkthroughs that show how to set up galleries, customize layouts, protect your work, and sell prints. Perfect if you prefer learning visually.', 'shutterpress-gallery' ) . '</p>
      <p><a class="button button-primary" href="' . esc_url( 'https://www.youtube.com/@ShutterPressGallery' ) . '" target="_blank" rel="noopener">' . esc_html__( 'Watch Videos', 'shutterpress-gallery' ) . '</a></p>
    </div>
    <div>
      <h3>Facebook Group</h3>
      <p>' . esc_html__( 'Join the private ShutterPress User Group to connect with fellow photographers, share your site, ask questions, and get early access to new features.', 'shutterpress-gallery' ) . '</p>
      <a class="button button-primary" href="' . esc_url( 'https://www.facebook.com/groups/shutterpressusers/' ) . '" target="_blank" rel="noopener">' . esc_html__( 'Join Facebook Group', 'shutterpress-gallery' ) . '</a>
    </div>
    <div>
      <h3>Discord Group</h3>
      <p>' . esc_html__( 'Hop into the ShutterPress Discord to chat with the team, request features, or show off your latest gallery. Great for quick feedback, ideas, and community hangouts.', 'shutterpress-gallery' ) . '</p>
      <a class="button button-primary" href="' . esc_url( 'https://shutterpress.io/discord' ) . '" target="_blank" rel="noopener">' . esc_html__( 'Join Discord Group', 'shutterpress-gallery' ) . '</a>
    </div>
  </div>
</div>';

		return $out;
	}

	/**
	 * Renders the Mailing List Signup card for the Help page.
	 *
	 * Displays a form allowing users to subscribe to the ShutterPress mailing list
	 * and receive the free photography guide. Pre-fills the email field with the
	 * current user’s email address when available and includes basic spam‑trap and
	 * confirmation checkbox elements for compliance.
	 *
	 * @since 1.7.0
	 *
	 * @param string $email The user’s email address to pre‑populate in the signup form.
	 *
	 * @return string HTML markup for the Signup help card.
	 */
	private function help_signup_card( $email ) {
		$out = '
<div class="sp-card sp-card-signup">
  <div class="sp-card-header">
    <h2>' . esc_html__( 'Signup to our Mailing List', 'shutterpress-gallery' ) . '</h2>
  </div>
  <div class="sp-card-content">
    <div class="sp-card-signup-left">
      <p>' . esc_html__( "Join our mailing list for tips and product updates, as a special bonus we'll send you a copy of our free 65-page guide: ", 'shutterpress-gallery' ) . '<strong>"' . esc_html__( 'How to make money from photography', 'shutterpress-gallery' ) . '"</strong></p>
      <img src="' . esc_url( SP_GALLERY_URL . 'includes/images/make-money-600px.jpeg' ) . '" alt="">
      <div class="sp-form-container">
        <div class="sp-form-success"><p><strong>' . esc_html__( 'Thanks for subscribing, please check your inbox for a confirmation email.', 'shutterpress-gallery' ) . '  </p></strong></div>
        <form id="sp-help-subscribe" action="' . esc_url( 'https://webhook.shutterpress.io/subscribe.php' ) . '" method="post">
          <label for="sp-help-email" class="screen-reader-text">' . esc_html__( 'Email address', 'shutterpress-gallery' ) . '</label>
          <input type="email" id="sp-help-email" name="email" value="' . esc_attr( $email ) . '" required />
          <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" />
          <label class="sp-confirm">
            <input type="checkbox" name="confirm" required>
            ' . esc_html__( 'I confirm I would like to receive occasional emails from ShutterPress', 'shutterpress-gallery' ) . '
          </label>
          <button type="submit" class="button button-primary">' . esc_html__( 'Subscribe', 'shutterpress-gallery' ) . '</button>
        </form>
      </div>
    </div>
  </div>
</div>';

		return $out;
	}

	/**
	 * Renders the Pro Upgrade card for the Help page.
	 *
	 * Displays a promotional card encouraging users to upgrade to ShutterPress Gallery Pro,
	 * outlining key Pro features such as watermarking, password protection, WooCommerce integration,
	 * client galleries, and more. Includes a call‑to‑action button linking to the upgrade page.
	 *
	 * @since 1.7.0
	 *
	 * @return string HTML markup for the Pro Upgrade help card.
	 */
	private function help_pro_card() {
		$out = '
<div class="sp-card sp-card-pro">
  <div class="sp-card-header">
    <h2>Upgrade to ShutterPress Gallery Pro</h2>
  </div>
  <div class="sp-card-content">
    <p>' . esc_html__( 'Take your galleries to the next level with advanced tools designed for professional photographers. ShutterPress Pro helps you protect, deliver, and sell your images effortlessly.', 'shutterpress-gallery' ) . '</p>
    <ul>
      <li><strong>' . esc_html__( 'Image Watermarking - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Add custom watermarks to protect your images.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'Password Protection - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Secure galleries with password protection.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'WooCommerce Integration - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Sell prints and downloads with full WooCommerce integration.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'Private User Galleries - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Create private client galleries with user accounts.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'Right Click Protection - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Prevent image theft by disabling right clicks and image dragging on your galleries.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'PDF Document Creation - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Create pdf documents like license agreements to include in downloads.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'Send Client Emails - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Create private client galleries with user accounts.', 'shutterpress-gallery' ) . '</li>
      <li><strong>' . esc_html__( 'Full Gallery Downloads - ', 'shutterpress-gallery' ) . '</strong>' . esc_html__( 'Download complete galleries as zip files containing the images and any pdf documents.', 'shutterpress-gallery' ) . '</li>
    </ul>
    <p><a class="button button-primary" href="' . esc_url( 'https://shutterpress.io/buy-now?utm_source=plugin_upgrade_link&utm_medium=plugin&utm_campaign=shutterpress_plugin&install_id=' . $this->distinct_id ) . '" target="_blank" rel="noopener">' . esc_html__( 'Upgrade Now', 'shutterpress-gallery' ) . '</a></p>
  </div>
</div>';

		return $out;
	}
}
