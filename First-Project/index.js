function loadGuestbook() {
  fetch("Routes.php?action=Read")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("guestbookContainer").innerHTML = data;
      attachEventListeners();

      if (document.body.classList.contains("dark-mode")) {
        applyDarkMode();
      }
    })
    .catch((error) => console.error("Error loading guestbook:", error));
}

loadGuestbook();

function attachEventListeners() {
  const deleteButtons = document.querySelectorAll(".delete-btn");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const entryId = button.getAttribute("data-id");
      if (confirm("Are you sure you want to delete this entry?")) {
        fetch(`Routes.php?action=Delete&id=${entryId}`, {
          method: "DELETE",
        })
          .then((response) => response.text())
          .then((data) => {
            alert(data);
            loadGuestbook();
          })
          .catch((error) => console.error("Error deleting entry:", error));
      }
    });
  });

  const updateButtons = document.querySelectorAll(".update-btn");
  updateButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const entryId = button.getAttribute("data-id");
      window.location.href = `gbook-edit.php?id=${entryId}`;
    });
  });
}

function directToAddEntry() {
  window.location.href = "gbook-add.php";
}
// Check for saved theme preference on page load
document.addEventListener("DOMContentLoaded", function () {
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
    applyDarkMode();
    updateToggleButton();
  }
});

function toggleDarkLightMode() {
  document.body.classList.toggle("dark-mode");

  if (document.body.classList.contains("dark-mode")) {
    applyDarkMode();
    localStorage.setItem("theme", "dark");
  } else {
    removeDarkMode();
    localStorage.setItem("theme", "light");
  }

  updateToggleButton();
}

function applyDarkMode() {
  // Apply dark mode to static elements
  const h1 = document.querySelector("h1");
  const addBtn = document.querySelector(".Add");

  if (h1) h1.classList.add("dark");
  if (addBtn) addBtn.classList.add("dark");

  // Apply dark mode to dynamically loaded content
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
}

function removeDarkMode() {
  // Remove dark mode from static elements
  const h1 = document.querySelector("h1");
  const addBtn = document.querySelector(".Add");

  if (h1) h1.classList.remove("dark");
  if (addBtn) addBtn.classList.remove("dark");

  // Remove dark mode from dynamically loaded content
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
}

function updateToggleButton() {
  const toggleBtn = document.querySelector(".DarkLight");
  if (document.body.classList.contains("dark-mode")) {
    toggleBtn.textContent = "☀️ Light";
    toggleBtn.classList.add("dark");
  } else {
    toggleBtn.textContent = "🌙 Dark";
    toggleBtn.classList.remove("dark");
  }
}
