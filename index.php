<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/conexao.php';
$conexao = new PDO('mysql:host=mysql;dbname=hospital_db;charset=utf8', 'hospital_user', 'hospital123');
$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$total_pacientes = $conexao->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
$total_medicos   = $conexao->query("SELECT COUNT(*) FROM medicos")->fetchColumn();
$total_consultas = $conexao->query("SELECT COUNT(*) FROM consultas")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — HSJ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
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
            <a href="logout.php" class="bg-white text-blue-800 text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-blue-50 transition">Sair</a>
        </div>
    </div>
</nav>

<!-- Menu -->
<div class="bg-blue-900">
    <div class="px-4 flex gap-1 overflow-x-auto">
        <a href="index.php" class="text-white text-sm px-4 py-3 border-b-2 border-white font-medium whitespace-nowrap">Painel</a>
        <a href="pacientes/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Pacientes</a>
        <a href="medicos/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Médicos</a>
        <a href="consultas/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Consultas</a>
        <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
        <a href="usuarios/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Usuários</a>
        <?php endif; ?>
    </div>
</div>

<!-- Conteúdo -->
<div class="max-w-7xl mx-auto px-4 py-12">

    <!-- Boas vindas -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 shadow">Painel do Administrador!</h1>
        <p class="text-gray-500 text-base mt-1">Bem-vindo ao sistema do Hospital São José</p>
    </div>

    <!-- Cards de contagem -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow-lg p-6 flex items-center gap-4 hover:scale-105 transition duration-200">
            <div class="bg-blue-100 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Total de Pacientes</p>
                <p class="text-2xl font-bold text-gray-800"><?= $total_pacientes ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 flex items-center gap-4 hover:scale-105 transition duration-200">
            <div class="bg-green-100 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Total de Médicos</p>
                <p class="text-2xl font-bold text-gray-800"><?= $total_medicos ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 flex items-center gap-4 hover:scale-105 transition duration-200">
            <div class="bg-purple-100 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Total de Consultas</p>
                <p class="text-2xl font-bold text-gray-800"><?= $total_consultas ?></p>
            </div>
        </div>

    </div>

    <!-- Seção Pacientes -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-700 shadow-lg">Pacientes</h2>
            <a href="pacientes/listar.php" class="text-blue-600 text-sm hover:underline">Ver todos →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="pacientes/listar.php" class="bg-white rounded-xl shadow p-5 flex items-center gap-4 hover:shadow-md border border-transparent hover:border-blue-200 transition">
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Listar Pacientes</p>
                    <p class="text-xs text-gray-400">Ver todos os Pacientes</p>
                </div>
            </a>
            <a href="pacientes/cadastrar.php" class="bg-white rounded-xl shadow p-5 flex items-center gap-4 hover:shadow-md border border-transparent hover:border-blue-200 transition">
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Novo Paciente</p>
                    <p class="text-xs text-gray-400">Cadastrar paciente</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Seção Médicos -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-700 shadow-lg">Médicos</h2>
            <a href="medicos/listar.php" class="text-green-600 text-sm hover:underline">Ver todos →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="medicos/listar.php" class="bg-white rounded-xl shadow p-5 flex items-center gap-4 hover:shadow-md border border-transparent hover:border-green-200 transition">
                <div class="bg-green-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Listar Médicos</p>
                    <p class="text-xs text-gray-400">Ver todos os médicos</p>
                </div>
            </a>
            <a href="medicos/cadastrar.php" class="bg-white rounded-xl shadow p-5 flex items-center gap-4 hover:shadow-md border border-transparent hover:border-green-200 transition">
                <div class="bg-green-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Novo Médico</p>
                    <p class="text-xs text-gray-400">Cadastrar médico</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Seção Consultas -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-700 shadow-lg">Consultas</h2>
            <a href="consultas/listar.php" class="text-purple-600 text-sm hover:underline">Ver todos →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="consultas/listar.php" class="bg-white rounded-xl shadow p-5 flex items-center gap-4 hover:shadow-md border border-transparent hover:border-purple-200 transition">
                <div class="bg-purple-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Listar Consultas</p>
                    <p class="text-xs text-gray-400">Ver todas as consultas</p>
                </div>
            </a>
            <a href="consultas/cadastrar.php" class="bg-white rounded-xl shadow p-5 flex items-center gap-4 hover:shadow-md border border-transparent hover:border-purple-200 transition">
                <div class="bg-purple-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Nova Consulta</p>
                    <p class="text-xs text-gray-400">Agendar consulta</p>
                </div>
            </a>
        </div>
    </div>

</div>

<!-- Rodapé -->
<footer class="text-center text-gray-400 text-xs py-6">
    © 2026 Hospital São José — Sistema Interno
</footer>

</body>
</html>