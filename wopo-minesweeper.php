<?php
/**
 * Plugin Name:       WoPo Minesweeper
 * Plugin URI:        https://wopoweb.com/contact-us/
 * Description:       Web Based Minesweeper game online
 * Version:           1.2.0
 * Requires at least: 5.2
 * Requires PHP:      7.1
 * Author:            WoPo Web
 * Author URI:        https://wopoweb.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wopo-minesweeper
 * Domain Path:       /languages
 */

function wopomi_get_app_url(){
    return plugins_url('html/app/index.html',__FILE__);
}

add_action('wp_enqueue_scripts', 'wopomi_enqueue_scripts');

function wopomi_enqueue_scripts(){
    global $post;
    $options = get_option( 'wopo_minesweeper_options', array('style' => 'XP') );
    
    $is_shortcode = intval(has_shortcode( $post->post_content, 'wopo-minesweeper'));
    if ((function_exists('wopopp_add_drawing_button') && is_singular()) || $is_shortcode){
        wp_enqueue_style('XP',plugins_url( '/assets/css/'. $options['style'] .'.css', __FILE__ ));
        wp_enqueue_style('wopo-minesweeper',plugins_url( '/assets/css/main.css', __FILE__ ));
        wp_enqueue_script('wopo-minesweeper', plugins_url( '/assets/js/main.js', __FILE__ ),array('jquery'));
        wp_localize_script( 'wopo-minesweeper', 'wopoSolitaire', array(
            'app_url' => wopomi_get_app_url(),
            'is_shortcode' => $is_shortcode,
        ) ); 
        do_action('wopo_minesweeper_enqueue_scripts');
    }
}

add_shortcode('wopo-minesweeper', 'wopo_minesweeper_shortcode');
function wopo_minesweeper_shortcode( $atts = [], $content = null) {
    ob_start();?>
    <div id="wopo_minesweeper_window" class="window">
        <div class="title-bar">
            <div class="title-bar-text"><?php echo __('WoPo Minesweeper - Web Based game online','wopo-minesweeper') ?></div>
            <div class="title-bar-controls">
            <button class="btn-minimize" aria-label="Minimize"></button>
            <button class="btn-maximize" aria-label="Maximize"></button>
            <button class="btn-close" aria-label="Close"></button>
            </div>
        </div>
        <div class="window-body">
            <iframe id="wopo_minesweeper"></iframe>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    return $content;
}

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function wopomi_settings_init() {
	// Register a new setting for "wporg" page.
	register_setting( 'wopo-minesweeper', 'wopo_minesweeper_options' );

	// Register a new section in the "wporg" page.
	add_settings_section(
		'wopomi_section_developers',
		__( 'WoPo Web', 'wopo-minesweeper' ), 'wopomi_section_developers_callback',
		'wopo-minesweeper'
	);

	// Register a new field in the "wporg_section_developers" section, inside the "wporg" page.
	add_settings_field(
		'style', // As of WP 4.6 this value is used only internally.
		                        // Use $args' label_for to populate the id inside the callback.
			__( 'Windows Style', 'wopo-minesweeper' ),
		'wopomi_field_stype_cb',
		'wopo-minesweeper',
		'wopomi_section_developers',
		array(
			'label_for'         => 'style',
			'class'             => 'wopo_minesweeper_style',
			'wopomi_custom_data' => 'custom',
		)
	);
}

/**
 * Register our wporg_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'wopomi_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function wopomi_section_developers_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>">
		<?php esc_html_e( 'Thank you for using my plugin.  If you encounter any issues or have suggestions for my plugin, you can contact me at the following link:', 'wopo-minesweeper' ); ?>
		<a target="_blank" href="https://wopoweb.com/contact-us/">https://wopoweb.com/contact-us/</a>
	</p>
	<?php
}

/**
 * Pill field callbakc function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function wopomi_field_stype_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'wopo_minesweeper_options',array('style' => 'XP') );
	?>
	<select
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			data-custom="<?php echo esc_attr( $args['wopomi_custom_data'] ); ?>"
			name="wopo_minesweeper_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
		<option value="XP" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'XP', false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'XP', 'wopo-minesweeper' ); ?>
		</option>
 		<option value="98" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '98', false ) ) : ( '' ); ?>>
			<?php esc_html_e( '98', 'wopo-minesweeper' ); ?>
		</option>
	</select>
	<p class="description">
		<?php esc_html_e( 'You can change your WoPo Minesweeper windows style', 'wopo-minesweeper' ); ?>
	</p>
	<?php
}

/**
 * Add the top level menu page.
 */
function wopomi_options_page() {
	add_menu_page(
		'WoPo Minesweeper',
		'WoPo Minesweeper',
		'manage_options',
		'wopo-minesweeper',
		'wopomi_options_page_html',
		'dashicons-location-alt'
	);
}


/**
 * Register our wporg_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'wopomi_options_page' );


/**
 * Top level menu callback function
 */
function wopomi_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'wopomi_messages', 'wopomi_message', __( 'Settings Saved', 'wopo-minesweeper' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'wopomi_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg"
			settings_fields( 'wopo-minesweeper' );
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections( 'wopo-minesweeper' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}