<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: listar.php');
    exit;
}

// Não pode excluir o próprio usuário logado
if ($id == $_SESSION['usuario_id']) {
    header('Location: listar.php?status=erro');
    exit;
}

$stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: listar.php?status=sucesso');
exit;
?>