<?php
class Boardgamelibrary_Prestito_Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_prestito_meta_boxes'));
        add_action('save_post', array($this, 'save_prestito_meta'));
    }

    public function add_prestito_meta_boxes() {
        add_meta_box(
            'prestito_details',
            __('Dettagli Prestito', 'boardgamelibrary'),
            array($this, 'render_prestito_meta_box'),
            'prestito',
            'normal',
            'default'
        );
    }

    public function render_prestito_meta_box($post) {
        wp_nonce_field('prestito_meta_box', 'prestito_meta_box_nonce');

        $gioco_id = get_post_meta($post->ID, '_prestito_gioco_id', true);
        $biblioteca_origine_id = get_post_meta($post->ID, '_prestito_biblioteca_origine_id', true);
        $biblioteca_destinazione_id = get_post_meta($post->ID, '_prestito_biblioteca_destinazione_id', true);
        $prestatario = get_post_meta($post->ID, '_prestito_prestatario', true);
        $data_inizio = get_post_meta($post->ID, '_prestito_data_inizio', true);
        $data_fine = get_post_meta($post->ID, '_prestito_data_fine', true);
        $stato = get_post_meta($post->ID, '_prestito_stato', true);

        // Render form fields
        echo '<p><label for="prestito_gioco_id">' . __('Gioco:', 'boardgamelibrary') . '</label> ';
        wp_dropdown_posts(array(
            'post_type' => 'gioco_da_tavolo',
            'selected' => $gioco_id,
            'name' => 'prestito_gioco_id',
            'show_option_none' => __('Seleziona un gioco', 'boardgamelibrary'),
            'option_none_value' => '',
        ));
        echo '</p>';

        echo '<p><label for="prestito_biblioteca_origine_id">' . __('Biblioteca di origine:', 'boardgamelibrary') . '</label> ';
        wp_dropdown_posts(array(
            'post_type' => 'biblioteca',
            'selected' => $biblioteca_origine_id,
            'name' => 'prestito_biblioteca_origine_id',
            'show_option_none' => __('Seleziona una biblioteca', 'boardgamelibrary'),
            'option_none_value' => '',
        ));
        echo '</p>';

        echo '<p><label for="prestito_biblioteca_destinazione_id">' . __('Biblioteca di destinazione:', 'boardgamelibrary') . '</label> ';
        wp_dropdown_posts(array(
            'post_type' => 'biblioteca',
            'selected' => $biblioteca_destinazione_id,
            'name' => 'prestito_biblioteca_destinazione_id',
            'show_option_none' => __('Seleziona una biblioteca', 'boardgamelibrary'),
            'option_none_value' => '',
        ));
        echo '</p>';

        echo '<p><label for="prestito_prestatario">' . __('Prestatario:', 'boardgamelibrary') . '</label> ';
        echo '<input type="text" id="prestito_prestatario" name="prestito_prestatario" value="' . esc_attr($prestatario) . '" /></p>';

        echo '<p><label for="prestito_data_inizio">' . __('Data inizio:', 'boardgamelibrary') . '</label> ';
        echo '<input type="date" id="prestito_data_inizio" name="prestito_data_inizio" value="' . esc_attr($data_inizio) . '" /></p>';

        echo '<p><label for="prestito_data_fine">' . __('Data fine:', 'boardgamelibrary') . '</label> ';
        echo '<input type="date" id="prestito_data_fine" name="prestito_data_fine" value="' . esc_attr($data_fine) . '" /></p>';

        echo '<p><label for="prestito_stato">' . __('Stato:', 'boardgamelibrary') . '</label> ';
        echo '<select id="prestito_stato" name="prestito_stato">';
        echo '<option value="in_corso"' . selected($stato, 'in_corso', false) . '>' . __('In corso', 'boardgamelibrary') . '</option>';
        echo '<option value="terminato"' . selected($stato, 'terminato', false) . '>' . __('Terminato', 'boardgamelibrary') . '</option>';
        echo '</select></p>';
    }

    public function save_prestito_meta($post_id) {
        if (!isset($_POST['prestito_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['prestito_meta_box_nonce'], 'prestito_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'prestito_gioco_id',
            'prestito_biblioteca_origine_id',
            'prestito_biblioteca_destinazione_id',
            'prestito_prestatario',
            'prestito_data_inizio',
            'prestito_data_fine',
            'prestito_stato'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}