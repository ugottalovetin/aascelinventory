(function () {
  if (typeof gsap === "undefined") {
    return;
  }

  function runAnalyticsSwitcher() {
    if (document.body.dataset.page !== "dashboard") {
      return;
    }

    var panel = document.getElementById("inventory-analytics-panel");
    if (!panel || panel.dataset.analyticsBound === "1") {
      return;
    }

    panel.dataset.analyticsBound = "1";

    var buttons = panel.querySelectorAll(".analytics-switch-btn");
    var currentView = document.getElementById("analytics-current-view");
    var chartView = document.getElementById("analytics-chart-view");
    var chartCanvas = document.getElementById("analytics-chart-canvas");

    if (!currentView || buttons.length === 0) {
      return;
    }

    var rawPoints = chartCanvas
      ? chartCanvas.getAttribute("data-chart-points") || "[]"
      : "[]";

    var points = [];
    try {
      points = JSON.parse(rawPoints);
    } catch (error) {
      points = [];
    }

    var hasChartSupport =
      !!chartCanvas &&
      typeof Chart !== "undefined" &&
      Array.isArray(points) &&
      points.length > 0;

    var labels = points.map(function (point) {
      return String(point.category || "Unknown");
    });

    var valueData = points.map(function (point) {
      return Number(point.value_total || 0);
    });

    var stockData = points.map(function (point) {
      return Number(point.stock_total || 0);
    });

    var palette = [
      "#dc551d",
      "#a42310",
      "#f5d96e",
      "#400c0c",
      "#e97d4a",
      "#f1b649",
    ];
    var chartInstance = null;

    function setButtonActive(activeView) {
      buttons.forEach(function (button) {
        var isActive =
          button.getAttribute("data-analytics-view") === activeView;

        button.classList.toggle("border-brand-300", isActive);
        button.classList.toggle("bg-brand-300", isActive);
        button.classList.toggle("text-white", isActive);
        button.classList.toggle("shadow-sm", isActive);

        button.classList.toggle("border-slate-300", !isActive);
        button.classList.toggle("bg-white", !isActive);
        button.classList.toggle("text-slate-600", !isActive);
      });
    }

    function getCommonScales() {
      return {
        x: {
          ticks: { color: "#64748b" },
          grid: { color: "rgba(148,163,184,0.18)" },
        },
        y: {
          beginAtZero: true,
          ticks: { color: "#64748b" },
          grid: { color: "rgba(148,163,184,0.18)" },
        },
      };
    }

    function destroyChart() {
      if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
      }
    }

    function buildChartConfig(view) {
      var commonPlugins = {
        legend: {
          labels: {
            color: "#475569",
            font: {
              family: "Sora",
            },
          },
        },
      };

      if (view === "column") {
        return {
          type: "bar",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Inventory Value (PHP)",
                data: valueData,
                backgroundColor: "rgba(220, 85, 29, 0.82)",
                borderColor: "#a42310",
                borderWidth: 1,
                borderRadius: 8,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: commonPlugins,
            scales: getCommonScales(),
          },
        };
      }

      if (view === "bar") {
        return {
          type: "bar",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Inventory Value (PHP)",
                data: valueData,
                backgroundColor: "rgba(164, 35, 16, 0.8)",
                borderColor: "#400c0c",
                borderWidth: 1,
                borderRadius: 8,
              },
            ],
          },
          options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: commonPlugins,
            scales: getCommonScales(),
          },
        };
      }

      if (view === "line") {
        return {
          type: "line",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Inventory Value (PHP)",
                data: valueData,
                borderColor: "#a42310",
                backgroundColor: "rgba(220, 85, 29, 0.16)",
                pointBackgroundColor: "#dc551d",
                pointRadius: 4,
                tension: 0.35,
                fill: true,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: commonPlugins,
            scales: getCommonScales(),
          },
        };
      }

      if (view === "pie") {
        return {
          type: "pie",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Inventory Value (PHP)",
                data: valueData,
                backgroundColor: palette,
                borderColor: "#ffffff",
                borderWidth: 2,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: commonPlugins,
          },
        };
      }

      return {
        type: "radar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Inventory Value (PHP)",
              data: valueData,
              borderColor: "#a42310",
              backgroundColor: "rgba(220, 85, 29, 0.18)",
              pointBackgroundColor: "#dc551d",
              pointBorderColor: "#ffffff",
            },
            {
              label: "Stock Units",
              data: stockData,
              borderColor: "#400c0c",
              backgroundColor: "rgba(64, 12, 12, 0.08)",
              pointBackgroundColor: "#400c0c",
              pointBorderColor: "#ffffff",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: commonPlugins,
          scales: {
            r: {
              angleLines: { color: "rgba(148,163,184,0.2)" },
              grid: { color: "rgba(148,163,184,0.2)" },
              pointLabels: { color: "#475569" },
              ticks: { color: "#64748b", backdropColor: "transparent" },
            },
          },
        },
      };
    }

    function showCurrentView() {
      setButtonActive("current");
      currentView.classList.remove("hidden");

      if (chartView) {
        chartView.classList.add("hidden");
      }

      destroyChart();
    }

    function showChartView(view) {
      if (!hasChartSupport || !chartView || !chartCanvas) {
        showCurrentView();
        return;
      }

      setButtonActive(view);
      currentView.classList.add("hidden");
      chartView.classList.remove("hidden");
      destroyChart();

      var config = buildChartConfig(view);
      chartInstance = new Chart(chartCanvas, config);
    }

    buttons.forEach(function (button) {
      button.addEventListener("click", function () {
        var view = button.getAttribute("data-analytics-view") || "current";

        if (view === "current") {
          showCurrentView();
          return;
        }

        showChartView(view);
      });
    });

    // Default mode requested: Current.
    showCurrentView();
  }

  function runDashboardReveal() {
    if (document.body.dataset.page !== "dashboard") {
      return;
    }

    var hasRevealTarget =
      document.querySelector(".dashboard-reveal") ||
      document.querySelector(".dashboard-card") ||
      document.querySelector(".dashboard-table");

    if (!hasRevealTarget) {
      return;
    }

    gsap.killTweensOf([
      ".dashboard-reveal",
      ".dashboard-card",
      ".dashboard-table",
      ".table-row-item",
    ]);

    var timeline = gsap.timeline({ defaults: { ease: "power3.out" } });

    if (document.querySelector(".dashboard-reveal")) {
      timeline.fromTo(
        ".dashboard-reveal",
        { y: 24, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.45, stagger: 0.08 },
        0,
      );
    }

    if (document.querySelector(".dashboard-card")) {
      timeline.fromTo(
        ".dashboard-card",
        { y: 44, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.55, stagger: 0.1 },
        0.08,
      );
    }

    if (document.querySelector(".dashboard-table")) {
      timeline.fromTo(
        ".dashboard-table",
        { y: 36, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.5 },
        0.22,
      );
    }

    if (document.querySelector(".table-row-item")) {
      timeline.fromTo(
        ".table-row-item",
        { x: -28, autoAlpha: 0 },
        {
          x: 0,
          autoAlpha: 1,
          duration: 0.4,
          stagger: 0.05,
          ease: "power2.out",
        },
        0.35,
      );
    }
  }

  runDashboardReveal();
  runAnalyticsSwitcher();

  // Re-run when navigating from browser cache so reveal still plays every dashboard visit.
  window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
      runDashboardReveal();
      runAnalyticsSwitcher();
    }
  });
})();
