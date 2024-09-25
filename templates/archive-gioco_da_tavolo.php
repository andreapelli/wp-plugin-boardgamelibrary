<?php get_header(); ?>

<div class="giochi-container">
    <h1><?php post_type_archive_title(); ?></h1>

    <div class="giochi-controls">
        <button id="grid-view">Vista Griglia</button>
        <button id="list-view">Vista Lista</button>
    </div>

    <!-- L'ordinamento è stato temporaneamente nascosto
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
    -->

    <div id="giochi-list" class="giochi-grid">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
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
            
            the_posts_pagination();
            
        else :
            echo '<p>Nessun gioco trovato.</p>';
        endif;
        ?>
    </div>
</div>

<div id="cover-modal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="cover-image">
</div>

<?php get_footer(); ?>