<?php
require_once __DIR__ . '/../includes/_guard.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) die("Aucune vidéo spécifiée.");
$id = (int) $_GET['id'];

/* Récup vidéo */
$st = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$st->execute([$id]);
$video = $st->fetch(PDO::FETCH_ASSOC);
if (!$video) die("Vidéo introuvable.");

/* Liste catégories */
$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

/* Soumission */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Gestion catégorie (existante / nouvelle)
    if (($_POST['id_categorie'] ?? '') === '__new__' && !empty($_POST['new_categorie'])) {
        $newNom = trim($_POST['new_categorie']);
        $chk = $pdo->prepare("SELECT id FROM categories WHERE nom = ?");
        $chk->execute([$newNom]);
        $catId = $chk->fetchColumn();
        if (!$catId) {
            $ins = $pdo->prepare("INSERT INTO categories(nom) VALUES(?)");
            $ins->execute([$newNom]);
            $catId = (int)$pdo->lastInsertId();
        }
    } else {
        $catId = (int)($_POST['id_categorie'] ?? 0);
    }

    // Dossiers
    $videosDir = __DIR__ . '/uploads/videos/';
    $thumbsDir = __DIR__ . '/uploads/images/thumbnails/';
    if (!is_dir($videosDir)) mkdir($videosDir, 0777, true);
    if (!is_dir($thumbsDir)) mkdir($thumbsDir, 0777, true);

    // Valeurs par défaut = actuelles
    $newVideoFile = $video['fichier'];
    $newThumbFile = $video['miniature'];

    // Remplacement vidéo (optionnel)
    if (!empty($_FILES['video']['name']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        // supprime l’ancienne
        $old = $videosDir . $video['fichier'];
        if ($video['fichier'] && file_exists($old)) unlink($old);

        $base = pathinfo($_FILES['video']['name'], PATHINFO_FILENAME);
        $ext  = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base) . '.' . $ext;
        move_uploaded_file($_FILES['video']['tmp_name'], $videosDir . $safe);
        $newVideoFile = $safe;
    }

    // Remplacement miniature (optionnel)
    if (!empty($_FILES['miniature']['name']) && $_FILES['miniature']['error'] === UPLOAD_ERR_OK) {
        if ($video['miniature']) {
            $oldT = $thumbsDir . $video['miniature'];
            if (file_exists($oldT)) unlink($oldT);
        }
        $base = pathinfo($_FILES['miniature']['name'], PATHINFO_FILENAME);
        $ext  = strtolower(pathinfo($_FILES['miniature']['name'], PATHINFO_EXTENSION));
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base) . '.' . $ext;
        move_uploaded_file($_FILES['miniature']['tmp_name'], $thumbsDir . $safe);
        $newThumbFile = $safe;
    }

    // UPDATE
    $up = $pdo->prepare("UPDATE videos
                         SET titre = ?, description = ?, id_categorie = ?, fichier = ?, miniature = ?
                         WHERE id = ?");
    $up->execute([$titre, $description, $catId, $newVideoFile, $newThumbFile, $id]);

    header('Location: dashboard.php');
    exit;
}
?>

<?php include __DIR__ . '/a-header.php';?>
<main class="container my-5">
  <h1 class="text-primary text-center mb-4">Modifier la vidéo</h1>

  <form method="post" enctype="multipart/form-data" class="shadow p-4 bg-white rounded" style="max-width: 720px;margin:auto;">
    <div class="mb-3">
      <label for="titre" class="form-label">Titre</label>
      <input type="text" id="titre" name="titre" class="form-control" value="<?= htmlspecialchars($video['titre']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="categorie" class="form-label">Catégorie</label>
      <div class="input-group">
        <select id="categorie" name="id_categorie" class="form-select" required>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= ($video['id_categorie'] == $c['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nom']) ?>
            </option>
          <?php endforeach; ?>
          <option value="__new__">Nouvelle catégorie...</option>
        </select>
        <input type="text" id="new_categorie" name="new_categorie" class="form-control d-none" placeholder="Nom de la nouvelle catégorie">
      </div>
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($video['description']) ?></textarea>
    </div>

    <!-- Aperçus actuels -->
    <div class="row g-3 mb-3">
      <div class="col-md-6 text-center">
        <label class="form-label">Vidéo actuelle</label><br>
        <video width="250" controls>
          <source src="uploads/videos/<?= htmlspecialchars($video['fichier']) ?>" type="video/mp4">
        </video>
      </div>
      <div class="col-md-6 text-center">
        <label class="form-label">Miniature actuelle</label><br>
        <img src="/../uploads/images/thumbnails/<?= htmlspecialchars($video['miniature'] ?: 'DP_default.jpg') ?>" alt="Miniature" width="220" class="rounded shadow">
      </div>
    </div>

    <div class="mb-3">
      <label for="video" class="form-label">Remplacer la vidéo (optionnel)</label>
      <input type="file" id="video" name="video" class="form-control" accept="video/mp4">
    </div>

    <div class="mb-4">
      <label for="miniature" class="form-label">Remplacer la miniature (optionnel)</label>
      <input type="file" id="miniature" name="miniature" class="form-control" accept="image/*">
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary px-4" style="background-color: #b30000">Enregistrer</button>
      <a href="dashboard.php" class="btn btn-secondary px-4">Retour</a>
    </div>
  </form>
</main>

<script>
document.getElementById('categorie').addEventListener('change', function () {
  const inputNew = document.getElementById('new_categorie');
  if (this.value === '__new__') {
    inputNew.classList.remove('d-none');
    inputNew.required = true;
    inputNew.focus();
  } else {
    inputNew.classList.add('d-none');
    inputNew.required = false;
  }
});
</script>
<?php include __DIR__ . '/a-footer.php';?>
