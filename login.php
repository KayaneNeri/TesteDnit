<?php
session_start();
require 'conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!isset($pdo)) {
        die("Erro: Conexão com o banco de dados não foi estabelecida.");
    }
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['senha'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: cadastro_alunos.php');
        exit;
    } else {
        $error = "Credenciais inválidas.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #6a11cb, #2575fc); /* Gradiente diagonal */
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    overflow: hidden;
}
form {
    background: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
    animation: fadeIn 1s ease-in-out; /* Animação sutil */
}
form h2 {
    font-size: 1.8rem;
    color: #6a11cb;
    margin-bottom: 20px;
}
form input {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
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
form button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(90deg, #6a11cb, #2575fc);
    color: #ffffff;
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
form p {
    color: #e74c3c;
    font-size: 0.9rem;
    margin-top: 10px;
}

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
    <form method="POST" action="">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</body>
</html>
