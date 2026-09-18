<?php

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");

if ($conexao->connect_error) {
    die("Erro na conexão com o banco.");
}

$conexao->set_charset("utf8mb4");

$sql = "SELECT * FROM esportes ORDER BY id_esporte DESC";
$resultado = $conexao->query($sql);

/*
|--------------------------------------------------------------------------
| Controle da página carregada
|--------------------------------------------------------------------------
| Exemplo:
| index.php              -> include/home.php
| index.php?id=noticias  -> include/noticias.php
| index.php?id=esportes  -> include/esportes.php
|
*/

$id = $_GET['id'] ?? null;

?>
<!doctype html>

<html lang="pt-BR">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>IFFar Sports</title>

    <!-- Bootstrap 5.3.8 - CDN oficial indicado pelo Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Estilos próprios -->
    <link
        rel="stylesheet"
        href="css/estilo.css"
    >

</head>

<body>

    <?php include("include/nav.php"); ?>

    <main class="conteudo-principal">

        <?php

        /*
        |--------------------------------------------------------------------------
        | Inclusão do conteúdo central
        |--------------------------------------------------------------------------
        */

        if ($id === null || $id === '') {

            include("include/home.php");

        } else {

            // Permite apenas caracteres seguros no nome do arquivo
            $idSeguro = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);

            $arquivo = "include/" . $idSeguro . ".php";

            if (file_exists($arquivo)) {

                include($arquivo);

            } else {

                echo '
                    <section class="container py-5">
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Página não encontrada.
                        </div>
                    </section>
                ';

            }

        }

        ?>

    </main>

    <?php include("include/foot.php"); ?>


    <!-- Bootstrap JS Bundle -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"
    ></script>

</body>

</html>