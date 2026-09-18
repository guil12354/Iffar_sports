<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'administrador') {
    header("Location: ../index.php");
    exit;
}

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");
$conexao->set_charset("utf8mb4");

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$mensagem = "";

if (isset($_POST['cadastrar'])) {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha, nivel)
            VALUES (?, ?, ?, 'administrador')";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sss",
        $nome,
        $email,
        $senhaHash
    );

    if ($stmt->execute()) {
        $mensagem = "Administrador cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar administrador.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar administrador</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container py-5">

    <h1>Cadastrar administrador</h1>

    <?php if ($mensagem != ""): ?>

        <div class="alert alert-info">
            <?= htmlspecialchars($mensagem) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">
                Nome
            </label>

            <input
                type="text"
                name="nome"
                class="form-control"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                E-mail
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Senha
            </label>

            <input
                type="password"
                name="senha"
                class="form-control"
                required
            >

        </div>

        <button
            type="submit"
            name="cadastrar"
            class="btn btn-success"
        >
            Cadastrar administrador
        </button>

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Voltar
        </a>

    </form>

</div>

</body>

</html>