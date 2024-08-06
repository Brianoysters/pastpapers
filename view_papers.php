<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$mysqli = new mysqli('localhost', 'root', '', 'campus_papers');
if ($mysqli->connect_error) {
    die('Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$query = "SELECT units.name as unit_name, past_papers.filename as filename 
          FROM past_papers 
          JOIN units ON past_papers.unit_id = units.id";
$result = $mysqli->query($query);
$papers = [];
while ($row = $result->fetch_assoc()) {
    $papers[] = $row;
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Past Papers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="login.html">Login</a></li>
                <li><a href="register.html">Register</a></li>
                <li><a href="upload.html">Upload</a></li>
                <li><a href="view_papers.php">View Past Papers</a></li>
                <li><a href="subscribe.html">Subscribe</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>View Past Papers</h1>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Filename</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($papers as $paper): ?>
                    <tr>
                        <td><?= htmlspecialchars($paper['unit_name']) ?></td>
                        <td><?= htmlspecialchars($paper['filename']) ?></td>
                        <td>
                            <form action="download.php" method="post">
                                <input type="hidden" name="paper" value="<?= htmlspecialchars($paper['filename']) ?>">
                                <input type="submit" value="Download">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Campus Past Papers</p>
    </footer>
</body>
</html>
