<?php
class Boardgamelibrary_Biblioteca_Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_biblioteca_meta_boxes'));
        add_action('save_post', array($this, 'save_biblioteca_meta'));
    }

    public function add_biblioteca_meta_boxes() {
        add_meta_box(
            'biblioteca_details',
            __('Dettagli Biblioteca', 'boardgamelibrary'),
            array($this, 'render_biblioteca_meta_box'),
            'biblioteca',
            'normal',
            'default'
        );
    }

    public function render_biblioteca_meta_box($post) {
        wp_nonce_field('biblioteca_meta_box', 'biblioteca_meta_box_nonce');

        $address = get_post_meta($post->ID, '_biblioteca_address', true);
        $town = get_post_meta($post->ID, '_biblioteca_town', true);
        $phone = get_post_meta($post->ID, '_biblioteca_phone', true);
        $email = get_post_meta($post->ID, '_biblioteca_email', true);

        echo '<p><label for="biblioteca_address">' . __('Indirizzo:', 'boardgamelibrary') . '</label>';
        echo '<input type="text" id="biblioteca_address" name="biblioteca_address" value="' . esc_attr($address) . '" size="50" /></p>';

        echo '<p><label for="biblioteca_town">' . __('Città:', 'boardgamelibrary') . '</label>';
        echo '<input type="text" id="biblioteca_town" name="biblioteca_town" value="' . esc_attr($town) . '" /></p>';

        echo '<p><label for="biblioteca_phone">' . __('Telefono:', 'boardgamelibrary') . '</label>';
        echo '<input type="text" id="biblioteca_phone" name="biblioteca_phone" value="' . esc_attr($phone) . '" /></p>';

        echo '<p><label for="biblioteca_email">' . __('Email:', 'boardgamelibrary') . '</label>';
        echo '<input type="email" id="biblioteca_email" name="biblioteca_email" value="' . esc_attr($email) . '" /></p>';
    }

    public function save_biblioteca_meta($post_id) {
        if (!isset($_POST['biblioteca_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['biblioteca_meta_box_nonce'], 'biblioteca_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'biblioteca_address',
            'biblioteca_town',
            'biblioteca_phone',
            'biblioteca_email'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}