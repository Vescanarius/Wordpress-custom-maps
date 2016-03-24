(function ($) {   
/*----------------------------------------------------------------------------------*/
/*----------------Liste d'implantations---------------------------------------------*/
/*----------------------------------------------------------------------------------*/

    /*---------Desactivation de certain input quick edit------------*/
    $('span:contains("Mot de passe")').each(function (i) {
        $(this).parent().parent().remove();
    });

    $('.inline-edit-date').each(function (i) {
        $(this).remove();
    });
    
    $('span:contains("Identifiant")').each(function (i) {
        $(this).parent().remove();
    });
   

      $('span:contains("État")').each(function (i) {
        $(this).parent().parent().parent().parent().remove();
    });
  

     /*--------Le js pour la modification rapide-----------
     // we create a copy of the WP inline edit post function
        var $wp_inline_edit = inlineEditPost.edit;

        // and then we overwrite the function with our own code
        inlineEditPost.edit = function( id ) {
            console.log('text');

                // "call" the original WP edit function
                // we don't want to leave WordPress hanging
                $wp_inline_edit.apply( this, arguments );

                // now we take care of our business

                // get the post ID
                var $post_id = 0;
                if ( typeof( id ) == 'object' ) {
                        $post_id = parseInt( this.getId( id ) );
                }

                if ( $post_id > 0 ) {
                        // define the edit row
                        var $edit_row = $( '#edit-' + $post_id );
                        var $post_row = $( '#post-' + $post_id );

                        // get the data
                        var $book_author = $( '.column-desc_alert', $post_row ).text();

                        // populate the data
                        $( ':input[name="desc_alert"]', $edit_row ).text( $book_author );
                }
        };
        */
       
       
    /*------Requete ajax pour l'edition groupée----------*/
    $('#bulk_edit').live('click', function () {
        // define the bulk edit row
        var $bulk_row = $('#bulk-edit');

        // get the selected post ids that are being edited
        var $post_ids = new Array();
            $bulk_row.find('#bulk-titles').children().each(function () {
            $post_ids.push($(this).attr('id').replace(/^(ttle)/i, ''));
        });
        // get the data
        var $statut = $bulk_row.find('#statut option:selected').val();
        var $desc_alert = $bulk_row.find('textarea[name="desc_alert"]').val();
        
        console.log($desc_alert + 'test');
        // save the data
        $.ajax({
            url: ajaxurl, // this is a variable that WordPress has already defined for us
            type: 'POST',
            async: false,
            cache: false,
            data: {
                action: 'save_bulk_edit_carte_coupures', // this is the name of our WP AJAX function that we'll set up next
                post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
                statut: $statut,
                desc_alert: $desc_alert
            }
        });
    });


/*----------------------------------------------------------------------------------*/
/*----------------Implantation------------------------------------------------------*/
/*----------------------------------------------------------------------------------*/

    /*Validation form*/
   jQuery(".post-type-carte_coupures #post").validate({
        rules: {
            lat: {
                required: true,
                minlength: 7
            },
            lng: {
                required: true,
                minlength: 7
            }
        },
        messages: {
            lat: {
                required: "Ce champ est obligatoire",
                minlength: "valeur trop courte"
            },
            lng: {
                required: "Ce champ est obligatoire",
                minlength: "valeur trop courte"
            }
        }

    });
       
})(jQuery);

