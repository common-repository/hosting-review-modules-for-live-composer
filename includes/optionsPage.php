<?php
/**
 * Created by PhpStorm.
 * User: juslintek
 * Date: 03/10/2017
 * Time: 18:29
 */


class HRMFLCOptionsPage {

	public $plugin = "Hosting Review Modules for Live Composer";
	public $shortname = "hrmflc";
	public $version = "0.0.1";

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options = [ 'powered_by_switch' => 'no' ];

	public function __construct( $plugin, $shortname, $version ) {
		$this->plugin    = $plugin;
		$this->shortname = $shortname;
		$this->version   = $version;

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
		}
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			$this->plugin . ' Settings',
			strtoupper( $this->shortname ) . ' Settings',
			'manage_options',
			$this->shortname . '-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( $this->shortname . '_main' );
		?>
        <div class="wrap">
            <form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( $this->shortname . '_main_group' );
				do_settings_sections( $this->shortname . '-setting-admin' );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			$this->shortname . '_main_group', // Option group
			$this->shortname . '_main', // Option name
			array( $this, 'sanitize_switch' ) // Sanitize
		);

		add_settings_section(
			$this->shortname . '_setting_section', // ID
			$this->plugin . ' Settings', // Title
			array( $this, 'print_section_info' ), // Callback
			$this->shortname . '-setting-admin' // Page
		);

		add_settings_field(
			'powered_by_switch', // ID
			__( 'Show Powered By', $this->shortname ), // Title
			array( $this, 'turn_off_powered_by_callback' ), // Callback
			$this->shortname . '-setting-admin', // Page
			$this->shortname . '_setting_section' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize_switch( $input ) {
		$new_input = [];
		foreach ( $input as $input_key => $input_val ) {
			if ( $input_key == 'powered_by_switch' && ! in_array( $input_val, [ 'no', 'yes' ] ) ) {
                add_settings_error(__( 'Show Powered By', $this->shortname ), 'powered_by_switch', __('Show Powered By value is not from fields', $this->shortname));
			}
			$new_input[ sanitize_key( $input_key ) ] = sanitize_text_field( $input_val );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		print 'Enter your settings below:';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function turn_off_powered_by_callback() {
		$option_id         = 'powered_by_switch';
		$option_group_name = $this->shortname . '_main';
		$option_value      = $this->options[ $option_id ];

		vprintf(
			'<label for="%s">Yes <input type="radio" id="%s" name="%s[%s]" value="yes" %s /></label><label for="%s">No <input type="radio" id="%s" name="%s[%s]" value="no" %s /></label>',
			[
				$option_id,
				$option_id,
				$option_group_name,
				$option_id,
				$option_value == 'yes' ? esc_attr( 'checked="checked"' ) : '',
				$option_id . '_on',
				$option_id . '_on',
				$option_group_name,
				$option_id,
				$option_value == 'no' ? esc_attr( 'checked="checked"' ) : ''
			]
		);
	}
}