<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Page Paramètres de Compte</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
      <style>
  body {
    background-color: #f7f8fa;
  }
  .card-custom {
    border-radius: 15px; /* Coins arrondis */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre subtile */
    background-color: #fff;
    margin-bottom: 30px;
  }
  .section-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #343a40;
    text-align: center;
  }
  .card-body p {
    font-size: 1rem;
    color: #6c757d;
  }
  .btn-custom {
    font-size: 1.1rem;
    padding: 12px 20px;
    width: 100%;
    background-color: #007bff; /* Bleu personnalisé */
    color: white; /* Texte blanc */
    border: none;
    transition: background-color 0.3s;
    border-radius: 5px; /* Coins arrondis pour les boutons */
  }
  .btn-custom:hover {
    background-color: #0056b3; /* Bleu plus foncé lors du survol */
  }
  .row-title {
    margin-bottom: 15px;
    font-weight: bold;
  }
</style>
    </style>
  </head>
  <body>
    <div class="container mt-5">
      <div class="row">
        <!-- Colonne de gauche - Informations utilisateur -->
        <div class="col-lg-4 col-md-12 mb-4">
          <div class="card card-custom">
            <div class="card-body">
              <h3 class="section-title">Informations du Compte</h3>
              <p><strong>Username:</strong> <?php  echo $_SESSION["nom"]; ?></p>
              <p><strong>Email:</strong> <?php  echo $_SESSION["email"]; ?></p>
              <p><strong>Date d'inscription:</strong> <?php  echo $_SESSION["createTime"];?></p>
              <p><strong>Rôle:</strong> Utilisateur</p>
            </div>
          </div>
        </div>

        <!-- Colonne de droite - Liste de boutons -->
        <div class="col-lg-8 col-md-12">
          <div class="card card-custom">
            <div class="card-body">
              <h3 class="section-title" style="margin-bottom: 20px">
                Historique Personnel
              </h3>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <button class="btn btn-custom">
                    Historique de Recherche
                  </button>
                </div>
                <div class="col-md-6 mb-3">
                  <button class="btn btn-custom">
                    Historique de Réservation
                  </button>
                </div>
              </div>

              <!-- Ligne de séparation -->
              <hr style="border: 1px solid #ccc; margin: 20px 0" />

              <!-- Nouveau div après les boutons -->
              <div class="col-12">
                <div class="card card-custom">
                  <div class="card-body" id="card">
                   

                    <!-- Carte-->

                    </div>

                    </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS (optionnel pour l'interactivité) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       
     
       var searchContent = "";
       var couleurSearch="";
fetch('http://localhost/api_files/get_search.php')
  .then(response => response.json())
  .then(data => {
    if(data.length !== 0){
    data.forEach(item => {
        if(item.resultat == 1){
            couleurSearch="bi bi-check-circle-fill me-2 text-success";
        }else{
            couleurSearch="bi bi-x-circle-fill me-2 text-danger";
        }
       
      searchContent = `
        <div class="card mb-3 historique-item">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title">${item.recherche} <i class="${couleurSearch}"></i> </h5>
              <p class="card-text">Date de Recherche : ${item.created_at}</p>
            </div>
            <button class="btn btn-danger btn-sm remove-btn" data-id="${item.id}" onclick="removeSearch(this)">Supprimer</button>
          </div>
        </div>`+searchContent;
    });

    searchContent=`<h3 class="section-title" style="margin-bottom: 20px;">Historique de recherche</h3>`+searchContent;
}else{
    searchContent=`<h3 class="section-title" style="margin-bottom: 20px;">Historique de recherche</h3><p style="text-align: center;">Aucun historique de recherche disponible.</p>`;
}
    document.getElementById("card").innerHTML = searchContent;

   
  })
  .catch(error => {
    console.error('Error:', error);
  });

  function removeSearch(button) {
    let searchContentVide="";
  const card = button.closest(".historique-item");
  card.remove();
  if(document.getElementsByClassName('historique-item').length === 0){
    searchContentVide=`<h3 class="section-title" style="margin-bottom: 20px;">Historique de recherche</h3><p style="text-align: center;">Aucun historique de recherche disponible.</p>`;
    document.getElementById("card").innerHTML = searchContentVide;
  }

  const dataId = button.getAttribute("data-id");
  if (dataId) {
        // Créer un objet contenant les données à envoyer
        const data = {
            id_search: dataId
        };

        // Envoyer les données via fetch (POST)
        fetch("http://localhost/api_files/remove_search.php", {
            method: "POST", 
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
        .catch(error => {
            console.error("Erreur : ", error);
        });
    } else {
        console.error("data-id non trouvé !");
    }
}
     
    </script>
  </body>
</html>
