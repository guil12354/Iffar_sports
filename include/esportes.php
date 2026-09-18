<?php

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

$esportes = $conexao->query("
    SELECT *
    FROM esportes
    ORDER BY nome ASC
");

?>

<section class="pagina-esportes">

    <div class="container py-5">

        <div class="cabecalho-secao mb-4">

            <div>

                <span class="mini-titulo">
                    IFFAR SPORTS
                </span>

                <h1 class="titulo-pagina">
                    Esportes
                </h1>

                <p class="subtitulo-pagina">
                    Conheça as modalidades esportivas oferecidas pelo IFFar.
                </p>

            </div>

        </div>

        <?php if ($esportes && $esportes->num_rows > 0): ?>

            <div class="row g-4">

                <?php while ($esporte = $esportes->fetch_assoc()): ?>

                    <?php

                    $imagem = !empty($esporte['imagem'])
                        ? $esporte['imagem']
                        : 'img/logo.png';

                    ?>

                    <div class="col-12 col-sm-6 col-lg-4">

                        <a
                            href="index.php?id=esporte&esporte=<?= (int)$esporte['id_esporte'] ?>"
                            class="card-esporte"
                        >

                            <img
                                src="<?= htmlspecialchars($imagem) ?>"
                                alt="<?= htmlspecialchars($esporte['nome']) ?>"
                            >

                            <div class="overlay-esporte"></div>

                            <div class="nome-esporte">

                                <span>
                                    <?= htmlspecialchars($esporte['nome']) ?>
                                </span>

                                <i class="bi bi-arrow-up-right"></i>

                            </div>

                        </a>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="estado-vazio">

                <i class="bi bi-trophy"></i>

                <h2>
                    Nenhuma modalidade cadastrada
                </h2>

                <p>
                    As modalidades esportivas aparecerão aqui assim que
                    forem cadastradas.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
