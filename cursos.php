<?php
require 'conexao.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome = $_POST['nome'];
    $area = $_POST['area'];

    $stmt = $pdo->prepare('INSERT INTO cursos (nome, area) VALUES (?, ?)');
    $stmt->execute([$nome, $area]);
    header('Location: cursos.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $area = $_POST['area'];

    $stmt = $pdo->prepare('UPDATE cursos SET nome = ?, area = ? WHERE id = ?');
    $stmt->execute([$nome, $area, $id]);
    header('Location: cursos.php');
    exit;
}
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    $stmt = $pdo->prepare('DELETE FROM cursos WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: cursos.php');
    exit;
}

$cursos = $pdo->query('SELECT * FROM cursos')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Cursos</title>
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
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            animation: fadeIn 1s ease-in-out;
        }

        input[type="text"] {
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 250px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
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
        a.delete-btn {
            color: red;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        a.delete-btn:hover {
            color: #d9534f;
        }
        form.edit-form {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        form.edit-form input {
            padding: 8px;
            font-size: 14px;
        }

        form.edit-form button {
            padding: 8px 12px;
            font-size: 14px;
            background-color: #6a11cb;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form.edit-form button:hover {
            background-color: #2575fc;
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
    <h1>Gerenciar Cursos</h1>
    <form method="POST">
        <input type="hidden" name="adicionar" value="1">
        <input type="text" name="nome" placeholder="Nome do Curso" required>
        <input type="text" name="area" placeholder="Área" required>
        <button type="submit">Adicionar Curso</button>
    </form>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Área</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($cursos as $curso): ?>
            <tr>
                <td><?= $curso['id'] ?></td>
                <td><?= $curso['nome'] ?></td>
                <td><?= $curso['area'] ?></td>
                <td>
                    <form method="POST" class="edit-form" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $curso['id'] ?>">
                        <input type="text" name="nome" value="<?= $curso['nome'] ?>" required>
                        <input type="text" name="area" value="<?= $curso['area'] ?>" required>
                        <button type="submit" name="editar">Salvar</button>
                    </form>
                    <a href="?excluir=<?= $curso['id'] ?>" class="delete-btn" onclick="return confirm('Deseja realmente excluir este curso?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
