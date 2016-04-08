<?php

/* -----------------Inclusion des js necessaires-------------------------- */
add_action('wp_enqueue_scripts', 'cc_scripts');

function cc_scripts() {
    if (is_page('ou-trouver-nos-produits')) {
        wp_enqueue_style('cc_css', plugins_url() . '/carte-coupures/front/front-css.css');
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js');
        wp_enqueue_script('script', plugins_url() . '/carte-coupures/front/generateur-map.js', array(), '1.0.', true);
        /* wp_localize_script('script', 'ajaxurl', admin_url( 'admin-ajax.php' ) ); à approfondir */
    }
}

/* Ne pas regarder le nombre max de post par pages
  function get_all_carte_coupures_posts( $query ) {
  if(is_page('carte-des-coupures')) {
  $query->set( 'posts_per_page', '-1' );
  }
  }
  add_action( 'pre_get_posts', 'get_all_carte_coupures_posts' );
 */

/* -----------------Requête ajax-------------------------- */
add_action('wp_ajax_mon_action', 'mon_action');
add_action('wp_ajax_nopriv_mon_action', 'mon_action');

function mon_action() {

    $json_response = array();
    $ajax_query = new WP_Query(array('post_type' => 'carte_coupures', 'posts_per_page' => -1));
    if ($ajax_query->have_posts()) {

        while ($ajax_query->have_posts()) {
            global $post;
            $ajax_query->the_post();
            $row_array['name'] = get_the_title();
            $row_array['description'] = get_post_meta($post->ID, '_description', true);
            $row_array['lat'] = get_post_meta($post->ID, '_lat', true);
            $row_array['lng'] = get_post_meta($post->ID, '_lng', true);
            $row_array['statut'] = get_post_meta($post->ID, '_statut', true);
            $row_array['departement'] = get_post_meta($post->ID, '_departement', true);
            $row_array['desc_alert'] = get_post_meta($post->ID, '_desc_alert', true);
            array_push($json_response, $row_array);
        }
        echo json_encode($json_response);
        /* wp_reset_postdata();à approfondir */
    } else {
        // no posts found
    }

    die();
}

?>
