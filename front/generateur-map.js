

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


/*--------------MAP-----------------*/

/*Variable pour infobulle*/
var infowindow;

function initialize() {
    /*Création de la map*/
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
        center: new google.maps.LatLng(14.6500000, -61.0297823),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 11
    });

    /*Parcour du JSON*/
    for (var i in points) {
        var building = points[i];
        var location = new google.maps.LatLng(building.lat, building.lng);
        addMarker(map, building.name, building.description, location, building.statut, building.desc_alert);
    }
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
        if(statut == 1){/* Si "en fonctionnement" */
           signalerButton = '<a href="/contact">Signaler une panne</a>';
           messageAlert = '';
        }
        else if(messageAlert && statut == 0) { /*Si "en panne" et qu'une date à été rentrée*/
          messageAlert = '<div class="alert" style="color:red;"><strong>' + messageAlert + '</strong></div>';  
        }
        
        var contentString = '<h3>'+ name +'</h3>' +
        '<p>'+ description +'</p>' + signalerButton + messageAlert;


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
