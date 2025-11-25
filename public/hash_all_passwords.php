<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Config\Database;

try {
    $pdo = Database::getConnection();

    // Liste des utilisateurs avec leurs nouveaux mots de passe
    $users = [
        'Ahmed' => 'Admin123',
        'Fatima' => 'Fatima2024',
        'Mohamed' => 'Mohamed2024',
        'Yasmine' => 'Yasmine2024',
        'Karim' => 'Karim2024',
        'Leila' => 'Leila2024',
        'Omar' => 'Omar2024',
        'Nadia' => 'Nadia2024',
        'Rachid' => 'Rachid2024',
        'Samira' => 'Samira2024'
    ];

    echo "<h2>🔐 Génération des mots de passe hashés</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #4a90e2; color: white;'>";
    echo "<th>Username</th><th>Mot de passe</th><th>Hash généré</th><th>Status</th>";
    echo "</tr>";

    foreach ($users as $username => $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Mise à jour dans la DB
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
        $success = $stmt->execute([
            'password' => $hash,
            'username' => $username
        ]);

        $status = $success ? "✅ Mis à jour" : "❌ Erreur";
        $color = $success ? "#d4edda" : "#f8d7da";

        echo "<tr style='background: $color;'>";
        echo "<td><strong>$username</strong></td>";
        echo "<td>$password</td>";
        echo "<td style='font-family: monospace; font-size: 10px;'>" . substr($hash, 0, 40) . "...</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<br><h3>📋 Informations de connexion :</h3>";
    echo "<ul>";
    foreach ($users as $username => $password) {
        echo "<li><strong>$username</strong> → Mot de passe : <code>$password</code></li>";
    }
    echo "</ul>";

    echo "<br><p style='color: red;'><strong>⚠️ IMPORTANT :</strong> Supprimez ce fichier après utilisation pour des raisons de sécurité !</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
