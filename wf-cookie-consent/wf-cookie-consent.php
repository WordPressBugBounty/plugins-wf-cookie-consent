<?php
	/*
	Plugin Name: WF Cookie Consent
	Plugin URI: http://www.wunderfarm.com/plugins/wf-cookie-consent
	Description: The wunderfarm-way to show how your website complies with the EU Cookie Law.
	Version: 1.3
	License: GNU General Public License v2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	Author: wunderfarm
	Author URI: http://www.wunderfarm.com
	*/

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

define( 'WFCOOKIECONSENT_VERSION', '1.3' );
define( 'WFCOOKIECONSENT_BUYMEACOFFEE_URL', 'https://www.buymeacoffee.com/wunderfarm' );

/*
* Enqueue CSS + JS
*/
function wf_cookieconsent_scripts() {
	wp_enqueue_style( 'wf-cookie-consent', plugin_dir_url( __FILE__ ) . 'css/wf-cookie-consent.min.css', array(), WFCOOKIECONSENT_VERSION );

	// Only emit a tiny inline style block when the user actually set custom colors.
	// It just overrides CSS custom properties on the banner container, so default
	// installs ship pure external CSS with no inline styling at all.
	$custom_css = wf_cookieconsent_custom_css();
	if ( $custom_css ) {
		wp_add_inline_style( 'wf-cookie-consent', $custom_css );
	}

	wp_enqueue_script( 'wf-cookie-consent-cookiechoices', plugin_dir_url( __FILE__ ) . 'js/cookiechoices.min.js', array(), WFCOOKIECONSENT_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'wf_cookieconsent_scripts' );


/*
* Build the optional custom-color override (CSS custom properties only).
* Returns an empty string when no custom colors are configured.
*/
function wf_cookieconsent_custom_css() {
	$options = get_option( 'wf_cookieconsent_options' );

	$map = array(
		'wf_color_bg'     => '--wf-cc-bg',
		'wf_color_text'   => '--wf-cc-text',
		'wf_color_accent' => '--wf-cc-accent',
	);

	$declarations = array();
	foreach ( $map as $option_key => $css_var ) {
		$color = isset( $options[ $option_key ] ) ? sanitize_hex_color( $options[ $option_key ] ) : '';
		if ( $color ) {
			$declarations[] = $css_var . ':' . $color;
			// Background also drives the rgba fallback variable; use the solid color.
			if ( $option_key === 'wf_color_bg' ) {
				$declarations[] = '--wf-cc-bg-alpha:' . $color;
			}
		}
	}

	if ( empty( $declarations ) ) {
		return '';
	}

	return '.wf-cc{' . implode( ';', $declarations ) . '}';
}


function wf_cookieconsent_get_options( $language = null ) {

	$options = get_option( 'wf_cookieconsent_options' );
	if ( ! $language ) {
		$language = wf_get_language();
	}

	$data = array(
		'wf_cookietext'  => empty( $options[ $language ]['wf_cookietext'] ) ? null : $options[ $language ]['wf_cookietext'],
		'wf_dismisstext' => empty( $options[ $language ]['wf_dismisstext'] ) ? null : $options[ $language ]['wf_dismisstext'],
		'wf_linktext'    => empty( $options[ $language ]['wf_linktext'] ) ? null : $options[ $language ]['wf_linktext'],
		'wf_linkhref'    => empty( $options[ $language ]['wf_linkhref'] ) ? null : $options[ $language ]['wf_linkhref'],
		'wf_position'    => empty( $options['wf_position'] ) ? 'bottom' : $options['wf_position'],
		'wf_style'       => empty( $options['wf_style'] ) ? 'light' : $options['wf_style'],
		// Stored as '1' / '' ; default to enabled when the option was never saved (old installs).
		'wf_auto_dark'   => ! isset( $options['wf_auto_dark'] ) ? true : ( $options['wf_auto_dark'] === '1' ),
		'language'       => $language,
	);

	switch ( $data['language'] ) {

		case 'de':
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = 'Cookies erleichtern die Bereitstellung unserer Dienste. Mit der Nutzung unserer Dienste erklären Sie sich damit einverstanden, dass wir Cookies verwenden. ';
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'OK';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'Weitere Informationen';
			}
			break;

		case 'it':
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = "I cookie ci aiutano ad erogare servizi di qualità. Utilizzando i nostri servizi, l'utente accetta le nostre modalità d'uso dei cookie.";
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'OK';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'Ulteriori informazioni';
			}
			break;

		case 'fr':
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = 'Les cookies nous permettent de vous proposer nos services plus facilement. En utilisant nos services, vous nous donnez expressément votre accord pour exploiter ces cookies.';
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'OK';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'En savoir plus';
			}
			break;

		case 'nl':
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = 'Cookies helpen ons bij het leveren van onze diensten. Door gebruik te maken van onze diensten, gaat u akkoord met ons gebruik van cookies.';
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'OK';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'Meer informatie';
			}
			break;

		case 'fi':
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = 'Evästeet auttavat meitä palvelujemme toimituksessa. Käyttämällä palvelujamme hyväksyt evästeiden käytön.';
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'Selvä';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'Lisätietoja';
			}
			break;

		case 'hu':
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = 'A weboldalon cookie-kat használunk, amik segítenek minket a lehető legjobb szolgáltatások nyújtásában. Weboldalunk további használatával jóváhagyja, hogy cookie-kat használjunk.';
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'OK';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'További információk';
			}
			break;

		default:
			if ( empty( $data['wf_cookietext'] ) ) {
				$data['wf_cookietext'] = 'Cookies help us deliver our services. By using our services, you agree to our use of cookies.';
			}
			if ( empty( $data['wf_dismisstext'] ) ) {
				$data['wf_dismisstext'] = 'Got it';
			}
			if ( empty( $data['wf_linktext'] ) ) {
				$data['wf_linktext'] = 'Learn more';
			}
			break;
	}
	return $data;
}


/*
* Load cookie consent
*/
function wf_cookieconsent_load() {

	$data = wf_cookieconsent_get_options();
	if ( is_numeric( $data['wf_linkhref'] ) ) {
		$data['wf_linkhref'] = get_page_link( $data['wf_linkhref'] );
	}

	?>
<script type="text/javascript">
	window._wfCookieConsentSettings = <?php echo wp_json_encode( $data ); ?>;
</script>
	<?php
}
add_action( 'wp_footer', 'wf_cookieconsent_load', 100, 1 );


/*
* Admin Page
*/

// add settings link on plugin page
function wf_cookieconsent_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=wf-cookieconsent">Settings</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wf_cookieconsent_settings_link' );


// add the admin options page
function wf_cookieconsent_admin_add_page() {
	add_options_page( 'WF Cookie Consent Settings', 'WF Cookie Consent', 'manage_options', 'wf-cookieconsent', 'wf_cookieconsent_options_page' );
}
add_action( 'admin_menu', 'wf_cookieconsent_admin_add_page' );

// display the admin options page
function wf_cookieconsent_options_page() {

	?>
	<div class="wrap">
		<h2>WF Cookie Consent - Settings</h2>
		<form action="options.php" method="post">
		<?php settings_fields( 'wf_cookieconsent_options' ); ?>
		<?php do_settings_sections( 'wf-cookieconsent' ); ?>
			<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'wf-cookie-consent' ); ?>" />
		</form>
	</div>
	<?php
}

// add the admin settings and such
function wf_cookieconsent_admin_init() {
	$languages = wf_get_languages();
	register_setting( 'wf_cookieconsent_options', 'wf_cookieconsent_options', array( 'sanitize_callback' => 'wf_cookieconsent_sanitize_options' ) );

	$sectionKey = 'plugin_main';
	add_settings_section( $sectionKey, count( $languages ) > 1 ? esc_html__( 'General settings', 'wf-cookie-consent' ) : '', '', 'wf-cookieconsent' );

	add_settings_field(
		'wf_position',
		esc_html__( 'Position', 'wf-cookie-consent' ),
		'wf_cookieconsent_setting_radio',
		'wf-cookieconsent',
		'plugin_main',
		array(
			'fieldname'        => 'wf_position',
			'fielddescription' => __( 'Choose the position for the banner', 'wf-cookie-consent' ),
			'radioFields'      => array( 'top', 'bottom' ),
		)
	);

	add_settings_field(
		'wf_style',
		esc_html__( 'Style', 'wf-cookie-consent' ),
		'wf_cookieconsent_setting_radio',
		'wf-cookieconsent',
		'plugin_main',
		array(
			'fieldname'        => 'wf_style',
			'fielddescription' => __( 'Choose a ready-made banner style to play with', 'wf-cookie-consent' ),
			'radioFields'      => array( 'light', 'dark', 'minimal', 'card' ),
		)
	);

	add_settings_field(
		'wf_auto_dark',
		esc_html__( 'Automatic dark mode', 'wf-cookie-consent' ),
		'wf_cookieconsent_setting_checkbox',
		'wf-cookieconsent',
		'plugin_main',
		array(
			'fieldname'        => 'wf_auto_dark',
			'fielddescription' => __( "Follow the visitor's browser setting and switch to a dark palette automatically (does not affect the Dark style)", 'wf-cookie-consent' ),
		)
	);

	add_settings_field(
		'wf_color_bg',
		esc_html__( 'Custom background color', 'wf-cookie-consent' ),
		'wf_cookieconsent_setting_color',
		'wf-cookieconsent',
		'plugin_main',
		array(
			'fieldname'        => 'wf_color_bg',
			'fielddescription' => __( 'Optional. Leave empty to use the style default.', 'wf-cookie-consent' ),
		)
	);

	add_settings_field(
		'wf_color_text',
		esc_html__( 'Custom text color', 'wf-cookie-consent' ),
		'wf_cookieconsent_setting_color',
		'wf-cookieconsent',
		'plugin_main',
		array(
			'fieldname'        => 'wf_color_text',
			'fielddescription' => __( 'Optional. Leave empty to use the style default.', 'wf-cookie-consent' ),
		)
	);

	add_settings_field(
		'wf_color_accent',
		esc_html__( 'Custom link color', 'wf-cookie-consent' ),
		'wf_cookieconsent_setting_color',
		'wf-cookieconsent',
		'plugin_main',
		array(
			'fieldname'        => 'wf_color_accent',
			'fielddescription' => __( 'Optional. Leave empty to use the style default.', 'wf-cookie-consent' ),
		)
	);

	foreach ( $languages as $lang ) {
		if ( count( $languages ) > 1 ) {
			$sectionKey = 'plugin_main_' . $lang;
			/* translators: %s: language code (e.g. en, de, it) */
			add_settings_section( $sectionKey, sprintf( esc_html__( 'Language specific settings: %s', 'wf-cookie-consent' ), esc_html( $lang ) ), '', 'wf-cookieconsent' );
		}
		add_settings_field(
			'wf_cookietext',
			esc_html__( 'Info text', 'wf-cookie-consent' ),
			'wf_cookieconsent_setting_textarea',
			'wf-cookieconsent',
			$sectionKey,
			array(
				'fieldname'        => 'wf_cookietext',
				'fielddescription' => '',
				'lang'             => $lang,
			)
		);
		add_settings_field(
			'wf_linkhref',
			esc_html__( 'Cookie policy page', 'wf-cookie-consent' ),
			'wf_cookieconsent_setting_page_selector',
			'wf-cookieconsent',
			$sectionKey,
			array(
				'fieldname' => 'wf_linkhref',
				'lang'      => $lang,
			)
		);
		add_settings_field(
			'wf_linktext',
			esc_html__( 'Cookie policy link text', 'wf-cookie-consent' ),
			'wf_cookieconsent_setting_input_text',
			'wf-cookieconsent',
			$sectionKey,
			array(
				'fieldname'        => 'wf_linktext',
				'fielddescription' => '',
				'lang'             => $lang,
			)
		);
		add_settings_field(
			'wf_dismisstext',
			esc_html__( 'Dismiss text', 'wf-cookie-consent' ),
			'wf_cookieconsent_setting_input_text',
			'wf-cookieconsent',
			$sectionKey,
			array(
				'fieldname'        => 'wf_dismisstext',
				'fielddescription' => '',
				'lang'             => $lang,
			)
		);
	}
}
add_action( 'admin_init', 'wf_cookieconsent_admin_init' );

function wf_cookieconsent_setting_input_text( $args ) {
	$options = wf_cookieconsent_get_options( $args['lang'] );
	$field   = "wf_cookieconsent_options[{$args['lang']}][{$args['fieldname']}]";
	echo "<input id='" . esc_attr( $field ) . "' name='" . esc_attr( $field ) . "' size='40' type='text' value='" . esc_attr( $options[ $args['fieldname'] ] ) . "' />";
	echo ( empty( $args['fielddescription'] ) ? '' : "<p class='description'>" . esc_html( $args['fielddescription'] ) . '</p>' );
}

function wf_cookieconsent_setting_textarea( $args ) {
	$options = wf_cookieconsent_get_options( $args['lang'] );
	$field   = "wf_cookieconsent_options[{$args['lang']}][{$args['fieldname']}]";
	echo "<textarea id='" . esc_attr( $field ) . "' name='" . esc_attr( $field ) . "' cols='40' rows='5'>" . esc_textarea( $options[ $args['fieldname'] ] ) . '</textarea>';
	echo ( empty( $args['fielddescription'] ) ? '' : "<p class='description'>" . esc_html( $args['fielddescription'] ) . '</p>' );
}

function wf_cookieconsent_setting_page_selector( $args ) {
	$options       = wf_cookieconsent_get_options( $args['lang'] );
	$field         = "wf_cookieconsent_options[{$args['lang']}][{$args['fieldname']}]";
	$wf_page_query = new WP_Query(
		array(
			'post_type'        => 'page',
			'suppress_filters' => true, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.SuppressFilters_suppress_filters -- needed so WPML returns pages in all languages
		'orderby'              => 'title',
		'order'                => 'asc',
		'lang'                 => 'all', // With this option, Polylang will return all languages
		'nopaging'             => true,
		)
	);
	echo "<select name='" . esc_attr( $field ) . "' id='" . esc_attr( $field ) . "'>";
	foreach ( $wf_page_query->posts as $post ) {
		$wf_language_information = wf_get_language_information( $post->ID );
		if ( ! empty( $wf_language_information ) ) {
			$wf_language_information = '(' . $wf_language_information . ')';
		}
		echo "<option class='level-0' value='" . esc_attr( $post->ID ) . "'" . ( ( (int) $options[ $args['fieldname'] ] === $post->ID ) ? " selected='selected'" : '' ) . '>' . esc_html( sanitize_title( $post->post_title ) ) . ' ' . esc_html( $wf_language_information ) . '</option>';
	}
	echo '</select>';
	echo ( empty( $args['fielddescription'] ) ? '' : "<p class='description'>" . esc_html( $args['fielddescription'] ) . '</p>' );
}

function wf_cookieconsent_setting_radio( $args ) {
	$options = wf_cookieconsent_get_options();
	if ( empty( $options[ $args['fieldname'] ] ) ) {
		$options[ $args['fieldname'] ] = '';
	}
	$name = "wf_cookieconsent_options[{$args['fieldname']}]";
	echo '<fieldset>';
	if ( ! empty( $args['radioFields'] ) ) {
		foreach ( $args['radioFields'] as $radioField ) {
			echo "<input type='radio' id='wf_rad_" . esc_attr( $radioField ) . "' name='" . esc_attr( $name ) . "' value='" . esc_attr( $radioField ) . "'" . ( ( $radioField === $options[ $args['fieldname'] ] ) ? ' checked' : '' ) . "><label for='wf_rad_" . esc_attr( $radioField ) . "'>" . esc_html( $radioField ) . '</label><br />';
		}
	}
	echo ( empty( $args['fielddescription'] ) ? '' : "<p class='description'>" . esc_html( $args['fielddescription'] ) . '</p>' );
	echo '</fieldset>';
}

function wf_cookieconsent_setting_checkbox( $args ) {
	$options = get_option( 'wf_cookieconsent_options' );
	$checked = isset( $options[ $args['fieldname'] ] ) ? ( $options[ $args['fieldname'] ] === '1' ) : true; // default on
	$name    = "wf_cookieconsent_options[{$args['fieldname']}]";
	echo "<input type='checkbox' id='" . esc_attr( $args['fieldname'] ) . "' name='" . esc_attr( $name ) . "' value='1'" . ( $checked ? ' checked' : '' ) . ' />';
	echo ( empty( $args['fielddescription'] ) ? '' : "<label for='" . esc_attr( $args['fieldname'] ) . "' class='description'> " . esc_html( $args['fielddescription'] ) . '</label>' );
}

function wf_cookieconsent_setting_color( $args ) {
	$options = get_option( 'wf_cookieconsent_options' );
	$value   = isset( $options[ $args['fieldname'] ] ) ? sanitize_hex_color( $options[ $args['fieldname'] ] ) : '';
	$name    = "wf_cookieconsent_options[{$args['fieldname']}]";
	// Single hex text field: empty means "use the style default". No extra JS needed.
	echo "<input type='text' id='" . esc_attr( $args['fieldname'] ) . "' name='" . esc_attr( $name ) . "' size='8' placeholder='#rrggbb' value='" . esc_attr( $value ) . "' />";
	echo ( empty( $args['fielddescription'] ) ? '' : "<p class='description'>" . esc_html( $args['fielddescription'] ) . '</p>' );
}

// sanitize the stored options before they are written to the database
function wf_cookieconsent_sanitize_options( $input ) {
	$output = array();
	if ( ! is_array( $input ) ) {
		return $output;
	}
	foreach ( $input as $key => $value ) {
		if ( $key === 'wf_position' ) {
			$output['wf_position'] = in_array( $value, array( 'top', 'bottom' ), true ) ? $value : 'bottom';
		} elseif ( $key === 'wf_style' ) {
			$output['wf_style'] = in_array( $value, array( 'light', 'dark', 'minimal', 'card' ), true ) ? $value : 'light';
		} elseif ( $key === 'wf_auto_dark' ) {
			// handled explicitly after the loop so an unchecked box is stored as off
			continue;
		} elseif ( in_array( $key, array( 'wf_color_bg', 'wf_color_text', 'wf_color_accent' ), true ) ) {
			$color = sanitize_hex_color( $value );
			if ( $color ) {
				$output[ $key ] = $color;
			}
		} elseif ( is_array( $value ) ) {
			// per-language settings keyed by language code
			$lang = sanitize_key( $key );
			foreach ( $value as $subkey => $subvalue ) {
				switch ( $subkey ) {
					case 'wf_cookietext':
						$output[ $lang ][ $subkey ] = sanitize_textarea_field( $subvalue );
						break;
					case 'wf_linkhref':
						$output[ $lang ][ $subkey ] = is_numeric( $subvalue ) ? absint( $subvalue ) : esc_url_raw( $subvalue );
						break;
					default:
						$output[ $lang ][ sanitize_key( $subkey ) ] = sanitize_text_field( $subvalue );
						break;
				}
			}
		} else {
			$output[ sanitize_key( $key ) ] = sanitize_text_field( $value );
		}
	}
	// Checkbox: absent in $input means the box was unchecked -> store as off.
	$output['wf_auto_dark'] = ( isset( $input['wf_auto_dark'] ) && $input['wf_auto_dark'] === '1' ) ? '1' : '';
	return $output;
}


function wf_cookieconsent_admin_notice__iubenda() {
	global $pagenow;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only reading the current admin page slug to decide whether to show the notice, no form data is processed.
	$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	if ( $pagenow === 'options-general.php' && $current_page === 'wf-cookieconsent' ) {
		?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'What do you think about our plug-in?', 'wf-cookie-consent' ); ?></strong>
			<br>
			<?php esc_html_e( "We hope you like it. There's just one catch: sustaining a free WordPress plug-in is quite pricey and believe us when we say we need a lot of good ☕ coffee to keep it running.", 'wf-cookie-consent' ); ?>
		</p>
		<p>
			<?php
				echo wp_kses(
					/* translators: %s: URL to the donation page */
					sprintf( __( "We'd definitely appreciate it if you could <a href='%s' target='_blank'>offer us some coffee!</a>", 'wf-cookie-consent' ), esc_url( WFCOOKIECONSENT_BUYMEACOFFEE_URL ) ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				);
			?>
		</p>
	</div>
		<?php
	}
}
add_action( 'admin_notices', 'wf_cookieconsent_admin_notice__iubenda' );


/*
* Helpers
*/
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- intentionally shared cross-plugin helpers (guarded by function_exists) and a third-party WPML hook name.
if ( ! function_exists( 'wf_get_language' ) ) {

	function wf_get_language() {
		$language = null;
		if ( function_exists( 'pll_current_language' ) ) {
			// Polylang: https://wordpress.org/plugins/polylang/ .
			$language = pll_current_language();
		} elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			// WPML: https://wpml.org .
			$language = ICL_LANGUAGE_CODE;
		} else {
			// Fallback: wp get_locale() - first 2 chars (en, it, de ...).
			$language = substr( get_locale(), 0, 2 );
		}

		return $language;
	}

}

if ( ! function_exists( 'wf_get_languages' ) ) {

	function wf_get_languages() {
		$languages = array();
		//get all languages from polylang plugin https://wordpress.org/plugins/polylang/
		global $polylang;
		if ( function_exists( 'PLL' ) ) {
			// for polylang versions > 1.8
			$pl_languages = PLL()->model->get_languages_list();
		} elseif ( isset( $polylang ) ) {
			// for older polylang version
			$pl_languages = $polylang->model->get_languages_list();
		}
		if ( isset( $pl_languages ) ) {
			// iterate through polylang language list
			foreach ( $pl_languages as $pl_language ) {
				$languages[] = $pl_language->slug;
			}
		} elseif ( function_exists( 'icl_get_languages' ) ) {
			//get all languages with icl_get_languages for wpml
			$wpml_languages = icl_get_languages( 'skip_missing=0' );
			foreach ( $wpml_languages as $wpml_language ) {
				$languages[] = ! empty( $wpml_language['language_code'] ) ? $wpml_language['language_code'] : $wpml_language['code'];
			}
		} else {
			//return wp get_locale() - first 2 chars (en, it, de ...)
			$languages[] = substr( get_locale(), 0, 2 );
		}
		return $languages;
	}

}

if ( ! function_exists( 'wf_get_language_information' ) ) {

	function wf_get_language_information( $post_id ) {
		$locale               = '';
		$language_information = '';
		if ( function_exists( 'pll_get_post_language' ) ) {
			// for polylang versions > 1.7
			$locale = pll_get_post_language( $post_id );
		} elseif ( has_filter( 'wpml_post_language_details' ) ) {
			// for wpml versions > 3.2
			$language_information = apply_filters( 'wpml_post_language_details', null, $post_id );
		} elseif ( function_exists( 'wpml_get_language_information' ) ) {
			// for older wpml versions
			$language_information = wpml_get_language_information( $post_id );
		}
		if ( is_wp_error( $language_information ) || empty( $language_information ) ) {
			$locale = '';
		} else {
			$locale = $language_information['display_name'];
		}
		return $locale;
	}

}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals

?>
