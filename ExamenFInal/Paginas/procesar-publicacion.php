<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require '/config.php';

$user_id = $_SESSION['user_id'];
$titulo = $_POST['service-title'];
$categoria = $_POST['category'];
$descripcion = $_POST['description'];
$cobertura = $_POST['service-area'];
$telefono = $_POST['contact-phone'];
$email = $_POST['contact-email'];

// Manejar imagen
$foto_nombre = '';
if (!empty($_FILES['profile-photo']['name'])) {
    $foto_nombre = basename($_FILES['profile-photo']['name']);
    $ruta_destino = '../uploads/' . $foto_nombre;
    move_uploaded_file($_FILES['profile-photo']['tmp_name'], $ruta_destino);
}

$sql = "INSERT INTO publicaciones_servicios 
    (user_id, titulo, categoria, descripcion, cobertura, telefono, email, foto_perfil) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssssss", $user_id, $titulo, $categoria, $descripcion, $cobertura, $telefono, $email, $foto_nombre);

if ($stmt->execute()) {
    header("Location: ../Paginas/perfil-profecional.php");
    exit();
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
