<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Permitir acesso somente a administradores
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'administrador') {
    header('Location: ../index.php');
    exit;
}

// Configurações da página
$paginaAtual = 'dashboard';
$tituloPagina = 'Dashboard';
$subtituloPagina = 'Visão geral do IFFar Sports';

// Valores iniciais dos indicadores
$totalNoticias = 0;
$totalEsportes = 0;
$totalEquipes = 0;

// Conexão com o banco de dados
$conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');

if (!$conexao->connect_error) {
    $conexao->set_charset('utf8mb4');

    // Total de notícias cadastradas
    $resultadoNoticias = $conexao->query("SELECT COUNT(*) AS total FROM noticias");
    $linhaNoticias = $resultadoNoticias->fetch_assoc();
    $totalNoticias = $linhaNoticias['total'];

    // Total de esportes cadastrados
    $resultadoEsportes = $conexao->query("SELECT COUNT(*) AS total FROM esportes");
    $linhaEsportes = $resultadoEsportes->fetch_assoc();
    $totalEsportes = $linhaEsportes['total'];

    // Total de equipes cadastradas
    $resultadoEquipes = $conexao->query("SELECT COUNT(*) AS total FROM equipes");
    $linhaEquipes = $resultadoEquipes->fetch_assoc();
    $totalEquipes = $linhaEquipes['total'];

    $conexao->close();
}

include __DIR__ . '/include/head.php';
include __DIR__ . '/include/nav.php';
?>

<main class="admin-main">
    <div class="container-fluid p-0">

        <div class="welcome-panel">
            <div>
                <span class="eyebrow">PAINEL ADMINISTRATIVO</span>
                <h1>
                    Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Administrador') ?>! 👋
                </h1>
                <p>
                    Visão geral dos conteúdos e modalidades esportivas do IFFar Sports.
                </p>
            </div>
        </div>

        <div class="row g-4 mb-4">

            <!-- Notícias -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <div>
                        <strong>Notícias</strong>
                        <h2 class="dashboard-count"><?= $totalNoticias ?></h2>
                        <span>Notícias cadastradas</span>
                    </div>
                </div>
            </div>

            <!-- Esportes -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-dribbble"></i>
                    </div>
                    <div>
                        <strong>Esportes</strong>
                        <h2 class="dashboard-count"><?= $totalEsportes ?></h2>
                        <span>Modalidades cadastradas</span>
                    </div>
                </div>
            </div>

            <!-- Equipes -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <strong>Equipes</strong>
                        <h2 class="dashboard-count"><?= $totalEquipes ?></h2>
                        <span>Equipes cadastradas</span>
                    </div>
                </div>
            </div>

            <!-- Administrador -->
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <strong>Administrador</strong>
                        <h2 class="dashboard-count">Ativo</h2>
                        <span><?= htmlspecialchars($_SESSION['nome'] ?? 'Administrador') ?></span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<?php include __DIR__ . '/include/foot.php'; ?>