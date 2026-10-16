<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header('Location: ../login.php'); exit; }
if (($_SESSION['nivel'] ?? '') !== 'administrador') { header('Location: ../index.php'); exit; }
$paginaAtual = 'dashboard';
$tituloPagina = 'Dashboard';
$subtituloPagina = 'Visão geral do IFFar Sports';
include __DIR__ . '/include/head.php';
include __DIR__ . '/include/nav.php';
?>
<main class="admin-main">
    <div class="container-fluid p-0">
        <div class="welcome-panel">
            <div>
                <span class="eyebrow">PAINEL ADMINISTRATIVO</span>
                <h1>Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Administrador', ENT_QUOTES, 'UTF-8') ?>! 👋</h1>
                <p>Acompanhe o conteúdo publicado e acesse rapidamente as tarefas mais frequentes.</p>
            </div>
            <div class="quick-actions">
                <a href="noticiascrud.php" class="btn btn-brand"><i class="bi bi-plus-lg"></i> Nova notícia</a>
                <a href="esportescrud.php" class="btn btn-outline-brand"><i class="bi bi-pencil"></i> Gerenciar esportes</a>
                <a href="equipescrud.php" class="btn btn-outline-brand"><i class="bi bi-people-fill"></i> Gerenciar equipes</a>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-newspaper"></i></div><div><strong>Notícias</strong><span>Conteúdos publicados</span><small>Gerenciar notícias</small></div></div></div>
            <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-person-running"></i></div><div><strong>Esportes</strong><span>Modalidades cadastradas</span><small>Gerenciar esportes</small></div></div></div>
            <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-people-fill"></i></div><div><strong>Equipes</strong><span>Elencos e temporadas</span><small>Gerenciar equipes</small></div></div></div>
            <div class="col-sm-6 col-xl-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-person-fill"></i></div><div><strong>Administrador</strong><span>Usuário logado</span><small><?= htmlspecialchars($_SESSION['nome'] ?? 'Administrador', ENT_QUOTES, 'UTF-8') ?></small></div></div></div>
        </div>
        <div class="row g-4">
            <div class="col-xl-8"><section class="panel"><div class="panel-heading"><div><h2>Gerenciamento</h2><p>Acesse rapidamente as principais áreas do sistema.</p></div></div>
                <div class="table-responsive"><table class="table admin-table"><thead><tr><th>Área</th><th>Descrição</th><th>Ação</th></tr></thead><tbody>
                    <tr><td><strong>Notícias</strong></td><td>Criar, editar e excluir notícias.</td><td><a href="noticiascrud.php" class="btn btn-sm btn-brand">Acessar</a></td></tr>
                    <tr><td><strong>Esportes</strong></td><td>Gerenciar as modalidades esportivas.</td><td><a href="esportescrud.php" class="btn btn-sm btn-brand">Acessar</a></td></tr>
                    <tr><td><strong>Equipes</strong></td><td>Cadastrar elencos e preservar o histórico das temporadas.</td><td><a href="equipescrud.php" class="btn btn-sm btn-brand">Acessar</a></td></tr>
                </tbody></table></div>
            </section></div>
            <div class="col-xl-4"><section class="panel h-100"><div class="panel-heading"><div><h2>Acesso rápido</h2><p>Tarefas frequentes</p></div></div>
                <div class="action-list">
                    <a href="noticiascrud.php"><i class="bi bi-file-earmark-plus"></i><div><strong>Gerenciar notícias</strong><span>Criar ou editar notícias</span></div><i class="bi bi-chevron-right"></i></a>
                    <a href="esportescrud.php"><i class="bi bi-person-running"></i><div><strong>Gerenciar esportes</strong><span>Modalidades cadastradas</span></div><i class="bi bi-chevron-right"></i></a>
                    <a href="equipescrud.php"><i class="bi bi-people-fill"></i><div><strong>Gerenciar equipes</strong><span>Elencos e histórico de temporadas</span></div><i class="bi bi-chevron-right"></i></a>
                    <a href="../index.php"><i class="bi bi-box-arrow-up-right"></i><div><strong>Ver portal</strong><span>Voltar para o IFFar Sports</span></div><i class="bi bi-chevron-right"></i></a>
                    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i><div><strong>Sair</strong><span>Encerrar sessão</span></div><i class="bi bi-chevron-right"></i></a>
                </div>
            </section></div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/include/foot.php'; ?>
