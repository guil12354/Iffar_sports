<?php

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

if (isset($_POST['cadastrar'])) {

    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    $feminino_dias = $_POST['feminino_dias'];
    $feminino_horario = $_POST['feminino_horario'];
    $feminino_local = $_POST['feminino_local'];
    $feminino_professor = $_POST['feminino_professor'];

    $masculino_dias = $_POST['masculino_dias'];
    $masculino_horario = $_POST['masculino_horario'];
    $masculino_local = $_POST['masculino_local'];
    $masculino_professor = $_POST['masculino_professor'];

    $imagem = "";

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {

        $pasta = "../img/esportes/";

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $nomeArquivo = basename($_FILES['imagem']['name']);

        $extensao = strtolower(
            pathinfo($nomeArquivo, PATHINFO_EXTENSION)
        );

        $nomeArquivo = uniqid() . "." . $extensao;

        $caminho = $pasta . $nomeArquivo;

        if (move_uploaded_file(
            $_FILES['imagem']['tmp_name'],
            $caminho
        )) {

            $imagem = "img/esportes/" . $nomeArquivo;

        }

    }

    $sql = "INSERT INTO esportes
    (
        nome,
        imagem,
        descricao,
        feminino_dias,
        feminino_horario,
        feminino_local,
        feminino_professor,
        masculino_dias,
        masculino_horario,
        masculino_local,
        masculino_professor
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssssssss",
        $nome,
        $imagem,
        $descricao,
        $feminino_dias,
        $feminino_horario,
        $feminino_local,
        $feminino_professor,
        $masculino_dias,
        $masculino_horario,
        $masculino_local,
        $masculino_professor
    );

    $stmt->execute();

    header("Location: esportescrud.php");
    exit;
}

if (isset($_GET['excluir'])) {

    $id = (int) $_GET['excluir'];

    $sql = "DELETE FROM esportes WHERE id_esporte = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: esportescrud.php");
    exit;
}

$esportes = $conexao->query("
    SELECT *
    FROM esportes
    ORDER BY nome ASC
");

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Esportes</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container py-5">

    <h1 class="mb-4">
        Gerenciar Esportes
    </h1>

    <div class="card mb-5">

        <div class="card-body">

            <h3 class="mb-4">
                Cadastrar esporte
            </h3>

            <form
                method="POST"
                enctype="multipart/form-data"
            >

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
                        Imagem
                    </label>

                    <input
                        type="file"
                        name="imagem"
                        class="form-control"
                        accept="image/*"
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Descrição
                    </label>

                    <textarea
                        name="descricao"
                        class="form-control"
                        rows="4"
                    ></textarea>

                </div>

                <h5 class="mt-4">
                    Treino Feminino
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Dias
                        </label>

                        <input
                            type="text"
                            name="feminino_dias"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Horário
                        </label>

                        <input
                            type="text"
                            name="feminino_horario"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Local
                        </label>

                        <input
                            type="text"
                            name="feminino_local"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Professor
                        </label>

                        <input
                            type="text"
                            name="feminino_professor"
                            class="form-control"
                        >

                    </div>

                </div>

                <h5 class="mt-4">
                    Treino Masculino
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Dias
                        </label>

                        <input
                            type="text"
                            name="masculino_dias"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Horário
                        </label>

                        <input
                            type="text"
                            name="masculino_horario"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Local
                        </label>

                        <input
                            type="text"
                            name="masculino_local"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Professor
                        </label>

                        <input
                            type="text"
                            name="masculino_professor"
                            class="form-control"
                        >

                    </div>

                </div>

                <button
                    type="submit"
                    name="cadastrar"
                    class="btn btn-success"
                >
                    Cadastrar
                </button>

            </form>

        </div>

    </div>

    <h3 class="mb-3">
        Esportes cadastrados
    </h3>

    <div class="table-responsive">

        <table class="table table-bordered table-striped align-middle">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Ações</th>

                </tr>

            </thead>

            <tbody>

                <?php while ($esporte = $esportes->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?= $esporte['id_esporte'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($esporte['nome']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($esporte['descricao']) ?>
                        </td>

                        <td>

                            <a
                                href="editar_esporte.php?id=<?= $esporte['id_esporte'] ?>"
                                class="btn btn-warning btn-sm"
                            >
                                Editar
                            </a>

                            <a
                                href="esportescrud.php?excluir=<?= $esporte['id_esporte'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Deseja realmente excluir este esporte?')"
                                class="btn btn-danger btn-sm"
                            >
                                Excluir
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

    <a
        href="../index.php"
        class="btn btn-secondary mt-3"
    >
        Voltar ao site
    </a>

</div>

</body>

</html>