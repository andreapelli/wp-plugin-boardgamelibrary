<?php
class Boardgamelibrary_Post_Types {

    public function __construct() {
        add_action('init', [$this, 'register_gioco_da_tavolo_post_type']);
        add_action('init', [$this, 'register_biblioteca_post_type']);
        add_action('init', [$this, 'register_prestito_post_type']);
        add_action('init', [$this, 'register_taxonomies']);
        
        // Add these new actions
        add_filter('manage_biblioteca_posts_columns', [$this, 'add_biblioteca_columns']);
        add_action('manage_biblioteca_posts_custom_column', [$this, 'display_biblioteca_columns'], 10, 2);
        add_filter('manage_edit-biblioteca_sortable_columns', [$this, 'make_biblioteca_columns_sortable']);
        add_action('pre_get_posts', [$this, 'biblioteca_custom_orderby']);
    }

    public function register_gioco_da_tavolo_post_type() {
        $labels = array(
            'name'               => _x('Giochi da Tavolo', 'post type general name', 'boardgamelibrary'),
            'singular_name'      => _x('Gioco da Tavolo', 'post type singular name', 'boardgamelibrary'),
            'menu_name'          => _x('Giochi da Tavolo', 'admin menu', 'boardgamelibrary'),
            'name_admin_bar'     => _x('Gioco da Tavolo', 'add new on admin bar', 'boardgamelibrary'),
            'add_new'            => _x('Aggiungi Nuovo', 'gioco', 'boardgamelibrary'),
            'add_new_item'       => __('Aggiungi Nuovo Gioco da Tavolo', 'boardgamelibrary'),
            'new_item'           => __('Nuovo Gioco da Tavolo', 'boardgamelibrary'),
            'edit_item'          => __('Modifica Gioco da Tavolo', 'boardgamelibrary'),
            'view_item'          => __('Visualizza Gioco da Tavolo', 'boardgamelibrary'),
            'all_items'          => __('Tutti i Giochi da Tavolo', 'boardgamelibrary'),
            'search_items'       => __('Cerca Giochi da Tavolo', 'boardgamelibrary'),
            'parent_item_colon'  => __('Gioco da Tavolo Genitore:', 'boardgamelibrary'),
            'not_found'          => __('Nessun gioco da tavolo trovato.', 'boardgamelibrary'),
            'not_found_in_trash' => __('Nessun gioco da tavolo trovato nel cestino.', 'boardgamelibrary')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'gioco-da-tavolo'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
        );

        register_post_type('gioco_da_tavolo', $args);
    }

    public function register_biblioteca_post_type() {
        $labels = array(
            'name'               => _x('Biblioteche', 'post type general name', 'boardgamelibrary'),
            'singular_name'      => _x('Biblioteca', 'post type singular name', 'boardgamelibrary'),
            'menu_name'          => _x('Biblioteche', 'admin menu', 'boardgamelibrary'),
            'name_admin_bar'     => _x('Biblioteca', 'add new on admin bar', 'boardgamelibrary'),
            'add_new'            => _x('Aggiungi Nuova', 'biblioteca', 'boardgamelibrary'),
            'add_new_item'       => __('Aggiungi Nuova Biblioteca', 'boardgamelibrary'),
            'new_item'           => __('Nuova Biblioteca', 'boardgamelibrary'),
            'edit_item'          => __('Modifica Biblioteca', 'boardgamelibrary'),
            'view_item'          => __('Visualizza Biblioteca', 'boardgamelibrary'),
            'all_items'          => __('Tutte le Biblioteche', 'boardgamelibrary'),
            'search_items'       => __('Cerca Biblioteche', 'boardgamelibrary'),
            'parent_item_colon'  => __('Biblioteca Genitore:', 'boardgamelibrary'),
            'not_found'          => __('Nessuna biblioteca trovata.', 'boardgamelibrary'),
            'not_found_in_trash' => __('Nessuna biblioteca trovata nel cestino.', 'boardgamelibrary')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'biblioteca'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author')
        );

        register_post_type('biblioteca', $args);
    }

    public function register_taxonomies() {
        $taxonomies = [
            'ambientazione' => 'Ambientazione',
            'meccanica' => 'Meccanica',
            'stile_di_gioco' => 'Stile di Gioco'
        ];

        foreach ($taxonomies as $taxonomy => $label) {
            register_taxonomy($taxonomy, 'gioco_da_tavolo', [
                'label' => __($label, 'boardgamelibrary'),
                'rewrite' => ['slug' => $taxonomy],
                'hierarchical' => true,
            ]);
        }
    }

    public function add_biblioteca_columns($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key == 'title') {
                $new_columns[$key] = $value;
                $new_columns['city'] = __('Città', 'boardgamelibrary');
            } else {
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public function display_biblioteca_columns($column, $post_id) {
        switch ($column) {
            case 'city':
                $city = get_post_meta($post_id, '_biblioteca_town', true);
                echo esc_html($city);
                break;
        }
    }

    public function make_biblioteca_columns_sortable($columns) {
        $columns['city'] = 'city';
        return $columns;
    }

    public function biblioteca_custom_orderby($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') === 'biblioteca') {
            $orderby = $query->get('orderby');
        }
    }

    public function register_prestito_post_type() {
        $labels = array(
            'name'               => _x('Prestiti', 'post type general name', 'boardgamelibrary'),
            'singular_name'      => _x('Prestito', 'post type singular name', 'boardgamelibrary'),
            'menu_name'          => _x('Prestiti', 'admin menu', 'boardgamelibrary'),
            'name_admin_bar'     => _x('Prestito', 'add new on admin bar', 'boardgamelibrary'),
            'add_new'            => _x('Aggiungi Nuovo', 'prestito', 'boardgamelibrary'),
            'add_new_item'       => __('Aggiungi Nuovo Prestito', 'boardgamelibrary'),
            'new_item'           => __('Nuovo Prestito', 'boardgamelibrary'),
            'edit_item'          => __('Modifica Prestito', 'boardgamelibrary'),
            'view_item'          => __('Visualizza Prestito', 'boardgamelibrary'),
            'all_items'          => __('Tutti i Prestiti', 'boardgamelibrary'),
            'search_items'       => __('Cerca Prestiti', 'boardgamelibrary'),
            'parent_item_colon'  => __('Prestito Genitore:', 'boardgamelibrary'),
            'not_found'          => __('Nessun prestito trovato.', 'boardgamelibrary'),
            'not_found_in_trash' => __('Nessun prestito trovato nel cestino.', 'boardgamelibrary')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'prestito'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'author')
        );

        register_post_type('prestito', $args);
    }
}