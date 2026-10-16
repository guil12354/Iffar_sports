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


<!-- HISTÓRICO DE EQUIPES -->
<?php
$equipesDoEsporte = [];

$consultaEquipes = $conexao->prepare(
    "SELECT
        id_equipe,
        nome,
        ano,
        categoria,
        tecnico,
        foto AS foto_equipe
     FROM equipes
     WHERE id_esporte = ?
     ORDER BY ano DESC, nome ASC"
);

if ($consultaEquipes) {
    $consultaEquipes->bind_param('i', $idEsporte);
    $consultaEquipes->execute();

    $resultadoEquipes = $consultaEquipes->get_result();
    $equipesDoEsporte = $resultadoEquipes->fetch_all(MYSQLI_ASSOC);

    $consultaEquipes->close();
}
?>

        <section class="historico-equipes mt-5" aria-labelledby="titulo-equipes">
            <div class="mb-4">
                <span class="text-uppercase fw-bold" style="color:#ed9700;font-size:.75rem;letter-spacing:.08em">IFFar Sports</span>
                <h2 id="titulo-equipes" style="color:#004b2c;font-weight:750;margin-top:5px">
                    <i class="bi bi-people-fill me-2"></i>Histórico de Equipes
                </h2>
                <p class="text-muted">Conheça os elencos que representaram esta modalidade ao longo dos anos.</p>
            </div>
            <?php if (!$equipesDoEsporte): ?>
                <div class="alert alert-light border">Nenhuma equipe cadastrada para esta modalidade.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($equipesDoEsporte as $equipe): ?>
                        <?php
                        $foto = str_replace('\\', '/', trim((string)($equipe['foto_equipe'] ?? '')));
                        $foto = ltrim($foto, '/');
                        $fotoSegura = $foto !== '' && str_starts_with($foto, 'img/equipes/')
                            && !str_contains($foto, '..');
                        ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="historico-equipe-card h-100">
                                <?php if ($fotoSegura): ?>
                                    <img class="historico-equipe-foto"
                                         src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                                         alt="Foto de <?= htmlspecialchars($equipe['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php else: ?>
                                    <div class="historico-equipe-placeholder"><i class="bi bi-people-fill"></i></div>
                                <?php endif; ?>
                                <div class="p-3 p-md-4">
                                    <span class="historico-equipe-ano"><?= (int)$equipe['ano'] ?></span>
                                    <h3 class="h5 mt-3 mb-2" style="color:#003b23;font-weight:700">
                                        <?= htmlspecialchars($equipe['nome'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-person-badge me-1"></i>
                                        <?= htmlspecialchars(ucfirst((string)$equipe['categoria']), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <?php if (!empty($equipe['tecnico'])): ?>
                                        <p class="text-muted mb-3">Técnico: <?= htmlspecialchars($equipe['tecnico'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                    <a class="btn btn-success" style="background:#004b2c;border-color:#004b2c"
                                       href="index.php?id=equipe&equipe=<?= (int)$equipe['id_equipe'] ?>">
                                        Ver elenco <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>



</section>

<style>
.historico-equipe-card { background:#fff; border:1px solid #dfe8e2; border-radius:14px; overflow:hidden; box-shadow:0 6px 22px rgba(0,0,0,.05); }
.historico-equipe-foto { display:block; width:100%; height:210px; object-fit:cover; }
.historico-equipe-placeholder { height:210px; background:#e7efe9; color:#004b2c; display:flex; align-items:center; justify-content:center; font-size:3rem; }
.historico-equipe-ano { display:inline-block; padding:4px 10px; border-radius:6px; background:#e7f3e9; color:#004b2c; font-size:.8rem; font-weight:700; }
</style>
