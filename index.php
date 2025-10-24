<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Récupération du mot clé et du filtre catégorie
$search = $_GET['search'] ?? '';
$categorie = $_GET['categorie'] ?? '';

$query = "SELECT * FROM videos WHERE titre LIKE :search";
$params = [':search' => "%$search%"];

if (!empty($categorie)) {
    $query .= " AND categorie = :categorie";
    $params[':categorie'] = $categorie;
}

$query .= " ORDER BY date_publication DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$videos = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">🎬 Gestion des vidéos</h1>

    <form method="get" class="row mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Rechercher une vidéo..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="categorie" class="form-select">
                <option value="">Toutes les catégories</option>
                <option value="Tutoriel" <?= $categorie=="Tutoriel"?"selected":"" ?>>Tutoriel</option>
                <option value="Présentation" <?= $categorie=="Présentation"?"selected":"" ?>>Présentation</option>
                <option value="Autre" <?= $categorie=="Autre"?"selected":"" ?>>Autre</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" type="submit">Filtrer</button>
            <a href="ajouter.php" class="btn btn-success">+ Ajouter</a>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($videos as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['titre']) ?></td>
                <td><?= htmlspecialchars($v['categorie']) ?></td>
                <td><?= date('d/m/Y', strtotime($v['date_publication'])) ?></td>
                <td>
                    <a href="uploads/videos/<?= htmlspecialchars($v['fichier']) ?>" target="_blank" class="btn btn-sm btn-outline-info">Voir</a>
                    <a href="modifier.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-warning">Modifier</a>
                    <a href="supprimer.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette vidéo ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="export.php?type=csv" class="btn btn-secondary">Exporter CSV</a>
    <a href="export.php?type=pdf" class="btn btn-secondary">Exporter PDF</a>
</div>

<?php include 'includes/footer.php'; ?>
