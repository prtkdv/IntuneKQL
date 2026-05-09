<?php get_header(); ?>

<main style="min-height:80vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:120px 24px 80px;">
    <div>
        <div style="font-size:6rem;font-weight:900;color:var(--primary);letter-spacing:-0.05em;line-height:1;">404</div>
        <h1 style="font-size:2rem;font-weight:800;margin:16px 0 12px;color:#fff;">Page Not Found</h1>
        <p style="color:var(--text-muted);margin-bottom:32px;max-width:420px;margin-left:auto;margin-right:auto;">
            The page you're looking for doesn't exist or has been moved. Let's get you back on track.
        </p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary btn-lg">← Back to Home</a>
    </div>
</main>

<?php get_footer(); ?>
