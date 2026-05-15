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

$stmt = $conexao->prepare("SELECT * FROM medicos WHERE id = :id");
$stmt->execute([':id' => $id]);
$medico = $stmt->fetch();

if (!$medico) {
    header('Location: listar.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = trim($_POST['nome']);
    $crm           = trim($_POST['crm']);
    $especialidade = trim($_POST['especialidade']);
    $telefone      = trim($_POST['telefone']);

    if (empty($nome) || empty($crm) || empty($especialidade) || empty($telefone)) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conexao->prepare("SELECT id FROM medicos WHERE crm = :crm AND id != :id");
        $stmt->execute([':crm' => $crm, ':id' => $id]);
        if ($stmt->fetch()) {
            $erro = "Já existe outro médico com esse CRM.";
        } else {
            $stmt = $conexao->prepare("UPDATE medicos SET nome = :nome, crm = :crm, especialidade = :especialidade, telefone = :telefone WHERE id = :id");
            $stmt->execute([
                ':nome'          => $nome,
                ':crm'           => $crm,
                ':especialidade' => $especialidade,
                ':telefone'      => $telefone,
                ':id'            => $id,
            ]);
            header('Location: listar.php?sucesso=Médico atualizado com sucesso!');
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
    <title>Editar Médico — HSJ</title>
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
        <a href="listar.php" class="text-white text-sm px-4 py-3 border-b-2 border-white font-medium whitespace-nowrap">Médicos</a>
        <a href="../consultas/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Consultas</a>
        <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
        <a href="../usuarios/listar.php" class="text-blue-300 hover:text-white text-sm px-4 py-3 border-b-2 border-transparent hover:border-blue-300 transition whitespace-nowrap">Usuários</a>
        <?php endif; ?>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Médico</h1>
        <p class="text-gray-500 text-sm mt-1">Altere os dados do médico</p>
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
                <input type="text" name="nome" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= htmlspecialchars($medico['nome']) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">CRM</label>
                <input type="text" name="crm" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= htmlspecialchars($medico['crm']) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Especialidade</label>
                <input type="text" name="especialidade" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= htmlspecialchars($medico['especialidade']) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Telefone</label>
                <input type="text" name="telefone" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= htmlspecialchars($medico['telefone']) ?>">
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