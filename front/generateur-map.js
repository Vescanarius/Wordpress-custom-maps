
/*--------------MAP-----------------*/

/*Variable pour infobulle*/
var infowindow;

function initialize() {

    /****** Récupération de toutes les maps ******/
    jQuery(function ($) {

       
       

        $('.map_canvas').each(function () {
            var lattitude = $(this).attr('lat');
            var longitude = $(this).attr('long');
            var categorie = $(this).attr('categorie');
            var zoomperso = $(this).attr('zoom');
            
            Creationmaps(this.id, lattitude, longitude, categorie, zoomperso);
        }
        );


        function Creationmaps(carte, lattitude, longitude, categorie, zoomperso) {
            /*Création de la map*/
            var map = 'map' + categorie;
            console.log (map);
            map = new google.maps.Map(document.getElementById(carte), {
                center: new google.maps.LatLng(lattitude, longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoom: 11
            });

            /*----------------REQUETE AJAX-------------------*/
            var points = {};
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", "../../wp-admin/admin-ajax.php?action=mon_action", false);
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    points = JSON.parse(xmlhttp.responseText);
                }
            };
            xmlhttp.send(null);


            /*Parcour du JSON*/
            for (var i in points) {

                var building = points[i];
                //console.log(categorie + " -- " + building.departement);
                if (categorie == building.departement) {
                    var location = new google.maps.LatLng(building.lat, building.lng);
                    addMarker(map, building.name, building.description, location, building.statut, building.desc_alert);
                }
            }
            
        }


    });
}


/*--Fonction pour ajouter le marker--*/
function addMarker(map, name, description, location, statut, messageAlert) {

    /*Creation du pin rouge ou vert*/
    var icon = "";
    switch (statut) {
        case "0":
            icon = "en-panne.png";
            break;
        case "1":
            icon = "fonctionnement.png";
            break;
    }
    icon = "/wp-content/plugins/carte-coupures/front/assets/" + icon;

    /*icon*/
    var marker = new google.maps.Marker({
        position: location,
        map: map,
        title: name,
        icon: new google.maps.MarkerImage(icon)
    });


    /*----Infos bulle-----*/

    /*Création du contenu*/
    var signalerButton = '';
    if (statut == 1) {/* Si "en fonctionnement" */
        //signalerButton = '<a href="/contact">Signaler une panne</a>';
        //messageAlert = '';
    } else if (messageAlert && statut == 0) { /*Si "en panne" et qu'une date à été rentrée*/
        messageAlert = '<div class="alert" style="color:red;"><strong>' + messageAlert + '</strong></div>';
    }

    var contentString = '<h3>' + name + '</h3>' +
            '<div class="imgleft"><img width="100" src="' + description + '"/>' + '</div><div class="txtright">'+ messageAlert +'</div>';


    /*Info bulle*/
    google.maps.event.addListener(marker, 'click', function () {
        /*On ferme les infos window ouvertes*/
        if (typeof infowindow != 'undefined')
            infowindow.close();
        infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        infowindow.open(map, marker);
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

jQuery(function ($) {

    $(".vc_tta-tab").click(function () {
        // initialize();


// redraw on tab changer - C4est moche mais ça marche pas bien autrement... a creuser 
        setTimeout(function () {
            //google.maps.event.trigger(mapmar, 'resize');
            initialize();
        }, 1);


    })
});