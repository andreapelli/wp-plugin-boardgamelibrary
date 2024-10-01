<?php
class Boardgamelibrary_Importer {
    private $batch_size = 5; // Number of games to import per batch

    public function __construct() {
        add_action('admin_menu', [$this, 'add_import_page']);
        add_action('admin_post_import_csv', [$this, 'handle_csv_import']);
    }

    public function add_import_page() {
        add_submenu_page(
            'edit.php?post_type=gioco_da_tavolo',
            __('Importa CSV', 'boardgamelibrary'),
            __('Importa CSV', 'boardgamelibrary'),
            'manage_options',
            'boardgamelibrary-import',
            [$this, 'render_import_page']
        );
    }

    public function render_import_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import_csv">
                <?php wp_nonce_field('import_csv_nonce', 'import_csv_nonce'); ?>
                <input type="file" name="csv_file" accept=".csv">
                <?php submit_button('Importa'); ?>
            </form>
        </div>
        <?php
    }

    public function handle_csv_import() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Non hai i permessi per eseguire questa azione.', 'boardgamelibrary'));
        }

        // Temporarily remove the nonce check for testing
        // if (!isset($_POST['import_csv_nonce']) || !wp_verify_nonce($_POST['import_csv_nonce'], 'import_csv_nonce')) {
        //     wp_die(__('Verifica di sicurezza fallita.', 'boardgamelibrary'));
        // }

        if (!isset($_FILES['csv_file'])) {
            wp_die(__('Nessun file caricato.', 'boardgamelibrary'));
        }

        $file = $_FILES['csv_file'];
        $csv = array_map('str_getcsv', file($file['tmp_name']));
        array_shift($csv); // Remove the header

        // Retrieve the current position from the hidden input
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $end = min($start + $this->batch_size, count($csv)); // Calculate the end of the batch

        $imported_games = [];
        $updated_games = [];

        for ($i = $start; $i < $end; $i++) {
            $row = $csv[$i];
            $title = $row[0];
            $bgg_id = $row[6];

            // Check if the game already exists
            $existing_game = $this->find_existing_game($title, $bgg_id);

            if ($existing_game) {
                // Update the existing game
                $post_id = $existing_game->ID;
                $updated_games[] = $title; // Add the title to the update list
            } else {
                // Insert a new game
                $post_id = wp_insert_post([
                    'post_title' => $title,
                    'post_type' => 'gioco_da_tavolo',
                    'post_status' => 'publish',
                    'post_content' => $row[9], // Set the abstract as the main content
                ]);
                $imported_games[] = $title; // Add the title to the new games list
            }

            // Update meta data
            update_post_meta($post_id, 'min_players', $row[1]);
            update_post_meta($post_id, 'max_players', $row[2]);
            update_post_meta($post_id, 'min_age', $row[3]);
            update_post_meta($post_id, 'play_time', $row[4]);
            update_post_meta($post_id, 'materiali', $row[5]);
            update_post_meta($post_id, 'bgg_id', $bgg_id);
            update_post_meta($post_id, 'numero_catalogo', $row[7]);
            update_post_meta($post_id, 'difficulty', $row[8]);
            update_post_meta($post_id, 'abstract', $row[9]); // Ensure to update the meta data
            wp_set_object_terms($post_id, explode(',', $row[10]), 'ambientazione');
            wp_set_object_terms($post_id, explode(',', $row[11]), 'meccanica');
            wp_set_object_terms($post_id, explode(',', $row[12]), 'stile_di_gioco');

            // Search and save the cover image from BoardGameGeek
            $this->set_featured_image_from_bgg($post_id, $bgg_id);
        }

        // If there are still games to import, redirect for the next batch
        if ($end < count($csv)) {
            $next_start = $end; // Set the new starting position
            wp_safe_redirect(admin_url('admin-post.php?action=import_csv&start=' . $next_start . '&import_csv_nonce=' . $_POST['import_csv_nonce']));
            exit;
        }

        // Redirect with a final success message
        wp_redirect(admin_url('edit.php?post_type=gioco_da_tavolo&import=success&imported=' . count($imported_games) . '&updated=' . count($updated_games)));
        exit;
    }

    private function find_existing_game($title, $bgg_id) {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'gioco_da_tavolo' LIMIT 1",
            $title
        );
        $post_id = $wpdb->get_var($query);

        if ($post_id) {
            // Check if the BGG ID matches
            $existing_bgg_id = get_post_meta($post_id, 'bgg_id', true);
            if ($existing_bgg_id == $bgg_id) {
                return (object) ['ID' => $post_id]; // Return the object with the ID
            }
        }

        return false; // No existing game found
    }

    private function set_featured_image_from_bgg($post_id, $bgg_id) {
        $image_url = $this->get_bgg_image_url($bgg_id);
        if (!$image_url) {
            return false;
        }

        $upload = $this->upload_image($image_url);
        if (is_wp_error($upload)) {
            return false;
        }

        $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => $upload['type'],
            'post_title' => sanitize_file_name($bgg_id),
            'post_content' => '',
            'post_status' => 'inherit'
        ), $upload['file'], $post_id);

        if (is_wp_error($attachment_id)) {
            return false;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);
        set_post_thumbnail($post_id, $attachment_id);

        return true;
    }

    private function get_bgg_image_url($bgg_id) {
        $url = 'https://boardgamegeek.com/xmlapi2/thing?id=' . $bgg_id;
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);

        if ($xml && isset($xml->item->image)) {
            return (string)$xml->item->image;
        }

        return false;
    }

    private function upload_image($image_url) {
        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            return $tmp;
        }

        $file_array = array(
            'name' => basename($image_url),
            'tmp_name' => $tmp
        );

        $id = media_handle_sideload($file_array, 0);
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return $id;
        }

        $src = wp_get_attachment_url($id);
        return array(
            'file' => get_attached_file($id),
            'url' => $src,
            'type' => wp_check_filetype($src)['type']
        );
    }
}