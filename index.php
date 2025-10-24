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
          ORDER BY v.date_publication DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Récupération des catégories pour le modal ---
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <h1>Bienvenue sur la plateforme vidéo du Département</h1>
    <p>Recherchez et gérez les vidéos officielles du Département de La Réunion.</p>

    <!-- Barre de recherche + bouton ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form class="d-flex" method="get" action="">
            <input type="text" name="search" class="form-control me-2"
                   placeholder="Rechercher une vidéo..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>

        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
            Ajouter une vidéo
        </button>
    </div>

    <?php if (empty($videos)) : ?>
        <p class="text-muted text-center">Aucune vidéo enregistrée pour le moment.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table video-list align-middle">
                <thead>
                    <tr>
                        <th>Miniature</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): 
                        $miniature = !empty($video['miniature'])
                            ? 'uploads/images/thumbnails/' . htmlspecialchars($video['miniature'])
                            : 'uploads/images/DP_default.jpg';
                    ?>
                        <tr>
                            <td style="width: 150px;">
                                <img src="<?= $miniature ?>" alt="<?= htmlspecialchars($video['titre']) ?>" class="thumb-mini">
                            </td>
                            <td><strong><?= htmlspecialchars($video['titre']) ?></strong></td>
                            <td><?= htmlspecialchars($video['categorie']) ?></td>
                            <td><?= htmlspecialchars($video['description']) ?></td>
                            <td><?= date('d/m/Y', strtotime($video['date_publication'])) ?></td>
                            <td class="text-center">
                                <a href="modifier.php?id=<?= $video['id'] ?>" class="btn btn-warning btn-sm me-2">
                                    Modifier
                                </a>
                                <a href="supprimer.php?id=<?= $video['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Supprimer cette vidéo ?');">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Modal d’ajout d’une vidéo -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post" enctype="multipart/form-data" action="upload.php">
            <div class="modal-header">
              <h5 class="modal-title">Ajouter une vidéo</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="mb-3">
                <label for="titre">Titre de la vidéo</label>
                <input type="text" id="titre" name="titre" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="id_categorie">Catégorie</label>
                <div class="input-group">
                  <select id="id_categorie" name="id_categorie" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                    <?php endforeach; ?>
                    <option value="__new__">Nouvelle catégorie...</option>
                  </select>
                  <input type="text" id="new_categorie" name="new_categorie" class="form-control mt-2 d-none" placeholder="Nouvelle catégorie">
                </div>
              </div>

              <div class="mb-3">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control"></textarea>
              </div>

              <div class="mb-3">
                <label for="video">Fichier vidéo (.mp4)</label>
                <input type="file" id="video" name="video" accept="video/mp4" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="miniature">Miniature (.jpg ou .png)</label>
                <input type="file" id="miniature" name="miniature" accept="image/*" class="form-control">
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Enregistrer</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
        <a href="export.php?type=csv" class="btn btn-secondary">Exporter la liste (CSV)</a>
        <a href="export.php?type=pdf" class="btn btn-danger">Exporter (PDF)</a>
    </div>
</main>

<script>
document.getElementById('id_categorie').addEventListener('change', function() {
    const inputNew = document.getElementById('new_categorie');
    if (this.value === '__new__') {
        inputNew.classList.remove('d-none');
        inputNew.required = true;
    } else {
        inputNew.classList.add('d-none');
        inputNew.required = false;
    }
});
</script>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/app.js"></script>
