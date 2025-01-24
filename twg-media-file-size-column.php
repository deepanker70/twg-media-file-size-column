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
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TWGP_Media_File_Manager {
    public function __construct() {
        // Hook into WordPress actions and filters.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('manage_media_columns', array($this, 'add_file_size_column'));
        add_action('manage_media_custom_column', array($this, 'display_file_size_column'), 10, 2);
        add_action('wp_ajax_twgp_get_file_size', array($this, 'ajax_get_file_size'));
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_admin_assets($hook) {
        if ('upload.php' !== $hook) {
            return;
        }

        // Enqueue CSS.
        wp_enqueue_style(
            'twgp-admin-css',
            plugins_url('/assets/css/admin-style.css', __FILE__),
            array(),
            '1.0.0'
        );

        // Enqueue JavaScript.
        wp_enqueue_script(
            'twgp-admin-js',
            plugins_url('/assets/js/admin-script.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );

        // Localize script with nonce and AJAX URL.
        wp_localize_script('twgp-admin-js', 'twgp_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'twgp_nonce' => wp_create_nonce('twgp_nonce_action')
        ));
    }

    /**
     * Add a new column for File Size.
     */
    public function add_file_size_column($columns) {
        $columns['file_size'] = __('File Size', 'twgp-media-file-manager');
        return $columns;
    }

    /**
     * Display the File Size in the new column.
     */
    public function display_file_size_column($column_name, $post_id) {
        if ('file_size' === $column_name) {
            $file_path = get_attached_file($post_id);
            $file_size = $file_path ? size_format(filesize($file_path)) : __('N/A', 'twgp-media-file-manager');
            echo esc_html($file_size);
        }
    }

    /**
     * Handle AJAX requests to get file size.
     */
    public function ajax_get_file_size() {
        check_ajax_referer('twgp_nonce_action', 'security');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(__('Unauthorized access.', 'twgp-media-file-manager'));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $file_path = get_attached_file($post_id);

        if (!$file_path || !file_exists($file_path)) {
            wp_send_json_error(__('File not found.', 'twgp-media-file-manager'));
        }

        $file_size = size_format(filesize($file_path));
        wp_send_json_success(array('file_size' => $file_size));
    }
}

// Initialize the plugin.
new TWGP_Media_File_Manager();

