<?php

$sql = "SELECT * FROM noticias ORDER BY data_noticia DESC LIMIT 3";

$resultado = $conexao->query($sql);

$noticias = [];

while ($noticia = $resultado->fetch_assoc()) {

    $noticias[] = [
        'data' => date("d/m/Y", strtotime($noticia['data_noticia'])),
        'titulo' => $noticia['titulo'],
        'categoria' => $noticia['categoria'],
        'imagem' => !empty($noticia['imagem']) ? $noticia['imagem'] : 'img/logo.png',
        'link' => 'index.php?id=noticia&noticia=' . $noticia['id_noticia']
    ];

}

?>

<section class="home">

    <div class="container py-5">

        <div class="cabecalho-secao mb-4">

            <div>

                <span class="mini-titulo">
                    IFFAR SPORTS
                </span>

                <h1>
                    Últimas notícias
                </h1>

            </div>

            <a
                href="index.php?id=noticias"
                class="link-ver-todas"
            >
                Ver todas
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>


        <?php if (count($noticias) > 0): ?>

        <div
            id="carouselNoticiasDesktop"
            class="carousel slide carousel-noticias d-none d-md-block"
            data-bs-ride="carousel"
        >

            <div class="carousel-inner">

                <?php for ($slide = 0; $slide < count($noticias); $slide++): ?>

                    <?php
                    $principal = $noticias[$slide];

                    $secundaria1 = $noticias[($slide + 1) % count($noticias)];

                    $secundaria2 = $noticias[($slide + 2) % count($noticias)];
                    ?>

                    <div class="carousel-item <?= $slide === 0 ? 'active' : '' ?>">

                        <div class="row g-4 align-items-stretch">

                            <div class="col-lg-7">

                                <a
                                    href="<?= $principal['link'] ?>"
                                    class="noticia-card noticia-destaque"
                                >

                                    <img
                                        src="<?= htmlspecialchars($principal['imagem']) ?>"
                                        alt="<?= htmlspecialchars($principal['titulo']) ?>"
                                    >

                                    <div class="noticia-overlay">

                                        <span class="noticia-categoria">
                                            <?= htmlspecialchars($principal['categoria']) ?>
                                        </span>

                                        <div class="noticia-data">
                                            <?= $principal['data'] ?>
                                        </div>

                                        <h2>
                                            <?= htmlspecialchars($principal['titulo']) ?>
                                        </h2>

                                    </div>

                                </a>

                            </div>


                            <div class="col-lg-5">

                                <div class="row g-4 h-100">

                                    <div class="col-12">

                                        <a
                                            href="<?= $secundaria1['link'] ?>"
                                            class="noticia-card noticia-secundaria"
                                        >

                                            <img
                                                src="<?= htmlspecialchars($secundaria1['imagem']) ?>"
                                                alt="<?= htmlspecialchars($secundaria1['titulo']) ?>"
                                            >

                                            <div class="noticia-overlay">

                                                <span class="noticia-categoria">
                                                    <?= htmlspecialchars($secundaria1['categoria']) ?>
                                                </span>

                                                <div class="noticia-data">
                                                    <?= $secundaria1['data'] ?>
                                                </div>

                                                <h3>
                                                    <?= htmlspecialchars($secundaria1['titulo']) ?>
                                                </h3>

                                            </div>

                                        </a>

                                    </div>


                                    <div class="col-12">

                                        <a
                                            href="<?= $secundaria2['link'] ?>"
                                            class="noticia-card noticia-secundaria"
                                        >

                                            <img
                                                src="<?= htmlspecialchars($secundaria2['imagem']) ?>"
                                                alt="<?= htmlspecialchars($secundaria2['titulo']) ?>"
                                            >

                                            <div class="noticia-overlay">

                                                <span class="noticia-categoria">
                                                    <?= htmlspecialchars($secundaria2['categoria']) ?>
                                                </span>

                                                <div class="noticia-data">
                                                    <?= $secundaria2['data'] ?>
                                                </div>

                                                <h3>
                                                    <?= htmlspecialchars($secundaria2['titulo']) ?>
                                                </h3>

                                            </div>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endfor; ?>

            </div>


            <button
                class="carousel-control-prev controle-noticias"
                type="button"
                data-bs-target="#carouselNoticiasDesktop"
                data-bs-slide="prev"
            >

                <span class="controle-circulo">
                    <i class="bi bi-chevron-left"></i>
                </span>

                <span class="visually-hidden">
                    Anterior
                </span>

            </button>


            <button
                class="carousel-control-next controle-noticias"
                type="button"
                data-bs-target="#carouselNoticiasDesktop"
                data-bs-slide="next"
            >

                <span class="controle-circulo">
                    <i class="bi bi-chevron-right"></i>
                </span>

                <span class="visually-hidden">
                    Próxima
                </span>

            </button>

        </div>


        <div
            id="carouselNoticiasMobile"
            class="carousel slide d-md-none"
            data-bs-ride="carousel"
        >

            <div class="carousel-indicators">

                <?php foreach ($noticias as $indice => $noticia): ?>

                    <button
                        type="button"
                        data-bs-target="#carouselNoticiasMobile"
                        data-bs-slide-to="<?= $indice ?>"
                        class="<?= $indice === 0 ? 'active' : '' ?>"
                        aria-label="Notícia <?= $indice + 1 ?>"
                    ></button>

                <?php endforeach; ?>

            </div>


            <div class="carousel-inner">

                <?php foreach ($noticias as $indice => $noticia): ?>

                    <div
                        class="carousel-item <?= $indice === 0 ? 'active' : '' ?>"
                    >

                        <a
                            href="<?= $noticia['link'] ?>"
                            class="noticia-card noticia-mobile"
                        >

                            <img
                                src="<?= htmlspecialchars($noticia['imagem']) ?>"
                                alt="<?= htmlspecialchars($noticia['titulo']) ?>"
                            >

                            <div class="noticia-overlay">

                                <span class="noticia-categoria">
                                    <?= htmlspecialchars($noticia['categoria']) ?>
                                </span>

                                <div class="noticia-data">
                                    <?= $noticia['data'] ?>
                                </div>

                                <h2>
                                    <?= htmlspecialchars($noticia['titulo']) ?>
                                </h2>

                            </div>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>


            <button
                class="carousel-control-prev"
                type="button"
                data-bs-target="#carouselNoticiasMobile"
                data-bs-slide="prev"
            >

                <span class="carousel-control-prev-icon"></span>

            </button>


            <button
                class="carousel-control-next"
                type="button"
                data-bs-target="#carouselNoticiasMobile"
                data-bs-slide="next"
            >

                <span class="carousel-control-next-icon"></span>

            </button>

        </div>


        <?php else: ?>

            <div class="text-center py-5">

                <h3>Nenhuma notícia cadastrada</h3>

                <p>
                    As notícias cadastradas no sistema aparecerão aqui.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>