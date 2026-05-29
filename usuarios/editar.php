<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'admin') {
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

$stmt = $conexao->prepare("SELECT id, nome, email, perfil FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: listar.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome']);
    $email  = trim($_POST['email']);
    $senha  = $_POST['senha'];
    $confirmar = $_POST['confirmar'];
    $perfil = $_POST['perfil'];

    if (empty($nome) || empty($email) || empty($perfil)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } elseif (!empty($senha) && strlen($senha) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
    } elseif (!empty($senha) && $senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } else {
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetch()) {
            $erro = "Já existe outro usuário com esse email.";
        } else {
            if (!empty($senha)) {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, perfil = :perfil WHERE id = :id");
                $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $hash, ':perfil' => $perfil, ':id' => $id]);
            } else {
                $stmt = $conexao->prepare("UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id");
                $stmt->execute([':nome' => $nome, ':email' => $email, ':perfil' => $perfil, ':id' => $id]);
            }
            header('Location: listar.php?sucesso=Usuário atualizado com sucesso!');
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
    <title>Editar Usuário — HSJ</title>
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

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Usuário</h1>
        <p class="text-gray-500 text-sm mt-1">Deixe a senha em branco para não alterá-la</p>
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
                <input type="text" name="nome" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= htmlspecialchars($usuario['nome']) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Perfil</label>
                <select name="perfil" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="admin" <?= $usuario['perfil'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="recepcionista" <?= $usuario['perfil'] === 'recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nova senha <span class="text-gray-400">(deixe em branco para não alterar)</span></label>
                <input type="password" name="senha" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Confirmar nova senha</label>
                <input type="password" name="confirmar" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
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