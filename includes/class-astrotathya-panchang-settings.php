<?php
/**
 * Settings > Panchang: site-wide defaults, built on the Settings API.
 *
 * @package PanchangByAstroTathya
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stores and edits the default city, language, theme, size, embed method and credit choice.
 */
final class Astrotathya_Panchang_Settings {

	const OPTION = 'astrotathya_panchang_options';
	const PAGE   = 'astrotathya-panchang';

	/** Longest site key accepted (a signed token; issued keys are far shorter). */
	const KEY_MAX = 2048;

	/**
	 * Built-in defaults.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'city'   => 'new-delhi',
			'type'   => 'panchang',
			'lang'   => 'auto',
			'theme'  => 'light',
			'size'   => 'card',
			// Iframe by default: it is isolated from the site; script mode runs astrotathya.com's
			// JavaScript on the page and is opt-in (security review, 21 Oct 2026).
			'mode'   => 'iframe',
			'credit' => false,
			// The site's embed key (free, from astrotathya.com/panchang-widget); without one the
			// plugin embeds nothing (visitors see nothing; administrators see a note).
			'key'    => '',
		);
	}

	/**
	 * Saved settings merged over the defaults.
	 *
	 * @return array
	 */
	public static function get() {
		$saved = get_option( self::OPTION, array() );
		return self::sanitize( is_array( $saved ) ? $saved : array() );
	}

	/**
	 * Admin hooks.
	 */
	public static function hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'no_key_notice' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( ASTROTATHYA_PANCHANG_FILE ), array( __CLASS__, 'action_link' ) );
	}

	/**
	 * Screens where a missing site key is pointed out: this plugin's settings, the Plugins list and
	 * the Widgets screen. Not every admin screen, so the note never nags elsewhere.
	 */
	const NOTICE_SCREENS = array( 'settings_page_astrotathya-panchang', 'plugins', 'widgets' );

	/**
	 * "Add your site key" notice while no key is saved: without one the block, widget and shortcode
	 * show nothing to visitors.
	 */
	public static function no_key_notice() {
		if ( ! current_user_can( 'manage_options' ) || '' !== self::get()['key'] ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! in_array( $screen->id, self::NOTICE_SCREENS, true ) ) {
			return;
		}
		$on_page = 'settings_page_' . self::PAGE === $screen->id;
		printf(
			'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s%3$s</p></div>',
			esc_html__( 'Panchang by AstroTathya: add your site key.', 'panchang-by-astrotathya' ),
			esc_html__( 'The widget needs a free site key for this site, from astrotathya.com/panchang-widget. Until one is entered, the block, widget and shortcode show nothing to visitors.', 'panchang-by-astrotathya' ),
			$on_page
				? ''
				: ' <a href="' . esc_url( admin_url( 'options-general.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Enter the key', 'panchang-by-astrotathya' ) . '</a>'
		);
	}

	/**
	 * Adds Settings > Panchang.
	 */
	public static function menu() {
		add_options_page(
			__( 'Panchang by AstroTathya', 'panchang-by-astrotathya' ),
			__( 'Panchang', 'panchang-by-astrotathya' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'page' )
		);
	}

	/**
	 * Adds a Settings link on the Plugins screen.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public static function action_link( $links ) {
		$url = admin_url( 'options-general.php?page=' . self::PAGE );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'panchang-by-astrotathya' ) . '</a>' );
		return $links;
	}

	/**
	 * Registers the option, section and fields.
	 */
	public static function register() {
		register_setting(
			self::PAGE,
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);

		add_settings_section( 'defaults', esc_html__( 'Defaults', 'panchang-by-astrotathya' ), '__return_false', self::PAGE );

		// The city list is built when the field is drawn on this plugin's own screen, never here:
		// admin_init runs on every admin request (AJAX and Heartbeat too), and a refresh of the
		// cached list would make an unrelated admin request wait on astrotathya.com.
		$fields = array(
			'city'  => array( esc_html__( 'City', 'panchang-by-astrotathya' ), 'cities' ),
			'type'  => array(
				esc_html__( 'Show', 'panchang-by-astrotathya' ),
				array(
					'panchang'  => __( "Today's panchang", 'panchang-by-astrotathya' ),
					'festivals' => __( 'Upcoming festivals', 'panchang-by-astrotathya' ),
				),
			),
			'lang'  => array(
				esc_html__( 'Language', 'panchang-by-astrotathya' ),
				array(
					'auto' => __( 'Same as the site', 'panchang-by-astrotathya' ),
					'en'   => __( 'English', 'panchang-by-astrotathya' ),
					'hi'   => __( 'Hindi', 'panchang-by-astrotathya' ),
				),
			),
			'theme' => array(
				esc_html__( 'Theme', 'panchang-by-astrotathya' ),
				array(
					'light'  => __( 'Light', 'panchang-by-astrotathya' ),
					'dark'   => __( 'Dark', 'panchang-by-astrotathya' ),
					'maroon' => __( 'Maroon', 'panchang-by-astrotathya' ),
				),
			),
			'size'  => array(
				esc_html__( 'Size', 'panchang-by-astrotathya' ),
				array(
					'card'   => __( 'Card', 'panchang-by-astrotathya' ),
					'banner' => __( 'Banner', 'panchang-by-astrotathya' ),
				),
			),
			'mode'  => array(
				esc_html__( 'Embed method', 'panchang-by-astrotathya' ),
				array(
					'iframe' => __( 'Iframe (recommended: kept separate from your site)', 'panchang-by-astrotathya' ),
					'script' => __( 'Script (adapts its height; runs JavaScript from astrotathya.com on your pages)', 'panchang-by-astrotathya' ),
				),
			),
		);

		foreach ( $fields as $key => $field ) {
			add_settings_field(
				$key,
				$field[0],
				array( __CLASS__, 'select' ),
				self::PAGE,
				'defaults',
				array(
					'key'       => $key,
					'choices'   => $field[1],
					'label_for' => 'astrotathya-panchang-' . $key,
				)
			);
		}

		add_settings_field(
			'key',
			esc_html__( 'Site key', 'panchang-by-astrotathya' ),
			array( __CLASS__, 'key_field' ),
			self::PAGE,
			'defaults',
			array( 'label_for' => 'astrotathya-panchang-key' )
		);

		add_settings_field(
			'credit',
			esc_html__( 'Credit link', 'panchang-by-astrotathya' ),
			array( __CLASS__, 'credit_field' ),
			self::PAGE,
			'defaults',
			array( 'label_for' => 'astrotathya-panchang-credit' )
		);
	}

	/**
	 * Renders a select field.
	 *
	 * @param array $args Field args: key, choices, label_for.
	 */
	public static function select( $args ) {
		$current = self::get()[ $args['key'] ];
		if ( 'cities' === $args['choices'] ) {
			$args['choices'] = array();
			foreach ( Astrotathya_Panchang_Cities::all() as $slug => $row ) {
				$args['choices'][ $slug ] = $row[0] . ( '' !== $row[2] ? ' (' . $row[2] . ')' : '' );
			}
			// A saved city no longer in the list stays selectable, so saving the page never
			// switches it silently to the first city.
			if ( ! isset( $args['choices'][ $current ] ) && Astrotathya_Panchang_Cities::is_slug( $current ) ) {
				$args['choices'] = array( $current => Astrotathya_Panchang_Cities::name( $current ) ) + $args['choices'];
			}
		}
		printf(
			'<select id="%1$s" name="%2$s[%3$s]">',
			esc_attr( $args['label_for'] ),
			esc_attr( self::OPTION ),
			esc_attr( $args['key'] )
		);
		foreach ( $args['choices'] as $value => $label ) {
			printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $label ) );
		}
		echo '</select>';
	}

	/**
	 * Renders the site key field.
	 *
	 * @param array $args Field args.
	 */
	public static function key_field( $args ) {
		printf(
			'<input type="text" class="regular-text code" id="%1$s" name="%2$s[key]" value="%3$s" autocomplete="off" spellcheck="false" maxlength="%4$d" aria-describedby="%1$s-note">',
			esc_attr( $args['label_for'] ),
			esc_attr( self::OPTION ),
			esc_attr( self::get()['key'] ),
			(int) self::KEY_MAX
		);
		printf(
			'<p class="description" id="%1$s-note">%2$s</p>',
			esc_attr( $args['label_for'] ),
			esc_html__( 'The key for this site. Get one free at astrotathya.com/panchang-widget: sign in, add the verification line it gives you to this site (a post or page is fine), and paste the key here. A key covers this site\'s address and the cities chosen for it. Until one is entered, visitors see nothing where a widget is placed.', 'panchang-by-astrotathya' )
		);
	}

	/**
	 * A site key as stored and sent: only the characters a key can contain (letters, digits, ".",
	 * "_" and "-"), at most KEY_MAX of them. Anything else is dropped, never passed on.
	 *
	 * @param mixed $key Raw key.
	 * @return string
	 */
	public static function clean_key( $key ) {
		$key = preg_replace( '/[^A-Za-z0-9._-]/', '', is_string( $key ) ? $key : '' );
		return strlen( $key ) <= self::KEY_MAX ? $key : '';
	}

	/**
	 * Renders the opt-in credit checkbox.
	 *
	 * @param array $args Field args.
	 */
	public static function credit_field( $args ) {
		printf(
			'<label><input type="checkbox" id="%1$s" name="%2$s[credit]" value="1"%3$s> %4$s</label>',
			esc_attr( $args['label_for'] ),
			esc_attr( self::OPTION ),
			checked( self::get()['credit'], true, false ),
			esc_html__( 'Show a small "panchang by AstroTathya" text link under each widget (off by default).', 'panchang-by-astrotathya' )
		);
	}

	/**
	 * Validates every value against its allow-list.
	 *
	 * @param mixed $input Raw option value.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$out   = self::defaults();
		$lists = array(
			'type'  => Astrotathya_Panchang_Render::TYPES,
			'lang'  => Astrotathya_Panchang_Render::LANGS,
			'theme' => Astrotathya_Panchang_Render::THEMES,
			'size'  => Astrotathya_Panchang_Render::SIZES,
			'mode'  => Astrotathya_Panchang_Render::MODES,
		);
		foreach ( $lists as $key => $allowed ) {
			if ( isset( $input[ $key ] ) && in_array( $input[ $key ], $allowed, true ) ) {
				$out[ $key ] = $input[ $key ];
			}
		}
		if ( isset( $input['city'] ) && Astrotathya_Panchang_Cities::is_slug( $input['city'] ) ) {
			$out['city'] = $input['city'];
		}
		$out['credit'] = ! empty( $input['credit'] );
		$out['key']    = isset( $input['key'] ) ? self::clean_key( trim( (string) $input['key'] ) ) : '';
		return $out;
	}

	/**
	 * The preview's script and styles, on this plugin's settings screen only.
	 *
	 * @param string $hook The admin page hook.
	 */
	public static function assets( $hook ) {
		if ( 'settings_page_' . self::PAGE !== $hook ) {
			return;
		}
		$url = plugin_dir_url( ASTROTATHYA_PANCHANG_FILE ) . 'assets/';
		wp_enqueue_style( 'astrotathya-panchang-settings', $url . 'settings.css', array(), ASTROTATHYA_PANCHANG_VERSION );
		wp_enqueue_script( 'astrotathya-panchang-settings', $url . 'settings.js', array(), ASTROTATHYA_PANCHANG_VERSION, true );
		wp_add_inline_script(
			'astrotathya-panchang-settings',
			'window.astrotathyaPanchangSettings = ' . wp_json_encode(
				array(
					'origin'   => Astrotathya_Panchang_Render::ORIGIN,
					'siteLang' => Astrotathya_Panchang_Render::site_lang(),
					'heights'  => Astrotathya_Panchang_Render::HEIGHTS,
					'key'      => self::get()['key'],
				)
			) . ';',
			'before'
		);
	}

	/**
	 * Settings page: the form, and beside it a preview of the widget as chosen. options.php checks
	 * the nonce written by settings_fields().
	 */
	public static function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="astrotathya-panchang-settings">
				<div class="astrotathya-panchang-settings__form">
					<p><?php esc_html_e( 'Choose how the panchang looks by default. Any block, widget or shortcode can still change these for itself.', 'panchang-by-astrotathya' ); ?></p>
					<form action="options.php" method="post">
						<?php
						settings_fields( self::PAGE );
						do_settings_sections( self::PAGE );
						submit_button();
						?>
					</form>
					<h2><?php esc_html_e( 'Adding it to a page', 'panchang-by-astrotathya' ); ?></h2>
					<p><?php esc_html_e( 'In the block editor, search for "Panchang". Under Appearance > Widgets, add the Panchang widget. Anywhere else, paste the shortcode shown beside the preview.', 'panchang-by-astrotathya' ); ?></p>
					<p><?php esc_html_e( 'Shortcode values: type is panchang or festivals; lang is en, hi or auto; theme is light, dark or maroon; size is card or banner. Leave one out to use the default above.', 'panchang-by-astrotathya' ); ?></p>
				</div>
				<div class="astrotathya-panchang-settings__preview">
					<h2><?php esc_html_e( 'Preview', 'panchang-by-astrotathya' ); ?></h2>
					<div class="astrotathya-panchang-settings__frame">
						<iframe id="astrotathya-panchang-preview" title="<?php esc_attr_e( 'Panchang preview', 'panchang-by-astrotathya' ); ?>" sandbox="allow-scripts allow-same-origin" referrerpolicy="strict-origin-when-cross-origin" loading="lazy"></iframe>
					</div>
					<p class="astrotathya-panchang-settings__note"><?php esc_html_e( 'Today, live from astrotathya.com. Changes show here before you save.', 'panchang-by-astrotathya' ); ?></p>
					<code class="astrotathya-panchang-settings__code" id="astrotathya-panchang-shortcode"></code>
				</div>
			</div>
		</div>
		<?php
	}
}
