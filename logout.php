<?php
session_start();
session_unset();
session_destroy();

// Giriş sayfasına yönlendir
header('Location: login.php');
exit;
