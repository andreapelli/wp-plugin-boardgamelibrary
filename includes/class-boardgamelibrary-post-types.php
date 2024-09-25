<?php
class Boardgamelibrary_Post_Types {
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
}
