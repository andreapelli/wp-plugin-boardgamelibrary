<?php
class Boardgamelibrary_Meta_Boxes {
    public function add_gioco_da_tavolo_meta_boxes() {
        add_meta_box(
            'gioco_da_tavolo_details',
            __('Dettagli Gioco da Tavolo', 'boardgamelibrary'),
            array($this, 'render_gioco_da_tavolo_meta_box'),
            'gioco_da_tavolo',
            'normal',
            'default'
        );
    }

    public function render_gioco_da_tavolo_meta_box($post) {
        // Recupera i valori esistenti dai campi personalizzati
        $min_players = get_post_meta($post->ID, 'min_players', true);
        $max_players = get_post_meta($post->ID, 'max_players', true);
        $play_time = get_post_meta($post->ID, 'play_time', true);
        $min_age = get_post_meta($post->ID, 'min_age', true);
        $difficulty = get_post_meta($post->ID, 'difficulty', true);

        // Output del form HTML
        ?>
        <p>
            <label for="min_players"><?php _e('Numero minimo di giocatori:', 'boardgamelibrary'); ?></label>
            <input type="number" id="min_players" name="min_players" value="<?php echo esc_attr($min_players); ?>" min="1">
        </p>
        <p>
            <label for="max_players"><?php _e('Numero massimo di giocatori:', 'boardgamelibrary'); ?></label>
            <input type="number" id="max_players" name="max_players" value="<?php echo esc_attr($max_players); ?>" min="1">
        </p>
        <p>
            <label for="play_time"><?php _e('Durata della partita (in minuti):', 'boardgamelibrary'); ?></label>
            <input type="number" id="play_time" name="play_time" value="<?php echo esc_attr($play_time); ?>" min="1">
        </p>
        <p>
            <label for="min_age"><?php _e('Età minima consigliata:', 'boardgamelibrary'); ?></label>
            <input type="number" id="min_age" name="min_age" value="<?php echo esc_attr($min_age); ?>" min="1">
        </p>
        <p>
            <label for="difficulty"><?php _e('Difficoltà (1-5):', 'boardgamelibrary'); ?></label>
            <input type="number" id="difficulty" name="difficulty" value="<?php echo esc_attr($difficulty); ?>" min="1" max="5">
        </p>
        <?php
    }

    public function save_gioco_da_tavolo_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ($parent_id = wp_is_post_revision($post_id)) {
            $post_id = $parent_id;
        }
        if (!current_user_can('edit_post', $post_id)) return;

        // Salva i valori dei campi personalizzati
        $fields = array('min_players', 'max_players', 'play_time', 'min_age', 'difficulty');
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}