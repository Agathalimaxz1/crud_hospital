<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: listar.php');
    exit;
}

$sql = "SELECT COUNT(*) FROM consultas WHERE paciente_id = :id";
$stmt = $conexao->prepare($sql);
$stmt->execute([':id' => $id]);
$total = $stmt->fetchColumn();

if ($total > 0) {
    header('Location: listar.php?erro');
    exit;
}

$stmt = $conexao->prepare("DELETE FROM pacientes WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: listar.php?sucesso');
exit;

?>