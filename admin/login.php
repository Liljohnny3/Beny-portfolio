<?php
session_start();
require_once '../includes/db.php';

if(isset($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admin_user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if($user && $password === $user['password']) {
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Identifiants incorrects.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="max-width: 450px; margin-top: 10%; justify-content: center;">
        <div style="background-color: var(--card-bg); padding: 2rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <h2 style="margin-bottom: 1.5rem; text-align: center;">Connexion Espace Admin</h2>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Identifiant</label>
                    <input type="text" name="username" class="form-control" required placeholder="admin">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 1rem;">Se connecter</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem;"><a href="../index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Retour au site</a></p>
        </div>
    </div>
</body>
</html>