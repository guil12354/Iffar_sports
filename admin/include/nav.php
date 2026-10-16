<?php

$paginaAtual = $paginaAtual ?? 'dashboard';

?>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">

        <div class="brand-mark">
            <i class="bi bi-trophy-fill"></i>
        </div>

        <div>
            <strong>IFFar Sports</strong>
            <small>Administração</small>
        </div>

        <button
            class="btn-close-sidebar d-lg-none"
            id="closeSidebar"
            type="button"
        >
            <i class="bi bi-x-lg"></i>
        </button>

    </div>

    <nav class="sidebar-nav">

        <a
            href="index.php"
            class="<?= $paginaAtual === 'dashboard' ? 'active' : '' ?>"
        >
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-caption">
            Conteúdo
        </div>

        <a
            href="noticiascrud.php"
            class="<?= $paginaAtual === 'noticias' ? 'active' : '' ?>"
        >
            <i class="bi bi-newspaper"></i>
            <span>Notícias</span>
        </a>

        <a
            href="esportescrud.php"
            class="<?= $paginaAtual === 'esportes' ? 'active' : '' ?>"
        >
            <i class="bi bi-person-running"></i>
            <span>Esportes</span>
        </a>

    </nav>

    <div class="sidebar-footer">

        <a href="../index.php">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Ver portal</span>
        </a>

        <a href="../logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>

    </div>

</aside>


<div
    class="sidebar-backdrop"
    id="sidebarBackdrop"
></div>


<header class="topbar">

    <button
        class="menu-toggle d-lg-none"
        id="openSidebar"
        type="button"
    >
        <i class="bi bi-list"></i>
    </button>

    <div>

        <strong>
            <?= htmlspecialchars($tituloPagina ?? 'Painel administrativo') ?>
        </strong>

        <small>
            <?= htmlspecialchars($subtituloPagina ?? 'Gerencie o conteúdo do portal') ?>
        </small>

    </div>

    <div class="topbar-user">

        <div class="user-text">

            <strong>
                <?= htmlspecialchars($_SESSION['nome'] ?? 'Administrador') ?>
            </strong>

            <span>IFFar Sports</span>

        </div>

        <div class="avatar">
            <i class="bi bi-person-fill"></i>
        </div>

    </div>

</header>