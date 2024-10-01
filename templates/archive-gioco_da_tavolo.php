<?php get_header(); ?>

<div class="giochi-container">
    <h1><?php post_type_archive_title(); ?></h1>

    <div class="giochi-controls">
        <form id="giochi-sort-form" method="get">
            <label for="orderby">Ordina per:</label>
            <select name="orderby" id="orderby">
                <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>>Nome</option>
                <option value="difficulty" <?php selected(get_query_var('orderby'), 'difficulty'); ?>>Difficoltà</option>
                <option value="play_time" <?php selected(get_query_var('orderby'), 'play_time'); ?>>Durata partita</option>
                <option value="min_players" <?php selected(get_query_var('orderby'), 'min_players'); ?>>Minimo giocatori</option>
                <option value="max_players" <?php selected(get_query_var('orderby'), 'max_players'); ?>>Massimo giocatori</option>
                <option value="min_age" <?php selected(get_query_var('orderby'), 'min_age'); ?>>Età minima</option>
            </select>
            <label for="order">Ordine:</label>
            <select name="order" id="order">
                <option value="ASC" <?php selected(get_query_var('order'), 'ASC'); ?>>Crescente</option>
                <option value="DESC" <?php selected(get_query_var('order'), 'DESC'); ?>>Decrescente</option>
            </select>
            <input type="submit" value="Applica">
        </form>
    </div>

    <div id="giochi-list" class="giochi-grid">
        <?php
        // Modifica il numero di post per pagina a 12
        $args = array(
            'post_type' => 'gioco_da_tavolo',
            'posts_per_page' => 12, // Mostra 12 risultati
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        );

        // Aggiungi ordinamento se specificato
        if (get_query_var('orderby')) {
            $args['orderby'] = get_query_var('orderby');
            $args['order'] = get_query_var('order') ? get_query_var('order') : 'ASC';
        }

        $the_query = new WP_Query($args);

        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) : 
                $the_query->the_post();
                $min_players = get_post_meta(get_the_ID(), 'min_players', true);
                $max_players = get_post_meta(get_the_ID(), 'max_players', true);
                $min_age = get_post_meta(get_the_ID(), 'min_age', true);
                $play_time = get_post_meta(get_the_ID(), 'play_time', true);
                $difficulty = get_post_meta(get_the_ID(), 'difficulty', true);
                $ambientazione = get_the_terms(get_the_ID(), 'ambientazione');
                $meccanica = get_the_terms(get_the_ID(), 'meccanica');
                $stile_di_gioco = get_the_terms(get_the_ID(), 'stile_di_gioco');
                ?>
                <div class="gioco-item">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="gioco-cover">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="gioco-details">
                        <div class="gioco-meta">
                            <span class="gioco-players"><span class="dashicons dashicons-groups"></span> <?php echo $min_players . '-' . $max_players; ?></span>
                            <span class="gioco-age"><span class="dashicons dashicons-calendar"></span> <?php echo $min_age; ?>+</span>
                            <span class="gioco-time"><span class="dashicons dashicons-clock"></span> <?php echo $play_time; ?> min</span>
                        </div>
                        <div class="gioco-difficulty">
                            <span>Difficoltà:&nbsp;</span>
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $difficulty) {
                                    echo '<span class="dashicons dashicons-admin-generic"></span>';
                                } else {
                                    echo '<span class="dashicons dashicons-admin-generic inactive"></span>';
                                }
                            }
                            ?>
                        </div>
                        <?php if ($ambientazione) : ?>
                            <p>Ambientazione: <?php echo join(', ', wp_list_pluck($ambientazione, 'name')); ?></p>
                        <?php endif; ?>
                        <?php if ($meccanica) : ?>
                            <p>Meccanica: <?php echo join(', ', wp_list_pluck($meccanica, 'name')); ?></p>
                        <?php endif; ?>
                        <?php if ($stile_di_gioco) : ?>
                            <p>Stile di gioco: <?php echo join(', ', wp_list_pluck($stile_di_gioco, 'name')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile;
        else: ?>
            <p>Nessun gioco trovato.</p>
        <?php endif; ?>
        
    </div>

    <div class="pagination-controls">
        <?php
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => __('« Precedente', 'boardgamelibrary'),
            'next_text' => __('Successivo »', 'boardgamelibrary'),
        ));
        ?>
    </div>
</div>

<div id="cover-modal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="cover-image">
</div>

<?php get_footer(); ?>