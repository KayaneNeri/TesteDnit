<?php
require 'conexao.php';

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $stmt = $pdo->prepare('DELETE FROM alunos WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: gerenciamento_alunos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $data_nascimento = $_POST['data_nascimento'];

    $stmt = $pdo->prepare('UPDATE alunos SET nome = ?, email = ?, data_nascimento = ? WHERE id = ?');
    $stmt->execute([$nome, $email, $data_nascimento, $id]);
    header('Location: gerenciamento_alunos.php');
    exit;
}

$alunos = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = 'SELECT DISTINCT a.* FROM alunos a 
              LEFT JOIN matriculas m ON a.id = m.aluno_id
              LEFT JOIN cursos c ON m.curso_id = c.id WHERE 1=1';
    $params = [];

    if (!empty($_GET['nome'])) {
        $query .= ' AND a.nome LIKE ?';
        $params[] = '%' . $_GET['nome'] . '%';
    }
    if (!empty($_GET['email'])) {
        $query .= ' AND a.email LIKE ?';
        $params[] = '%' . $_GET['email'] . '%';
    }
    if (!empty($_GET['materia'])) {
        $query .= ' AND c.area LIKE ?';
        $params[] = '%' . $_GET['materia'] . '%';
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Alunos</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            padding: 20px;
        }

        h1 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            animation: fadeIn 1s ease-in-out;
        }

        input[type="text"], input[type="email"], input[type="date"] {
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 250px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="date"]:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        button {
            padding: 12px 20px;
            font-size: 14px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            transform: translateY(-2px);
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        a.add-aluno {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px;
            background-color: #333;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            font-size: 24px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        a.add-aluno:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        .delete-btn {
            color: #ff4d4d;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .delete-btn:hover {
            color: #d93636;
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
    <h1>Gerenciar Alunos</h1>

    <form method="GET">
        <input type="text" name="nome" placeholder="Nome">
        <input type="email" name="email" placeholder="Email">
        <input type="text" name="materia" placeholder="Matéria">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Matéria</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($alunos as $aluno): ?>
            <tr>
                <td><?= $aluno['id'] ?></td>
                <td><?= $aluno['nome'] ?></td>
                <td><?= $aluno['email'] ?></td>
                <td>
                    <?php 
                    $materias = [];
                    $stmt = $pdo->prepare('SELECT c.area FROM cursos c 
                                           LEFT JOIN matriculas m ON c.id = m.curso_id 
                                           WHERE m.aluno_id = ?');
                    $stmt->execute([$aluno['id']]);
                    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($cursos as $curso) {
                        $materias[] = $curso['area'];
                    }
                    echo implode(', ', $materias);
                    ?>
                </td>
                <td>
                    <form method="POST" action="gerenciamento_alunos.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $aluno['id'] ?>">
                        <input type="text" name="nome" value="<?= $aluno['nome'] ?>" required>
                        <input type="email" name="email" value="<?= $aluno['email'] ?>" required>
                        <input type="date" name="data_nascimento" value="<?= $aluno['data_nascimento'] ?>" required>
                        <button type="submit" name="editar">Salvar</button>
                    </form>
                    <a href="?excluir=<?= $aluno['id'] ?>" class="delete-btn" onclick="return confirm('Deseja realmente excluir este aluno?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="cadastro_alunos.php" class="add-aluno">+</a>
</body>
</html>
