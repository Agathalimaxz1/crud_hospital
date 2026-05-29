<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$conexao = new PDO('mysql:host=mysql;dbname=hospital_db;charset=utf8', 'hospital_user', 'hospital123');
$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: listar.php');
    exit;
}

$stmt = $conexao->prepare("DELETE FROM consultas WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: listar.php?sucesso=Consulta excluída com sucesso!');
exit;
?>