<?php
require 'conexao.php';
session_start();

// Verifica se há pelo menos um administrador no sistema
$stmt = $pdo->query('SELECT COUNT(*) FROM admins');
$temAdmin = $stmt->fetchColumn() > 0;

// Verifica se o usuário está autenticado (somente se já existir administrador)
if ($temAdmin && !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Processa o formulário de cadastro de administradores
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografa a senha

    // Verifica se o email já existe
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $adminExistente = $stmt->fetch();

    if ($adminExistente) {
        $erro = "Este email já está cadastrado.";
    } else {
        // Insere o novo administrador no banco
        $stmt = $pdo->prepare('INSERT INTO admins (email, senha) VALUES (?, ?)');
        if ($stmt->execute([$email, $senha])) {
            $sucesso = "Administrador cadastrado com sucesso!";
            header('Location: cadastro_admins.php?success=1'); // Evita reenvio de formulário
            exit;
        } else {
            $erro = "Erro ao cadastrar administrador. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Administradores</title>
    <link rel="stylesheet" href="style.css">
    <style>
    /* Reset básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Estilo geral do body */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #6a11cb, #2575fc); /* Gradiente diagonal */
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    overflow: hidden;
}

/* Cabeçalho */
h1 {
    font-size: 2rem;
    color: #fff;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}

/* Formulário */
form {
    background-color: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    animation: fadeIn 1s ease-in-out; /* Animação de fade-in */
}

/* Estilo para labels */
form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #6a11cb;
}

/* Estilo para os campos de entrada */
form input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
    transition: border-color 0.3s ease;
}

form input:focus {
    border-color: #6a11cb;
    box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
}

/* Botão */
form button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(90deg, #6a11cb, #2575fc);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

form button:hover {
    background: linear-gradient(90deg, #2575fc, #6a11cb);
    transform: translateY(-2px);
}

/* Mensagens de feedback */
p {
    margin-top: 10px;
    text-align: center;
    font-size: 1rem;
}

p[style="color: blue;"] {
    color: #3498db;
}

p[style="color: red;"] {
    color: #e74c3c;
}

/* Link para Login */
a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    transition: color 0.3s ease;
}

a:hover {
    color: #45a049;
}

/* Animação de Fade-in */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


</style>

</head>
<body>
    <h1>Cadastrar Novo Administrador</h1>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Digite o email" required>
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" placeholder="Digite a senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <?php if (isset($_GET['success'])): ?>
        <p style="color: blue;">Administrador cadastrado com sucesso!</p>
    <?php elseif (isset($erro)): ?>
        <p style="color: red;"><?= $erro ?></p>
    <?php endif; ?>
    <?php if ($temAdmin): ?>
        
        <a href="login.php">Ir para o Login</a>
    <?php endif; ?>
</body>
</html>
