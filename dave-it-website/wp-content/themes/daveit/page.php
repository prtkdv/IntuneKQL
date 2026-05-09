<?php get_header(); ?>

<main id="main" style="padding:120px 0 80px;">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h1 style="font-size:clamp(2rem,5vw,3.2rem);font-weight:900;letter-spacing:-0.03em;margin-bottom:24px;">
                    <?php the_title(); ?>
                </h1>
                <div class="entry-content" style="color:var(--text-muted);line-height:1.8;max-width:760px;">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
