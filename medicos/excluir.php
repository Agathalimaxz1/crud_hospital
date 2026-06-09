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

$stmt = $conexao->prepare("SELECT COUNT(*) FROM consultas WHERE medico_id = :id");
$stmt->execute([':id' => $id]);
$total = $stmt->fetchColumn();

if ($total > 0) {
    header('Location: listar.php?status=erro');
    exit;
}

$stmt = $conexao->prepare("DELETE FROM medicos WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: listar.php?status=sucesso');
exit;
?>