<?php
/**
 * Classic widget.
 *
 * @package PanchangByAstroTathya
 */

defined( 'ABSPATH' ) || exit;

/**
 * Panchang / festivals widget for classic widget areas.
 */
class Astrotathya_Panchang_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'astrotathya_panchang',
			__( 'Panchang by AstroTathya', 'panchang-by-astrotathya' ),
			array(
				'description'           => __( "Today's panchang or upcoming festivals for a city.", 'panchang-by-astrotathya' ),
				'show_instance_in_rest' => true,
			)
		);
	}

	/**
	 * Front-end output.
	 *
	 * @param array $args     Sidebar arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$body = Astrotathya_Panchang_Render::html( $instance );
		if ( '' === $body ) {
			return; // No site key and a visitor: no empty widget box, no title.
		}

		echo wp_kses_post( $args['before_widget'] );
		if ( '' !== $title ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}
		echo wp_kses( $body, Astrotathya_Panchang_Render::allowed_html() );
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Saved values.
	 * @return string
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',
				'city'  => '',
				'type'  => '',
				'lang'  => '',
				'theme' => '',
				'size'  => '',
			)
		);
		$default  = __( 'Site default', 'panchang-by-astrotathya' );

		$cities = array( '' => $default );
		foreach ( Astrotathya_Panchang_Cities::all() as $slug => $row ) {
			$cities[ $slug ] = $row[0];
		}

		$fields = array(
			'city'  => array( __( 'City', 'panchang-by-astrotathya' ), $cities ),
			'type'  => array(
				__( 'Show', 'panchang-by-astrotathya' ),
				array(
					''          => $default,
					'panchang'  => __( "Today's panchang", 'panchang-by-astrotathya' ),
					'festivals' => __( 'Upcoming festivals', 'panchang-by-astrotathya' ),
				),
			),
			'lang'  => array(
				__( 'Language', 'panchang-by-astrotathya' ),
				array(
					''   => $default,
					'en' => __( 'English', 'panchang-by-astrotathya' ),
					'hi' => __( 'Hindi', 'panchang-by-astrotathya' ),
				),
			),
			'theme' => array(
				__( 'Theme', 'panchang-by-astrotathya' ),
				array(
					''       => $default,
					'light'  => __( 'Light', 'panchang-by-astrotathya' ),
					'dark'   => __( 'Dark', 'panchang-by-astrotathya' ),
					'maroon' => __( 'Maroon', 'panchang-by-astrotathya' ),
				),
			),
			'size'  => array(
				__( 'Size', 'panchang-by-astrotathya' ),
				array(
					''       => $default,
					'card'   => __( 'Card', 'panchang-by-astrotathya' ),
					'banner' => __( 'Banner', 'panchang-by-astrotathya' ),
				),
			),
		);
		if ( '' === Astrotathya_Panchang_Settings::get()['key'] ) {
			printf(
				'<p><strong>%s</strong></p>',
				esc_html__( 'Add your site key in Settings > Panchang. Until then this widget shows nothing to visitors.', 'panchang-by-astrotathya' )
			);
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'panchang-by-astrotathya' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<?php foreach ( $fields as $key => $field ) : ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo esc_html( $field[0] ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>">
				<?php foreach ( $field[1] as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $instance[ $key ], (string) $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
			<?php
		endforeach;
		return '';
	}

	/**
	 * Sanitizes the form values. Empty means "use the site default".
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Previous values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$lists = array(
			'type'  => Astrotathya_Panchang_Render::TYPES,
			'lang'  => array( 'en', 'hi' ),
			'theme' => Astrotathya_Panchang_Render::THEMES,
			'size'  => Astrotathya_Panchang_Render::SIZES,
		);
		$out   = array(
			'title' => isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '',
			'city'  => isset( $new_instance['city'] ) && Astrotathya_Panchang_Cities::is_slug( $new_instance['city'] ) ? $new_instance['city'] : '',
		);
		foreach ( $lists as $key => $allowed ) {
			$out[ $key ] = isset( $new_instance[ $key ] ) && in_array( $new_instance[ $key ], $allowed, true ) ? $new_instance[ $key ] : '';
		}
		return $out;
	}
}
