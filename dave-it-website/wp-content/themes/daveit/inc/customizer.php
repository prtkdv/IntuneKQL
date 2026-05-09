<?php
/**
 * Extended Customizer Controls for Dave IT theme
 * Additional sections beyond what's registered in functions.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function daveit_customizer_extended( $wp_customize ) {

    /* ---- Services Section ---- */
    $wp_customize->add_section( 'daveit_services', [
        'title'    => __( 'Services Section', 'daveit' ),
        'priority' => 40,
    ] );

    $wp_customize->add_setting( 'daveit_services_heading', [
        'default'           => 'Complete IT Services for Modern Business',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'daveit_services_heading', [
        'label'   => __( 'Services Heading', 'daveit' ),
        'section' => 'daveit_services',
        'type'    => 'text',
    ] );

    /* ---- Contact Section ---- */
    $wp_customize->add_section( 'daveit_contact', [
        'title'    => __( 'Contact & Company Info', 'daveit' ),
        'priority' => 50,
    ] );

    $contact_fields = [
        'daveit_address' => [ __( 'Office Address', 'daveit' ),  '123 Tech Avenue, Suite 400, Austin TX 78701' ],
        'daveit_email'   => [ __( 'Contact Email', 'daveit' ),   'hello@daveit.com' ],
        'daveit_phone'   => [ __( 'Phone Number', 'daveit' ),    '+1 (800) 328-3483' ],
        'daveit_tagline' => [ __( 'Footer Tagline', 'daveit' ),  'Your trusted partner for managed IT services.' ],
    ];

    foreach ( $contact_fields as $id => [ $label, $default ] ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => $label,
            'section' => 'daveit_contact',
            'type'    => 'text',
        ] );
    }
}
add_action( 'customize_register', 'daveit_customizer_extended' );
