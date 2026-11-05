<?php
require 'db.php';
 
$fouten = [];
$succes = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
 

    if ($email === '' || $wachtwoord === '') {
        $fouten[] = 'Vul je e-mailadres en wachtwoord in.';
    } else {
        try {
            $db = verbindMetDatabase();
 
            $stmt = $db->prepare('SELECT wachtwoord_hash FROM gebruikers WHERE email = ?');
            $stmt->execute([$email]);
            $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);
 
            if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord_hash'])) {
                $succes = 'Welkom! Je bent succesvol ingelogd.';
            } else {
                $fouten[] = 'E-mailadres of wachtwoord is onjuist.';
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
    <title>Inloggen</title>
    <link rel="stylesheet" href="opdracht hash je wachtwoord.css">
</head>
<body>
    <h1>Inloggen</h1>
 
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
        <button type="submit">Inloggen</button>
    </form>
 
    <p>Nog geen account? <a href="register.php">Registreren</a></p>
</body>
</html>