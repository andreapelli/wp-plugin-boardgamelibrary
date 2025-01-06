<?php
/**
 * Plugin Name: Board Game Library
 * Plugin URI: http://example.com/boardgamelibrary
 * Description: Un plugin per gestire e visualizzare una libreria di giochi da tavolo.
 * Version: 1.2.0
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
define('BOARDGAMELIBRARY_VERSION', '1.2.0');

// Includi i file necessari
require_once plugin_dir_path(__FILE__) . 'includes/class-boardgamelibrary-post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-boardgamelibrary-biblioteca-meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-boardgamelibrary-prestito-meta-boxes.php';

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

// Includi la classe di importazione da CSV
require plugin_dir_path(__FILE__) . 'includes/class-boardgamelibrary-importer.php';

// Aggiungi il codice per modificare il numero di post visualizzati per pagina nella vista archivio
add_action('pre_get_posts', 'set_posts_per_page');

function set_posts_per_page($query) {
    if (!is_admin() && $query->is_post_type_archive('gioco_da_tavolo')) {
        $query->set('posts_per_page', 12); // Imposta il numero di post per pagina a 12
    }
}

// Inizia l'esecuzione del plugin
function run_boardgamelibrary() {
    $plugin = new Boardgamelibrary();
    $plugin->run();

    $importer = new Boardgamelibrary_Importer();
    $biblioteca_meta_boxes = new Boardgamelibrary_Biblioteca_Meta_Boxes();
    $prestito_meta_boxes = new Boardgamelibrary_Prestito_Meta_Boxes();
    
    // Aggiungi il pulsante di importazione
    add_action('admin_menu', 'add_import_libraries_button');
}

function add_import_libraries_button() {
    add_submenu_page(
        'edit.php?post_type=biblioteca',
        'Importa Biblioteche',
        'Importa Biblioteche',
        'manage_options',
        'import-libraries',
        'import_libraries_page'
    );
}

function import_libraries_page() {
    echo '<div class="wrap">';
    echo '<h1>Importa Biblioteche</h1>';
    echo '<form method="post">';
    echo '<input type="hidden" name="import_libraries" value="1">';
    echo '<p><input type="submit" class="button button-primary" value="Importa Biblioteche"></p>';
    echo '</form>';
    echo '</div>';

    if (isset($_POST['import_libraries'])) {
        import_libraries_from_json();
    }
}

function import_libraries_from_json() {
    $json_file = plugin_dir_path(__FILE__) . 'biblioteche.json';
    $libraries = json_decode(file_get_contents($json_file), true);

    $imported = 0;
    $updated = 0;

    foreach ($libraries as $library) {
        $existing_post = get_page_by_title($library['name'], OBJECT, 'biblioteca');

        $post_data = array(
            'post_title'    => $library['name'],
            'post_type'     => 'biblioteca',
            'post_status'   => 'publish',
        );

        if ($existing_post) {
            $post_data['ID'] = $existing_post->ID;
            wp_update_post($post_data);
            $post_id = $existing_post->ID;
            $updated++;
        } else {
            $post_id = wp_insert_post($post_data);
            $imported++;
        }

        if ($post_id) {
            update_post_meta($post_id, '_biblioteca_address', $library['address']);
            update_post_meta($post_id, '_biblioteca_town', $library['town']);
            update_post_meta($post_id, '_biblioteca_phone', $library['phone']);
            update_post_meta($post_id, '_biblioteca_email', $library['email']);
        }
    }

    echo "<p>Importazione completata. $imported biblioteche importate, $updated aggiornate.</p>";
}

run_boardgamelibrary();