<?php
/**
 * Plugin Name: Board Game Library
 * Plugin URI: http://example.com/boardgamelibrary
 * Description: Un plugin per gestire e visualizzare una libreria di giochi da tavolo.
 * Version: 1.0.0
 * Author: Il tuo nome
 * Author URI: http://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: boardgamelibrary
 * Domain Path: /languages
 */

// Se questo file viene chiamato direttamente, interrompi.
if (!defined('WPINC')) {
    die;
}

// Definisci la costante per la versione del plugin
define('BOARDGAMELIBRARY_VERSION', '1.0.0');

// Includi i file necessari
require_once plugin_dir_path(__FILE__) . 'includes/class-boardgamelibrary-post-types.php';

// Funzione che viene eseguita all'attivazione del plugin
function activate_boardgamelibrary() {
    $post_types = new Boardgamelibrary_Post_Types();
    $post_types->register_gioco_da_tavolo_post_type();
    flush_rewrite_rules();
}

// Funzione che viene eseguita alla disattivazione del plugin
function deactivate_boardgamelibrary() {
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'activate_boardgamelibrary');
register_deactivation_hook(__FILE__, 'deactivate_boardgamelibrary');

// Includi la classe principale del plugin
require plugin_dir_path(__FILE__) . 'includes/class-boardgamelibrary.php';

// Inizia l'esecuzione del plugin
function run_boardgamelibrary() {
    $plugin = new Boardgamelibrary();
    $plugin->run();
}
run_boardgamelibrary();