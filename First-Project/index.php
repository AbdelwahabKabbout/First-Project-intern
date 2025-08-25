<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1 class="Welcome-Light">Welcome to Gbook</h1>
    <form action="post.php" method="POST">
        <label for="Name">
            Name:
            <input type="text" id="Name" name="Name" required>
        </label>
        <label for="Email">
            Email:
            <input type="email" id="Email" name="Email" required>
        </label>
 
        <label for="Message" >
  Message:
  <input type="text" id="Message" class="Message" name="Message" required>
</label>

        
        <button class="Submit-btn">Submit</button>
    </form>

</body>
</html>