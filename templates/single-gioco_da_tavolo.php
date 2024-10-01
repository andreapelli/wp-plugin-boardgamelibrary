<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        while ( have_posts() ) : the_post();
            // Contenuto del gioco da tavolo
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header>

                <div class="entry-content">
                    <?php
                    // Mostra l'immagine di copertura
                    if (has_post_thumbnail()) {
                        echo '<div class="gioco-cover">';
                        the_post_thumbnail('large');
                        echo '</div>';
                    }

                    // Mostra il contenuto
                    the_content();

                    // Mostra i campi personalizzati
                    $min_players = get_post_meta(get_the_ID(), 'min_players', true);
                    $max_players = get_post_meta(get_the_ID(), 'max_players', true);
                    $min_age = get_post_meta(get_the_ID(), 'min_age', true);
                    $play_time = get_post_meta(get_the_ID(), 'play_time', true);
                    $difficulty = get_post_meta(get_the_ID(), 'difficulty', true);
                    ?>
                    <div class="gioco-details">
                        <p><strong>Giocatori:</strong> <?php echo $min_players . '-' . $max_players; ?></p>
                        <p><strong>Età:</strong> <?php echo $min_age; ?>+</p>
                        <p><strong>Durata:</strong> <?php echo $play_time; ?> min</p>
                        <p><strong>Difficoltà:</strong> 
                            <div class="gioco-difficulty">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $difficulty) {
                                        echo '<span class="dashicons dashicons-admin-generic"></span>'; // Icona piena
                                    } else {
                                        echo '<span class="dashicons dashicons-admin-generic inactive"></span>'; // Icona vuota
                                    }
                                }
                                ?>
                            </div>
                        </p>
                    </div>

                    <?php
                    // Mostra le tassonomie
                    $taxonomies = array('ambientazione', 'meccanica', 'stile_di_gioco');
                    foreach ($taxonomies as $taxonomy) {
                        $terms = get_the_terms(get_the_ID(), $taxonomy);
                        if ($terms && !is_wp_error($terms)) {
                            echo '<p><strong>' . ucfirst(str_replace('_', ' ', $taxonomy)) . ':</strong> '; // Sostituisci underscore con spazio
                            foreach ($terms as $term) {
                                echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>, ';
                            }
                            echo '</p>';
                        }
                    }

                    // Mostra l'elenco dei materiali
                    $materiali = get_post_meta(get_the_ID(), 'materiali', true);
                    if ($materiali) {
                        echo '<h2>Materiali inclusi</h2>';
                        echo '<ul class="gioco-materiali">';
                        $materiali_array = explode(',', $materiali);
                        foreach ($materiali_array as $materiale) {
                            echo '<li>' . trim($materiale) . '</li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </main>
</div>

<?php get_footer(); ?>