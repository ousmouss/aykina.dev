<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';
require_login_page();

$active = 'settings';
$page_title = 'Settings';
$user_id = (int) $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST['form'] ?? '';

    if ($form === 'profile') {
        $newUsername = trim($_POST['username'] ?? '');
        $newEmail    = trim($_POST['email'] ?? '');

        if ($newUsername === '') {
            $error = "Le nom d'utilisateur ne peut pas être vide.";
        } else {
            $check = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $check->execute([$newUsername, $user_id]);
            if ($check->fetch()) {
                $error = "Ce nom d'utilisateur est déjà pris.";
            } else {
                $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
                $stmt->execute([$newUsername, $newEmail ?: null, $user_id]);
                $_SESSION['username'] = $newUsername;
                $_SESSION['email'] = $newEmail;
                $success = 'Profil mis à jour.';
            }
        }
    } elseif ($form === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, $row['password_hash'])) {
            $error = 'Mot de passe actuel incorrect.';
        } elseif (strlen($new) < 6) {
            $error = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
        } elseif ($new !== $confirm) {
            $error = 'Les nouveaux mots de passe ne correspondent pas.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$hash, $user_id]);
            $success = 'Mot de passe mis à jour.';
        }
    }
}
 
require __DIR__ . '/includes/layout_top.php';
?>
      <h1 class="page-heading">Settings</h1>
      <p class="page-sub">Gère ton profil et ton mot de passe.</p>

      <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
      <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

      <section class="panel settings-panel">
        <div class="panel-head">
          <div class="panel-title-group">
            <?= icon_svg('gear') ?>
            <h2>Profil</h2>
          </div>
        </div>
        <form method="post" class="settings-form">
          <input type="hidden" name="form" value="profile">
          <label for="settingsUsername">Nom d'utilisateur</label>
          <input type="text" id="settingsUsername" name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>

          <label for="settingsEmail">Email</label>
          <input type="email" id="settingsEmail" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">

          <button type="submit">Enregistrer le profil</button>
        </form>
      </section>

      <section class="panel settings-panel">
        <div class="panel-head">
          <div class="panel-title-group">
            <h2>Mot de passe</h2>
          </div>
        </div>
        <form method="post" class="settings-form">
          <input type="hidden" name="form" value="password">
          <label for="currentPassword">Mot de passe actuel</label>
          <input type="password" id="currentPassword" name="current_password" required>

          <label for="newPassword">Nouveau mot de passe</label>
          <input type="password" id="newPassword" name="new_password" minlength="6" required>

          <label for="confirmPassword">Confirmer le nouveau mot de passe</label>
          <input type="password" id="confirmPassword" name="confirm_password" minlength="6" required>

          <button type="submit">Changer le mot de passe</button>
        </form>
      </section>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
