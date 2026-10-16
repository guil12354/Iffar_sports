
<?php
$idEquipe = filter_input(INPUT_GET, 'equipe', FILTER_VALIDATE_INT);

if (!$idEquipe) {
    echo '<section class="container py-5">
        <div class="alert alert-warning">Equipe não informada.</div>
    </section>';
    return;
}

$sql = "SELECT e.*, s.nome AS nome_esporte
        FROM equipes e
        INNER JOIN esportes s ON s.id_esporte = e.id_esporte
        WHERE e.id_equipe = ?
        LIMIT 1";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idEquipe);
$stmt->execute();

$equipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$equipe) {
    echo '<section class="container py-5">
        <div class="alert alert-warning">Equipe não encontrada.</div>
    </section>';
    return;
}

$stmt = $conexao->prepare(
    "SELECT nome, numero, posicao
     FROM atletas_equipe
     WHERE id_equipe = ?
     ORDER BY numero ASC, nome ASC"
);
$stmt->bind_param("i", $idEquipe);
$stmt->execute();
$atletas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function escaparEquipe($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$foto = trim((string)($equipe['foto'] ?? ''));
$foto = str_replace('\\', '/', $foto);
$foto = ltrim($foto, '/');

$fotoValida = $foto !== ''
    && !str_contains($foto, '..')
    && !preg_match('~^(https?:)?//~i', $foto);

?>

<section class="container py-4 py-md-5">

    <a href="index.php?id=esporte&esporte=<?= (int)$equipe['id_esporte'] ?>"
       class="text-decoration-none"
       style="color:#004b2c">
        <i class="bi bi-arrow-left"></i>
        Voltar para <?= escaparEquipe($equipe['nome_esporte']) ?>
    </a>

    <div class="mt-4 mb-4">
        <span class="text-uppercase fw-bold"
              style="color:#e79b00;font-size:12px">
            IFFAR SPORTS · HISTÓRICO DE EQUIPES
        </span>

        <h1 class="fw-bold mt-2" style="color:#003b23">
            <?= escaparEquipe($equipe['nome']) ?>
        </h1>

        <p class="text-muted">
            <?= escaparEquipe($equipe['nome_esporte']) ?>
            · <?= (int)$equipe['ano'] ?>
            · <?= escaparEquipe(ucfirst($equipe['categoria'])) ?>
        </p>
    </div>

    <?php if ($fotoValida): ?>
        <img
            src="<?= escaparEquipe($foto) ?>"
            alt="Foto oficial da equipe"
            class="w-100 rounded-4 mb-4"
            style="max-height:420px;object-fit:cover"
        >
    <?php endif; ?>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="bg-white border rounded-4 p-4 h-100">
                <h2 class="h5 fw-bold" style="color:#004b2c">
                    <i class="bi bi-calendar-event me-2"></i>
                    Temporada
                </h2>
                <p class="mb-0"><?= (int)$equipe['ano'] ?></p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-white border rounded-4 p-4 h-100">
                <h2 class="h5 fw-bold" style="color:#004b2c">
                    <i class="bi bi-person-badge me-2"></i>
                    Técnico responsável
                </h2>
                <p class="mb-0">
                    <?= escaparEquipe($equipe['tecnico'] ?: 'Não informado') ?>
                </p>
            </div>
        </div>

    </div>

    <section class="bg-white border rounded-4 p-4 mb-4">

        <h2 class="h4 fw-bold mb-4" style="color:#003b23">
            <i class="bi bi-people-fill me-2"></i>
            Elenco
        </h2>

        <?php if ($atletas): ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Atleta</th>
                            <th>Posição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atletas as $atleta): ?>
                            <tr>
                                <td>
                                    <?= escaparEquipe($atleta['numero'] ?? '-') ?>
                                </td>
                                <td class="fw-semibold">
                                    <?= escaparEquipe($atleta['nome']) ?>
                                </td>
                                <td>
                                    <?= escaparEquipe($atleta['posicao'] ?: 'Não informada') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <p class="text-muted mb-0">
                Nenhum atleta cadastrado nesta equipe.
            </p>
        <?php endif; ?>

    </section>

    <section class="bg-white border rounded-4 p-4 mb-5">

        <h2 class="h4 fw-bold mb-3" style="color:#003b23">
            <i class="bi bi-journal-text me-2"></i>
            Histórico da temporada
        </h2>

        <?php
        // Exibe apenas texto simples por segurança.
        // A formatação HTML do Quill exige sanitização no servidor.
        $historico = trim((string)($equipe['historico'] ?? ''));
        ?>

        <?php if ($historico !== ''): ?>
            <div style="line-height:1.8">
                <?= nl2br(escaparEquipe(strip_tags($historico))) ?>
            </div>
        <?php else: ?>
            <p class="text-muted">
                Nenhum histórico cadastrado.
            </p>
        <?php endif; ?>

    </section>

</section>
