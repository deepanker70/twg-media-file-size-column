<?php
/**
 * Plugin Name: TWG Media File Size Column
 * Plugin URI: https://thewpguides.com/
 * Description: Adds a column displaying the file size of media files in the WordPress media library.
 * Version: 1.0
 * Author: Deepanker Verma
 * Author URI: https://thewpguides.com/
 * Text Domain: twg-media-file-size-column
 * License: GPL2
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add the "File Size" column to the media library
 */
function twg_add_file_size_column( $columns ) {
    $columns['twg_file_size'] = __( 'File Size', 'twg-media-file-size-column' );
    return $columns;
}
add_filter( 'manage_upload_columns', 'twg_add_file_size_column' );

/**
 * Display file size in the new column
 */
function twg_display_file_size_column( $column_name, $post_id ) {
    // Ensure we are dealing with the correct column
    if ( 'twg_file_size' === $column_name ) {
        // Only proceed if the user has the correct capability
        if ( current_user_can( 'manage_options' ) ) {
            $file_path = get_attached_file( $post_id );
            
            // Ensure the file exists before attempting to get the file size
            if ( file_exists( $file_path ) ) {
                $file_size = filesize( $file_path );
                $file_size = size_format( $file_size, 1 );  // Format file size (KB, MB, GB)
                echo esc_html( $file_size );
            } else {
                echo esc_html__( 'File not found', 'twg-media-file-size-column' );
            }
        } else {
            echo esc_html__( 'Insufficient permissions', 'twg-media-file-size-column' );
        }
    }
}
add_action( 'manage_media_custom_column', 'twg_display_file_size_column', 10, 2 );

/**
 * Add the plugin's CSS to style the column if needed
 */
function twg_media_file_size_column_style() {
    // Only add styles if the user is in the media library
    if ( isset( $_GET['post_type'] ) && 'attachment' === $_GET['post_type'] ) {
        echo '<style>
            .column-twg_file_size {
                width: 100px;
                text-align: center;
            }
        </style>';
    }
}
add_action( 'admin_head', 'twg_media_file_size_column_style' );

/**
 * Enqueue scripts and styles for admin area securely
 */
function twg_enqueue_admin_assets() {
    // Ensure we're only loading assets in the admin area and for this plugin's page
    if ( is_admin() ) {
        wp_enqueue_style( 'twg-media-size-column-css', plugin_dir_url( __FILE__ ) . 'assets/admin-style.css', array(), '1.0' );
    }
}
add_action( 'admin_enqueue_scripts', 'twg_enqueue_admin_assets' );

/**
 * Ensure plugin data is sanitized and validated during plugin setup
 */
function twg_plugin_setup() {
    // Example: Sanitize plugin options (if you have any settings or options in the future)
    if ( isset( $_POST['twg_example_option'] ) ) {
        $safe_value = sanitize_text_field( $_POST['twg_example_option'] );
        update_option( 'twg_example_option', $safe_value );
    }
}
add_action( 'admin_init', 'twg_plugin_setup' );

