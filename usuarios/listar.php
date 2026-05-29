<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$conexao = new PDO('mysql:host=mysql;dbname=hospital_db;charset=utf8', 'hospital_user', 'hospital123');
$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $conexao->query("SELECT id, nome, email, perfil, criado_em FROM usuarios ORDER BY nome ASC");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários — HSJ</title>
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
        <a href="../consultas/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Consultas</a>
        <a href="listar.php" class="text-white text-sm px-4 py-3 border-b-2 border-white font-medium whitespace-nowrap">Usuários</a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Usuários</h1>
            <p class="text-gray-500 text-sm mt-1">Gerenciamento de usuários do sistema</p>
        </div>
        <a href="cadastrar.php" class="bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">+ Novo Usuário</a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-5">
            <?= htmlspecialchars($_GET['sucesso']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <?= htmlspecialchars($_GET['erro']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">#</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Nome</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Email</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Perfil</th>
                    <th class="text-left px-5 py-3 text-gray-500 font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-8">Nenhum usuário cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-400"><?= $u['id'] ?></td>
                        <td class="px-5 py-3 font-medium text-gray-800"><?= htmlspecialchars($u['nome']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-5 py-3">
                            <?php if ($u['perfil'] === 'admin'): ?>
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">Admin</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full font-medium">Recepcionista</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 flex gap-2">
                            <a href="editar.php?id=<?= $u['id'] ?>" class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-lg hover:bg-yellow-200 transition">Editar</a>
                            <?php if ($u['id'] !== $_SESSION['usuario_id']): ?>
                                <a href="excluir.php?id=<?= $u['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')" class="bg-red-100 text-red-600 text-xs px-3 py-1 rounded-lg hover:bg-red-200 transition">Excluir</a>
                            <?php else: ?>
                                <span class="text-xs text-gray-300 px-3 py-1">Você</span>
                            <?php endif; ?>
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