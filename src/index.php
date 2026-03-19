<?php
// $host = 'devsecops-bdd';
// $db   = 'myapp';
// $user = 'appuser';
// $pass = 'apppassword';
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

try {
    // Connexion PDO standard
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion BDD : " . $e->getMessage()); // ERREUR: Mauvaise indentation (collé à gauche)
}

$search = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annuaire Interne</title>
    <style>body {
            font-family: sans-serif;
            padding: 20px;
        }</style>
</head>
<body>
<h1>Annuaire de l'entreprise</h1>

<p>Résultats de recherche pour : <b><?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?></b></p>

<form method="GET">
    <input type="text" name="search" placeholder="Rechercher un collègue..."
           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
    <button type="submit">Rechercher</button>
</form>
<hr>

<?php
if ($search) {
    // Utilisation de requêtes préparées pour éviter l'injection SQL
    $sql = "SELECT username, role, password FROM users WHERE username = :username";
    // echo "<div style='color:gray; font-size:0.8em'>DEBUG SQL: " . htmlspecialchars($sql, ENT_QUOTES, 'UTF-8') . "</div><br>";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $search]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo "<ul>";
            foreach ($results as $row) {
                echo "<li>";
                echo "<strong>" . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . "</strong> ";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "Aucun utilisateur trouvé.";
        }
    } catch (PDOException $e) {
        echo "Erreur SQL : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>

<hr>
<div style="background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb;">
    <h3>Zone Admin : Diagnostic Réseau</h3>
    <p>Vérifier la connectivité d'un serveur interne.</p>

    <form method="GET">
        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">

        <label>IP à tester :</label>
        <input type="text" name="ip" placeholder="ex: 8.8.8.8"
               value="<?php echo htmlspecialchars($_GET['ip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Pinger</button>
    </form>

    <?php
    if (isset($_GET['ip']) && !empty($_GET['ip'])) {
        $ip = $_GET['ip'];

        echo "<pre>";
        echo "Test de ping sur : " . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . "\n";
        echo "--------------------------\n";

        // Validation de l'IP et utilisation de escapeshellarg pour éviter l'injection de commande
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            system("ping -c 2 " . escapeshellarg($ip));
        } else {
            echo "Erreur : Adresse IP invalide.";
        }

        echo "</pre>";
    }
    ?>
</div>
</body>
</html>
