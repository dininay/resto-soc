<?php
// Mulai session
session_start();

// Hapus semua data session
$_SESSION = array();

// Hapus session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect pengguna ke halaman index
header("location: /resto-soc/index.php");
exit;
?>
