<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';
$conexao = new PDO('mysql:host=mysql;dbname=hospital_db;charset=utf8', 'hospital_user', 'hospital123');
$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $conexao->query("SELECT * FROM pacientes ORDER BY nome ASC");
$pacientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes — HSJ</title>
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
        <a href="listar.php" class="text-white text-sm px-4 py-3 border-b-2 border-white font-medium whitespace-nowrap">Pacientes</a>
        <a href="../medicos/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Médicos</a>
        <a href="../consultas/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Consultas</a>
        <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
        <a href="../usuarios/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Usuários</a>
        <?php endif; ?>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pacientes</h1>
            <p class="text-gray-500 text-sm mt-1">Lista de todos os pacientes cadastrados</p>
        </div>
        <a href="cadastrar.php" class="bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">+ Novo Paciente</a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-5">
            <?= htmlspecialchars($_GET['sucesso']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">#</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Nome</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">CPF</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Telefone</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Nascimento</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($pacientes)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-8">Nenhum paciente cadastrado ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pacientes as $p): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-400"><?= $p['id'] ?></td>
                        <td class="px-5 py-3 font-medium text-gray-800"><?= htmlspecialchars($p['nome']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= htmlspecialchars($p['cpf']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= htmlspecialchars($p['telefone']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= date('d/m/Y', strtotime($p['data_nascimento'])) ?></td>
                        <td class="px-5 py-3 flex gap-2">
                            <a href="editar.php?id=<?= $p['id'] ?>" class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-lg hover:bg-yellow-200 transition">Editar</a>
                            <a href="excluir.php?id=<?= $p['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este paciente?')" class="bg-red-100 text-red-600 text-xs px-3 py-1 rounded-lg hover:bg-red-200 transition">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

