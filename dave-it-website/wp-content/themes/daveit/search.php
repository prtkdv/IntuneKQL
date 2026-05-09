<?php get_header(); ?>

<main id="main" style="padding:120px 0 80px;">
    <div class="container">
        <h1 style="font-size:clamp(1.8rem,4vw,2.6rem);font-weight:900;margin-bottom:8px;">
            Search Results for: <span style="color:var(--accent);"><?php echo get_search_query(); ?></span>
        </h1>
        <p style="color:var(--text-muted);margin-bottom:40px;">
            <?php echo $wp_query->found_posts; ?> result(s) found
        </p>

        <?php if ( have_posts() ) : ?>
            <div style="display:flex;flex-direction:column;gap:24px;">
            <?php while ( have_posts() ) : the_post(); ?>
                <article style="background:var(--dark-2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;">
                    <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:10px;">
                        <a href="<?php the_permalink(); ?>" style="color:#fff;"><?php the_title(); ?></a>
                    </h2>
                    <p style="color:var(--text-muted);font-size:0.92rem;line-height:1.65;"><?php the_excerpt(); ?></p>
                </article>
            <?php endwhile; ?>
            </div>
            <div style="margin-top:40px;"><?php the_posts_pagination(); ?></div>
        <?php else : ?>
            <p style="color:var(--text-muted);">No results found. Try a different search term.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
