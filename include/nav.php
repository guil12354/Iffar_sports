<?php

/*
|--------------------------------------------------------------------------
| Descobre qual página está ativa
|--------------------------------------------------------------------------
*/

$paginaAtual = $_GET['id'] ?? 'home';

?>

<header>

    <!-- ============================================================
         NAVBAR DESKTOP
         ============================================================ -->

    <nav class="navbar-principal d-none d-md-flex">

        <div class="nav-logo">

            <a href="index.php">
                <img
                    src="img/logo.png"
                    alt="IFFar Sports"
                >
            </a>

        </div>


        <div class="nav-links">

            <a
                href="index.php"
                class="nav-item-principal <?= $paginaAtual === 'home' ? 'ativo' : '' ?>"
            >
                <i class="bi bi-house-fill"></i>
                <span>Início</span>
            </a>


            <a
                href="index.php?id=noticias"
                class="nav-item-principal <?= $paginaAtual === 'noticias' ? 'ativo' : '' ?>"
            >
                <i class="bi bi-megaphone-fill"></i>
                <span>Notícias</span>
            </a>


            <a
                href="index.php?id=esportes"
                class="nav-item-principal <?= $paginaAtual === 'esportes' ? 'ativo' : '' ?>"
            >
                <i class="bi bi-person-running"></i>
                <span>Esportes</span>
            </a>

       
        </div>

    </nav>


    <!-- ============================================================
         CABEÇALHO MOBILE
         ============================================================ -->

    <div class="cabecalho-mobile d-md-none">

        <a href="index.php">

            <img
                src="img/logo.png"
                alt="IFFar Sports"
            >

        </a>

    </div>

</header>


<!-- ==============================================================
     NAVEGAÇÃO MOBILE INFERIOR
     ============================================================== -->

<nav class="nav-mobile d-md-none">

    <a
        href="index.php"
        class="<?= $paginaAtual === 'home' ? 'ativo' : '' ?>"
    >
        <i class="bi bi-house-fill"></i>
        <span>Início</span>
    </a>


    <a
        href="index.php?id=noticias"
        class="<?= $paginaAtual === 'noticias' ? 'ativo' : '' ?>"
    >
        <i class="bi bi-megaphone-fill"></i>
        <span>Notícias</span>
    </a>


    <a
        href="index.php?id=esportes"
        class="<?= $paginaAtual === 'esportes' ? 'ativo' : '' ?>"
    >
        <i class="bi bi-person-running"></i>
        <span>Esportes</span>
    </a>


    <a
        href="index.php?id=login"
        class="<?= $paginaAtual === 'login' ? 'ativo' : '' ?>"
    >
        <i class="bi bi-person-circle"></i>
        <span>Entrar</span>
    </a>

</nav>