<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Barre de recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// --- Récupération des vidéos ---
$query = "SELECT v.*, c.nom AS categorie
          FROM videos v
          JOIN categories c ON v.id_categorie = c.id
          WHERE v.titre LIKE :search
             OR v.description LIKE :search
             OR c.nom LIKE :search
          ORDER BY v.date_publication DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="container my-5">
    <h1 class="mb-4 text-center">Vidéothèque du Département de La Réunion</h1>

    <!-- Barre de recherche -->
    <form class="d-flex justify-content-center mb-5" method="get" action="">
        <input type="text" name="search" class="form-control w-50 me-2"
               placeholder="Rechercher une vidéo..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>

    <?php if (empty($videos)) : ?>
        <p class="text-muted text-center">Aucune vidéo trouvée.</p>
    <?php else : ?>
        <div class="row justify-content-center g-4">
            <?php foreach ($videos as $video): 
                $miniature = !empty($video['miniature'])
                    ? asset_url('uploads/images/thumbnails/' . htmlspecialchars($video['miniature']))
                    : asset_url('uploads/images/DP_default.jpg');
                $videoFile = asset_url('uploads/videos/' . htmlspecialchars($video['fichier']));
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <img src="<?= $miniature ?>" class="card-img-top" alt="<?= htmlspecialchars($video['titre']) ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($video['titre']) ?></h5>
                        <p class="text-muted mb-1"><?= htmlspecialchars($video['categorie']) ?></p>
                        <p class="small"><?= htmlspecialchars($video['description']) ?></p>
                        <video controls preload="metadata" class="w-100 mt-2 rounded">
                            <source src="<?= $videoFile ?>" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture vidéo.
                        </video>
                        <p class="text-secondary mt-2 small">
                            Publiée le <?= date('d/m/Y', strtotime($video['date_publication'])) ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
     <div class="text-center mt-4">
        <a href="export.php?type=csv" class="btn btn-secondary">Exporter la liste (CSV)</a>
        <a href="export.php?type=pdf" class="btn btn-danger">Exporter (PDF)</a>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="<?= asset_url('assets/js/app.js') ?>"></script>
