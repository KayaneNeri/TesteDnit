<?php
require 'conexao.php';

$cursos = $pdo->query('SELECT * FROM cursos')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $dataNascimento = $_POST['data_nascimento'];
    $cursoIds = $_POST['curso_id'];  

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('INSERT INTO alunos (nome, email, data_nascimento) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $email, $dataNascimento]);
        $alunoId = $pdo->lastInsertId(); 

        if ($cursoIds) {
            foreach ($cursoIds as $cursoId) {
                $stmt = $pdo->prepare('INSERT INTO matriculas (aluno_id, curso_id) VALUES (?, ?)');
                $stmt->execute([$alunoId, $cursoId]);
            }
        }

        $pdo->commit();

        echo "Aluno cadastrado e matriculado com sucesso!";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Alunos e Matrícula</title>
    <link rel="stylesheet" href="../css/style.css">
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }
        h1 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #6a11cb;
        }

        input[type="text"], input[type="email"], input[type="date"] {
            width: 100%;
            padding: 12px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="date"]:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .courses-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .courses-checkboxes label {
            display: inline-block;
            font-size: 16px;
            padding: 5px 10px;
            background-color: #e1e1e1;
            border-radius: 5px;
            cursor: pointer;
        }

        .courses-checkboxes input[type="checkbox"] {
            margin-right: 8px;
        }
        button {
            padding: 12px 20px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            transform: translateY(-2px);
        }
        .add-course-btn, .management-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .add-course-btn {
            background-color: #4CAF50;
        }

        .add-course-btn:hover {
            background-color: #45a049;
        }

        .management-btn {
            background-color: #008CBA;
        }

        .management-btn:hover {
            background-color: #007B9E;
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
    <h1>Cadastrar Aluno e Matricular em Cursos</h1>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" placeholder="Nome do Aluno" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email do Aluno" required>

        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" id="data_nascimento" name="data_nascimento" required>

        <label>Cursos:</label>
        <div class="courses-checkboxes">
            <?php foreach ($cursos as $curso): ?>
                <label>
                    <input type="checkbox" name="curso_id[]" value="<?= $curso['id'] ?>">
                    <?= htmlspecialchars($curso['nome']) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" name="cadastrar">Cadastrar e Matricular</button>
    </form>
    <a href="cursos.php" class="add-course-btn">Adicionar Curso</a>
    <a href="gerenciamento_alunos.php" class="management-btn">Gerenciamento de Alunos</a>
</body>
</html>
