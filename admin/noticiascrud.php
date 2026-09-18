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


if (isset($_POST['cadastrar'])) {

    $titulo = $_POST['titulo'] ?? '';
    $resumo = $_POST['resumo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $categoria = $_POST['categoria'] ?? '';

    $autor = $_SESSION['nome'] ?? 'Administrador';

    $data_noticia = date("Y-m-d H:i:s");

    $imagem = "";


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


    $sql = "INSERT INTO noticias
    (titulo, resumo, descricao, autor, categoria, imagem, data_noticia)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssssss",
        $titulo,
        $resumo,
        $descricao,
        $autor,
        $categoria,
        $imagem,
        $data_noticia
    );

    $stmt->execute();

    header("Location: noticiascrud.php");
    exit;
}


if (isset($_GET['excluir'])) {

    $id = filter_var($_GET['excluir'], FILTER_VALIDATE_INT);

    if ($id) {

        $sql = "DELETE FROM noticias WHERE id_noticia = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();
    }

    header("Location: noticiascrud.php");
    exit;
}


$sqlNoticias = "
    SELECT *
    FROM noticias
    ORDER BY id_noticia DESC
";

$noticias = $conexao->query($sqlNoticias);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciamento de notícias - IFFar Sports</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1>Gerenciamento de notícias</h1>

            <p class="text-muted">
                Criar, editar e excluir notícias
            </p>

        </div>

        <div>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Voltar
            </a>

            <a
                href="../logout.php"
                class="btn btn-danger"
            >
                Sair
            </a>

        </div>

    </div>


    <div class="card mb-5">

        <div class="card-header">

            <h2 class="h4 mb-0">
                Cadastrar notícia
            </h2>

        </div>

        <div class="card-body">

            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <div class="mb-3">

                    <label class="form-label">
                        Título
                    </label>

                    <input
                        type="text"
                        name="titulo"
                        class="form-control"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Resumo
                    </label>

                    <textarea
                        name="resumo"
                        class="form-control"
                        rows="3"
                        required
                    ></textarea>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Descrição
                    </label>

                    <textarea
                        name="descricao"
                        class="form-control"
                        rows="6"
                        required
                    ></textarea>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Categoria
                    </label>

                    <select
                        name="categoria"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Selecione uma categoria
                        </option>

                        <option value="Esportes">
                            Esportes
                        </option>

                        <option value="Eventos">
                            Eventos
                        </option>

                        <option value="Competições">
                            Competições
                        </option>

                        <option value="Campus">
                            Campus
                        </option>

                    </select>

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


                <button
                    type="submit"
                    name="cadastrar"
                    class="btn btn-success"
                >
                    Publicar notícia
                </button>

            </form>

        </div>

    </div>


    <h2 class="mb-4">
        Notícias cadastradas
    </h2>


    <div class="table-responsive">

        <table class="table table-bordered table-striped align-middle">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Imagem</th>

                    <th>Título</th>

                    <th>Categoria</th>

                    <th>Autor</th>

                    <th>Data</th>

                    <th>Ações</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($noticias && $noticias->num_rows > 0): ?>

                    <?php while ($noticia = $noticias->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= (int)($noticia['id_noticia'] ?? 0) ?>
                            </td>


                            <td>

                                <?php if (!empty($noticia['imagem'])): ?>

                                    <img
                                        src="../<?= htmlspecialchars($noticia['imagem']) ?>"
                                        alt="Imagem da notícia"
                                        width="80"
                                        height="60"
                                        style="object-fit: cover;"
                                    >

                                <?php else: ?>

                                    Sem imagem

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars($noticia['titulo'] ?? '') ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($noticia['categoria'] ?? '') ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($noticia['autor'] ?? 'Administrador') ?>
                            </td>


                            <td>

                                <?php

                                if (!empty($noticia['data_noticia'])) {

                                    $data = strtotime($noticia['data_noticia']);

                                    echo $data
                                        ? date("d/m/Y H:i", $data)
                                        : htmlspecialchars($noticia['data_noticia']);

                                }

                                ?>

                            </td>


                            <td>

                                <a
                                    href="editar_noticia.php?id=<?= (int)($noticia['id_noticia'] ?? 0) ?>"
                                    class="btn btn-warning btn-sm"
                                >
                                    Editar
                                </a>


                                <a
                                    href="noticiascrud.php?excluir=<?= (int)($noticia['id_noticia'] ?? 0) ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir esta notícia?')"
                                >
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="7"
                            class="text-center"
                        >
                            Nenhuma notícia cadastrada.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <div class="mt-4">

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Voltar ao painel
        </a>

    </div>

</div>

</body>

</html>