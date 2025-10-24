<?php
require_once __DIR__ . '/../includes/_guard.php';
require_once '/../includes/db.php';
require_once '/../includes/functions.php';

/* Catégories existantes */
$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Catégorie existante / nouvelle
    if (($_POST['id_categorie'] ?? '') === '__new__' && !empty($_POST['new_categorie'])) {
        $newNom = trim($_POST['new_categorie']);
        $chk = $pdo->prepare("SELECT id FROM categories WHERE nom = ?");
        $chk->execute([$newNom]);
        $idCategorie = $chk->fetchColumn();
        if (!$idCategorie) {
            $ins = $pdo->prepare("INSERT INTO categories(nom) VALUES(?)");
            $ins->execute([$newNom]);
            $idCategorie = (int)$pdo->lastInsertId();
        }
    } else {
        $idCategorie = (int)($_POST['id_categorie'] ?? 0);
    }

    // Uploads (NOTE: chemins corrigés)
    $fichierVideo = uploadFile($_FILES['video'], 'uploads/videos/');
    $miniature    = uploadFile($_FILES['miniature'], 'uploads/images/thumbnails/');

    // Insertion
    $st = $pdo->prepare("INSERT INTO videos (titre, description, fichier, miniature, id_categorie)
                         VALUES (:titre, :description, :fichier, :miniature, :id_categorie)");
    $st->execute([
        ':titre'         => $titre,
        ':description'   => $description,
        ':fichier'       => $fichierVideo,
        ':miniature'     => $miniature,
        ':id_categorie'  => $idCategorie
    ]);

    header('Location: dashboard.php');
    exit;
}
?>
<?php include '/a-header.php'; ?>

<div class="container mt-4">
  <h2 class="text-primary mb-4">Ajouter une vidéo</h2>

  <form method="post" enctype="multipart/form-data" class="shadow p-4 bg-white rounded" style="max-width: 640px;margin:auto;">
    <div class="mb-3">
      <label for="titre" class="form-label">Titre</label>
      <input type="text" id="titre" name="titre" class="form-control" required>
    </div>

    <div class="mb-3">
  <label for="categorie">Catégorie</label>
  <div class="input-group">
    <select id="categorie" name="categorie" class="form-select" required>
      <?php foreach ($cats as $cat): ?>
        <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
      <?php endforeach; ?>
      <option value="__new__">Nouvelle catégorie...</option>
    </select>
    <input type="text" id="new_categorie" name="new_categorie" class="form-control d-none" placeholder="Nouvelle catégorie">
  </div>
</div>

    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea id="description" name="description" class="form-control" rows="4"></textarea>
    </div>

    <div class="mb-3">
      <label for="video" class="form-label">Vidéo (.mp4)</label>
      <input type="file" id="video" name="video" accept="video/mp4" class="form-control" required>
    </div>

    <div class="mb-4">
      <label for="miniature" class="form-label">Miniature (.jpg/.png)</label>
      <input type="file" id="miniature" name="miniature" accept="image/*" class="form-control">
    </div>

    <div class="text-center">
      <button class="btn btn-success px-4">Enregistrer</button>
      <a href="dashboard.php" class="btn btn-secondary px-4">Annuler</a>
    </div>
  </form>
</div>

<script>
document.getElementById('categorie').addEventListener('change', function () {
  const newInput = document.getElementById('new_categorie');
  if (this.value === '__new__') {
    newInput.classList.remove('d-none');
    newInput.required = true;
  } else {
    newInput.classList.add('d-none');
    newInput.required = false;
  }
});
</script>

<?php include 'a-footer.php'; ?>
