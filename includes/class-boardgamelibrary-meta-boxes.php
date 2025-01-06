<?php
class Boardgamelibrary_Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_gioco_da_tavolo_meta_boxes'));
        add_action('add_meta_boxes', array($this, 'add_gioco_da_tavolo_copies_meta_box'));
        add_action('save_post', array($this, 'save_gioco_da_tavolo_meta'));
        add_action('wp_ajax_add_game_copy', array($this, 'add_game_copy_ajax'));
    }

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

        if (isset($_POST['gioco_da_tavolo_copies_meta_box_nonce']) && 
            wp_verify_nonce($_POST['gioco_da_tavolo_copies_meta_box_nonce'], 'gioco_da_tavolo_copies_meta_box')) {
            
            $copies = isset($_POST['copies']) ? $_POST['copies'] : array();
            update_post_meta($post_id, '_gioco_da_tavolo_copies', $copies);
        }
    }

    public function add_gioco_da_tavolo_copies_meta_box() {
        add_meta_box(
            'gioco_da_tavolo_copies',
            __('Copie e Prestiti', 'boardgamelibrary'),
            array($this, 'render_gioco_da_tavolo_copies_meta_box'),
            'gioco_da_tavolo',
            'normal',
            'default'
        );
    }

    public function render_gioco_da_tavolo_copies_meta_box($post) {
        wp_nonce_field('gioco_da_tavolo_copies_meta_box', 'gioco_da_tavolo_copies_meta_box_nonce');

        $copies = get_post_meta($post->ID, '_gioco_da_tavolo_copies', true);
        if (!is_array($copies)) {
            $copies = array();
        }

        echo '<div id="gioco-da-tavolo-copies">';
        echo '<h4>' . __('Copie del gioco', 'boardgamelibrary') . '</h4>';
        
        foreach ($copies as $index => $copy) {
            $this->render_copy_fields($index, $copy);
        }

        echo '<button type="button" id="add-copy">' . __('Aggiungi copia', 'boardgamelibrary') . '</button>';
        echo '</div>';

        // Add JavaScript to handle dynamic copy fields
        $this->add_copies_javascript();
    }

    private function render_copy_fields($index, $copy) {
        $libraries = get_posts(array('post_type' => 'biblioteca', 'posts_per_page' => -1));

        echo '<div class="copy-fields">';
        echo '<h5>' . sprintf(__('Copia %d', 'boardgamelibrary'), $index + 1) . '</h5>';
        
        echo '<label>' . __('Biblioteca di default:', 'boardgamelibrary') . '</label>';
        echo '<select name="copies[' . $index . '][default_library]">';
        foreach ($libraries as $library) {
            echo '<option value="' . $library->ID . '" ' . selected($copy['default_library'], $library->ID, false) . '>' . $library->post_title . '</option>';
        }
        echo '</select>';

        echo '<label>' . __('Stato:', 'boardgamelibrary') . '</label>';
        echo '<select name="copies[' . $index . '][status]">';
        echo '<option value="available" ' . selected($copy['status'], 'available', false) . '>' . __('Disponibile', 'boardgamelibrary') . '</option>';
        echo '<option value="on_loan" ' . selected($copy['status'], 'on_loan', false) . '>' . __('In prestito', 'boardgamelibrary') . '</option>';
        echo '</select>';

        if ($copy['status'] == 'on_loan') {
            echo '<div class="loan-details">';
            echo '<label>' . __('Biblioteca di destinazione:', 'boardgamelibrary') . '</label>';
            echo '<select name="copies[' . $index . '][loan_library]">';
            foreach ($libraries as $library) {
                echo '<option value="' . $library->ID . '" ' . selected($copy['loan_library'], $library->ID, false) . '>' . $library->post_title . '</option>';
            }
            echo '</select>';

            echo '<label>' . __('Prestatario:', 'boardgamelibrary') . '</label>';
            echo '<input type="text" name="copies[' . $index . '][borrower]" value="' . esc_attr($copy['borrower']) . '">';

            echo '<label>' . __('Data inizio prestito:', 'boardgamelibrary') . '</label>';
            echo '<input type="date" name="copies[' . $index . '][loan_start]" value="' . esc_attr($copy['loan_start']) . '">';

            echo '<label>' . __('Data fine prestito:', 'boardgamelibrary') . '</label>';
            echo '<input type="date" name="copies[' . $index . '][loan_end]" value="' . esc_attr($copy['loan_end']) . '">';
            echo '</div>';
        }

        echo '<button type="button" class="remove-copy">' . __('Rimuovi copia', 'boardgamelibrary') . '</button>';
        echo '</div>';
    }

    private function add_copies_javascript() {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            console.log('Copies JavaScript loaded');
            var copyIndex = <?php 
                $copies = get_post_meta(get_the_ID(), '_gioco_da_tavolo_copies', true);
                echo is_array($copies) ? count($copies) : 0;
            ?>;
            $('#add-copy').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'add_game_copy',
                        index: copyIndex,
                        post_id: <?php echo get_the_ID(); ?>,
                        nonce: '<?php echo wp_create_nonce('add_game_copy_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var newCopy = $('<div class="copy-fields"></div>').html(response.data);
                            $('#gioco-da-tavolo-copies').append(newCopy);
                            copyIndex++;
                        } else {
                            console.error("Error adding new copy: " + response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: " + status + " " + error);
                    }
                });
            });

            $(document).on('click', '.remove-copy', function() {
                $(this).closest('.copy-fields').remove();
            });

            $(document).on('change', 'select[name^="copies"][name$="[status]"]', function() {
                var loanDetails = $(this).closest('.copy-fields').find('.loan-details');
                if ($(this).val() == 'on_loan') {
                    loanDetails.show();
                } else {
                    loanDetails.hide();
                }
            });
        });
        </script>
        <?php
    }

    public function add_game_copy_ajax() {
        if (!check_ajax_referer('add_game_copy_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $index = isset($_POST['index']) ? intval($_POST['index']) : 0;
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error('Invalid post ID');
            return;
        }

        ob_start();
        $this->render_copy_fields($index, array(
            'default_library' => '',
            'status' => 'available',
            'loan_library' => '',
            'borrower' => '',
            'loan_start' => '',
            'loan_end' => ''
        ));
        $html = ob_get_clean();

        wp_send_json_success($html);
    }
}