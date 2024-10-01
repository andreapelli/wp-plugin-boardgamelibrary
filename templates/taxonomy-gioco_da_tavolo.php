<?php get_header(); ?>

<div class="giochi-container">
    <h1><?php single_term_title(); ?></h1>
    <div class="giochi-grid">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                $min_players = get_post_meta(get_the_ID(), 'min_players', true);
                $max_players = get_post_meta(get_the_ID(), 'max_players', true);
                $min_age = get_post_meta(get_the_ID(), 'min_age', true);
                $play_time = get_post_meta(get_the_ID(), 'play_time', true);
                $difficulty = get_post_meta(get_the_ID(), 'difficulty', true);
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
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Nessun gioco trovato.</p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
