<?php
// auth/register.php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/_init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["success" => false, "message" => "Método no permitido"], 405);
}

$data = getRequestData();
$nombre    = trim($data['nombre'] ?? '');
$email     = strtolower(trim($data['email'] ?? ''));
$password  = $data['password'] ?? '';
$direccion = trim($data['direccion'] ?? '');
$telefono  = trim($data['telefono'] ?? '');
// si no envían rol, será cliente por defecto
$rol       = $data['rol'] ?? 'cliente';

// validaciones básicas
if (!$nombre || !$email || !$password) {
    jsonResponse(["success" => false, "message" => "Todos los campos son obligatorios"], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(["success" => false, "message" => "Email inválido"], 400);
}
if (strlen($password) < 6) {
    jsonResponse(["success" => false, "message" => "La contraseña debe tener al menos 6 caracteres"], 400);
}
if (!in_array($rol, ['admin', 'cliente'])) {
    $rol = 'cliente';
}

// verificar si email ya existe
$stmt = $conn->prepare("SELECT user_id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(["success" => false, "message" => "El email ya está registrado"], 409);
}

// insertar usuario
$hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $conn->prepare("
    INSERT INTO usuarios (nombre, email, password_hash, direccion, telefono, rol, usuario_registro_id)
    VALUES (?, ?, ?, ?, ?, ?, NULL)
");

try {
    $insert->execute([$nombre, $email, $hash, $direccion, $telefono, $rol]);
    $id = $conn->lastInsertId();

    // obtener datos del usuario sin password
    $get = $conn->prepare("
        SELECT user_id, nombre, email, direccion, telefono, rol, fecha_registro 
        FROM usuarios 
        WHERE user_id = ?
    ");
    $get->execute([$id]);
    $user = $get->fetch(PDO::FETCH_ASSOC);

    // crear sesión
    $_SESSION['user'] = $user;

    header("Location: /digitalstreamline/index.php?registro=ok");
    exit;
} catch (Exception $e) {
    jsonResponse(["success" => false, "message" => "Error en registro"], 500);
}
