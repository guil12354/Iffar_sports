<?php

$idEsporte = filter_input(INPUT_GET, 'esporte', FILTER_VALIDATE_INT);

if (!$idEsporte) {
    echo '
        <section class="container py-5">
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Esporte não informado.
            </div>
        </section>
    ';
    return;
}

// Buscar dados do esporte
$sql = "SELECT * FROM esportes WHERE id_esporte = ? LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idEsporte);
$stmt->execute();

$resultadoEsporte = $stmt->get_result();
$esporte = $resultadoEsporte->fetch_assoc();
$stmt->close();

if (!$esporte) {
    echo '
        <section class="container py-5">
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Esporte não encontrado.
            </div>
        </section>
    ';
    return;
}

// Buscar equipes deste esporte
$sqlEquipes = "
    SELECT
        e.id_equipe,
        e.nome AS nome_equipe,
        e.ano,
        e.categoria,
        e.foto AS foto_equipe,
        e.tecnico
    FROM equipes e
    WHERE e.id_esporte = ?
    ORDER BY e.ano DESC, e.categoria ASC, e.nome ASC
";

$stmtEquipes = $conexao->prepare($sqlEquipes);
$stmtEquipes->bind_param("i", $idEsporte);
$stmtEquipes->execute();

$resultadoEquipes = $stmtEquipes->get_result();
$equipesDoEsporte = [];
while ($linhaEquipe = $resultadoEquipes->fetch_assoc()) {
    $equipesDoEsporte[] = $linhaEquipe;
}
$stmtEquipes->close();

// Função auxiliar para escapar textos
function escaparTexto($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Verificar horários disponíveis
$temFeminino =
    !empty($esporte['feminino_dias']) ||
    !empty($esporte['feminino_horario']) ||
    !empty($esporte['feminino_local']) ||
    !empty($esporte['feminino_professor']);

$temMasculino =
    !empty($esporte['masculino_dias']) ||
    !empty($esporte['masculino_horario']) ||
    !empty($esporte['masculino_local']) ||
    !empty($esporte['masculino_professor']);

?>

<section class="esporte-detalhe-secao">
    <div class="container">

        <!-- CABEÇALHO -->
        <header class="esporte-detalhe-topo">
            <span class="esporte-tag">
                IFFAR SPORTS · MODALIDADES
            </span>

            <h1 class="esporte-detalhe-titulo">
                <?= escaparTexto($esporte['nome']) ?>
            </h1>

            <p class="esporte-detalhe-sub">
                Informações da modalidade, horários de treino e equipes cadastradas no campus.
            </p>
        </header>

        <!-- IMAGEM COM BOTÃO DE LUPA -->
        <?php if (!empty($esporte['imagem'])): ?>
            <div class="esporte-detalhe-capa mb-4 position-relative">
                <img
                    src="<?= escaparTexto($esporte['imagem']) ?>"
                    alt="<?= escaparTexto($esporte['nome']) ?>"
                    onclick="abrirModalImagemEsporte(this.src)"
                    style="cursor: zoom-in;"
                    onerror="this.parentElement.style.display='none'"
                >
                <button
                    type="button"
                    class="btn-zoom-esporte"
                    onclick="abrirModalImagemEsporte('<?= escaparTexto($esporte['imagem']) ?>')"
                    title="Ampliar imagem"
                >
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <!-- MODAL PARA VER IMAGEM INTEIRA -->
            <div class="modal fade" id="modalImagemEsporte" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body p-0 text-center position-relative">
                            <button
                                type="button"
                                class="btn-close btn-close-white position-absolute top-0 end-0 m-3 bg-dark p-2 rounded-circle"
                                data-bs-dismiss="modal"
                                aria-label="Fechar"
                            ></button>
                            <img
                                id="imagemModalEsporteSrc"
                                src=""
                                alt="<?= escaparTexto($esporte['nome']) ?>"
                                class="img-fluid rounded-4 shadow-lg"
                                style="max-height: 85vh; object-fit: contain;"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function abrirModalImagemEsporte(src) {
                document.getElementById('imagemModalEsporteSrc').src = src;
                var modal = new bootstrap.Modal(document.getElementById('modalImagemEsporte'));
                modal.show();
            }
            </script>
        <?php endif; ?>

        <!-- DESCRIÇÃO -->
        <div class="esporte-detalhe-bloco">
            <h2>
                <i class="bi bi-info-circle-fill"></i>
                Sobre a modalidade
            </h2>

            <?php if (!empty($esporte['descricao'])): ?>
                <p class="esporte-detalhe-texto">
                    <?= nl2br(escaparTexto($esporte['descricao'])) ?>
                </p>
            <?php else: ?>
                <p class="esporte-detalhe-vazio">
                    Nenhuma descrição cadastrada para este esporte.
                </p>
            <?php endif; ?>
        </div>

        <!-- TREINOS -->
        <div class="esporte-detalhe-bloco">
            <h2>
                <i class="bi bi-calendar-week-fill"></i>
                Horários de treino
            </h2>

            <?php if ($temFeminino || $temMasculino): ?>
                <div class="row g-4 mt-1">

                    <!-- FEMININO -->
                    <?php if ($temFeminino): ?>
                        <div class="col-md-6">
                            <div class="treino-card">
                                <span class="treino-badge feminino">
                                    Treino Feminino
                                </span>

                                <ul class="treino-lista">
                                    <li>
                                        <strong>Dias:</strong>
                                        <?= escaparTexto($esporte['feminino_dias'] ?: 'Não informado') ?>
                                    </li>
                                    <li>
                                        <strong>Horário:</strong>
                                        <?= escaparTexto($esporte['feminino_horario'] ?: 'Não informado') ?>
                                    </li>
                                    <li>
                                        <strong>Local:</strong>
                                        <?= escaparTexto($esporte['feminino_local'] ?: 'Não informado') ?>
                                    </li>
                                    <li>
                                        <strong>Professor:</strong>
                                        <?= escaparTexto($esporte['feminino_professor'] ?: 'Não informado') ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- MASCULINO -->
                    <?php if ($temMasculino): ?>
                        <div class="col-md-6">
                            <div class="treino-card">
                                <span class="treino-badge masculino">
                                    Treino Masculino
                                </span>

                                <ul class="treino-lista">
                                    <li>
                                        <strong>Dias:</strong>
                                        <?= escaparTexto($esporte['masculino_dias'] ?: 'Não informado') ?>
                                    </li>
                                    <li>
                                        <strong>Horário:</strong>
                                        <?= escaparTexto($esporte['masculino_horario'] ?: 'Não informado') ?>
                                    </li>
                                    <li>
                                        <strong>Local:</strong>
                                        <?= escaparTexto($esporte['masculino_local'] ?: 'Não informado') ?>
                                    </li>
                                    <li>
                                        <strong>Professor:</strong>
                                        <?= escaparTexto($esporte['masculino_professor'] ?: 'Não informado') ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php else: ?>
                <p class="esporte-detalhe-vazio">
                    Nenhum horário de treino cadastrado para este esporte.
                </p>
            <?php endif; ?>
        </div>

        <!-- EQUIPES DO ESPORTE -->
        <div class="esporte-detalhe-bloco">
            <h2>
                <i class="bi bi-people-fill"></i>
                Equipes da modalidade
            </h2>

            <?php if (!empty($equipesDoEsporte)): ?>
                <div class="row g-4 mt-1">

                    <?php foreach ($equipesDoEsporte as $equipe): ?>
                        <?php
                        $foto = trim((string) ($equipe['foto_equipe'] ?? ''));
                        ?>

                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border rounded-4 shadow-sm overflow-hidden">

                                <?php if ($foto != ''): ?>
                                    <img
                                        src="<?= escaparTexto($foto) ?>"
                                        alt="Foto da equipe <?= escaparTexto($equipe['nome_equipe']) ?>"
                                        class="w-100"
                                        style="height: 200px; object-fit: cover;"
                                    >
                                <?php else: ?>
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-center text-muted"
                                        style="height: 200px; background: #eef3f0;"
                                    >
                                        <i class="bi bi-people-fill fs-1 mb-2"></i>
                                        <span>Sem foto oficial</span>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            <?= escaparTexto(ucfirst($equipe['categoria'])) ?>
                                        </span>
                                        <small class="text-muted fw-semibold">
                                            Temporada <?= (int) $equipe['ano'] ?>
                                        </small>
                                    </div>

                                    <h3 class="h5 fw-bold mb-2" style="color: #003b23;">
                                        <?= escaparTexto($equipe['nome_equipe']) ?>
                                    </h3>

                                    <p class="text-muted small mb-4">
                                        <strong>Técnico:</strong>
                                        <?= escaparTexto(!empty($equipe['tecnico']) ? $equipe['tecnico'] : 'Não informado') ?>
                                    </p>

                                    <a
                                        href="index.php?id=equipe&equipe=<?= (int) $equipe['id_equipe'] ?>"
                                        class="btn btn-success mt-auto rounded-3"
                                        style="background: #004b2c; border-color: #004b2c;"
                                    >
                                        Ver elenco e detalhes
                                        <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <p class="esporte-detalhe-vazio">
                    Nenhuma equipe cadastrada para este esporte.
                </p>
            <?php endif; ?>
        </div>

    </div>
</section>

<style>
.esporte-detalhe-secao .container {
    max-width: 900px;
}

.esporte-detalhe-capa {
    width: 100%;
    height: 420px;
    border-radius: 16px;
    overflow: hidden;
    background: #eef3f0;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
}

.esporte-detalhe-capa img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 20%;
    display: block;
    transition: transform 0.25s ease;
}

.esporte-detalhe-capa:hover img {
    transform: scale(1.02);
}

.btn-zoom-esporte {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: #0b4f26;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
}

.btn-zoom-esporte:hover {
    background: #146c38;
    transform: scale(1.08);
}

@media (max-width: 768px) {
    .esporte-detalhe-capa {
        height: 250px;
    }
}
</style>