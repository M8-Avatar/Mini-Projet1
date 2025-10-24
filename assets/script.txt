document.addEventListener("DOMContentLoaded", () => {
  const videoCards = document.querySelectorAll(".video-card");
  const popup = document.getElementById("video-popup");
  const videoPlayer = document.getElementById("video-player");
  const closeBtn = document.getElementById("close-popup");

  videoCards.forEach(card => {
    card.addEventListener("click", () => {
      const src = card.dataset.video;
      videoPlayer.src = src;
      popup.classList.remove("hidden");
      videoPlayer.play();
    });
  });

  closeBtn.addEventListener("click", () => {
    videoPlayer.pause();
    videoPlayer.src = "";
    popup.classList.add("hidden");
  });

  // Ferme le popup si on clique à l’extérieur
  popup.addEventListener("click", (e) => {
    if (e.target === popup) {
      videoPlayer.pause();
      videoPlayer.src = "";
      popup.classList.add("hidden");
    }
  });
});
