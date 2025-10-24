<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $categorie = $_POST['categorie'];

    $fichierVideo = uploadFile($_FILES['video'], 'uploads/videos/');
    $miniature = uploadFile($_FILES['miniature'], 'uploads/thumbnails/');

    $stmt = $pdo->prepare("INSERT INTO videos (titre, description, categorie, fichier, miniature)
                           VALUES (:titre, :description, :categorie, :fichier, :miniature)");
    $stmt->execute([
        ':titre' => $titre,
        ':description' => $description,
        ':categorie' => $categorie,
        ':fichier' => $fichierVideo,
        ':miniature' => $miniature
    ]);

    header('Location: index.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Ajouter une vidéo</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Titre</label>
            <input type="text" name="titre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Catégorie</label>
            <select name="categorie" class="form-select">
                <option value="Tutoriel">Tutoriel</option>
                <option value="Présentation">Présentation</option>
                <option value="Autre">Autre</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Vidéo (.mp4)</label>
            <input type="file" name="video" accept="video/mp4" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Miniature (.jpg/.png)</label>
            <input type="file" name="miniature" accept="image/*" class="form-control">
        </div>
        <button class="btn btn-success">Enregistrer</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
