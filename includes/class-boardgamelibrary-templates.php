<?php
class Boardgamelibrary_Templates {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function load_gioco_da_tavolo_template($single_template) {
        if (get_post_type() === 'gioco_da_tavolo') {
            $plugin_template = plugin_dir_path(dirname(__FILE__)) . 'templates/single-gioco_da_tavolo.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $single_template;
    }

    public function load_gioco_da_tavolo_archive_template($archive_template) {
        if (is_post_type_archive('gioco_da_tavolo')) {
            $plugin_template = plugin_dir_path(dirname(__FILE__)) . 'templates/archive-gioco_da_tavolo.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $archive_template;
    }

    public function enqueue_styles() {
        wp_enqueue_style('boardgamelibrary-style', plugin_dir_url(dirname(__FILE__)) . 'assets/css/boardgamelibrary-styles.css', array(), BOARDGAMELIBRARY_VERSION);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('boardgamelibrary-script', plugin_dir_url(dirname(__FILE__)) . 'assets/js/boardgamelibrary-scripts.js', array('jquery'), BOARDGAMELIBRARY_VERSION, true);
    }
}