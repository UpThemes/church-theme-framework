<?php
/**
 * Recent Posts
 *
 * This replaces the core Recent Posts widget, adding options to show author, date, excerpt and thumbnail.
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2013 - 2016, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Posts widget class
 *
 * @since 0.9
 */
class CTFW_Widget_Posts extends CTFW_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 0.9
	 */
	function __construct() {

		parent::__construct(
			'ctfw-posts',
			_x( 'CT Posts', 'widget', 'onechurch' ),
			array(
				'description' => __( 'Shows blog posts according to options', 'onechurch' )
			)
		);

	}

	/**
	 * Field configuration
	 *
	 * This is used by CTFW_Widget class for automatic field output, filtering, sanitization and saving.
	 *
	 * @since 0.9
	 * @return array Fields for widget
	 */
	function ctfw_fields() { // prefix in case WP core adds method with same name

		// Fields
		$fields = array(

			// Example
			/*
			'field_id' => array(
				'name'				=> __( 'Field Name', 'onechurch' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> __( 'This is the description below the field.', 'onechurch' ),
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			);
			*/

			// Title
			'title' => array(
				'name'				=> _x( 'Title', 'posts widget', 'onechurch' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> _x( 'Posts', 'posts widget title default', 'onechurch' ), // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Category
			'category' => array(
				'name'				=> _x( 'Category', 'posts widget', 'onechurch' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'select', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> ctfw_term_options( 'category', array( // array of keys/values for radio or select
					'all' => _x( 'All Categories', 'posts widget', 'onechurch' )
				) ),
				'default'			=> 'all', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Order By
			'orderby' => array(
				'name'				=> _x( 'Order By', 'posts widget', 'onechurch' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'select', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'title'				=> _x( 'Title', 'posts widget order by', 'onechurch' ),
					'publish_date'		=> _x( 'Date', 'posts widget order by', 'onechurch' ),
					'comment_count'		=> _x( 'Comment Count', 'posts widget order by', 'onechurch' ),
				),
				'default'			=> 'publish_date', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Order
			'order' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'radio', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', // show text after checkbox
				'radio_inline'		=> true, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'asc'	=> _x( 'Low to High', 'posts widget order', 'onechurch' ),
					'desc'	=> _x( 'High to Low', 'posts widget order', 'onechurch' ),
				),
				'default'			=> 'desc', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Limit
			'limit' => array(
				'name'				=> _x( 'Limit', 'posts widget', 'onechurch' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'number', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '1', // lowest possible value for number type
				'number_max'		=> '50', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> '5', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Image
			'show_image' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> _x( 'Show image', 'posts widget', 'onechurch' ), //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> true, // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Date
			'show_date' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'checkbox_label'	=> _x( 'Show date', 'posts widget', 'onechurch' ), //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> true, // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Author
			'show_author' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'checkbox_label'	=> _x( 'Show author', 'posts widget', 'onechurch' ), //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> false, // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Categories
			'show_category' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'checkbox_label'	=> _x( 'Show category', 'posts widget', 'onechurch' ), //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> false, // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array( 'category' ), // hide field if taxonomies are not supported
			),

			// Excerpt
			'show_excerpt' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> _x( 'Show excerpt', 'posts widget', 'onechurch' ), //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> false, // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

		);

		// Return config
		return apply_filters( 'ctfw_posts_widget_fields', $fields );

	}

	/**
	 * Get posts
	 *
	 * This can optionally be used by the template.
	 * $this->instance is sanitized before being made available here.
	 *
	 * @since 0.9
	 * @return array Posts for widget template
	 */
	function ctfw_get_posts() {

		// Base arguments
		$args = array(
			'orderby'         	=> $this->ctfw_instance['orderby'],
			'order'           	=> $this->ctfw_instance['order'],
			'numberposts'     	=> $this->ctfw_instance['limit'],
			'suppress_filters'	=> false // keep WPML from showing posts from all languages: http://bit.ly/I1JIlV + http://bit.ly/1f9GZ7D
		);

		// Group argument
		if ( 'all' != $this->ctfw_instance['category'] ) {
			$args['category'] = $this->ctfw_instance['category']; // ID
		}

		// Get posts
		$posts = get_posts( $args );

		// Return filtered
		return apply_filters( 'ctfw_posts_widget_get_posts', $posts );

	}

}
