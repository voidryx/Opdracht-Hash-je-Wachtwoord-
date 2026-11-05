<?php
require 'db.php';
 
$fouten = [];
$succes = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
    $wachtwoord2 = $_POST['wachtwoord2'] ?? '';
 
    if ($email === '' || $wachtwoord === '' || $wachtwoord2 === '') {
        $fouten[] = 'Vul alle velden in.';
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fouten[] = 'Dit is geen geldig e-mailadres.';
        }
        if (strlen($wachtwoord) < 8) {
            $fouten[] = 'Het wachtwoord moet minimaal 8 tekens lang zijn.';
        }
        if ($wachtwoord !== $wachtwoord2) {
            $fouten[] = 'De twee wachtwoorden zijn niet hetzelfde.';
        }
    }
 
    if (empty($fouten)) {
        try {
            $db = verbindMetDatabase();
 
            $stmt = $db->prepare('SELECT id FROM gebruikers WHERE email = ?');
            $stmt->execute([$email]);
 
            if ($stmt->fetch()) {
                $fouten[] = 'Dit e-mailadres is al geregistreerd.';
            } else {
                $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);
 
                if ($hash === false) {
                    throw new Exception('Het hashen van het wachtwoord is mislukt.');
                }
 
                $stmt = $db->prepare('INSERT INTO gebruikers (email, wachtwoord_hash) VALUES (?, ?)');
                $stmt->execute([$email, $hash]);
 
                $succes = 'Registratie gelukt! Je kunt nu inloggen.';
            }
        } catch (Exception $e) {
            $fouten[] = 'Er ging iets mis: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren</title>
    <link rel="stylesheet" href="opdracht hash je wachtwoord.css">
</head>
<body>
    <h1>Registreren</h1>
 
    <?php foreach ($fouten as $fout): ?>
        <p style="color: red;"><?= htmlspecialchars($fout) ?></p>
    <?php endforeach; ?>
 
    <?php if ($succes): ?>
        <p style="color: green;"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
 
    <form method="post">
        <p>
            <label>E-mail:<br>
            <input type="text" name="email" value="<?= htmlspecialchars($email ?? '') ?>"></label>
        </p>
        <p>
            <label>Wachtwoord:<br>
            <input type="password" name="wachtwoord"></label>
        </p>
        <p>
            <label>Wachtwoord nogmaals:<br>
            <input type="password" name="wachtwoord2"></label>
        </p>
        <button type="submit">Registreren</button>
    </form>
 
    <p>Al een account? <a href="login.php">Inloggen</a></p>
</body>
</html>