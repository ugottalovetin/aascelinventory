(function () {
  var butterfly = document.getElementById("butterfly-cursor");

  if (!butterfly || typeof gsap === "undefined") {
    return;
  }

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    butterfly.style.display = "none";
    return;
  }

  var position = {
    x: window.innerWidth / 2,
    y: window.innerHeight / 2,
  };

  var target = {
    x: position.x,
    y: position.y,
  };

  var isVisible = false;

  window.addEventListener("mousemove", function (event) {
    target.x = event.clientX + 12;
    target.y = event.clientY + 8;

    if (!isVisible) {
      butterfly.classList.add("is-visible");
      isVisible = true;
    }
  });

  // Smoothly interpolate toward mouse coordinates for a subtle lag effect.
  gsap.ticker.add(function () {
    position.x += (target.x - position.x) * 0.18;
    position.y += (target.y - position.y) * 0.18;

    var deltaX = target.x - position.x;

    gsap.set(butterfly, {
      x: position.x,
      y: position.y,
      rotation: deltaX * 0.35,
    });
  });
})();
