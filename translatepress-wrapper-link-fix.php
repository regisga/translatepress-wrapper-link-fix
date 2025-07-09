<?php
/**
 * Plugin Name: TranslatePress Wrapper Link Fix for XStore
 * Description: Corrige les liens "wrapper" du thème XStore pour qu'ils pointent vers la version traduite via TranslatePress.
 * Version:     1.0.0
 * Author:      Regis GA
 * License:     GPL2+
 * Text Domain: tp-wrapper-fix
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'plugins_loaded', function() {

    // S'assure que Elementor est actif côté public
    if ( ! defined( 'ELEMENTOR_VERSION' ) || is_admin() ) {
        return;
    }

    add_action( 'elementor/init', function() {
        add_action(
            'elementor/frontend/before_render',
            'tp_wrapper_link_translatepress_support',
            999 // Priorité haute pour surcharger le thème
        );
    } );

}, 20 );


/**
 * Remplace l'URL de data-etheme-element-link par celle traduite via TranslatePress.
 *
 * @param \Elementor\Element_Base $element
 */
function tp_wrapper_link_translatepress_support( $element ) {
    $link_settings = $element->get_settings_for_display( 'etheme_element_link' );

    if (
        ! $link_settings ||
        empty( $link_settings['url'] ) ||
        ! class_exists( 'TRP_Translate_Press' )
    ) {
        return;
    }

    $trp = TRP_Translate_Press::get_trp_instance();

    global $TRP_LANGUAGE;

    $translated_url = $trp
        ->get_component( 'url_converter' )
        ->get_url_for_language( $TRP_LANGUAGE, $link_settings['url'], '' );

    $link_settings['url'] = $translated_url;

    $element->set_render_attribute(
        '_wrapper',
        'data-etheme-element-link',
        wp_json_encode( $link_settings )
    );
}
