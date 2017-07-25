<?php
/*
 * Plugin Name:       Timberin testimonials
 * Description:       Simple testimonials plugin created by Plum Tree Systems
 * Version:           0.1.0
 * Author:            Plum Tree Systems
 * Author URI:        http://plumtreesystems.com
 * Text Domain:       timberin-testimonials-locale
 * Plugin URI:        https://github.com/PlumTreeSystems/timberin-testimonials

 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class.timberin-testimonials.php';

function run_timberin_testimonials(){
    $tt = new TimberinTestimonials();
    $tt->run();
    register_activation_hook( __FILE__, [$tt, 'install'] );
}

run_timberin_testimonials();

