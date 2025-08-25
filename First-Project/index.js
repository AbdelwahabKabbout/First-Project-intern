function loadGuestbook() {
  fetch("gbook-display-service.php")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("guestbookContainer").innerHTML = data;
      attachDeleteListeners(); // Attach event listeners AFTER content is loaded
    })
    .catch((error) => console.error("Error loading guestbook:", error));
}

loadGuestbook();

function attachDeleteListeners() {
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
}

function updateEntry() {
  const updateButtons = document.querySelectorAll(".update-btn");
  updateButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const entryId = button.getAttribute("data-id");
      const newMessage = prompt("Enter the new message:");
      if (newMessage) {
        fetch(`gbook-update-service.php?id=${entryId}`, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ message: newMessage }),
        })
          .then((response) => response.text())
          .then((data) => {
            alert(data);
            loadGuestbook();
          })
          .catch((error) => console.error("Error updating entry:", error));
      }
    });
  });
}
