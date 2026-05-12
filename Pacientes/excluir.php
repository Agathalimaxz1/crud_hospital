<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';
$conexao = new PDO('mysql:host=mysql;dbname=hospital_db;charset=utf8', 'hospital_user', 'hospital123');
$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: listar.php');
    exit;
}
$stmt = $conexao->prepare("SELECT COUNT(*) FROM consultas WHERE paciente_id = :id");
$stmt->execute([':id' => $id]);
$total = $stmt->fetchColumn();

if ($total > 0) {
    header('Location: listar.php?erro=Não é possível excluir — paciente possui consultas cadastradas.');
    exit;
}
$stmt = $conexao->prepare("DELETE FROM pacientes WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: listar.php?sucesso=Paciente excluído com sucesso!');
exit;
?>
