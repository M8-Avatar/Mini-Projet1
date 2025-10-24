<?php
require_once __DIR__ . '/../includes/_guard.php';
// --- FOOTER ADMIN ---
?>
<footer class="admin-footer mt-auto text-center py-3 text-light">
  <style>
    footer.admin-footer {
      background-color: #b30000;
      color: white;
      font-size: 14px;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
    }
    footer.admin-footer a {
      color: #fff;
      text-decoration: underline;
      transition: 0.2s;
      opacity: 0.9;
    }
    footer.admin-footer a:hover {
      color: #ffd700;
      opacity: 1;
      text-decoration: none;
    }
  </style>

  <div class="container">
    <p class="mb-1">© 2025 Département de La Réunion – Espace Administrateur</p>
    <p class="mb-0">
      <a href="../mentions-legales.php">Mentions légales</a> ·
      <a href="../confidentialite.php">Politique de confidentialité</a>
    </p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
