<?php
/**
 * Dave IT Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DAVEIT_VERSION', '1.0.0' );
define( 'DAVEIT_DIR', get_template_directory() );
define( 'DAVEIT_URI', get_template_directory_uri() );

/* ------------------------------------------
   Theme Setup
   ------------------------------------------ */
function daveit_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    register_nav_menus( [
        'primary'   => __( 'Primary Navigation', 'daveit' ),
        'footer'    => __( 'Footer Navigation', 'daveit' ),
    ] );

    load_theme_textdomain( 'daveit', DAVEIT_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'daveit_setup' );

/* ------------------------------------------
   Enqueue Assets
   ------------------------------------------ */
function daveit_assets() {
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap', [], null );
    wp_enqueue_style( 'daveit-style', get_stylesheet_uri(), [ 'google-fonts' ], DAVEIT_VERSION );
    wp_enqueue_script( 'daveit-main', DAVEIT_URI . '/js/main.js', [], DAVEIT_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'daveit_assets' );

/* ------------------------------------------
   Register Widget Areas
   ------------------------------------------ */
function daveit_widgets_init() {
    register_sidebar( [
        'name'          => __( 'Footer Column 1', 'daveit' ),
        'id'            => 'footer-1',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ] );
}
add_action( 'widgets_init', 'daveit_widgets_init' );

/* ------------------------------------------
   Custom Post Type: Services
   ------------------------------------------ */
function daveit_register_cpts() {
    register_post_type( 'daveit_service', [
        'labels' => [
            'name'          => __( 'Services', 'daveit' ),
            'singular_name' => __( 'Service', 'daveit' ),
        ],
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-admin-tools',
        'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
    ] );

    register_post_type( 'daveit_testimonial', [
        'labels' => [
            'name'          => __( 'Testimonials', 'daveit' ),
            'singular_name' => __( 'Testimonial', 'daveit' ),
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-format-quote',
        'supports'     => [ 'title', 'editor' ],
    ] );
}
add_action( 'init', 'daveit_register_cpts' );

/* ------------------------------------------
   Contact Form Handler (AJAX)
   ------------------------------------------ */
function daveit_handle_contact() {
    check_ajax_referer( 'daveit_contact_nonce', 'nonce' );

    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $email   = sanitize_email( $_POST['email'] ?? '' );
    $message = sanitize_textarea_field( $_POST['message'] ?? '' );

    if ( empty( $name ) || ! is_email( $email ) || empty( $message ) ) {
        wp_send_json_error( [ 'message' => __( 'Please fill in all fields correctly.', 'daveit' ) ] );
    }

    $to      = get_option( 'admin_email' );
    $subject = sprintf( __( 'New contact from %s — Dave IT', 'daveit' ), $name );
    $body    = sprintf( "Name: %s\nEmail: %s\n\n%s", $name, $email, $message );
    $headers = [ 'Content-Type: text/plain; charset=UTF-8', "Reply-To: $email" ];

    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( $sent ) {
        wp_send_json_success( [ 'message' => __( 'Thanks! We\'ll be in touch shortly.', 'daveit' ) ] );
    } else {
        wp_send_json_error( [ 'message' => __( 'There was a problem sending your message. Please try again.', 'daveit' ) ] );
    }
}
add_action( 'wp_ajax_daveit_contact',        'daveit_handle_contact' );
add_action( 'wp_ajax_nopriv_daveit_contact', 'daveit_handle_contact' );

/* ------------------------------------------
   Customizer Options
   ------------------------------------------ */
function daveit_customizer( $wp_customize ) {
    $wp_customize->add_section( 'daveit_hero', [
        'title'    => __( 'Hero Section', 'daveit' ),
        'priority' => 30,
    ] );

    $fields = [
        'daveit_hero_heading'  => [ __( 'Hero Heading', 'daveit' ),    'IT Solutions\nBuilt for\nBusiness Growth' ],
        'daveit_hero_subtitle' => [ __( 'Hero Subtitle', 'daveit' ),   'We keep your technology running so you can focus on what matters — growing your business.' ],
        'daveit_hero_cta'      => [ __( 'Hero CTA Text', 'daveit' ),   'Get a Free Consultation' ],
        'daveit_phone'         => [ __( 'Phone Number', 'daveit' ),     '+1 (800) 328-3483' ],
    ];

    foreach ( $fields as $id => [ $label, $default ] ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
        $wp_customize->add_control( $id, [ 'label' => $label, 'section' => 'daveit_hero', 'type' => 'text' ] );
    }
}
add_action( 'customize_register', 'daveit_customizer' );

/* ------------------------------------------
   Helper: get theme mod with fallback
   ------------------------------------------ */
function daveit_mod( string $key, string $fallback = '' ): string {
    return esc_html( get_theme_mod( $key, $fallback ) );
}
