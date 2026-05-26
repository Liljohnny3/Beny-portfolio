<?php
require_once 'includes/db.php';

// Fetch projects
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
    
    // Get unique categories for filters
    $catStmt = $pdo->query("SELECT DISTINCT category FROM projects");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $projects = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Portfolio Dynamique</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="logo">Dev&amp;Reseau_Portfolio</a>
            <ul class="nav-links">
                <li><a href="#projets" class="active">Projets</a></li>
                <li><a href="admin/login.php">Administration</a></li>
                <li><button id="theme-toggle" class="theme-toggle">☀️ Mode Clair</button></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="hero">
            <h1>Bonjour, je suis <span>Étudiant en Informatique</span></h1>
            <p>Spécialisé en Systèmes, Réseaux et Développement d'applications. Découvrez mes réalisations techniques ci-dessous.</p>
            <a href="#projets" class="btn">Voir mes projets</a>
        </section>

        <section id="projets">
            <h2 style="text-align: center; margin-bottom: 1.5rem;">Mes Réalisations</h2>
            
            <!-- Dynamic Filters -->
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">Tous</button>
                <?php foreach($categories as $category): ?>
                    <button class="filter-btn" data-filter="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars(ucfirst($category)); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Projects Grid -->
            <div class="projects-grid">
                <?php if(empty($projects)): ?>
                    <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">
                        Aucun projet publié pour le moment. Allez sur l'espace d'administration pour en rajouter !
                    </p>
                <?php else: ?>
                    <?php foreach($projects as $project): ?>
                        <div class="project-card" data-category="<?php echo htmlspecialchars($project['category']); ?>">
                            <div class="project-body">
                                <span class="project-category"><?php echo htmlspecialchars($project['category']); ?></span>
                                <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                                <p class="project-desc"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                                
                                <div class="project-techs">
                                    <?php 
                                    $techs = explode(',', $project['technologies']);
                                    foreach($techs as $tech): 
                                    ?>
                                        <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <div class="project-links">
                                    <?php if(!empty($project['github_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank">📂 GitHub</a>
                                    <?php endif; ?>
                                    <?php if(!empty($project['demo_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($project['demo_link']); ?>" target="_blank">🌐 Démo Live</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Mon Portfolio Dynamique. Propulsé par PHP, HTML, CSS et JS.</p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>