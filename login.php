<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $conexao = new PDO("mysql:host=mysql;dbname=hospital_db;charset=utf8", "hospital_user", "hospital123");
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];

            header('Location: index.php');
            exit;
        } else {
            $erro = "Email ou senha incorretos.";
        }
    }
}
?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HSJ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center px-4" style="background-image: linear-gradient(rgba(10, 36, 99, 0.75), rgba(10, 36, 99, 0.75)), url('img/hospital.jpg'); background-size: cover; background-position: center;">

    <div class="w-full max-w-md">

        
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            
            <div class="bg-blue-800 px-8 py-8 text-center">
                
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-blue-800" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 8h-3V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1z"/>
                    </svg>
                </div>
                <h1 class="text-white text-3xl font-bold tracking-widest">HSJ</h1>
                <p class="text-blue-200 text-sm mt-1 tracking-wide">Hospital São José</p>
            </div>

            
            <div class="px-8 py-8">

                <h2 class="text-gray-700 text-lg font-semibold mb-6 text-center">Acesso ao Sistema</h2>

                <?php if ($erro): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <input
                            type="email"
                            name="email"
                            placeholder="seu@email.com"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Senha</label>
                        <input
                            type="password"
                            name="senha"
                            placeholder="••••••••"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition duration-200 text-sm tracking-wide"
                    >
                        Entrar
                    </button>

                </form>
            </div>

            
            <div class="px-8 pb-6 text-center">
                <p class="text-xs text-gray-400">© 2026 Hospital São José — Sistema Interno</p>
            </div>

        </div>
    </div>

</body>
</html>