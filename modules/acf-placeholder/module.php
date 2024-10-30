<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}


add_action( 'dslc_hook_register_modules',
	create_function( '', 'return dslc_register_module( "ACF_Placeholder" );' )
);

class ACF_Placeholder extends DSLC_Module {

	// Module Attributes
	var $module_id = 'ACF_Placeholder';
	var $module_title = 'Advanced Custom Fields Placeholder';
	var $module_icon = 'tasks';
	var $module_category = 'Hosting Review';

	// Module Options
	function options() {

		$post_id = get_the_ID() ? get_the_ID() : filter_input( INPUT_POST, 'post_id', FILTER_VALIDATE_INT );

		if ( is_null( $post_id ) ) {
			$post_id = current( get_posts( 'limit=1' ) )->ID;
		}

		$acf_fields_data_for_post = get_field_objects( $post_id );

		if ( empty( $acf_fields_data_for_post ) ) {
			$acf_fields_data_for_post = get_field_objects( current( get_posts( [
					'limit'     => 1,
					'post_type' => current( get_post_custom_values( 'dslc_template_for', $post_id ) ),
				]
			) )->ID );


		}

		$acf_field_for_post = [];

		foreach ( $acf_fields_data_for_post as $acf_key => $acf_objects ) {
			array_push( $acf_field_for_post, [
				'label' => esc_attr( $acf_objects['label'] ),
				'value' => esc_attr( $acf_key )
			] );
		}

		global $_wp_additional_image_sizes;

		$images_size_choices = [];

		foreach ( get_intermediate_image_sizes() as $_size ) {
			$width  = get_option( "{$_size}_size_w" );
			$height = get_option( "{$_size}_size_h" );

			array_push( $images_size_choices, [
				'label' => esc_attr( $_size . " ({$width}x{$height})" ),
				'value' => esc_attr( $_size )
			] );
		}

		foreach ( $_wp_additional_image_sizes as $size_name => $size ) {
			array_push( $images_size_choices, [
				'label' => esc_attr( $size_name . " ($size[width]x$size[height])" ),
				'value' => esc_attr( $size_name )
			] );
		}

		// The options array
		$dslc_options = array(
			array(
				'label'   => __( 'Show On', 'live-composer-page-builder' ),
				'id'      => 'css_show_on',
				'std'     => 'desktop tablet phone',
				'type'    => 'checkbox',
				'choices' => array(
					array(
						'label' => __( 'Desktop', 'live-composer-page-builder' ),
						'value' => 'desktop'
					),
					array(
						'label' => __( 'Tablet', 'live-composer-page-builder' ),
						'value' => 'tablet'
					),
					array(
						'label' => __( 'Phone', 'live-composer-page-builder' ),
						'value' => 'phone'
					),
				),
			),

			array(
				'label' => __( 'Custom Label Class', 'hrmflc' ),
				'std'   => '',
				'id'    => 'custom_label_class',
				'type'  => 'text'
			),

			array(
				'label' => __( 'Custom Value Class', 'hrmflc' ),
				'std'   => '',
				'id'    => 'custom_value_class',
				'type'  => 'text'
			),

			array(
				'label' => __( 'Custom Title Class', 'hrmflc' ),
				'std'   => '',
				'id'    => 'custom_title_class',
				'type'  => 'text'
			),

			array(
				'label' => __( 'Custom Title', 'hrmflc' ),
				'std'   => '',
				'id'    => 'custom_title',
				'type'  => 'text'
			),

			array(
				'label'              => __( 'Advanced Custom Field', 'hrmflc' ),
				'id'                 => 'acf_field',
				'std'                => 'web_hosting_rank',
				'type'               => 'select',
				'choices'            => $acf_field_for_post,
				'help'               => __( 'Select Post field to display', 'hrmflc' ),
				'dependent_controls' => [
					'score_review' => 'show_as_rating',
					'visit'        => 'browsershot'
				]
			),

			array(
				'label'   => __( 'Label', 'hrmflc' ),
				'id'      => 'show_label',
				'std'     => 'disabled',
				'type'    => 'select',
				'choices' => [
					[
						'label' => __( 'Enabled' ),
						'value' => 'enabled'
					],
					[
						'label' => __( 'Disabled' ),
						'value' => 'disabled'
					]
				],
				'help'    => __( 'Should the label be visible for the field?', 'hrmflc' ),

			),

			array(
				'label'   => __( 'Is browser shot', 'live-composer-page-builder' ),
				'id'      => 'browsershot',
				'std'     => 'disable',
				'type'    => 'radio',
				'choices' => array(
					array(
						'label' => __( 'Enable', 'live-composer-page-builder' ),
						'value' => 'enable'
					),
					array(
						'label' => __( 'Disable', 'live-composer-page-builder' ),
						'value' => 'disable'
					)
				),
				'tab'     => __( 'URL specific', 'hrmflc' )
			),

			array(
				'label'   => __( 'Wrap image with url', 'hrmflc' ),
				'id'      => 'wrap_image_with_url',
				'std'     => 'no',
				'type'    => 'select',
				'choices' => [
					[
						'label' => __( 'Yes' ),
						'value' => 'yes'
					],
					[
						'label' => __( 'No' ),
						'value' => 'no'
					]
				],
				'tab'     => __( 'Image specific', 'hrmflc' )
			),

			array(
				'label'   => __( 'Image size', 'hrmflc' ),
				'id'      => 'image_size',
				'std'     => 'thumbnail',
				'type'    => 'select',
				'choices' => $images_size_choices,
				'tab'     => __( 'Image specific', 'hrmflc' )
			),

			/* Styling */

			array(
				'label'                 => __( 'Align', 'live-composer-page-builder' ),
				'id'                    => 'css_align',
				'std'                   => 'center',
				'type'                  => 'text_align',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'text-align',
				'section'               => 'styling',
			),
			array(
				'label'                 => __( 'BG Color', 'live-composer-page-builder' ),
				'id'                    => 'css_bg_color',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'background-color',
				'section'               => 'styling',
			),
			array(
				'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
				'id'                    => 'css_border_color',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'border-color',
				'section'               => 'styling',
			),
			array(
				'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
				'id'                    => 'css_border_width',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'border-width',
				'section'               => 'styling',
				'ext'                   => 'px',
			),
			array(
				'label'                 => __( 'Borders', 'live-composer-page-builder' ),
				'id'                    => 'css_border_trbl',
				'std'                   => 'top right bottom left',
				'type'                  => 'checkbox',
				'choices'               => array(
					array(
						'label' => __( 'Top', 'live-composer-page-builder' ),
						'value' => 'top'
					),
					array(
						'label' => __( 'Right', 'live-composer-page-builder' ),
						'value' => 'right'
					),
					array(
						'label' => __( 'Bottom', 'live-composer-page-builder' ),
						'value' => 'bottom'
					),
					array(
						'label' => __( 'Left', 'live-composer-page-builder' ),
						'value' => 'left'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'border-style',
				'section'               => 'styling',
			),
			array(
				'label'                 => __( 'Border Radius', 'live-composer-page-builder' ),
				'id'                    => 'css_border_radius',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'border-radius',
				'section'               => 'styling',
				'ext'                   => 'px',
			),

			/**
			 * Margins
			 */
			array(
				'label'                 => __( 'Margin Top', 'live-composer-page-builder' ),
				'id'                    => 'general_css_margin_top',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'margin-top',
				'section'               => 'styling',
				'ext'                   => 'px',
				'min'                   => - 100
			),

			array(
				'label'                 => __( 'Margin Left', 'live-composer-page-builder' ),
				'id'                    => 'general_css_margin_left',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'margin-left',
				'section'               => 'styling',
				'ext'                   => 'px',
				'min'                   => - 100
			),

			array(
				'label'                 => __( 'Margin Right', 'live-composer-page-builder' ),
				'id'                    => 'general_css_margin_right',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'margin-right',
				'section'               => 'styling',
				'ext'                   => 'px',
				'min'                   => - 100
			),

			array(
				'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
				'id'                    => 'general_css_margin_bottom',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'margin-bottom',
				'section'               => 'styling',
				'ext'                   => 'px',
				'min'                   => - 100
			),


			/**
			 * paddings
			 */
			array(
				'label'                 => __( 'Padding Top', 'live-composer-page-builder' ),
				'id'                    => 'general_css_padding_top',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'padding-top',
				'section'               => 'styling',
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Padding Left', 'live-composer-page-builder' ),
				'id'                    => 'general_css_padding_left',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'padding-left',
				'section'               => 'styling',
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Padding Right', 'live-composer-page-builder' ),
				'id'                    => 'general_css_padding_right',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'padding-right',
				'section'               => 'styling',
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Padding Bottom', 'live-composer-page-builder' ),
				'id'                    => 'general_css_padding_bottom',
				'std'                   => 25,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'padding-bottom',
				'section'               => 'styling',
				'ext'                   => 'px'
			),


			array(
				'label'                 => __( 'Minimum Height', 'live-composer-page-builder' ),
				'id'                    => 'css_min_height',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'min-height',
				'section'               => 'styling',
				'ext'                   => 'px',
				'min'                   => 0,
				'max'                   => 1000,
				'increment'             => 5
			),
			array(
				'label'                 => __( 'Force 100% Width', 'live-composer-page-builder' ),
				'id'                    => 'css_force_width',
				'std'                   => 'auto',
				'type'                  => 'select',
				'choices'               => array(
					array(
						'label' => __( 'Enabled', 'live-composer-page-builder' ),
						'value' => '100%'
					),
					array(
						'label' => __( 'Disabled', 'live-composer-page-builder' ),
						'value' => 'auto'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf',
				'affect_on_change_rule' => 'width',
				'section'               => 'styling',
			),

			/* Title Options */

			array(
				'label'                 => __( 'Color', 'live-composer-page-builder' ),
				'id'                    => 'title_color',
				'std'                   => '#4d4d4d',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'color',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Color - Hover', 'live-composer-page-builder' ),
				'id'                    => 'title_color_hover',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'color',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
				'id'                    => 'title_font_size',
				'std'                   => '24',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'font-size',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),
			array(
				'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
				'id'                    => 'css_title_font_weight',
				'std'                   => '300',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'font-weight',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
				'ext'                   => '',
				'min'                   => 100,
				'max'                   => 900,
				'increment'             => 100
			),
			array(
				'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
				'id'                    => 'css_title_font_family',
				'std'                   => 'Roboto',
				'type'                  => 'font',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'font-family',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
				'id'                    => 'title_line_height',
				'std'                   => '29',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'line-height',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),
			array(
				'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
				'id'                    => 'title_margin',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'margin-bottom',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),
			array(
				'label'                 => __( 'Text Transform', 'live-composer-page-builder' ),
				'id'                    => 'css_title_text_transform',
				'std'                   => 'none',
				'type'                  => 'select',
				'choices'               => array(
					array(
						'label' => __( 'None', 'live-composer-page-builder' ),
						'value' => 'none'
					),
					array(
						'label' => __( 'Capitalize', 'live-composer-page-builder' ),
						'value' => 'capitalize'
					),
					array(
						'label' => __( 'Uppercase', 'live-composer-page-builder' ),
						'value' => 'uppercase'
					),
					array(
						'label' => __( 'Lowercase', 'live-composer-page-builder' ),
						'value' => 'lowercase'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'text-transform',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
				'id'                    => 'css_title_text_align',
				'std'                   => 'center',
				'type'                  => 'text_align',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .custom-title',
				'affect_on_change_rule' => 'text-align',
				'section'               => 'styling',
				'tab'                   => __( 'Title', 'live-composer-page-builder' ),
			),


			/**
			 * Value Styling
			 */

			array(
				'label'                 => __( 'Display Type', 'live-composer-page-builder' ),
				'id'                    => 'value_display',
				'std'                   => 'block',
				'type'                  => 'select',
				'choices'               => array(
					array(
						'label' => __( 'None', 'live-composer-page-builder' ),
						'value' => 'none'
					),
					array(
						'label' => __( 'Block', 'live-composer-page-builder' ),
						'value' => 'block'
					),
					array(
						'label' => __( 'Inline Block', 'live-composer-page-builder' ),
						'value' => 'inline-block'
					),
					array(
						'label' => __( 'Table', 'live-composer-page-builder' ),
						'value' => 'table'
					),
					array(
						'label' => __( 'Table Cell', 'live-composer-page-builder' ),
						'value' => 'table-cell'
					),
					array(
						'label' => __( 'Inline', 'live-composer-page-builder' ),
						'value' => 'inline'
					),
					array(
						'label' => __( 'Flex', 'live-composer-page-builder' ),
						'value' => 'flex'
					),
					array(
						'label' => __( 'Inline Flex', 'live-composer-page-builder' ),
						'value' => 'inline-flex'
					),
					array(
						'label' => __( 'Inline Table', 'live-composer-page-builder' ),
						'value' => 'inline-table'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'display',
				'section'               => 'styling',

				'tab' => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
				'id'                    => 'value_border_color',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'border-color',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
				'id'                    => 'value_border_width',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'border-width',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Borders', 'live-composer-page-builder' ),
				'id'                    => 'value_border_trbl',
				'std'                   => 'top right bottom left',
				'type'                  => 'checkbox',
				'choices'               => array(
					array(
						'label' => __( 'Top', 'live-composer-page-builder' ),
						'value' => 'top'
					),
					array(
						'label' => __( 'Right', 'live-composer-page-builder' ),
						'value' => 'right'
					),
					array(
						'label' => __( 'Bottom', 'live-composer-page-builder' ),
						'value' => 'bottom'
					),
					array(
						'label' => __( 'Left', 'live-composer-page-builder' ),
						'value' => 'left'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'border-style',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Border Radius', 'live-composer-page-builder' ),
				'id'                    => 'value_border_radius',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'border-radius',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Color', 'live-composer-page-builder' ),
				'id'                    => 'value_color',
				'std'                   => '#4d4d4d',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value, .hrmflc_acf .value a',
				'affect_on_change_rule' => 'color',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Color - Hover', 'live-composer-page-builder' ),
				'id'                    => 'value_color_hover',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value:hover, .hrmflc_acf .value a:hover',
				'affect_on_change_rule' => 'color',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Text Align', 'live-composer-page-builder' ),
				'id'                    => 'css_value_text_align',
				'std'                   => 'left',
				'type'                  => 'text_align',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value, .hrmflc_acf .value a',
				'affect_on_change_rule' => 'text-align',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Font Size', 'live-composer-page-builder' ),
				'id'                    => 'value_font_size',
				'std'                   => '17',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value, .hrmflc_acf .value a',
				'affect_on_change_rule' => 'font-size',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),
			array(
				'label'                 => __( 'Font Weight', 'live-composer-page-builder' ),
				'id'                    => 'css_value_font_weight',
				'std'                   => '300',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value, .hrmflc_acf .value a',
				'affect_on_change_rule' => 'font-weight',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => '',
				'min'                   => 100,
				'max'                   => 900,
				'increment'             => 100
			),
			array(
				'label'                 => __( 'Font Family', 'live-composer-page-builder' ),
				'id'                    => 'css_value_font_family',
				'std'                   => 'Open Sans',
				'type'                  => 'font',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value, .hrmflc_acf .value a',
				'affect_on_change_rule' => 'font-family',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
				'id'                    => 'value_line_height',
				'std'                   => '29',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value, .hrmflc_acf .value a',
				'affect_on_change_rule' => 'line-height',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),
			array(
				'label'                 => __( 'Margin Top', 'live-composer-page-builder' ),
				'id'                    => 'value_margin_top',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'margin-top',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Margin Left', 'live-composer-page-builder' ),
				'id'                    => 'value_margin_left',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'margin-left',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Margin Right', 'live-composer-page-builder' ),
				'id'                    => 'value_margin_right',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'margin-right',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Margin Bottom', 'live-composer-page-builder' ),
				'id'                    => 'value_margin_bottom',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'margin-bottom',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),


			array(
				'label'                 => __( 'Padding Top', 'live-composer-page-builder' ),
				'id'                    => 'value_padding_top',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'padding-top',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Padding Left', 'live-composer-page-builder' ),
				'id'                    => 'value_padding_left',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'padding-left',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Padding Right', 'live-composer-page-builder' ),
				'id'                    => 'value_padding_right',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'padding-right',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),

			array(
				'label'                 => __( 'Padding Bottom', 'live-composer-page-builder' ),
				'id'                    => 'value_padding_bottom',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'padding-bottom',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px'
			),


			array(
				'label'                 => __( 'Text Transform', 'live-composer-page-builder' ),
				'id'                    => 'css_value_text_transform',
				'std'                   => 'none',
				'type'                  => 'select',
				'choices'               => array(
					array(
						'label' => __( 'None', 'live-composer-page-builder' ),
						'value' => 'none'
					),
					array(
						'label' => __( 'Capitalize', 'live-composer-page-builder' ),
						'value' => 'capitalize'
					),
					array(
						'label' => __( 'Uppercase', 'live-composer-page-builder' ),
						'value' => 'uppercase'
					),
					array(
						'label' => __( 'Lowercase', 'live-composer-page-builder' ),
						'value' => 'lowercase'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'text-transform',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Minimum Height', 'live-composer-page-builder' ),
				'id'                    => 'value_height',
				'std'                   => '',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'height',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => 'px',
				'min'                   => 0,
				'max'                   => 1900,
				'step'                  => 1
			),

			array(
				'label'                 => __( 'Width', 'live-composer-page-builder' ),
				'id'                    => 'value_width',
				'std'                   => '',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'width',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
				'ext'                   => '%',
				'min'                   => 0,
				'max'                   => 100,
				'step'                  => 1
			),

			array(
				'label'                 => __( 'Vertical Align', 'live-composer-page-builder' ),
				'id'                    => 'value_vertical_align',
				'std'                   => 'initial',
				'type'                  => 'select',
				'choices'               => [
					[
						'label' => 'Baseline',
						'value' => 'baseline'
					],
					[
						'label' => 'Subscript',
						'value' => 'sub'
					],
					[
						'label' => 'Superscript',
						'value' => 'sup'
					],
					[
						'label' => 'Top',
						'value' => 'top'
					],
					[
						'label' => 'Text Top',
						'value' => 'text-top'
					],
					[
						'label' => 'Middle',
						'value' => 'middle'
					],
					[
						'label' => 'Bottom',
						'value' => 'bottom'
					],
					[
						'label' => 'Text Bottom',
						'value' => 'text-bottom'
					],
					[
						'label' => 'Initial',
						'value' => 'initial'
					],
					[
						'label' => 'Inherit',
						'value' => 'inherit'
					],
				],
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value',
				'affect_on_change_rule' => 'vertical-align',
				'section'               => 'styling',
				'tab'                   => __( 'Value', 'live-composer-page-builder' ),
			),

			/**
			 * Button Container
			 */
			array(
				'label'                 => __( 'Display Type', 'live-composer-page-builder' ),
				'id'                    => 'button_display',
				'std'                   => 'block',
				'type'                  => 'select',
				'choices'               => array(
					array(
						'label' => __( 'None', 'live-composer-page-builder' ),
						'value' => 'none'
					),
					array(
						'label' => __( 'Block', 'live-composer-page-builder' ),
						'value' => 'block'
					),
					array(
						'label' => __( 'Inline Block', 'live-composer-page-builder' ),
						'value' => 'inline-block'
					),
					array(
						'label' => __( 'Table', 'live-composer-page-builder' ),
						'value' => 'table'
					),
					array(
						'label' => __( 'Table Cell', 'live-composer-page-builder' ),
						'value' => 'table-cell'
					),
					array(
						'label' => __( 'Inline', 'live-composer-page-builder' ),
						'value' => 'inline'
					),
					array(
						'label' => __( 'Flex', 'live-composer-page-builder' ),
						'value' => 'flex'
					),
					array(
						'label' => __( 'Inline Flex', 'live-composer-page-builder' ),
						'value' => 'inline-flex'
					),
					array(
						'label' => __( 'Inline Table', 'live-composer-page-builder' ),
						'value' => 'inline-table'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'display',
				'section'               => 'styling',

				'tab' => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Color', 'live-composer-page-builder' ),
				'id'                    => 'button_bg_color',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'background-color',
				'section'               => 'styling',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Color', 'live-composer-page-builder' ),
				'id'                    => 'button_bg_color_hover',
				'std'                   => 'rgb(42,160,239)',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button:hover',
				'affect_on_change_rule' => 'background-color',
				'section'               => 'styling',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'label'   => __( 'Border', 'live-composer-page-builder' ),
				'id'      => 'button_css_border_group',
				'type'    => 'group',
				'action'  => 'open',
				'section' => 'styling',
				'tab'     => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Border Color', 'live-composer-page-builder' ),
				'id'                    => 'button_css_border_color',
				'std'                   => '',
				'type'                  => 'color',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'border-color',
				'section'               => 'styling',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Border Width', 'live-composer-page-builder' ),
				'id'                    => 'button_css_border_width',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'border-width',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Borders', 'live-composer-page-builder' ),
				'id'                    => 'button_css_border_trbl',
				'std'                   => 'top right bottom left',
				'type'                  => 'checkbox',
				'choices'               => array(
					array(
						'label' => __( 'Top', 'live-composer-page-builder' ),
						'value' => 'top'
					),
					array(
						'label' => __( 'Right', 'live-composer-page-builder' ),
						'value' => 'right'
					),
					array(
						'label' => __( 'Bottom', 'live-composer-page-builder' ),
						'value' => 'bottom'
					),
					array(
						'label' => __( 'Left', 'live-composer-page-builder' ),
						'value' => 'left'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'border-style',
				'section'               => 'styling',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Border Radius', 'live-composer-page-builder' ),
				'id'                    => 'button_css_border_radius',
				'std'                   => 0,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'border-radius',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'id'      => 'button_css_border_group',
				'type'    => 'group',
				'action'  => 'close',
				'section' => 'styling',
				'tab'     => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'label'   => __( 'Padding', 'live-composer-page-builder' ),
				'id'      => 'button_css_padding_group',
				'type'    => 'group',
				'action'  => 'open',
				'section' => 'styling',
				'tab'     => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Padding Vertical', 'live-composer-page-builder' ),
				'id'                    => 'button_css_padding_vertical',
				'std'                   => 14,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'padding-top,padding-bottom',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),

			),

			array(
				'label'                 => __( 'Padding Horizontal', 'live-composer-page-builder' ),
				'id'                    => 'button_css_padding_horizontal',
				'std'                   => 18,
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'padding-left,padding-right',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),

			),

			array(
				'id'      => 'button_css_padding_group',
				'type'    => 'group',
				'action'  => 'close',
				'section' => 'styling',
				'tab'     => __( 'Button', 'live-composer-page-builder' ),
			),

			array(
				'label'                 => __( 'Width', 'live-composer-page-builder' ),
				'id'                    => 'button_css_width',
				'onlypositive'          => true, // Value can't be negative.
				'std'                   => '100',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'width',
				'section'               => 'styling',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
				'ext'                   => '%',
			),

			array(
				'label'                 => __( 'Line Height', 'live-composer-page-builder' ),
				'id'                    => 'button_css_line_height',
				'onlypositive'          => true, // Value can't be negative.
				'std'                   => '22px',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'line-height',
				'section'               => 'styling',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
				'ext'                   => 'px',
			),

			array(
				'label'   => __( 'Margin', 'live-composer-page-builder' ),
				'id'      => 'button_css_margin_group',
				'type'    => 'group',
				'action'  => 'open',
				'section' => 'styling',
				'tab'     => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Top', 'live-composer-page-builder' ),
				'id'                    => 'button_css_margin_top',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'margin-top',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Right', 'live-composer-page-builder' ),
				'id'                    => 'button_css_margin_right',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'margin-right',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Bottom', 'live-composer-page-builder' ),
				'id'                    => 'button_css_margin_bottom',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'margin-bottom',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'label'                 => __( 'Left', 'live-composer-page-builder' ),
				'id'                    => 'button_css_margin_left',
				'std'                   => '0',
				'type'                  => 'slider',
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .regular-acf-button',
				'affect_on_change_rule' => 'margin-left',
				'section'               => 'styling',
				'ext'                   => 'px',
				'tab'                   => __( 'Button', 'live-composer-page-builder' ),
			),
			array(
				'id'      => 'button_css_margin_group',
				'type'    => 'group',
				'action'  => 'close',
				'section' => 'styling',
				'tab'     => __( 'Button', 'live-composer-page-builder' ),
			),

			/** Link styles */

			array(
				'label'                 => __( 'Display Type', 'live-composer-page-builder' ),
				'id'                    => 'link_display',
				'std'                   => 'block',
				'type'                  => 'select',
				'choices'               => array(
					array(
						'label' => __( 'None', 'live-composer-page-builder' ),
						'value' => 'none'
					),
					array(
						'label' => __( 'Block', 'live-composer-page-builder' ),
						'value' => 'block'
					),
					array(
						'label' => __( 'Inline Block', 'live-composer-page-builder' ),
						'value' => 'inline-block'
					),
					array(
						'label' => __( 'Table', 'live-composer-page-builder' ),
						'value' => 'table'
					),
					array(
						'label' => __( 'Table Cell', 'live-composer-page-builder' ),
						'value' => 'table-cell'
					),
					array(
						'label' => __( 'Inline', 'live-composer-page-builder' ),
						'value' => 'inline'
					),
					array(
						'label' => __( 'Flex', 'live-composer-page-builder' ),
						'value' => 'flex'
					),
					array(
						'label' => __( 'Inline Flex', 'live-composer-page-builder' ),
						'value' => 'inline-flex'
					),
					array(
						'label' => __( 'Inline Table', 'live-composer-page-builder' ),
						'value' => 'inline-table'
					),
				),
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value a',
				'affect_on_change_rule' => 'display',
				'section'               => 'styling',

				'tab' => __( 'Link', 'live-composer-page-builder' ),
			),

			/** Images styles */

			array(
				'label'                 => __( 'Vertical Align', 'live-composer-page-builder' ),
				'id'                    => 'css_image_vertical_align',
				'std'                   => 'initial',
				'type'                  => 'select',
				'choices'               => [
					[
						'label' => 'Baseline',
						'value' => 'baseline'
					],
					[
						'label' => 'Subscript',
						'value' => 'sub'
					],
					[
						'label' => 'Superscript',
						'value' => 'sup'
					],
					[
						'label' => 'Top',
						'value' => 'top'
					],
					[
						'label' => 'Text Top',
						'value' => 'text-top'
					],
					[
						'label' => 'Middle',
						'value' => 'middle'
					],
					[
						'label' => 'Bottom',
						'value' => 'bottom'
					],
					[
						'label' => 'Text Bottom',
						'value' => 'text-bottom'
					],
					[
						'label' => 'Initial',
						'value' => 'initial'
					],
					[
						'label' => 'Inherit',
						'value' => 'inherit'
					],
				],
				'refresh_on_change'     => false,
				'affect_on_change_el'   => '.hrmflc_acf .value img',
				'affect_on_change_rule' => 'vertical-align',
				'section'               => 'styling',
				'tab'                   => __( 'Images', 'live-composer-page-builder' ),
			),

		);

		// Return the array
		$dslc_options = array_merge( $dslc_options, $this->shared_options( 'animation_options', array( 'hover_opts' => false ) ) );
		$dslc_options = array_merge( $dslc_options, $this->presets_options() );

		$dslc_options = apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );

		return $dslc_options;

	}

	/**
	 * Renders the module
	 *
	 * @param $options
	 */
	function output( $options ) {

		// REQUIRED
		$this->module_start( $options );

		/* Module output starts here */

		global $dslc_active;

		if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) {
			$dslc_is_admin = true;
		} else {
			$dslc_is_admin = false;
		}

		if ( empty( $options['acf_field'] ) && $dslc_is_admin ) {
			?>
            <div class="dslc-notification dslc-red">
				<?php _e( 'If you are using this module in template, then assign template to post_type.', 'hrmflc' ); ?>
				<?php _e( 'Please select the field which content you want to display.', 'hrmflc' ); ?>
                <span class="dslca-refresh-module-hook dslc-icon dslc-icon-refresh"></span>
            </div>
			<?php
		} else {
			?>
            <div class="hrmflc_acf">
				<?php
				$post_id   = $options['post_id'];
				$acf_field = get_field_object( $options['acf_field'], $post_id, [
					'load_value'   => true,
					'format_value' => true
				] );

				$post_custom = get_post_custom( $post_id );

				/**
				 * Gets one of hosting providers from the list if none is delivered via ajax from editable template or page.
				 */
				if ( ! $acf_field['value'] && isset( $post_custom['dslc_template_for'] ) ) {

					$current_provider        = 1;
					$hosting_providers_posts = get_posts( [
						'limit'     => 1,
						'post_type' => current( $post_custom['dslc_template_for'] )
					] );

					if ( is_array( $hosting_providers_posts ) ) {
						$current_provider = current( $hosting_providers_posts );
						if ( is_object( $current_provider ) ) {
							$current_provider = $current_provider->ID;
						} else {
							$current_provider = 1;
						}
					}

					$post_id   = $current_provider;
					$acf_field = get_field_object( $options['acf_field'], $post_id );
				}

				if ( ! empty( $options['custom_title'] ) ): ?>
                    <h3 class="custom-title <?php echo $options['custom_title_class'] ?>"><?php echo do_shortcode( $options['custom_title'] ) ?></h3>
				<?php endif; ?>

				<?php if ( $options['show_label'] === 'enabled' ): ?>
                    <label class="label <?php echo $options['custom_label_class'] ?>"
                           for="<?php echo $options['acf_field'] . '_' . $post_id ?>">
						<?php echo $acf_field['label']; ?>
                    </label>
				<?php endif; ?>

                <div class="value <?php echo $options['custom_value_class'] ?>">
					<?php if ( $acf_field['type'] === 'url' ) {
						echo $this->generate_url( $acf_field, $post_id, $options );
					} elseif ( $acf_field['type'] === 'image' ) {
						echo $this->generate_image( $acf_field, $post_id, $options );
					} elseif ( $acf_field['type'] === 'repeater' ) {
						echo $this->repeater_generator( $acf_field, $post_id, $options );
					} else {
						echo is_bool( $acf_field['value'] ) ? ( $acf_field['value'] ? __( 'Yes' ) : __( 'No' ) ) : $acf_field['value'];
					} ?>
                </div>
            </div>
			<?php
		}

		// REQUIRED
		$this->module_end( $options );

	}

	/**
	 * @return string
	 */
	function add_protocol_preamble() {
		return strtolower( substr( $_SERVER["SERVER_PROTOCOL"], 0, strpos( $_SERVER["SERVER_PROTOCOL"], '/' ) ) ) . '://';
	}

	/**
	 * @param $acf_field
	 * @param $post_id
	 * @param array $options
	 *
	 * @return string
	 */
	function repeater_generator( $acf_field, $post_id, $options = [] ) {
		ob_start();
		if ( have_rows( $acf_field['name'], $post_id ) ) {
			if ( $acf_field['layout'] === 'table' ) {
				?>
                <table>
					<?php foreach ( get_fields( $acf_field['name'], $post_id ) as $key => $repeater_row ) { ?>
						<?php
						if ( $key == 0 ) {
							?>
                            <thead>
                            <tr>
								<?php
								foreach ( $repeater_row as $field_name_h => $field_value_h ) {
									echo "<th class='sortable'>" . esc_html( acf_get_field_label( $field_name_h ) ) . "</th>";
								}

								?>
                            </tr>
                            </thead>
                            <tbody>
							<?php
						}
						?>
                        <tr>
							<?php foreach ( $repeater_row as $field_name => $field_value ): ?>
                                <td><?php echo esc_html( $field_value ) ?></td>
							<?php endforeach; ?>
                        </tr>
						<?php
						if ( $key == 0 ) {
							?></tbody><?php
						}
						?>
					<?php } ?>
                </table>
				<?php
			} elseif ( $acf_field['layout'] === 'block' ) {
				foreach ( get_fields( $acf_field['name'], $post_id ) as $key => $repeater_row ) {
					?>
                    <div class="dslc-clearfix"><?php
					$count = 1;
					foreach ( $repeater_row as $field_name => $field_value ): ?>
                        <div class="dslc-col dslc-3-col <?php if ( $count % 4 == 1 ) {
							echo 'dslc-first-col';
						} elseif ( $count % 4 == 0 ) {
							echo 'dslc-last-col';
						} ?>">
                            <div class="dslc-blog-post-title"><?php echo esc_html( acf_get_field_label( $field_name ) ) ?></div>
                            <div class="dslc-blog-post-excerpt"><?php echo esc_html( $field_value ) ?></div>
                        </div>
						<?php $count ++; endforeach;
					?></div><?php


					if ( ! wp_script_is( 'jquery' ) ) {
						wp_enqueue_script( 'jquery' );
					}

					if ( ! wp_script_is( 'acf-placeholder' ) ) {
						wp_enqueue_script( 'acf-placeholder', plugin_dir_url( __FILE__ ) . 'assets/js/acf-placeholder.js', [ 'jquery' ], '0.0.1', true );
					}

					if ( ! wp_style_is( 'acf-placeholder' ) ) {
						wp_enqueue_style( 'acf-placeholder', plugin_dir_url( __FILE__ ) . 'assets/css/acf-placeholder.css', [], '0.0.1', 'all' );
					}
				}
			} else {
				$rows = [];
				foreach ( get_fields( $acf_field['name'], $post_id ) as $key => $repeater_row ) {
					foreach ( $repeater_row as $field_name => $field_value ): ?>
						<?php $rows[ $field_name ][] = esc_html( $field_value ); ?>
					<?php endforeach;
				}

				$largest_size = 0;
				foreach ( $rows as $label => $value ) {
					if ( $largest_size < count( $value ) ) {
						$largest_size = count( $value );
					}
				}

				$largest_size = $largest_size + 1;
				foreach ( $rows as $label => $value ) {
					?>
                    <div class="dslc-clearfix">
					<?php
					$count = 0;
					foreach ( $value as $val ) {
						?>
                        <div class="dslc-col dslc-<?php echo $largest_size ?>-col <?php if ( $count % $largest_size == 1 ) {
							echo 'dslc-first-col';
						} elseif ( $count % $largest_size == 0 ) {
							echo 'dslc-last-col';
						} ?>">
							<?php if ( $count % $largest_size == 1 ) {
								echo esc_html( acf_get_field_label( $label ) );
							} else {
								echo $val;
							} ?>
                        </div>
                        </div><?php
						$count ++;
					}
				}
			}
		}

		return ob_get_clean();
	}

	/**
	 * @param $acf_field
	 * @param $post_id
	 * @param $options
	 *
	 * @return string
	 */
	function generate_image( $acf_field, $post_id, $options ) {
		ob_start();
		if ( $acf_field['value'] > 0 ) {
			echo wp_get_attachment_image( $acf_field['value']['ID'], $options['image_size'], false );
		}

		return ob_get_clean();
	}

	/**
	 * @param $acf_field
	 * @param $post_id
	 * @param $options
	 *
	 * @return string
	 */
	function generate_url( $acf_field, $post_id, $options ) {
		ob_start();
		?>
        <a
                href="<?php echo esc_attr( $acf_field['value'] ) ?>"
                class="regular-acf-button <?php echo esc_attr( sanitize_title( get_the_title( $post_id ) ) . '-' . $acf_field['name'] . '-link' ) ?>"
                rel="nofollow"
                target="_blank"
                title="<?php echo esc_attr( $acf_field['description'] ) ?>"
        >
			<?php echo $acf_field['label'] ?>
        </a>
		<?php

		return ob_get_clean();
	}

}






