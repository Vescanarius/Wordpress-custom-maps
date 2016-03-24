<?php
/*---------------------------------------------------------------------------------------*/
/*----------------------------Inclusion des js/css----------------------------------------*/
/*----------------------------------------------------------------------------------------*/

/*head*/
add_action('admin_enqueue_scripts', 'add_cc_admin_scripts');
function add_cc_admin_scripts(){
    
    wp_enqueue_style( 'cc_admin_css', plugins_url() .'/carte-coupures/admin/admin-css.css' );
    wp_enqueue_script('jquery_validate', plugins_url() .'/carte-coupures/admin/js/jquery.validate.min.js', array('jquery'));
}

/* Script footer*/
add_action( 'admin_enqueue_scripts', 'add_cc_footer_enqueue_scripts' );
if ( ! function_exists('wp_my_admin_enqueue_scripts') ):
    function add_cc_footer_enqueue_scripts( $hook ) {
    global $post_type;
    if( $post_type == 'carte_coupures' ){
        wp_enqueue_script( 'my_custom_script', plugins_url('js/admin_edit.js', __FILE__),false, null, true );	
    } 	
}
endif;


/*---------------------------------------------------------------------------------------*/
/*-------------------------------Création du custom post type-----------------------------*/
/*----------------------------------------------------------------------------------------*/
add_action('init', 'create_post_type');
function create_post_type() {
    register_post_type('carte_coupures', array(
        'labels' => array(
            'name'                => __( 'Carte', THEMENAME ),
            'singular_name'       => __( 'Implantations', THEMENAME ),
            'add_new'             => __( 'Ajouter', THEMENAME ),
            'add_new_item'        => __( 'Ajouter une nouvelle implantation', THEMENAME ),
            'edit_item'           => __( 'Editer l\'implantation', THEMENAME ),
            'new_item'            => __( 'Nouvelle implantation', THEMENAME ),
            'all_items'           => __( 'Toutes les implantations', THEMENAME ),
            'view_item'           => __( 'Voir l\'implantation', THEMENAME ),
            'search_items'        => __( 'rechercher une implantation', THEMENAME ),
            'not_found'           => __( 'Aucune implantation trouvée', THEMENAME ),
            'not_found_in_trash'  => __( 'Aucune implantation dans la corbeille', THEMENAME ),
            'menu_name'           => __( 'Carte', THEMENAME ),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'carte_coupures'),
        'exclude_from_search' => true,
        'menu_icon' => 'dashicons-location',
        'supports' => array('title'),
            )
    );

}


/*------------------------------------------------------------------------------------*/
/*-----------------Création des metaboxes---------------------------------------------*/
/*------------------------------------------------------------------------------------*/

add_action('add_meta_boxes', 'init_metabox');
function init_metabox() {
    add_meta_box('infos_implantation', 'Informations sur l\'implantation', 'infos_implantation', 'carte_coupures', 'normal');
    add_meta_box('alert_implantation', 'Alert', 'alert_implantation', 'carte_coupures', 'normal');
}

/*-------Construction des metaboxes----------*/

/*Les informations*/
function infos_implantation($post) {
    $lat = get_post_meta($post->ID, '_lat', true);
    $lng = get_post_meta($post->ID, '_lng', true);
    $description = get_post_meta($post->ID, '_description', true);
    ?>
        <label for="lat">Latitude : </label>
        <input id="lat" maxlength="11" type="text" name="lat" value="<?php echo $lat; ?>"/>
        
        <label for="lng">Longitude : </label>
        <input id="lng" maxlength="11" type="text" name="lng" value="<?php echo $lng; ?>"/>
        
        <label for="description">La description générique : </label>
        <textarea id="description" name="description"><?php echo $description; ?></textarea>
    <?php
}

/* Le statut -  */
function alert_implantation($post) {
    $statut =  get_post_meta($post->ID, '_statut', true);
    $desc_alert =  get_post_meta($post->ID, '_desc_alert', true);
    ?>
        <label>Statut :</label>
        <select name="statut" id="statut">
            <option value="1" <?php if($statut == '1'){echo 'selected';}?>>En fonctionnement</option>
            <option value="0" <?php if($statut == '0'){echo 'selected';}?>>En panne</option>
        </select>
        
        <label for="desc-alert">Le message d'alert en cas de panne : </label>
        <textarea id="desc-alert" name="desc_alert"><?php echo $desc_alert; ?></textarea>
    <?php 
}

/*----Sauvegarde des metaboxes----*/
add_action('save_post', 'save_metabox');
function save_metabox($post_id) {
    /*informations implantations*/
    if (isset($_POST['lat'])) {
        update_post_meta($post_id, '_lat', esc_html($_POST['lat']));
    }
    if (isset($_POST['lng'])) {
        update_post_meta($post_id, '_lng', esc_html($_POST['lng']));
    }
    if (isset($_POST['description'])) {
        update_post_meta($post_id, '_description', esc_textarea($_POST['description']));
    }
    /*les alerts*/ 
    if (isset($_POST['statut'])) {
        update_post_meta($post_id, '_statut', esc_html($_POST['statut']));
    }
    if (isset($_POST['desc_alert'])) {
        update_post_meta($post_id, '_desc_alert', esc_html($_POST['desc_alert']));
    } 
}


/*---------------------------------------------------------------------------------------*/
/*-----------------Paramétrage de la liste admin ----------------------------------------*/
/*---------------------------------------------------------------------------------------*/
/*----On change le tri par default-----------*/
function set_carte_coupures_admin_order($wp_query) {
  if (is_admin()) {
    $post_type = $wp_query->query['post_type'];
    if ( $post_type == 'carte_coupures') {
      $wp_query->set('orderby', 'title');
      $wp_query->set('order', 'ASC');
    }
  }
}
add_filter('pre_get_posts', 'set_carte_coupures_admin_order');


/*----Ajout du statut dans les colonnes -----*/
add_filter('manage_carte_coupures_posts_columns', 'gppm_implantation_table_head');
function gppm_implantation_table_head( $defaults ) {
    $defaults['statut'] = 'Statut';
    $defaults['desc_alert'] = 'Message d\'alert';
    return $defaults;
}

/*On récupère la valeur de statut dans chaques colonnes*/
add_action( 'manage_carte_coupures_posts_custom_column' , 'custom_carte_coupures_column', 10, 2 );
function custom_carte_coupures_column( $column, $post_id ){  
    /*couleur en fonction de l'état*/
    $statut = get_post_meta( $post_id , '_statut' , true ); 
    if($statut == '1'){ $couleur_voyant = 'vert'; } else {$couleur_voyant = 'rouge';}
    $desc_alert = get_post_meta( $post_id , '_desc_alert' , true );
    
    switch ( $column ) {
      case 'statut':
        echo '<div class="voyant '.$couleur_voyant.'"></div>'; 
        break;
     case 'desc_alert':
        echo $desc_alert;
        break;
    }
}

/*-----On désactive la colonne date-----*/
add_filter( 'manage_edit-carte_coupures_columns', 'carte_coupures_filter',10, 1 );
function carte_coupures_filter( $columns ) {
   unset($columns['date']);
   return $columns;
}

/*-----On désactive la modification rapide-----*/
add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
function remove_row_actions( $actions )
{
    if( get_post_type() === 'carte_coupures' )
        unset( $actions['inline hide-if-no-js'] );
    return $actions;
}


/*---------------------------------------------------------------------------------------*/
/*-----------------Actions groupées--------------------------------------------------*/
/*---------------------------------------------------------------------------------------*/

/*add_action( 'quick_edit_custom_box', 'display_custom_quickedit_carte_coupures', 10, 2 ); Au cas on l'on veut réactiver la modification rapide*/
add_action( 'bulk_edit_custom_box', 'display_custom_quickedit_carte_coupures', 10, 2 );
function display_custom_quickedit_carte_coupures( $column_name, $post_type ) {
    
    static $printNonce = TRUE;
    if ( $printNonce ) {
        $printNonce = FALSE;
        wp_nonce_field( plugin_basename( __FILE__ ), 'carte_coupures_edit_nonce' );
    }
    ?>
    <!--Message d'alert-->
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <div class="inline-edit-group">
            <?php 
             switch ( $column_name ) {
             case 'statut':?>
                  <label class="inline-edit-<?php echo $column_name; ?>">
                    <span class="title">Statut :</span>
                    <select name="statut" id="statut">
                        <option value="-1">- Modifier -</option>
                        <option value="1">En fonctionnement</option>
                        <option value="0">En panne</option>
                    </select>
                  </label><?php
                 break;
             case 'desc_alert':?> 
                <label class="inline-edit-<?php echo $column_name; ?>">
                    <span for="title">Le message d'alert en cas de panne : </span>
                    <textarea id="desc-alert" name="desc_alert"></textarea>
                    
                </label><?php
                break;
            }
            ?>
       </div>
      </div>
    </fieldset>
    <?php
}

/*Save ajax action groupées*/
add_action( 'wp_ajax_save_bulk_edit_carte_coupures', 'save_bulk_edit_carte_coupures' );
function save_bulk_edit_carte_coupures() {
	// TODO perform nonce checking
	// get our variables
	$post_ids   = ( ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
	$statut = $_POST[ 'statut' ];
        $desc_alert = $_POST[ 'desc_alert' ];
        
	// if everything is in order
	if ( ! empty( $post_ids ) && is_array( $post_ids )) {
		foreach( $post_ids as $post_id ) {
                    
                    if($statut !== '-1'){
                        update_post_meta( $post_id, '_statut', $statut ); 
                        }
                        
                    if(!empty($desc_alert)){/*On envoie que si rempli*/
                            update_post_meta( $post_id, '_desc_alert', $desc_alert ); 
                    }        
		}
	}
	die();
}