<?php

$idEquipe = filter_input(INPUT_GET, 'equipe', FILTER_VALIDATE_INT);

if (!$idEquipe) {
    echo '<section class="container py-5">
        <div class="alert alert-warning">
            Equipe não informada.
        </div>
    </section>';
    return;
}

// Buscar informações da equipe
$sql = "
    SELECT
        e.*,
        s.nome AS nome_esporte
    FROM equipes e
    INNER JOIN esportes s
        ON s.id_esporte = e.id_esporte
    WHERE e.id_equipe = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idEquipe);
$stmt->execute();

$resultadoEquipe = $stmt->get_result();
$equipe = $resultadoEquipe->fetch_assoc();
$stmt->close();

if (!$equipe) {
    echo '<section class="container py-5">
        <div class="alert alert-warning">
            Equipe não encontrada.
        </div>
    </section>';
    return;
}

// Buscar atletas da equipe
$sqlAtletas = "
    SELECT
        nome,
        numero,
        posicao
    FROM atletas_equipe
    WHERE id_equipe = ?
    ORDER BY numero ASC, nome ASC
";

$stmt = $conexao->prepare($sqlAtletas);
$stmt->bind_param("i", $idEquipe);
$stmt->execute();

$resultadoAtletas = $stmt->get_result();
$atletas = [];
while ($linha = $resultadoAtletas->fetch_assoc()) {
    $atletas[] = $linha;
}
$stmt->close();

// Proteger textos exibidos
function escaparEquipe($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Preparar foto da equipe
$foto = trim((string) ($equipe['foto'] ?? ''));

?>

<section class="container py-4 py-md-5">

    <!-- VOLTAR PARA O ESPORTE -->
    <a
        href="index.php?id=esporte&esporte=<?= (int) $equipe['id_esporte'] ?>"
        class="text-decoration-none"
        style="color: #004b2c;"
    >
        <i class="bi bi-arrow-left"></i>
        Voltar para <?= escaparEquipe($equipe['nome_esporte']) ?>
    </a>

    <!-- CABEÇALHO DA EQUIPE -->
    <div class="mt-4 mb-4">

        <span
            class="text-uppercase fw-bold"
            style="color: #e79b00; font-size: 12px;"
        >
            IFFAR SPORTS · EQUIPES
        </span>

        <h1
            class="fw-bold mt-2"
            style="color: #003b23;"
        >
            <?= escaparEquipe($equipe['nome']) ?>
        </h1>

        <p class="text-muted">
            <?= escaparEquipe($equipe['nome_esporte']) ?>
            · <?= (int) $equipe['ano'] ?>
            · <?= escaparEquipe(ucfirst($equipe['categoria'] ?? '')) ?>
        </p>

    </div>

    <!-- FOTO DA EQUIPE -->
    <?php if ($foto != ''): ?>

        <img
            src="<?= escaparEquipe($foto) ?>"
            alt="Foto oficial da equipe"
            class="w-100 rounded-4 mb-4"
            style="max-height: 420px; object-fit: cover;"
        >

    <?php endif; ?>

    <!-- INFORMAÇÕES DA EQUIPE -->
    <div class="row g-4 mb-4">

        <!-- TEMPORADA -->
        <div class="col-md-6">

            <div class="bg-white border rounded-4 p-4 h-100">

                <h2
                    class="h5 fw-bold"
                    style="color: #004b2c;"
                >
                    <i class="bi bi-calendar-event me-2"></i>
                    Temporada
                </h2>

                <p class="mb-0">
                    <?= (int) $equipe['ano'] ?>
                </p>

            </div>

        </div>

        <!-- TÉCNICO RESPONSÁVEL -->
        <div class="col-md-6">

            <div class="bg-white border rounded-4 p-4 h-100">

                <h2
                    class="h5 fw-bold"
                    style="color: #004b2c;"
                >
                    <i class="bi bi-person-badge me-2"></i>
                    Técnico responsável
                </h2>

                <p class="mb-0">
                    <?= escaparEquipe(
                        !empty($equipe['tecnico'])
                            ? $equipe['tecnico']
                            : 'Não informado'
                    ) ?>
                </p>

            </div>

        </div>

    </div>

    <!-- ELENCO DA EQUIPE -->
    <section class="bg-white border rounded-4 p-4 mb-4">

        <h2
            class="h4 fw-bold mb-4"
            style="color: #003b23;"
        >
            <i class="bi bi-people-fill me-2"></i>
            Elenco
        </h2>

        <?php if (!empty($atletas)): ?>

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
                                <!-- NÚMERO -->
                                <td>
                                    <?= escaparEquipe($atleta['numero'] ?? '-') ?>
                                </td>

                                <!-- NOME -->
                                <td class="fw-semibold">
                                    <?= escaparEquipe($atleta['nome']) ?>
                                </td>

                                <!-- POSIÇÃO -->
                                <td>
                                    <?= escaparEquipe(
                                        !empty($atleta['posicao'])
                                            ? $atleta['posicao']
                                            : 'Não informada'
                                    ) ?>
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

</section>