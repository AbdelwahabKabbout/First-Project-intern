document.addEventListener("DOMContentLoaded", function () {
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
    updateToggleButton();
    if (typeof applyDarkMode === "function") {
      applyDarkMode();
    }
  }
});

function toggleDarkLightMode() {
  document.body.classList.toggle("dark-mode");
  if (document.body.classList.contains("dark-mode")) {
    localStorage.setItem("theme", "dark");
    if (typeof applyDarkMode === "function") {
      applyDarkMode();
    }
  } else {
    localStorage.setItem("theme", "light");
    if (typeof removeDarkMode === "function") {
      removeDarkMode();
    }
  }
  updateToggleButton();
}

function updateToggleButton() {
  const toggleBtn = document.querySelector(".DarkLight");
  if (toggleBtn) {
    if (document.body.classList.contains("dark-mode")) {
      toggleBtn.textContent = "☀️ Light";
    } else {
      toggleBtn.textContent = "🌙 Dark";
    }
  }
}

function applyDarkMode() {
  const h1 = document.querySelector("h1");
  const addBtn = document.querySelector(".Add");
  if (h1) h1.classList.add("dark");
  if (addBtn) addBtn.classList.add("dark");
  document.querySelectorAll(".entry").forEach((entry) => {
    entry.classList.add("dark");
  });
  document.querySelectorAll(".update-btn").forEach((btn) => {
    btn.classList.add("dark");
  });
  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.classList.add("dark");
  });
  document.querySelectorAll(".entry-buttons").forEach((container) => {
    container.classList.add("dark");
  });
  const toggleBtn = document.querySelector(".DarkLight");
  if (toggleBtn) toggleBtn.classList.add("dark");
}

function removeDarkMode() {
  const h1 = document.querySelector("h1");
  const addBtn = document.querySelector(".Add");
  if (h1) h1.classList.remove("dark");
  if (addBtn) addBtn.classList.remove("dark");
  document.querySelectorAll(".entry").forEach((entry) => {
    entry.classList.remove("dark");
  });
  document.querySelectorAll(".update-btn").forEach((btn) => {
    btn.classList.remove("dark");
  });
  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.classList.remove("dark");
  });
  document.querySelectorAll(".entry-buttons").forEach((container) => {
    container.classList.remove("dark");
  });
  const toggleBtn = document.querySelector(".DarkLight");
  if (toggleBtn) toggleBtn.classList.remove("dark");
}
