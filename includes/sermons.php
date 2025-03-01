<?php
/**
 * Sermon Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************
 * SERMON ARCHIVES
 **********************************/

/**
 * Enable date archives for sermon posts
 *
 * At time of making, WordPress (3.6 and possibly later) does not support dated archives for custom post types as it does for standard posts
 * This injects rules so that URL's like /cpt/2012/05 can be used with the custom post type archive template.
 * Refer to ctfw_cpt_date_archive_setup() for full details.
 *
 * Use add_theme_support( 'ctfw-sermon-date-archive' )
 *
 * @since 0.9
 * @param object $wp_rewrite object
 */
function ctfw_sermon_date_archive( $wp_rewrite ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-sermon-date-archive' ) ) {
		return;
	}

	// Post types to setup date archives for
	$post_types = array(
		'ctc_sermon'
	);

	// Do it
	ctfw_cpt_date_archive_setup( $post_types, $wp_rewrite );

}

add_action( 'generate_rewrite_rules', 'ctfw_sermon_date_archive' ); // enable date archive for sermon post type

/**********************************
 * SERMON DATA
 **********************************/

/**
 * Get sermon data
 *
 * @since 0.9
 * @param int $post_id Post ID to get data for; null for current post
 * @return array Sermon data
 */
function ctfw_sermon_data( $post_id = null ) {

	// Get URL to upload directory
	$upload_dir = wp_upload_dir();
	$upload_dir_url = $upload_dir['baseurl'];

	// Get meta values
	$data = ctfw_get_meta_data( array( // without _ctc_sermon_ prefix
		'video',		// URL to uploaded file, external file, external site with oEmbed support, or manual embed code (HTML or shortcode)
		'audio',		// URL to uploaded file, external file, external site with oEmbed support, or manual embed code (HTML or shortcode)
		'pdf',			// URL to uploaded file or external file
		'has_full_text'
	), $post_id );

	// Get media player code
	// Embed code generated from uploaded file, URL for file on other site, page on oEmbed-supported site, or manual embed code (HTML or shortcode)
	$data['video_player'] = ctfw_embed_code( $data['video'] );
	$data['audio_player'] = ctfw_embed_code( $data['audio'] );

	// Get file data for media
	// Path and size will be populated for local files only
	$media_types = array( 'audio', 'video', 'pdf' );
	foreach ( $media_types as $media_type ) {

		$data[$media_type . '_extension'] = '';
		$data[$media_type . '_path'] = '';
		$data[$media_type . '_size_bytes'] = '';
		$data[$media_type . '_size'] = '';

		// Get extension
		// This can be determined for local and external files
		// Empty for YouTube, SoundCloud, etc.
		$filetype = wp_check_filetype( $data[$media_type] );
		$data[$media_type . '_extension'] = $filetype['ext'];

		// File is local, so can get path and size
		if ( $data[$media_type] && ctfw_is_local_url( $data[$media_type] ) ) {

			// Local path
			$data[$media_type . '_path'] = $upload_dir['basedir'] . str_replace( $upload_dir_url, '', $data[$media_type] );

			// Exists?
			if ( ! file_exists( $data[$media_type . '_path'] ) ) {
				$data[$media_type . '_path'] = ''; // clear it
			} else {

				// File type
				$filetype = wp_check_filetype( $data[$media_type] );
				$data[$media_type . '_extension'] = $filetype['ext'];

				// File size
				$data[$media_type . '_size_bytes'] = filesize( $data[$media_type . '_path'] );
				$data[$media_type . '_size'] = size_format( $data[$media_type . '_size_bytes'] ); // 30 MB, 2 GB, 220 kB, etc.

			}

		}

	}

	// Get download URL's
	// URL is returned if is local or external and has an extension.
	// Those without an extension (YouTube, SoundCloud, etc. page URL) return empty (nothing to download).
	// If locally hosted, URL is changed to force "Save As" via headers.
	// Use <a href="" download="download"> to attempt Save As via limited browser support for externally hosted files.
	$data['video_download_url'] = ctfw_download_url( $data['video'] );
	$data['audio_download_url'] = ctfw_download_url( $data['audio'] );
	$data['pdf_download_url'] = ctfw_download_url( $data['pdf'] );

	// Has at least one downloadable file URL?
	$data['has_download'] = false;
	if ( $data['video_download_url'] || $data['audio_download_url'] || $data['pdf_download_url'] ) { // path empty if doesn't exist
		$data['has_download'] = true;
	}

	// Return filtered
	return apply_filters( 'ctfw_sermon_data', $data );

}

/**********************************
 * BOOKS
 **********************************/

/**
 * Books of the Bible
 *
 * Books of the Bible in old and new testaments, listed in canonical order.
 * This can assist with ordering the Book taxonomy terms and creating a Scripture archive template.
 *
 * More data may be added later, such as abbreviations.
 *
 * @since 1.7
 * @return Array Multidimentional array with keys for old_testament, new_testament and all
 */
function ctfw_bible_books() {

	$books = array();

	$books['old_testament'] = array(
		array(
			'name'	=> __( 'Genesis', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Exodus', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Leviticus', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Numbers', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Deuteronomy', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Joshua', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Judges', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Ruth', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Samuel', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Samuel', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Kings', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Kings', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Chronicles', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Chronicles', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Ezra', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Nehemiah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Esther', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Job', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Psalms', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Proverbs', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Ecclesiastes', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Song of Solomon', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Isaiah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Jeremiah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Lamentations', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Ezekiel', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Daniel', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Hosea', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Joel', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Amos', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Obadiah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Jonah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Micah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Nahum', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Habakkuk', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Zephaniah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Haggai', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Zechariah', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Malachi', 'onechurch' ),
		),
	);

	$books['new_testament'] = array(
		array(
			'name'	=> __( 'Matthew', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Mark', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Luke', 'onechurch' ),
		),
		array(
			'name'	=> __( 'John', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Acts', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Romans', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Corinthians', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Corinthians', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Galatians', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Ephesians', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Philippians', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Colossians', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Thessalonians', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Thessalonians', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Timothy', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Timothy', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Titus', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Philemon', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Hebrews', 'onechurch' ),
		),
		array(
			'name'	=> __( 'James', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 Peter', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 Peter', 'onechurch' ),
		),
		array(
			'name'	=> __( '1 John', 'onechurch' ),
		),
		array(
			'name'	=> __( '2 John', 'onechurch' ),
		),
		array(
			'name'	=> __( '3 John', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Jude', 'onechurch' ),
		),
		array(
			'name'	=> __( 'Revelation', 'onechurch' ),
		),
	);

	// Make filterable
	$books['old_testament'] = apply_filters( 'ctfw_bible_books_new_testament', $books['old_testament'] );
	$books['new_testament'] = apply_filters( 'ctfw_bible_books_old_testament', $books['new_testament'] );

	// Add testament to each book
	foreach ( $books['old_testament'] as $book_key => $book ) {
		$books['old_testament'][$book_key]['testament'] = 'old';
	}
	foreach ( $books['new_testament'] as $book_key => $book ) {
		$books['new_testament'][$book_key]['testament'] = 'new';
	}

	// Combine arrays for convenience
	$books['all'] = array_merge( $books['old_testament'], $books['new_testament'] );

	// Return everything filtered
	return apply_filters( 'ctfw_bible_books', $books );

}

/**
 * Sermon books by testament
 *
 * Return sermon books in order and organized by testament and with URL, number of sermons, etc.
 *
 * @since 1.7.2
 * @return array Books by testament
 */
function ctfw_sermon_books_by_testament() {

	// Get books, alphabetical
	$books = ctfw_content_type_archives( array(
		'specific_archive' => 'ctc_sermon_book',
	) );

	// Old new and other testaments
	$books_by_testament = array(
		'old' => array(
			'name' => __( 'Old Testament', 'onechurch' ),
		),
		'new' => array(
			'name' => __( 'New Testament', 'onechurch' ),
		),
		'other' => array(
			/* translators: Label for books not in the Old or New Testaments */
			'name' => __( 'Other Books', 'onechurch' ),
		),
	);

	// Loop books to add per testament
	foreach ( $books['items'] as $book ) {

		$testament = isset( $book->book_data['testament'] ) ? $book->book_data['testament'] : '';

		if ( 'old' == $testament ) {
			$books_by_testament['old']['books'][] = $book;
		} else if ( 'new' == $testament ) {
			$books_by_testament['new']['books'][] = $book;
		} else {
			$books_by_testament['other']['books'][] = $book;
		}

	}

	return apply_filters( 'ctfw_sermon_books_by_testament', $books_by_testament );

}
