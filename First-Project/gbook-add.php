<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Guestbook Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Cormorant+Garamond:wght@400;600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="gbook-add.css">
</head>
<body>
    <button class="DarkLight" onclick="toggleDarkLightMode()">🌙 Dark</button>
    
    <h1>Welcome to Gbook</h1>
    
    <div id="message-container"></div>
    
    <form id="addForm" action="gbook-add-service.php" method="POST">
        <label for="Name">
            Name:
            <input type="text" id="Name" name="Name" required>
        </label>
        
        <label for="Email">
            Email:
            <input type="email" id="Email" name="Email" required>
        </label>
 
        <label for="Message">
            Message:
            <textarea id="Message" class="Message" name="Message" required placeholder="Write your message here..."></textarea>
        </label>

        <button type="submit" class="Submit-btn">Submit</button>
        <button type="button" class="Back-btn" onclick="window.location.href='index.php'">Back</button>
    </form>

    <div class="back-link">
        <a href="index.php">← Back to Guestbook</a>
    </div>

    <script src="gbook-add.js"></script>
</body>
</html>