<?php
session_start();

echo $_SESSION['id'];
if (!isset($_SESSION['email'])) {
    header('Location: login');
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Carte des stations Vélib'</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" /> <!-- Ajout de la feuille de style du géocodeur -->
    <link rel="stylesheet" href="page/css/styles.css"> <!-- Lien vers le fichier CSS externe -->
</head>
<body>
    
    <div id="map"></div> <!-- Conteneur pour la carte -->
        <div id="topRightSquare"><img src="page/images/personnes.png" id="clickableImage"> 
    </div>

    <div id="overlay"></div> 

    <div id="searchBar"> <!-- Barre de recherche -->
        <input type="text" id="searchInput" placeholder="Rechercher une station..."/> 
    </div>

    <div id="errorPopup"> <!--  Pop up erreur -->
        <p id="errorMessage"></p> 
    </div>
    
    <div id="popup"> <!--  Pop up contenue -->
        <div id="popupContent"> </div>
    </div>

    <div id="popupInfo" style="display: none;"> <!-- Second pop-up -->
        <div id="popupContentInfo">
    </div>
    </div>


    <div id="popupRes">
    <span class="close-btn" onclick="removePopupRes()">X</span> <!-- Bouton de fermeture -->
    <div id="popupContentRes" class="bike-container">
    <div class="bike-option">
        <img src="page/images/bike_me.png" alt="Vélo mécanique" class="bike-logo">
        <label>
            Vélo mécanique</br>
            <span id="mechDispo"></span>
            <input id="inputMech" type="radio" name="bike-type" value="mechanical">
        </label>
    </div>
    <div class="bike-option">
        <img src="page/images/bike_el.png" alt="Vélo électrique" class="bike-logo">
        <label>
            Vélo électrique</br>
            <span id="elDispo">fg</span>
            <input id="inputEl" type="radio" name="bike-type" value="electric">
        </label>
    </div>
    <button class="submitConfirmation" onclick="submitSelection()">Confirmer</button>
</div>

</div>
    
    
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script> <!-- Ajout du script du geocodeur -->


    <script>
        
    var storedId;

    var clientReservation;


    var floatingMarker; // Stocket l'icone de personnage jaune qui prsente les cordonners de user
    var map;
    var routingControl; // Variable pour stocker l'iteneraire 

    // déclenché lorsque le document HTML a ete completement charge et analyse par le navigateur
    document.addEventListener("DOMContentLoaded", function() { 
        // Cree une carte centree sur Paris
        map = L.map('map').setView([48.8566, 2.3522], 16); 

        // Ajouter des tuiles de carte OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        map.invalidateSize(); // informer Leaflet que la taille de la carte a change

        // Récupérer les coordonnées de l'utilisateur
if (navigator.geolocation) { // Demander au navigateur de nous envoyer les coordonnées GPS
    navigator.geolocation.getCurrentPosition(
        function(position) { // Toutes les données GPS de l'utilisateur seront envoyées dans position

            const lat1 = position.coords.latitude; // Stocker la latitude
            const lon1 = position.coords.longitude; // Stocker la longitude

            // Créer et afficher l'icône sur la carte
            floatingMarker = L.marker([lat1, lon1], { 
                icon: L.icon({
                    iconUrl: imageSrc, // Image de l'icône
                    iconSize: [40, 40], // Taille de l'icône
                    iconAnchor: [20, 10] // Point d'ancrage pour l'icône
                })
            }).addTo(map).bindPopup(`<b>Votre position actuelle</b>`).openPopup();

            // Stocker les coordonnées dans un attribut personnalisé
            floatingMarker.altValue = { lat: lat1, lng: lon1 }; // Stocker les coordonnées dans altValue
        },
        function(error) {
            console.error("Erreur lors de la récupération de la position : " + error.message);
        }
    );
} else {
    console.error("La géolocalisation n'est pas supportée par ce navigateur.");
}


        console.log(floatingMarker) ;


        // Récupérer les données JSON des stations de vélo depuis le serveur Flask
        fetch('http://127.0.0.1:5000/stations')  // L'URL est relative à la base de l'application Flask
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                    return response.json(); // convertit -> objet
                })
                .then(data => {
                    // Parcourir les stations et les afficher sur la carte
                    data.forEach(station => {
                        const marker = L.marker([station.lat, station.lon]).addTo(map);// ajout de chaque station
                        marker.station_id = station.station_id; // stocker id dans attribue station_id
                        marker.bindPopup(`<b>Station: ${station.name}</b><br>Capacité: ${station.capacity}`);// afficher un 

                        marker.on('click', function () { // sur chaque station ajouter un evenement click 
                            console.log('Station ID:', this.station_id);

                            // Effectuer un fetch pour obtenir les détails de la station
                            fetch(`http://127.0.0.1:5000/station/${this.station_id}`) // Remplace avec l'URL de ton API Flask
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Erreur réseau : ' + response.status);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Données de la station:', data); // Afficher les données de la station

                                    // Vérifier si la station est trouvée dans les données
                                   if (data) {
                                    const inputMech= document.getElementById('inputMech');
                                    const inputEl= document.getElementById('inputEl');
                                    if (data.num_bikes_available_types[0].mechanical === 0) {
                                           inputMech.disabled = true;
                                           document.getElementById('mechDispo').textContent ="Pas Disponible";
                                           document.getElementById('mechDispo').style.color="red";
                                        } else {
                                         inputMech.disabled = false;
                                         document.getElementById('mechDispo').textContent ="Disponible";
                                           document.getElementById('mechDispo').style.color="green";
                                          }       

// Désactiver le champ s'il n'y a pas de vélos électriques disponibles
                                        if (data.num_bikes_available_types[1].ebike === 0) {
                                         inputEl.disabled = true;
                                         document.getElementById('elDispo').textContent ="Pas Disponible";
                                           document.getElementById('elDispo').style.color="red";
                                        } else {
                                                 inputEl.disabled = false;
                                                 document.getElementById('elDispo').textContent="Disponible";
                                           document.getElementById('elDispo').style.color="green";
                                            }
                                  document.getElementById('popupContent').innerHTML = `
        <button id="submitButton" lat="${station.lat}" lon="${station.lon}" onclick="submitForm()" nom="${station.name}">Itinéraire</button>
        <button class="submitRes" lat="${station.lat}" lon="${station.lon}" onclick="submitRes(${data.is_installed},${data.is_returning})" nom="${station.name}">Réserver</button>
        <strong>Nom:</strong> ${station.name}<br><br>
        <strong>Capacité:</strong> ${station.capacity}<br><br>
        <strong>Vélos disponibles:</strong> ${data.num_bikes_available} </br>
        <strong> Vélos mécaniques:</strong> ${data.num_bikes_available_types[0].mechanical} ||
        <strong> Vélos électriques:</strong> ${data.num_bikes_available_types[1].ebike}`;
        clientReservation={
            station: station.name,
            lon: station.lon,
            lat: station.lat,
            station_id: station.station_id
        };
            
    // Sélectionner le bouton de réservation
    const button = document.querySelector('.submitRes');

    if (data.is_installed === 0) {
        document.getElementById('popupContentInfo').innerHTML = `<p style="color:red">La station est en cours de déploiement.</p>`;
        button.classList.add('disabled');
    } else if (data.is_returning === 0 ) {
        document.getElementById('popupContentInfo').innerHTML = `<p style="color:red">Cette station ne peut pas louer de vélos actuellement.</p>`;
        button.classList.add('disabled');
    } else if(data.num_bikes_available === 0) {
        document.getElementById('popupContentInfo').innerHTML = `<p style="color:red">Désolé, il n'y a pas de vélos disponibles à cette station.</p>`;
        button.classList.add('disabled');
    }else{
        document.getElementById('popupContentInfo').innerHTML = `<p style="color:green">La station est disponible pour la réservation.</p>`;
        if (button.classList.contains('disabled')) {
            button.classList.remove('disabled'); // Retirer la classe 'disabled' si elle existe
        }
    }
    
    document.querySelector('.submitConfirmation').classList.add('disabled');
    document.getElementById('popupInfo').style.display = 'block'; // Afficher la fenêtre d'information
    document.getElementById('popup').style.display = 'block'; // Afficher le popup principal
}
 else {
                                        console.error('Station non trouvée dans les données');
                                    }
                                })
                                .catch(error => {
                                    console.error('Erreur lors de la récupération des données de la station:', error);
                                });
                        });
                    });
                })
                .catch(error => console.error('Erreur lors de la récupération des données:', error));
        });

        var affichageMessage;
        document.addEventListener("DOMContentLoaded", function () {
            // Créez une fonction pour rechercher la station
            function searchStation() {
                const name = document.getElementById('searchInput').value.trim(); // recuperer le champs saisie dans le input text
                
                console.log("regr");
                
               // envoie la variable vers le script php pour stocker chaque recherche faite sur SQL
                
                if (name) { 
                    // Vérifier si le nom n'est pas vide
                    fetch(`http://127.0.0.1:5000/search_station/${name}`) // retourne les information d'une station 
                        .then(response => {
                            // Vérifier si la réponse est correcte
                            if (!response.ok) {
                                throw new Error(`Erreur réseau : ${response.status}`); // Lancer une erreur si la réponse n'est pas OK
                            }
                            return response.json(); // Retourner le JSON
                        })
                        .then(data => {
                            if (data.error) {
                                sendData(name,false,0);
                                showErrorPopup('<p>Station / Adresse non trouvée.</p>',1400); // Message d'erreur pour l'utilisateur
                            } else {

                                if (data.station_id === null) {
                                    affichageMessage = "Localisation trouvée!";
                                } else {
                                    affichageMessage = "Station trouvée!";
                                }

                                sendData(name,true,data.station_id);
                                console.log(`Latitude: ${data.lat}, Longitude: ${data.lon}`); // Affiche les coordonnées de la station
                                map.setView([data.lat, data.lon], 16); // pour aller voir la station
                                const marker = L.marker([data.lat, data.lon]).addTo(map)
                                    .bindPopup(`<b>${affichageMessage}</b>`)
                                    .openPopup(); // faire afficher un markeur(sur lencienne markeur) pour voir quelle station et apres la supprimer
                                    setTimeout(() => {
                                        map.removeLayer(marker);
                                    }, 1350);

                            }
                        })
                        .catch(error => {
                            showErrorPopup('<p>Station / Adresse Invalide</p>',1400);
                        });
                } else {
                    showErrorPopup("<p>Veuillez entrer un nom de station / adresse.</p>",1400); // Affiche le message si le champ est vide
                }
            }

            // Événement pour appuyer sur la touche "Entrée" dans le champ de recherche
            document.getElementById('searchInput').addEventListener('keypress', function (event) {
                if (event.key === 'Enter') {
                    searchStation(); // Appeler la fonction de recherche si "Entrée" est pressée
                }
            });
        });



//CREARTION DE ITENNIRAIRE//
function traceRoute(depart,destination) {
    const start = depart; // Point de départ (par exemple, Paris)

    // Si un itinéraire est déjà tracé, le supprimer
    if (routingControl) {
        map.removeControl(routingControl);
    }

    // Tracer l'itinéraire vers la destination
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(start),      // Point de départ
            L.latLng(destination) // Point de destination
        ],
        routeWhileDragging: true,
        geocoder: L.Control.Geocoder.nominatim(), // Utilisation du géocodeur
        createMarker: function() { return null; }, // Ne pas créer de marqueurs
        show: false, // Ne pas afficher le panneau de routage
        collapsible: false, // Ne pas permettre de développer/replier le panneau
        hide: true // S'assurer que le panneau de routage est caché  
    }).addTo(map);
}

        // POUR AFFICHER POP D'ERREUR
        function showErrorPopup(message,time) {
            const errorPopup = document.getElementById('errorPopup');
            const errorMessage = document.getElementById('errorMessage');

            errorMessage.innerHTML  = message; // Mettre à jour le message
            errorPopup.style.display = 'block'; // Afficher le pop-up

            // Utiliser setTimeout pour faire disparaître le pop-up après un certain temps
            setTimeout(() => {
                errorPopup.style.display = 'none';
            }, time); // Délai de 1350 ms avant de commencer à disparaître
        }

        //+######################################""""   //+######################################""""
//SCRIPT POUR PLACER MA POSITION N'IMPORTE OU DANS LA MAP
const square = document.getElementById('topRightSquare'); // recuperer le div ou ce trouve mon markeure 
const imageSrc = 'page/images/personnes.png'; // Lien de l'image à suivre



// Événements pour les ordinateurs de bureau
square.addEventListener('mousedown', startDragging);
document.addEventListener('mouseup', removeMarker);

// Événements pour les appareils tactiles
square.addEventListener('touchstart', startDragging);
document.addEventListener('touchend', removeMarker);

// Fonction pour démarrer le glissement
function startDragging(event) {
    
    event.preventDefault();// Empecher le comportement par defaut ou icone sera glisser auddi

    if (floatingMarker) {
        map.removeLayer(floatingMarker); // Retirer le marqueur de la carte si il existe 
      
    }
    const defaultLatLng = L.latLng(1000000000000, 5100000); // pour le moment ou en creer notre markeur il se naffiche pas sur la map 

    floatingMarker = L.marker(map.getCenter(), { // creer un nouveau marqueur flottant
        icon: L.icon({
            iconUrl: imageSrc,
            iconSize: [40, 40], // Taille de l'icône
            iconAnchor: [20, 40] // Ancre pour le bas de l'icône
        })
    }).addTo(map); // Ajouter le marqueur à la carte

    floatingMarker.setLatLng(defaultLatLng); // postiononner le markeure endroit ou on peut pas le voir

    // Déplacer le marqueur avec le curseur ou le doigt
    document.addEventListener('mousemove', moveMarker);
    document.addEventListener('touchmove', moveMarker);
}


// Fonction pour déplacer le marqueur
function moveMarker(event) {
    if (floatingMarker) {
        // si on est sur smartphone on prend le premier var sinon le 2 eme
        const clientX = event.changedTouches ? event.changedTouches[0].clientX : event.clientX;
        const clientY = event.changedTouches ? event.changedTouches[0].clientY : event.clientY;

        // Convertir les coordonnées de l'écran en coordonnées de la carte
        var latLng = map.containerPointToLatLng(L.point(clientX, clientY));
        floatingMarker.setLatLng(latLng); // Déplacer le marqueur à la position lat/lng
  
    floatingMarker.altValue = latLng;  // stacoker les donner du markeure dans attribue  altValue
    }
}


// Fonction pour retirer le marqueur flottant
function removeMarker(event) {
    
        

        // Retirer le marqueur flottant après relâchement
     
         // Réinitialiser
        document.removeEventListener('mousemove', moveMarker);
        document.removeEventListener('touchmove', moveMarker);
    
}




// Ajouter un gestionnaire d'événements pour supprimer le marqueur lorsque le icone est cliqué
square.addEventListener('click', function(event) {
    if (floatingMarker) {
        map.removeLayer(floatingMarker); // Retirer le marqueur de la carte
        if (routingControl){ 
            map.removeControl(routingControl); // aussi etiniraire 
        }
        floatingMarker = null; // Réinitialiser le marqueur
    }
});

// Ajouter un gestionnaire d'événements pour les appareils tactiles
square.addEventListener('touchstart', function(event) {
    if (floatingMarker) {
      if (routingControl){
            map.removeControl(routingControl);
        }
    
    }
});

///////////////////////////////// TERMINER /////////////////////////////////////

function submitForm() {
    if (floatingMarker) {
        const info = document.getElementById('submitButton');
        const latt = info.getAttribute('lat');
        const lonn = info.getAttribute('lon');
        const name = info.getAttribute('nom');

        const altValue = floatingMarker.altValue;
        if (!altValue || !altValue.lat || !altValue.lng) {
            if (routingControl) {
                map.removeControl(routingControl);
            }
            showErrorPopup('<p>Vous devez sélectionner un point de départ.</p>',1400);
            return;
        }

        const coordinatesArray_destination = [latt, lonn];
        const coordinatesArray_depart = [altValue.lat, altValue.lng];
        traceRoute(coordinatesArray_depart, coordinatesArray_destination);
        document.getElementById("searchInput").value = name;

    } else {
        showErrorPopup("<p>Vous devez sélectionner un point de départ.</p>",1400);
    }
}

function submitRes(is_installed,is_returning) {
    contentPopUp=document.getElementById('popupContentRes');
    if (is_installed ===0 ){
        contentPopUp.innerHTML=`<p>La station est en cours de déploiement</p>`;
    }
   
    document.getElementById('popupRes').style.display = 'block'; // Afficher la fenêtre
    document.getElementById('overlay').style.display = 'block'; // Afficher l'overlay
    console.log(is_installed,is_returning);
}

function inputRen(){
    const radios = document.querySelectorAll('input[name="bike-type"]');
    radios.forEach(radio => {
        radio.checked = false;
    });
}

function removePopupRes(){
    document.getElementById('popupRes').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    inputRen();
    
}

function submitSelection() {
    
    // Sélectionne le bouton radio coché dans le groupe "bike-type"
    const selectedBike = document.querySelector('input[name="bike-type"]:checked');
        // Récupère la valeur de l'option sélectionnée
        const bikeType = selectedBike.value;
        console.log(`Vous avez sélectionné: ${bikeType}`);
        document.getElementById('popupRes').style.display = 'none';
        inputRen()
        document.getElementById('overlay').style.display = 'none';
        const id_confirmation=Math.floor(10000 + Math.random() * 90000).toString();
        showErrorPopup(`<p style="margin: 0px;">Merci, votre réservation a été confirmée !</p>
                        </br>  <p style="margin: 0px;font-weight: bold;">***Id de confirmation: ${id_confirmation}***</p>`,4000);
                        clientReservation.confirmationID=id_confirmation;
                        clientReservation.type=bikeType;
                        clientReservation.methode="post_order";
                        
  

                        fetch('controller/PostGetController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            
        },
        body: JSON.stringify(clientReservation)
    })
    
    .catch(error => {
        console.error('Erreur:', error);
    });
}


document.querySelectorAll('input[name="bike-type"]').forEach(input => {
    // Ajout d'un event listener 'click' pour chaque input
    input.addEventListener('click', function() {
        // Ici, tu peux ajouter ce que tu veux faire pour chaque clic
        const submitButton = document.querySelector('.submitConfirmation');
        
        
        // Si une option est sélectionnée, on enlève la classe 'disabled'
       
            submitButton.classList.remove('disabled');
        
    });
});
////////////////// BACK-END FUNCTION ///////////////////////////////////////////////////////
function sendData(searchs,bool,staionID) {
        const data={
                search:searchs,
                resultat:bool,
                station_id:staionID,
                methode:"post_search"
        };

        fetch('controller/PostGetController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrf-token': '<?php echo $_SESSION["token"]; ?>'
            },
            body: JSON.stringify(data)
        })

    .catch(error => console.error('Erreur:', error));



}


   


    
    </script>
</body>
</html>
