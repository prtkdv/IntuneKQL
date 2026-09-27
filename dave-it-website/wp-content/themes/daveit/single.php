<?php get_header(); ?>

<main id="main" style="padding:120px 0 80px;">
    <div class="container" style="max-width:800px;">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div style="margin-bottom:12px;">
                    <span style="font-size:0.82rem;color:var(--text-muted);">
                        <?php echo get_the_date(); ?> &bull; <?php the_category( ', ' ); ?>
                    </span>
                </div>
                <h1 style="font-size:clamp(1.8rem,4vw,2.6rem);font-weight:900;letter-spacing:-0.03em;margin-bottom:28px;line-height:1.2;">
                    <?php the_title(); ?>
                </h1>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:36px;">
                        <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;' ] ); ?>
                    </div>
                <?php endif; ?>
                <div class="entry-content" style="color:var(--text);line-height:1.85;font-size:1.05rem;">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
