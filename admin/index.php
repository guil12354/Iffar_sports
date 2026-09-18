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

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Administrativo - IFFar Sports</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

    <div class="container py-5">

        <div class="text-center mb-5">

            <h1>Painel administrativo</h1>

            <p>
                Olá,
                <?= htmlspecialchars($_SESSION['nome'] ?? 'Administrador') ?>
            </p>

        </div>

        <div class="row justify-content-center g-4">

            <div class="col-md-5">

                <div class="card p-4 text-center">

                    <h3>Notícias</h3>

                    <p>
                        Gerencie as notícias do IFFar Sports.
                    </p>

                    <a
                        href="noticiascrud.php"
                        class="btn btn-success"
                    >
                        Gerenciar notícias
                    </a>

                </div>

            </div>

            <div class="col-md-5">

                <div class="card p-4 text-center">

                    <h3>Esportes</h3>

                    <p>
                        Gerencie os esportes cadastrados.
                    </p>

                    <a
                        href="esportescrud.php"
                        class="btn btn-success"
                    >
                        Gerenciar esportes
                    </a>

                </div>

            </div>

        </div>

        <div class="text-center mt-5">

            <a
                href="../index.php"
                class="btn btn-secondary me-2"
            >
                Voltar ao site
            </a>

            <a
                href="../logout.php"
                class="btn btn-danger"
            >
                Sair
            </a>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>