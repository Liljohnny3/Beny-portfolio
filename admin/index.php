<?php
session_start();
require_once '../includes/db.php';

// Route Guard
if(!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle Delete Action
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    if($stmt->execute([$id])) {
        $success = "Projet supprimé avec succès !";
    } else {
        $error = "Impossible de supprimer le projet.";
    }
}

// Handle Add / Edit Action
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $technologies = trim($_POST['technologies']);
    $github_link = trim($_POST['github_link']);
    $demo_link = trim($_POST['demo_link']);

    if(!empty($title) && !empty($description) && !empty($category) && !empty($technologies)) {
        if($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, category=?, technologies=?, github_link=?, demo_link=? WHERE id=?");
            $stmt->execute([$title, $description, $category, $technologies, $github_link, $demo_link, $id]);
            $success = "Projet mis à jour !";
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, category, technologies, github_link, demo_link) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category, $technologies, $github_link, $demo_link]);
            $success = "Nouveau projet ajouté avec succès !";
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Fetch all projects for listing
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();

// If editing a project, fetch its specific data
$editProject = null;
if(isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$editId]);
    $editProject = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="logo">Espace Admin</a>
            <ul class="nav-links">
                <li><a href="../index.php" target="_blank">Voir le Site ↗</a></li>
                <li><a href="logout.php" style="color: #ef4444;">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="background-color: var(--card-bg); padding: 2rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 3rem;">
            <h2><?php echo $editProject ? "Modifier le projet" : "Ajouter un nouveau projet"; ?></h2>
            <form action="index.php" method="POST" style="margin-top: 1.5rem;">
                <input type="hidden" name="id" value="<?php echo $editProject ? $editProject['id'] : ''; ?>">
                
                <div class="form-group">
                    <label>Titre du projet *</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo $editProject ? htmlspecialchars($editProject['title']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Catégorie * (ex: Web, Réseau, Système, POO)</label>
                    <input type="text" name="category" class="form-control" required placeholder="Web" value="<?php echo $editProject ? htmlspecialchars($editProject['category']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Technologies utilisées * (Séparées par des virgules)</label>
                    <input type="text" name="technologies" class="form-control" required placeholder="PHP, HTML, Bootstrap, MySQL" value="<?php echo $editProject ? htmlspecialchars($editProject['technologies']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Lien GitHub</label>
                    <input type="url" name="github_link" class="form-control" placeholder="https://github.com/..." value="<?php echo $editProject ? htmlspecialchars($editProject['github_link']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Lien Démo Live</label>
                    <input type="url" name="demo_link" class="form-control" placeholder="https://..." value="<?php echo $editProject ? htmlspecialchars($editProject['demo_link']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Description détaillée *</label>
                    <textarea name="description" class="form-control" rows="5" required><?php echo $editProject ? htmlspecialchars($editProject['description']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn"><?php echo $editProject ? "Mettre à jour" : "Publier le projet"; ?></button>
                <?php if($editProject): ?>
                    <a href="index.php" class="btn btn-secondary">Annuler l'édition</a>
                <?php endif; ?>
            </form>
        </div>

        <h2>Gestion des projets publiés</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Technologies</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($projects)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">Aucun projet pour l'instant.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($projects as $p): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                            <td><span class="tech-tag" style="color: var(--primary);"><?php echo htmlspecialchars($p['category']); ?></span></td>
                            <td><?php echo htmlspecialchars($p['technologies']); ?></td>
                            <td>
                                <a href="index.php?edit=<?php echo $p['id']; ?>" class="btn" style="padding: 0.3rem 0.7rem; font-size: 0.85rem;">Modifier</a>
                                <a href="index.php?delete=<?php echo $p['id']; ?>" class="btn" style="padding: 0.3rem 0.7rem; font-size: 0.85rem; background-color: #ef4444;" onclick="return confirm('Supprimer ce projet ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </main>

</body>
</html>