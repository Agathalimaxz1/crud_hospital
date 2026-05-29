<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$conexao = new PDO('mysql:host=mysql;dbname=hospital_db;charset=utf8', 'hospital_user', 'hospital123');
$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: listar.php');
    exit;
}

$stmt = $conexao->prepare("SELECT * FROM consultas WHERE id = :id");
$stmt->execute([':id' => $id]);
$consulta = $stmt->fetch();

if (!$consulta) {
    header('Location: listar.php');
    exit;
}

$pacientes = $conexao->query("SELECT id, nome FROM pacientes ORDER BY nome")->fetchAll();
$medicos   = $conexao->query("SELECT id, nome, especialidade FROM medicos ORDER BY nome")->fetchAll();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'];
    $medico_id   = $_POST['medico_id'];
    $data_hora   = $_POST['data_hora'];
    $status      = $_POST['status'];
    $observacoes = trim($_POST['observacoes']);

    if (empty($paciente_id) || empty($medico_id) || empty($data_hora) || empty($status)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        $stmt = $conexao->prepare("SELECT id FROM consultas WHERE medico_id = :medico_id AND data_hora = :data_hora AND status != 'cancelada' AND id != :id");
        $stmt->execute([':medico_id' => $medico_id, ':data_hora' => $data_hora, ':id' => $id]);
        if ($stmt->fetch()) {
            $erro = "Este médico já tem uma consulta agendada nesse horário.";
        } else {
            $stmt = $conexao->prepare("UPDATE consultas SET paciente_id = :paciente_id, medico_id = :medico_id, data_hora = :data_hora, status = :status, observacoes = :observacoes WHERE id = :id");
            $stmt->execute([
                ':paciente_id' => $paciente_id,
                ':medico_id'   => $medico_id,
                ':data_hora'   => $data_hora,
                ':status'      => $status,
                ':observacoes' => $observacoes,
                ':id'          => $id,
            ]);
            header('Location: listar.php?sucesso=Consulta atualizada com sucesso!');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Consulta — HSJ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-blue-800 shadow-lg">
    <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="bg-white rounded-full w-9 h-9 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-800" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 8h-3V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-xl tracking-widest">HSJ</span>
            <span class="text-blue-200 text-sm hidden sm:block">Hospital São José</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-blue-200 text-sm">Olá, <span class="text-white font-medium"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span></span>
            <a href="../logout.php" class="bg-white text-blue-800 text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-blue-50 transition">Sair</a>
        </div>
    </div>
</nav>

<div class="bg-blue-900">
    <div class="px-4 flex gap-1 overflow-x-auto">
        <a href="../index.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Painel</a>
        <a href="../pacientes/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Pacientes</a>
        <a href="../medicos/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Médicos</a>
        <a href="listar.php" class="text-white text-sm px-4 py-3 border-b-2 border-white font-medium whitespace-nowrap">Consultas</a>
        <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
        <a href="../usuarios/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Usuários</a>
        <?php endif; ?>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Consulta</h1>
        <p class="text-gray-500 text-sm mt-1">Altere os dados da consulta</p>
    </div>

    <?php if ($erro): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Paciente</label>
                <select name="paciente_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $consulta['paciente_id'] == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Médico</label>
                <select name="medico_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= $consulta['medico_id'] == $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nome']) ?> — <?= htmlspecialchars($m['especialidade']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Data e Hora</label>
                <input type="datetime-local" name="data_hora" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= date('Y-m-d\TH:i', strtotime($consulta['data_hora'])) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="agendada"  <?= $consulta['status'] === 'agendada'  ? 'selected' : '' ?>>Agendada</option>
                    <option value="realizada" <?= $consulta['status'] === 'realizada' ? 'selected' : '' ?>>Realizada</option>
                    <option value="cancelada" <?= $consulta['status'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Observações <span class="text-gray-400">(opcional)</span></label>
                <textarea name="observacoes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"><?= htmlspecialchars($consulta['observacoes'] ?? '') ?></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-800 text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:bg-blue-700 transition">Salvar</button>
                <a href="listar.php" class="bg-gray-100 text-gray-600 text-sm font-semibold px-6 py-2.5 rounded-lg hover:bg-gray-200 transition">Cancelar</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>