<?php
session_start();

$registered = isset($_SESSION['register']) && $_SESSION['register'] === true; // Vérifie si la session 'register' est définie et vraie
$mail_error = isset($_SESSION['mail_error']) && $_SESSION['mail_error'] === true; // Vérifie si la session 'mail_error' est définie et vraie
$psw_error = isset($_SESSION['psw_error']) && $_SESSION['psw_error'] === true; // Vérifie si la session 'psw_error' est définie et vraie




?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ FontAwesome Icons (pour une belle icône si besoin) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="page/css/login.css"> <!-- Lien vers le fichier CSS externe -->


</head>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- ✅ Ton fichier CSS personnalisé -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div class="container-fluid">
        <div class="row vh-100 align-items-center justify-content-center">
            
            <!-- ✅ Image à gauche (disparaît en mode mobile) -->
            <div class="col-md-6 d-none d-md-flex justify-content-center align-items-center image-container">
                <img src="page/images/image.png"class="image-full">
            </div>

            <!-- ✅ Formulaire de connexion (toujours visible) -->
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div class="login-form">
                    <h2 class="text-center">Connexion</h2>

                    <!-- ✅ Alertes session -->
            <?php
            session_start();
            if (isset($_SESSION['register']) && $_SESSION['register'] === true) {
                echo '<div class="alert alert-success" role="alert">
                        Inscription réussie ! Vous pouvez maintenant vous connecter.
                      </div>';
                $_SESSION['register'] = false;
            }

            if (isset($_SESSION['mail_error']) && $_SESSION['mail_error'] === true) {
                echo '<div class="alert alert-danger" role="alert">
                        Erreur : Mail incorrect.
                      </div>';
                $_SESSION['mail_error'] = false;
            } elseif (isset($_SESSION['psw_error']) && $_SESSION['psw_error'] === true) {
                echo '<div class="alert alert-danger" role="alert">
                        Erreur : Mot de passe incorrect.
                      </div>';
                $_SESSION['psw_error'] = false;
            }
            ?>

                    <form action="requete/log" method="POST">
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Adresse mail" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Mot de passe" required>
                        </div>
                        <input type="hidden" name="methode" value="login">
                        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                    </form>

                    <p class="text-center mt-3">Pas encore inscrit ? <a href="/register">S'inscrire</a></p>
                </div>
            </div>

        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

</html>
