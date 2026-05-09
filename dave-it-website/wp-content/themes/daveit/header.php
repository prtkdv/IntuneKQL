<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo( 'description' ); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="site-header">
    <div class="container">
        <nav class="nav-inner">
            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo" aria-label="<?php bloginfo( 'name' ); ?>">
                <div class="nav-logo-icon">D</div>
                <span class="nav-logo-text">Dave<span>IT</span></span>
            </a>

            <!-- Desktop Links -->
            <ul class="nav-links">
                <li><a href="#services">Services</a></li>
                <li><a href="#why-us">Why Us</a></li>
                <li><a href="#process">Process</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>

            <!-- Desktop CTA -->
            <a href="#contact" class="btn btn-primary nav-cta">Get Free Consultation</a>

            <!-- Mobile Toggle -->
            <button id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </nav>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" role="navigation" aria-label="Mobile navigation">
        <a href="#services">Services</a>
        <a href="#why-us">Why Us</a>
        <a href="#process">Process</a>
        <a href="#pricing">Pricing</a>
        <a href="#contact">Contact</a>
        <a href="#contact" class="btn btn-primary" style="width:100%;justify-content:center;">Get Free Consultation</a>
    </div>
</header>
