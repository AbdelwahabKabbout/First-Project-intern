function loadGuestbook() {
  fetch("gbook-display-service.php")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("guestbookContainer").innerHTML = data;
      attachEventListeners(); // Attach ALL event listeners AFTER content is loaded
    })
    .catch((error) => console.error("Error loading guestbook:", error));
}

loadGuestbook();

function attachEventListeners() {
  // Attach delete listeners
  const deleteButtons = document.querySelectorAll(".delete-btn");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const entryId = button.getAttribute("data-id");
      if (confirm("Are you sure you want to delete this entry?")) {
        fetch(`gbook-delete-service.php?id=${entryId}`, {
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
