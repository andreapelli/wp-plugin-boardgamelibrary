<?php
class Boardgamelibrary {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'boardgamelibrary';
        $this->version = BOARDGAMELIBRARY_VERSION;
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-boardgamelibrary-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-boardgamelibrary-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-boardgamelibrary-post-types.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-boardgamelibrary-meta-boxes.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-boardgamelibrary-templates.php';

        $this->loader = new Boardgamelibrary_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Boardgamelibrary_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {
        $post_types = new Boardgamelibrary_Post_Types();
        $this->loader->add_action('init', $post_types, 'register_gioco_da_tavolo_post_type');

        $meta_boxes = new Boardgamelibrary_Meta_Boxes();
        $this->loader->add_action('add_meta_boxes', $meta_boxes, 'add_gioco_da_tavolo_meta_boxes');
        $this->loader->add_action('save_post', $meta_boxes, 'save_gioco_da_tavolo_meta');
    }

    private function define_public_hooks() {
        $templates = new Boardgamelibrary_Templates();
        $this->loader->add_filter('single_template', $templates, 'load_gioco_da_tavolo_template');
        $this->loader->add_filter('archive_template', $templates, 'load_gioco_da_tavolo_archive_template');
        $this->loader->add_action('wp_enqueue_scripts', $templates, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $templates, 'enqueue_scripts');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}

add_action('admin_notices', 'boardgamelibrary_import_notice');

function boardgamelibrary_import_notice() {
    if (isset($_GET['import']) && $_GET['import'] === 'success') {
        $imported_count = isset($_GET['imported']) ? intval($_GET['imported']) : 0;
        $updated_count = isset($_GET['updated']) ? intval($_GET['updated']) : 0;

        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>' . sprintf(__('Importazione completata con successo! %d giochi importati e %d giochi aggiornati.', 'boardgamelibrary'), $imported_count, $updated_count) . '</p>';
        echo '</div>';
    }
}