<?php
$paginaAtual = $paginaAtual ?? 'dashboard';
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-mark"><i class="bi bi-trophy-fill"></i></div>
    <div><strong>IFFar Sports</strong><small>Administração</small></div>
    <button class="btn-close-sidebar d-lg-none" id="closeSidebar" aria-label="Fechar menu"><i class="bi bi-x-lg"></i></button>
  </div>
  <nav class="sidebar-nav">
    <a href="index.php" class="<?= $paginaAtual==='dashboard'?'active':'' ?>"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <div class="nav-caption">Conteúdo</div>
    <a href="noticias.php" class="<?= in_array($paginaAtual,['noticias','noticia-form'])?'active':'' ?>"><i class="bi bi-newspaper"></i><span>Notícias</span></a>
    <a href="esportes.php" class="<?= in_array($paginaAtual,['esportes','esporte-form'])?'active':'' ?>"><i class="bi bi-person-running"></i><span>Esportes</span></a>
  </nav>
  <div class="sidebar-footer"><a href="../index.php"><i class="bi bi-box-arrow-up-right"></i><span>Ver portal</span></a></div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<header class="topbar">
  <button class="menu-toggle d-lg-none" id="openSidebar" aria-label="Abrir menu"><i class="bi bi-list"></i></button>
  <div><strong><?= htmlspecialchars($tituloPagina ?? 'Painel administrativo') ?></strong><small><?= htmlspecialchars($subtituloPagina ?? 'Gerencie o conteúdo do portal') ?></small></div>
  <div class="topbar-user"><div class="user-text"><strong>Administrador</strong><span>IFFar Sports</span></div><div class="avatar"><i class="bi bi-person-fill"></i></div></div>
</header>
