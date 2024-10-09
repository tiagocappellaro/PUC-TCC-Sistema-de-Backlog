<?php
session_start();
if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    require_once __DIR__ . '/../controllers/LoginController.php';
    $controller = new LoginController();

    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if ($user = $controller->autenticar($usuario, $senha)) {
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['nome'] = $user['nome']; // Adicionando nome à sessão
        $_SESSION['agencia'] = $user['agencia']; // Adicionando agência à sessão
        header('Location: ../../index.php');
        exit();
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Backlog</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-header">
            <h1>BACKLOG</h1>
        </div>
        <div class="login-container">
            <h3 class="text-center">Bem-vindo</h3>
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-block btn-login">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Inclusão dos Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
