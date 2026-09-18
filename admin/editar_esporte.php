<?php

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");
$conexao->set_charset("utf8mb4");

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: esportescrud.php");
    exit;
}

$sql = "SELECT * FROM esportes WHERE id_esporte = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$esporte = $resultado->fetch_assoc();

if (!$esporte) {
    header("Location: esportescrud.php");
    exit;
}

if (isset($_POST['editar'])) {

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

    $imagem = $esporte['imagem'];

    if (!empty($_FILES['imagem']['name'])) {

        $pasta = "../img/esportes/";

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $nomeImagem = time() . "_" . basename($_FILES['imagem']['name']);
        $caminho = $pasta . $nomeImagem;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $imagem = "img/esportes/" . $nomeImagem;
        }
    }

    $sql = "UPDATE esportes SET
        nome = ?,
        imagem = ?,
        descricao = ?,
        feminino_dias = ?,
        feminino_horario = ?,
        feminino_local = ?,
        feminino_professor = ?,
        masculino_dias = ?,
        masculino_horario = ?,
        masculino_local = ?,
        masculino_professor = ?
        WHERE id_esporte = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssssssssi",
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
        $masculino_professor,
        $id
    );

    if ($stmt->execute()) {
        header("Location: esportescrud.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Esporte</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

    <h1 class="mb-4">Editar esporte</h1>

    <form method="POST" enctype="multipart/form-data">

        <div class="card">

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label">Nome</label>

                    <input
                        type="text"
                        name="nome"
                        class="form-control"
                        value="<?= htmlspecialchars($esporte['nome']) ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">Imagem</label>

                    <?php if (!empty($esporte['imagem'])): ?>

                        <div class="mb-2">

                            <img
                                src="../<?= htmlspecialchars($esporte['imagem']) ?>"
                                width="150"
                                height="100"
                                style="object-fit: cover;"
                                class="rounded"
                            >

                        </div>

                    <?php endif; ?>

                    <input
                        type="file"
                        name="imagem"
                        class="form-control"
                        accept="image/*"
                    >

                    <small class="text-muted">
                        Se não escolher uma imagem, a atual será mantida.
                    </small>

                </div>

                <div class="mb-3">

                    <label class="form-label">Descrição</label>

                    <textarea
                        name="descricao"
                        class="form-control"
                        rows="4"
                    ><?= htmlspecialchars($esporte['descricao']) ?></textarea>

                </div>

                <h5 class="mt-4">Treino Feminino</h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Dias</label>

                        <input
                            type="text"
                            name="feminino_dias"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['feminino_dias']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Horário</label>

                        <input
                            type="text"
                            name="feminino_horario"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['feminino_horario']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Local</label>

                        <input
                            type="text"
                            name="feminino_local"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['feminino_local']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Professor</label>

                        <input
                            type="text"
                            name="feminino_professor"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['feminino_professor']) ?>"
                        >

                    </div>

                </div>

                <h5 class="mt-4">Treino Masculino</h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Dias</label>

                        <input
                            type="text"
                            name="masculino_dias"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['masculino_dias']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Horário</label>

                        <input
                            type="text"
                            name="masculino_horario"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['masculino_horario']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Local</label>

                        <input
                            type="text"
                            name="masculino_local"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['masculino_local']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Professor</label>

                        <input
                            type="text"
                            name="masculino_professor"
                            class="form-control"
                            value="<?= htmlspecialchars($esporte['masculino_professor']) ?>"
                        >

                    </div>

                </div>

                <button
                    type="submit"
                    name="editar"
                    class="btn btn-success"
                >
                    Salvar alterações
                </button>

                <a
                    href="esportescrud.php"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>

        </div>

    </form>

</div>

</body>

</html>