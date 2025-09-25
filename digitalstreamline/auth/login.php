<?php
// auth/login.php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../auth/_init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["success" => false, "message" => "Método no permitido"], 405);
}

$data = getRequestData();
$email    = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if (!$email || !$password) {
    jsonResponse(["success" => false, "message" => "Email y contraseña son obligatorios"], 400);
}

// buscar usuario
$stmt = $pdo->prepare("
    SELECT user_id, nombre, email, password_hash, direccion, telefono, rol, fecha_registro 
    FROM usuarios 
    WHERE email = ?
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    jsonResponse(["success" => false, "message" => "Credenciales incorrectas"], 401);
}

// quitar password antes de guardar en sesión
unset($user['password_hash']);

// guardar datos en sesión
$_SESSION['user'] = $user;
$_SESSION['user_id'] = $user['user_id'];

// redirección según rol
if ($user['rol'] === 'admin') {
    header("Location: /digitalstreamline/admin/dashboard.php");
} else {
    header("Location: /digitalstreamline/index.php");
}
exit;

?>
