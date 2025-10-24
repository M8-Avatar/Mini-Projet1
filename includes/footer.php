<footer class="text-center py-3 mt-auto" style="background:#0056b3;color:white;">
  <div class="container">
    <p class="mb-1">
      <a href="mentions-legales.php" class="text-white text-decoration-none me-3">Mentions légales</a>
      <a href="confidentialite.php" class="text-white text-decoration-none">Politique de confidentialité</a>
    </p>

    <!-- Easter Egg : le © déclenche le modal -->
    <p class="mb-0">
      <span id="admin-login-trigger" style="cursor:pointer;text-decoration:underline;">
        © 2025 Département de La Réunion
      </span>
    </p>
  </div>
</footer>


<!-- === MODAL DE CONNEXION ADMIN CACHÉ === -->
<div class="modal fade" id="adminLoginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" style="background:#b30000;">
        <h5 class="modal-title">Connexion administrateur</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="adminLoginForm" method="post" action="admin/login.php">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="identifiant" class="form-label">Identifiant</label>
            <input type="text" id="identifiant" name="identifiant" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="motdepasse" class="form-label">Mot de passe</label>
            <input type="password" id="motdepasse" name="motdepasse" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn text-white" style="background:#b30000;">Connexion</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</div>



<script>
document.getElementById('admin-login-trigger').addEventListener('click', () => {
  const modal = new bootstrap.Modal(document.getElementById('adminLoginModal'));
  modal.show();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
