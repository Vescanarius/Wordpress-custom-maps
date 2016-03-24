<?php
/**
*
* Plugin Name: Carte des coupures 
* Plugin URI: Na
* Description: Carte interactive des coupures d'eau sur la Martinique
* Version: 1.0
* Author: Graphidom - Roman Boursier
* Author URI: http://www.graphidom.fr
* License: Na
*/


// Nous vérifier l'existence de certaines constantes.
if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');


/*---------------------Partie admin----------------------------*/
if (is_admin()) {  
    require_once('admin/admin.php'); 
}

/*---------------------Partie front----------------------------*/
    include('front/front.php'); 


?>
