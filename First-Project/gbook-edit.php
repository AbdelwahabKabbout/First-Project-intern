<?php
require 'config.php';

$entryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$entry = null;
$error = '';

if ($entryId > 0) {
    $stmt = $conn->prepare("SELECT id, name, email, message, createdAt FROM guestbook WHERE id=? AND Active=1");
    $stmt->bind_param("i", $entryId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $entry = $result->fetch_assoc();
    } else {
        $error = 'Entry not found or has been deleted.';
    }
    $stmt->close();
} else {
    $error = 'Invalid entry ID.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guestbook Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Cormorant+Garamond:wght@400;600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Cinzel", "Cormorant Garamond", Garamond, "Times New Roman", serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
            color: #79B4B7;
            margin-bottom: 30px;
        }
        .entry {
            background-color: #f0f0f0;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-family: "Cinzel", "Cormorant Garamond", Garamond, "Times New Roman", serif;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #79B4B7;
            font-size: 16px;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #79B4B7;
            border-radius: 5px;
            font-family: "Cinzel", serif;
            font-size: 14px;
            box-sizing: border-box;
            background-color: white;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #5a8b8e;
            box-shadow: 0 0 8px rgba(121, 180, 183, 0.3);
        }
        .form-group textarea {
            height: 120px;
            resize: vertical;
            font-family: "Cinzel", serif;
        }
        .entry-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }
        .Add {
            background-color: #79B4B7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            padding: 12px 25px;
            transition: background-color 0.3s ease;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            user-select: none;
            border: 2px solid #79B4B7;
        }
        .Add:hover {
            background-color: #5a8b8e;
            transform: scale(1.02);
        }
        .cancel-btn {
            padding: 12px 25px;
            background-color: #6c757d;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: "Poppins", sans-serif;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .cancel-btn:hover {
            background-color: #545b62;
            transform: scale(1.02);
        }
        .error {
            color: #d9534f;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #d9534f;
            font-family: "Poppins", sans-serif;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            font-family: "Poppins", sans-serif;
        }
        .meta-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #6c757d;
            font-family: "Poppins", sans-serif;
            font-size: 14px;
        }
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        .back-link a {
            color: #79B4B7;
            text-decoration: none;
            font-family: "Cinzel", serif;
            font-size: 16px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Edit Guestbook Entry</h1>
    <div id="message-container"></div>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($entry): ?>
        <div class="meta-info">
            <strong>Entry ID:</strong> <?php echo $entry['id']; ?> | 
            <strong>Created:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($entry['createdAt'])); ?>
        </div>
        <div class="entry">
            <form id="editForm">
                <input type="hidden" id="entryId" value="<?php echo $entry['id']; ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($entry['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($entry['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" required><?php echo htmlspecialchars($entry['message']); ?></textarea>
                </div>
                <div class="entry-buttons">
                    <button type="submit" class="Add">Update Entry</button>
                    <a href="index.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="entry"></div></div>
            <p style="text-align: center; color: #d9534f;">Unable to load entry for editing.</p>
        </div>
    <?php endif; ?>
    <div class="back-link">
        <a href="index.php">← Back to Guestbook</a>
    </div>
    <script>
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('id', document.getElementById('entryId').value);
            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('message', document.getElementById('message').value);
            fetch('gbook-update-service.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const messageContainer = document.getElementById('message-container');
                if (data.includes('successfully')) {
                    messageContainer.innerHTML = '<div class="success">' + data + '</div>';
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    messageContainer.innerHTML = '<div class="error">' + data + '</div>';
                }
                window.scrollTo(0, 0);
            })
            .catch(error => {
                console.error('Error:', error);
                const messageContainer = document.getElementById('message-container');
                messageContainer.innerHTML = '<div class="error">An error occurred while updating the entry.</div>';
                window.scrollTo(0, 0);
            });
        });
    </script>
</body>
</html>
