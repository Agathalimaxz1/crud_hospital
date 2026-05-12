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

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome            = trim($_POST['nome']);
    $cpf             = trim($_POST['cpf']);
    $data_nascimento = $_POST['data_nasc'];
    $telefone        = trim($_POST['telefone']);
    $endereco        = trim($_POST['endereco']);

    // Validação
    if (empty($nome) || empty($cpf) || empty($data_nascimento) || empty($telefone) || empty($endereco)) {
        $erro = "Preencha todos os campos.";
    } elseif (strlen(preg_replace('/\D/', '', $cpf)) !== 11) {
        $erro = "CPF inválido — deve ter 11 dígitos.";
    } else {
        $stmt = $conexao->prepare("SELECT id FROM pacientes WHERE cpf = :cpf");
        $stmt->execute([':cpf' => $cpf]);
        if ($stmt->fetch()) {
            $erro = "Já existe um paciente cadastrado com esse CPF.";
        } else {
            $stmt = $conexao->prepare("INSERT INTO pacientes (nome, cpf, data_nasc, telefone, endereco) VALUES (:nome, :cpf, :data_nasc, :telefone, :endereco)");
            $stmt->execute([
                ':nome'            => $nome,
                ':cpf'             => $cpf,
                ':data_nasc'       => $data_nascimento,
                ':telefone'        => $telefone,
                ':endereco'        => $endereco,
            ]);
            header('Location: listar.php?sucesso=Paciente cadastrado com sucesso!');
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
    <title>Novo Paciente — HSJ</title>
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

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Novo Paciente</h1>
        <p class="text-gray-500 text-sm mt-1">Preencha os dados para cadastrar um novo paciente</p>
    </div>

    <?php if ($erro): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nome completo</label>
                <input type="text" name="nome" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">CPF</label>
                <input type="text" name="cpf" required placeholder="000.000.000-00" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Data de nascimento</label>
                <input type="date" name="data_nasc" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= isset($_POST['data_nascimento']) ? htmlspecialchars($_POST['data_nascimento']) : '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Telefone</label>
                <input type="text" name="telefone" required placeholder="(00) 00000-0000" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Endereço</label>
                <input type="text" name="endereco" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : '' ?>">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-800 text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:bg-blue-700 transition">Cadastrar</button>
                <a href="listar.php" class="bg-gray-100 text-gray-600 text-sm font-semibold px-6 py-2.5 rounded-lg hover:bg-gray-200 transition">Cancelar</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>