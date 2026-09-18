<?php

/*
|--------------------------------------------------------------------------
| ESPORTE
|--------------------------------------------------------------------------
| Página individual de uma modalidade.
|
| Exemplo:
| index.php?id=esporte&esporte=2
|--------------------------------------------------------------------------
*/


$idEsporte = filter_input(
    INPUT_GET,
    'esporte',
    FILTER_VALIDATE_INT
);


/*
|--------------------------------------------------------------------------
| Verifica se o parâmetro é válido
|--------------------------------------------------------------------------
*/

if (!$idEsporte) {
?>

    <section class="container py-5">

        <div class="alert alert-warning text-center">

            <i class="bi bi-exclamation-triangle me-2"></i>

            Esporte não informado.

        </div>

    </section>

<?php

    return;
}


/*
|--------------------------------------------------------------------------
| Consulta a modalidade
|--------------------------------------------------------------------------
*/

$sqlEsporte = "
    SELECT *
    FROM esportes
    WHERE id_esporte = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sqlEsporte);

$stmt->bind_param(
    "i",
    $idEsporte
);

$stmt->execute();

$resultadoEsporte = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Modalidade não encontrada
|--------------------------------------------------------------------------
*/

if ($resultadoEsporte->num_rows === 0) {
?>

    <section class="container py-5">

        <div class="estado-vazio">

            <i class="bi bi-exclamation-circle"></i>

            <h2>
                Esporte não encontrado
            </h2>

            <p>
                A modalidade solicitada não existe ou não está mais disponível.
            </p>

            <a
                href="index.php?id=esportes"
                class="btn btn-voltar"
            >
                <i class="bi bi-arrow-left"></i>
                Voltar aos esportes
            </a>

        </div>

    </section>

<?php

    return;
}


$esporte = $resultadoEsporte->fetch_assoc();

?>


<section class="pagina-esporte">

    <div class="container py-4 py-md-5">


        <!-- ========================================================
             BANNER
             ======================================================== -->

        <div class="banner-esporte">

            <img
                src="<?= htmlspecialchars($esporte['imagem']) ?>"
                alt="<?= htmlspecialchars($esporte['nome']) ?>"
            >

            <div class="banner-esporte-overlay"></div>


            <div class="banner-esporte-conteudo">

                <a
                    href="index.php?id=esportes"
                    class="voltar-esportes"
                >
                    <i class="bi bi-arrow-left"></i>
                    Esportes
                </a>

                <h1>
                    <?= htmlspecialchars($esporte['nome']) ?>
                </h1>

            </div>

        </div>


        <!-- ========================================================
             INFORMAÇÕES
             ======================================================== -->

        <div class="row g-4 mt-2">


            <!-- SOBRE -->

            <div class="col-12 col-lg-3">

                <article class="card-info-esporte">

                    <h2>
                        <span></span>
                        Sobre o <?= htmlspecialchars($esporte['nome']) ?>
                    </h2>

                    <div class="texto-sobre-esporte">

                        <?= nl2br(
                            htmlspecialchars(
                                $esporte['descricao']
                            )
                        ) ?>

                    </div>

                </article>

            </div>



            <!-- CONQUISTAS -->

            <div class="col-12 col-lg-3">

                <article class="card-info-esporte">

                    <h2>
                        <span></span>
                        Conquistas
                    </h2>


                    <?php if (!empty($esporte['conquistas'])): ?>

                        <div class="conquista-esporte">

                            <i class="bi bi-trophy-fill"></i>

                            <div>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $esporte['conquistas']
                                    )
                                ) ?>

                            </div>

                        </div>


                    <?php else: ?>

                        <p class="sem-informacao">
                            Nenhuma conquista cadastrada.
                        </p>

                    <?php endif; ?>

                </article>

            </div>



            <!-- TREINOS FEMININO -->

            <div class="col-12 col-md-6 col-lg-3">

                <article class="card-info-esporte">

                    <h2>
                        <span></span>
                        Treinos - Feminino
                    </h2>


                    <div class="lista-info-treino">


                        <div class="item-info-treino">

                            <i class="bi bi-calendar3"></i>

                            <div>

                                <strong>Dias</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['feminino_dias']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                        <div class="item-info-treino">

                            <i class="bi bi-clock"></i>

                            <div>

                                <strong>Horário</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['feminino_horario']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                        <div class="item-info-treino">

                            <i class="bi bi-geo-alt-fill"></i>

                            <div>

                                <strong>Local</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['feminino_local']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                        <div class="item-info-treino">

                            <i class="bi bi-person-circle"></i>

                            <div>

                                <strong>Professor responsável</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['feminino_professor']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                    </div>

                </article>

            </div>



            <!-- TREINOS MASCULINO -->

            <div class="col-12 col-md-6 col-lg-3">

                <article class="card-info-esporte">

                    <h2>
                        <span></span>
                        Treinos - Masculino
                    </h2>


                    <div class="lista-info-treino">


                        <div class="item-info-treino">

                            <i class="bi bi-calendar3"></i>

                            <div>

                                <strong>Dias</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['masculino_dias']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                        <div class="item-info-treino">

                            <i class="bi bi-clock"></i>

                            <div>

                                <strong>Horário</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['masculino_horario']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                        <div class="item-info-treino">

                            <i class="bi bi-geo-alt-fill"></i>

                            <div>

                                <strong>Local</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['masculino_local']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                        <div class="item-info-treino">

                            <i class="bi bi-person-circle"></i>

                            <div>

                                <strong>Professor responsável</strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $esporte['masculino_professor']
                                    ) ?>
                                </span>

                            </div>

                        </div>


                    </div>

                </article>

            </div>

        </div>

    </div>

</section>