<?php

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");
$conexao->set_charset("utf8mb4");

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: noticiascrud.php");
    exit;
}

$sql = "SELECT * FROM noticias WHERE id_noticia = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$noticia = $resultado->fetch_assoc();

if (!$noticia) {
    header("Location: noticiascrud.php");
    exit;
}

if (isset($_POST['editar'])) {

    $titulo = $_POST['titulo'];
    $resumo = $_POST['resumo'];
    $descricao = $_POST['descricao'];
    $autor = $_POST['autor'];
    $categoria = $_POST['categoria'];

    $imagem = $noticia['imagem'];

    if (!empty($_FILES['imagem']['name'])) {

        $pasta = "../img/noticias/";

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $nomeImagem = time() . "_" . basename($_FILES['imagem']['name']);
        $caminho = $pasta . $nomeImagem;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $imagem = "img/noticias/" . $nomeImagem;
        }
    }

    $sql = "UPDATE noticias SET
            titulo = ?,
            resumo = ?,
            descricao = ?,
            autor = ?,
            categoria = ?,
            imagem = ?
            WHERE id_noticia = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ssssssi",
        $titulo,
        $resumo,
        $descricao,
        $autor,
        $categoria,
        $imagem,
        $id
    );

    $stmt->execute();

    header("Location: noticiascrud.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Notícia - IFFar Sports</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container py-5">

    <h1 class="mb-4">Editar notícia</h1>

    <div class="card">

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">

                    <label class="form-label">Título</label>

                    <input
                        type="text"
                        name="titulo"
                        class="form-control"
                        value="<?= htmlspecialchars($noticia['titulo']) ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">Resumo</label>

                    <textarea
                        name="resumo"
                        class="form-control"
                        rows="3"
                        required
                    ><?= htmlspecialchars($noticia['resumo']) ?></textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">Descrição</label>

                    <textarea
                        name="descricao"
                        class="form-control"
                        rows="7"
                        required
                    ><?= htmlspecialchars($noticia['descricao']) ?></textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">Autor</label>

                    <input
                        type="text"
                        name="autor"
                        class="form-control"
                        value="<?= htmlspecialchars($noticia['autor']) ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">Categoria</label>

                    <select
                        name="categoria"
                        class="form-select"
                        required
                    >

                        <option value="">Selecione</option>

                        <option
                            value="Esportes"
                            <?= $noticia['categoria'] == 'Esportes' ? 'selected' : '' ?>
                        >
                            Esportes
                        </option>

                        <option
                            value="Institucional"
                            <?= $noticia['categoria'] == 'Institucional' ? 'selected' : '' ?>
                        >
                            Institucional
                        </option>

                        <option
                            value="Eventos"
                            <?= $noticia['categoria'] == 'Eventos' ? 'selected' : '' ?>
                        >
                            Eventos
                        </option>

                        <option
                            value="Notícias"
                            <?= $noticia['categoria'] == 'Notícias' ? 'selected' : '' ?>
                        >
                            Notícias
                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">Imagem atual</label>

                    <br>

                    <?php if (!empty($noticia['imagem'])): ?>

                        <img
                            src="../<?= htmlspecialchars($noticia['imagem']) ?>"
                            width="200"
                            height="130"
                            style="object-fit: cover;"
                            class="rounded"
                        >

                    <?php else: ?>

                        <p>Sem imagem cadastrada.</p>

                    <?php endif; ?>

                </div>

                <div class="mb-4">

                    <label class="form-label">Trocar imagem</label>

                    <input
                        type="file"
                        name="imagem"
                        class="form-control"
                        accept="image/*"
                    >

                </div>

                <button
                    type="submit"
                    name="editar"
                    class="btn btn-success"
                >
                    Salvar alterações
                </button>

                <a
                    href="noticiascrud.php"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>
