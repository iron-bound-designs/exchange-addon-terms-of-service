<?php
/**
 * Add-on Settings Page
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace ITETOS;

/**
 * Class Settings
 *
 * @package ITEGMS
 */
class Settings {

	/**
	 * @var string $status_message will be displayed if not empty
	 */
	private $status_message;

	/**
	 * @var string $error_message will be displayed if not empty
	 */
	private $error_message;

	/**
	 * @var array
	 */
	private $form_values;

	/**
	 * Settings page.
	 */
	const PAGE = 'it-exchange-addons';

	/**
	 * Short slug.
	 */
	const SHORT = 'itetos';

	/**
	 * Display the settings page.
	 *
	 * @since 1.0
	 */
	public static function display() {
		$settings = new Settings();
		$settings->print_settings_page();
	}

	/**
	 * Initialize the addon settings.
	 *
	 * @since 1.0
	 */
	public static function init() {

		add_filter( 'it_storage_get_defaults_exchange_addon_' . self::SHORT, function ( $defaults ) {

			$defaults['terms'] = '';

			return $defaults;
		} );
	}

	/**
	 * Get an option.
	 *
	 * @since 1.0
	 *
	 * @param string $field
	 *
	 * @return mixed|null
	 */
	public static function get( $field = '' ) {

		$options = it_exchange_get_option( 'addon_' . self::SHORT );

		if ( empty( $field ) ) {
			return $options;
		}

		if ( isset( $options[ $field ] ) ) { // if the field exists with that name just return it
			return $options[ $field ];
		} else if ( strpos( $field, "." ) !== false ) { // if the field name was passed using array dot notation
			$pieces  = explode( '.', $field );
			$context = $options;
			foreach ( $pieces as $piece ) {
				if ( ! is_array( $context ) || ! array_key_exists( $piece, $context ) ) {
					// error occurred
					return null;
				}
				$context = &$context[ $piece ];
			}

			return $context;
		} else {
			return null; // we didn't find the data specified
		}
	}

	/**
	 * Class constructor
	 *
	 * Sets up the class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$page  = empty( $_GET['page'] ) ? false : $_GET['page'];
		$addon = empty( $_GET['add-on-settings'] ) ? false : $_GET['add-on-settings'];

		if ( empty( $_POST ) || ! is_admin() ) {
			return;
		}

		if ( self::PAGE != $page || Plugin::ADD_ON != $addon ) {
			return;
		}

		add_action( 'it_exchange_save_add_on_settings_' . self::SHORT, array(
			$this,
			'save'
		) );
		do_action( 'it_exchange_save_add_on_settings_' . self::SHORT );
	}

	/**
	 * Prints settings page
	 *
	 * @since 1.0
	 */
	function print_settings_page() {
		$settings          = it_exchange_get_option( 'addon_' . self::SHORT, true );
		$this->form_values = empty( $this->error_message ) ? $settings : \ITForm::get_post_data();

		$form_options = array(
			'id'     => 'it-exchange-add-on-' . self::SHORT . '-settings',
			'action' => 'admin.php?page=' . self::PAGE . '&add-on-settings=' . Plugin::ADD_ON,
		);

		$form = new \ITForm( $this->form_values, array(
			'prefix' => 'it-exchange-add-on-' . self::SHORT
		) );

		if ( ! empty ( $this->status_message ) ) {
			\ITUtility::show_status_message( $this->status_message );
		}
		if ( ! empty( $this->error_message ) ) {
			\ITUtility::show_error_message( $this->error_message );
		}
		?>
		<div class="wrap">
			<h2><?php _e( 'Terms of Service Settings', Plugin::SLUG ); ?></h2>

			<?php do_action( 'it_exchange_' . self::SHORT . '_settings_page_top' ); ?>
			<?php do_action( 'it_exchange_addon_settings_page_top' ); ?>
			<?php $form->start_form( $form_options, 'it-exchange-' . self::SHORT .'-settings' ); ?>
			<?php do_action( 'it_exchange_' . self::SHORT . '_settings_form_top', $form ); ?>
			<?php $this->get_form_table( $form, $this->form_values ); ?>
			<?php do_action( 'it_exchange_' . self::SHORT . '_settings_form_bottom', $form ); ?>

			<p class="submit">
				<?php $form->add_submit( 'submit', array(
					'value' => __( 'Save Changes', Plugin::SLUG ),
					'class' => 'button button-primary button-large'
				) ); ?>
			</p>

			<?php $form->end_form(); ?>
			<?php $this->inline_scripts(); ?>
			<?php do_action( 'it_exchange_' . self::SHORT . '_settings_page_bottom' ); ?>
			<?php do_action( 'it_exchange_addon_settings_page_bottom' ); ?>
		</div>

		<?php
	}

	/**
	 * Render the settings table
	 *
	 * @since 1.0
	 *
	 * @param \ITForm $form
	 * @param array   $settings
	 */
	function get_form_table( $form, $settings = array() ) {
		if ( ! empty( $settings ) ) {
			foreach ( $settings as $key => $var ) {
				$form->set_option( $key, $var );
			}
		}

		?>

		<div class="it-exchange-addon-settings it-exchange-<?php echo esc_attr( self::SHORT ); ?>-addon-settings">

			<h3><?php _e( "General", Plugin::SLUG ); ?></h3>

			<div class="terms-container">
				<label for="terms"><?php _e( "Terms of Service", Plugin::SLUG ); ?></label>

				<p class="description">
					<?php _e( "Terms your customers must agree to before checking out.", Plugin::SLUG ); ?>
				</p>

				<?php
				wp_editor( $settings['terms'], 'terms', array(
					'textarea_name' => 'it-exchange-add-on-' . self::SHORT . '-terms',
					'textarea_rows' => 10,
					'textarea_cols' => 30,
					'editor_class'  => 'large-text',
					'media_buttons' => false
				) );

				$form->get_text_area( 'terms', array(
					'rows'  => 10,
					'cols'  => 30,
					'class' => 'large-text'
				) ); ?>
			</div>

		</div>

		<?php
	}

	/**
	 * Render inline scripts.
	 *
	 * @since 1.0
	 */
	function inline_scripts() {

	}

	/**
	 * Save settings.
	 *
	 * @since 1.0
	 */
	function save() {
		$defaults = it_exchange_get_option( 'addon_' . self::SHORT );

		$new_values = wp_parse_args( \ITForm::get_post_data(), $defaults );
		// Check nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'it-exchange-' . self::SHORT . '-settings' ) ) {
			$this->error_message = __( 'Error. Please try again', Plugin::SLUG );

			return;
		}

		/**
		 * Filter the settings errors before saving.
		 *
		 * @since 1.0
		 *
		 * @param string[] $errors     Errors
		 * @param array    $new_values Mixed
		 */
		$errors = apply_filters( 'it_exchange_add_on_' . self::SHORT . '_validate_settings',
			$this->get_form_errors( $new_values, $defaults ), $new_values );

		if ( ! $errors && it_exchange_save_option( 'addon_' . self::SHORT, $new_values ) ) {
			$this->status_message = __( 'Settings saved.', Plugin::SLUG );
		} else if ( $errors ) {
			$errors              = implode( '<br />', $errors );
			$this->error_message = $errors;
		} else {
			$this->error_message = __( 'Settings not saved.', Plugin::SLUG );
		}
	}

	/**
	 * Validates for values.
	 *
	 * @since 1.0
	 *
	 * @param array $values
	 * @param array $old_values
	 *
	 * @return array
	 */
	public function get_form_errors( $values, $old_values ) {
		$errors = array();

		return $errors;
	}
}