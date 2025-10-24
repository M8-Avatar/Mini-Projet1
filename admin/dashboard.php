<?php
require_once __DIR__ . '/../includes/_guard.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// --- Récupération des vidéos ---
$search = $_GET['search'] ?? '';
$sql = "SELECT v.*, c.nom AS categorie
        FROM videos v
        JOIN categories c ON v.id_categorie = c.id
        WHERE v.titre LIKE :search
           OR v.description LIKE :search
           OR c.nom LIKE :search
        ORDER BY v.date_publication DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Récupération des catégories ---
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/a-header.php'; ?>

<style>
/* ================================
   STYLE ADMIN (ROUGE)
================================ */
header {
    background-color: #b30000 !important;
}
.btn-primary {
    background-color: #b30000;
    border: none;
}
.btn-primary:hover {
    background-color: #cc0000;
}
.table thead {
    background-color: #b30000;
    color: white;
}
.modal-header {
    background-color: #b30000;
    color: white;
}
</style>

<main class="container my-5">
    <h1 class="mb-4 text-center">Tableau de bord – Administration</h1>

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

    <!-- Tableau des vidéos -->
    <div class="table-responsive shadow-sm">
        <table class="table table-hover align-middle">
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
                <?php if (empty($videos)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Aucune vidéo trouvée.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($videos as $video): 
                        $miniature = !empty($video['miniature'])
                            ? asset_url('uploads/images/thumbnails/' . htmlspecialchars($video['miniature']))
                            : asset_url('uploads/images/DP_default.jpg');
                    ?>
                    <tr>
                        <td style="width: 150px;">
                            <img src="<?= $miniature ?>" alt="Miniature" class="img-thumbnail" style="max-height:90px;">
                        </td>
                        <td><strong><?= htmlspecialchars($video['titre']) ?></strong></td>
                        <td><?= htmlspecialchars($video['categorie']) ?></td>
                        <td><?= htmlspecialchars($video['description']) ?></td>
                        <td><?= date('d/m/Y', strtotime($video['date_publication'])) ?></td>
                        <td class="text-center">
                            <a href="modifier.php?id=<?= $video['id'] ?>" class="btn btn-warning btn-sm me-2">Modifier</a>
                            <a href="supprimer.php?id=<?= $video['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cette vidéo ?');">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Ajout Vidéo -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data" action="upload.php">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter une nouvelle vidéo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="titre">Titre de la vidéo</label>
            <input type="text" id="titre" name="titre" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="categorie">Catégorie</label>
            <select id="categorie" name="categorie" class="form-select" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
              <?php endforeach; ?>
              <option value="__new__">Nouvelle catégorie...</option>
            </select>
            <input type="text" id="new_categorie" name="new_categorie" class="form-control mt-2 d-none"
                   placeholder="Nouvelle catégorie">
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

<script>
document.getElementById('categorie').addEventListener('change', function() {
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

<?php include __DIR__ . '/a-footer.php'; ?>
