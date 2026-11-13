<?php

$noticias = [];

// BUSCA AS 10 ÚLTIMAS NOTÍCIAS
$sql = "SELECT * FROM noticias ORDER BY data_noticia DESC, id_noticia DESC LIMIT 10";
$resultado = $conexao->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($noticia = $resultado->fetch_assoc()) {
        $imgCaminho = trim($noticia['imagem'] ?? '');
        $semImagem = $imgCaminho == '' || $imgCaminho == 'img/logo.png';

        $noticias[] = [
            'data' => date("d/m/Y", strtotime($noticia['data_noticia'])),
            'titulo' => $noticia['titulo'],
            'resumo' => $noticia['resumo'] ?? '',
            'autor' => !empty($noticia['autor']) ? $noticia['autor'] : 'Administrador',
            'categoria' => $noticia['categoria'],
            'imagem' => $semImagem ? 'img/logo.png' : $imgCaminho,
            'sem_imagem' => $semImagem,
            'link' => 'index.php?id=noticia&noticia=' . $noticia['id_noticia']
        ];
    }
}

?>

<section class="noticias-secao">
    <div class="container">

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="text-uppercase fw-bold small" style="color: #d9a014; letter-spacing: 1px;">IFFAR SPORTS</span>
                <h2 class="titulo-secao mb-0">
                    Notícias
                </h2>
            </div>

            <a
                href="index.php?id=noticias"
                class="btn-ver-todas"
            >
                Ver todas
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>


        <!-- SE NÃO HOUVER NOTÍCIAS -->
        <?php if (empty($noticias)): ?>

            <div class="alert alert-light border text-center py-5">
                Nenhuma notícia publicada ainda.
            </div>


        <!-- SE HOUVER NOTÍCIAS -->
        <?php else: ?>

            <!-- ================= DESKTOP ================= -->
            <div
                id="carrosselNoticiasDesktop"
                class="carousel slide carousel-noticias d-none d-md-block"
            >

                <div class="carousel-inner">

                    <?php for ($i = 0; $i < count($noticias); $i += 3): ?>

                        <?php
                        $principal = $noticias[$i];
                        $secundaria1 = $noticias[$i + 1] ?? null;
                        $secundaria2 = $noticias[$i + 2] ?? null;
                        ?>

                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">

                            <div class="row g-3">

                                <!-- NOTÍCIA PRINCIPAL -->
                                <div class="col-lg-7">

                                    <a
                                        href="<?= $principal['link'] ?>"
                                        class="home-card-limpo home-card-principal <?= $principal['sem_imagem'] ? 'sem-imagem-limpa' : '' ?>"
                                    >
                                        <div class="home-card-img">
                                            <img
                                                src="<?= $principal['imagem'] ?>"
                                                alt="<?= $principal['titulo'] ?>"
                                                onerror="this.onerror=null;this.src='img/logo.png';this.closest('.home-card-limpo').classList.add('sem-imagem-limpa');"
                                            >
                                        </div>

                                        <div class="home-card-corpo">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge-categoria-limpa"><?= $principal['categoria'] ?></span>
                                                <span class="data-limpa"><?= $principal['data'] ?></span>
                                            </div>

                                            <h3 class="titulo-principal-limpo">
                                                <?= $principal['titulo'] ?>
                                            </h3>

                                            <?php if ($principal['resumo'] !== ''): ?>
                                                <p class="resumo-limpo mb-0">
                                                    <?= $principal['resumo'] ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="rodape-limpo mt-auto pt-3">
                                                <span><i class="bi bi-person me-1"></i><?= $principal['autor'] ?></span>
                                                <span class="ler-mais-limpo">Ler notícia <i class="bi bi-arrow-right ms-1"></i></span>
                                            </div>
                                        </div>
                                    </a>

                                </div>


                                <!-- COLUNA DA DIREITA -->
                                <div class="col-lg-5 d-flex flex-column gap-3">

                                    <!-- SECUNDÁRIA 1 -->
                                    <?php if ($secundaria1): ?>

                                        <a
                                            href="<?= $secundaria1['link'] ?>"
                                            class="home-card-limpo home-card-secundario <?= $secundaria1['sem_imagem'] ? 'sem-imagem-limpa' : '' ?>"
                                        >
                                            <div class="home-card-img">
                                                <img
                                                    src="<?= $secundaria1['imagem'] ?>"
                                                    alt="<?= $secundaria1['titulo'] ?>"
                                                    onerror="this.onerror=null;this.src='img/logo.png';this.closest('.home-card-limpo').classList.add('sem-imagem-limpa');"
                                                >
                                            </div>

                                            <div class="home-card-corpo">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge-categoria-limpa"><?= $secundaria1['categoria'] ?></span>
                                                    <span class="data-limpa"><?= $secundaria1['data'] ?></span>
                                                </div>

                                                <h3 class="titulo-secundario-limpo">
                                                    <?= $secundaria1['titulo'] ?>
                                                </h3>

                                                <div class="rodape-limpo mt-auto pt-2">
                                                    <span><i class="bi bi-person me-1"></i><?= $secundaria1['autor'] ?></span>
                                                    <span class="ler-mais-limpo">Ler notícia <i class="bi bi-arrow-right ms-1"></i></span>
                                                </div>
                                            </div>
                                        </a>

                                    <?php endif; ?>


                                    <!-- SECUNDÁRIA 2 -->
                                    <?php if ($secundaria2): ?>

                                        <a
                                            href="<?= $secundaria2['link'] ?>"
                                            class="home-card-limpo home-card-secundario <?= $secundaria2['sem_imagem'] ? 'sem-imagem-limpa' : '' ?>"
                                        >
                                            <div class="home-card-img">
                                                <img
                                                    src="<?= $secundaria2['imagem'] ?>"
                                                    alt="<?= $secundaria2['titulo'] ?>"
                                                    onerror="this.onerror=null;this.src='img/logo.png';this.closest('.home-card-limpo').classList.add('sem-imagem-limpa');"
                                                >
                                            </div>

                                            <div class="home-card-corpo">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge-categoria-limpa"><?= $secundaria2['categoria'] ?></span>
                                                    <span class="data-limpa"><?= $secundaria2['data'] ?></span>
                                                </div>

                                                <h3 class="titulo-secundario-limpo">
                                                    <?= $secundaria2['titulo'] ?>
                                                </h3>

                                                <div class="rodape-limpo mt-auto pt-2">
                                                    <span><i class="bi bi-person me-1"></i><?= $secundaria2['autor'] ?></span>
                                                    <span class="ler-mais-limpo">Ler notícia <i class="bi bi-arrow-right ms-1"></i></span>
                                                </div>
                                            </div>
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endfor; ?>

                </div>


                <!-- CONTROLES DESKTOP -->
                <?php if (count($noticias) > 3): ?>

                    <button
                        class="carousel-control-prev controle-noticias"
                        type="button"
                        data-bs-target="#carrosselNoticiasDesktop"
                        data-bs-slide="prev"
                    >
                        <span class="controle-circulo">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </button>

                    <button
                        class="carousel-control-next controle-noticias"
                        type="button"
                        data-bs-target="#carrosselNoticiasDesktop"
                        data-bs-slide="next"
                    >
                        <span class="controle-circulo">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </button>

                <?php endif; ?>

            </div>


            <!-- ================= MOBILE ================= -->
            <div
                id="carrosselNoticiasMobile"
                class="carousel slide carousel-noticias d-md-none"
            >

                <div class="carousel-inner">

                    <?php foreach ($noticias as $index => $noticia): ?>

                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

                            <a
                                href="<?= $noticia['link'] ?>"
                                class="home-card-limpo home-card-principal <?= $noticia['sem_imagem'] ? 'sem-imagem-limpa' : '' ?>"
                            >
                                <div class="home-card-img">
                                    <img
                                        src="<?= $noticia['imagem'] ?>"
                                        alt="<?= $noticia['titulo'] ?>"
                                        onerror="this.onerror=null;this.src='img/logo.png';this.closest('.home-card-limpo').classList.add('sem-imagem-limpa');"
                                    >
                                </div>

                                <div class="home-card-corpo">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge-categoria-limpa"><?= $noticia['categoria'] ?></span>
                                        <span class="data-limpa"><?= $noticia['data'] ?></span>
                                    </div>

                                    <h3 class="titulo-secundario-limpo">
                                        <?= $noticia['titulo'] ?>
                                    </h3>

                                    <div class="rodape-limpo mt-auto pt-2">
                                        <span><i class="bi bi-person me-1"></i><?= $noticia['autor'] ?></span>
                                        <span class="ler-mais-limpo">Ler notícia <i class="bi bi-arrow-right ms-1"></i></span>
                                    </div>
                                </div>
                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>


                <!-- CONTROLES MOBILE -->
                <?php if (count($noticias) > 1): ?>

                    <button
                        class="carousel-control-prev controle-noticias"
                        type="button"
                        data-bs-target="#carrosselNoticiasMobile"
                        data-bs-slide="prev"
                    >
                        <span class="controle-circulo">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </button>

                    <button
                        class="carousel-control-next controle-noticias"
                        type="button"
                        data-bs-target="#carrosselNoticiasMobile"
                        data-bs-slide="next"
                    >
                        <span class="controle-circulo">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </button>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>
</section>

<style>
.home-card-limpo {
    display: flex;
    background: #ffffff;
    border: 1px solid #e6ece8;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none !important;
    color: inherit !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.home-card-limpo:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(11, 79, 38, 0.10);
}

.home-card-principal {
    flex-direction: column;
    height: 100%;
    min-height: 410px;
}

.home-card-principal .home-card-img {
    width: 100%;
    height: 225px;
    overflow: hidden;
    background: #e3efe7;
}

.home-card-secundario {
    flex-direction: row;
    height: 197px;
}

.home-card-secundario .home-card-img {
    width: 38%;
    min-width: 150px;
    height: 100%;
    overflow: hidden;
    background: #e3efe7;
}

.home-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.home-card-limpo.sem-imagem-limpa .home-card-img {
    background: linear-gradient(135deg, #dff0e5, #eef6f1);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.home-card-limpo.sem-imagem-limpa .home-card-img img {
    width: auto;
    height: auto;
    max-width: 72px;
    max-height: 72px;
    object-fit: contain;
    opacity: 0.45;
}

.home-card-corpo {
    flex: 1;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
}

.badge-categoria-limpa {
    background: #e8f3ec;
    color: #0b4f26;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    padding: 3px 9px;
    border-radius: 6px;
}

.data-limpa {
    color: #d9a014;
    font-size: 0.82rem;
    font-weight: 600;
}

.titulo-principal-limpo {
    font-size: 1.25rem;
    font-weight: 700;
    color: #143622;
    margin-bottom: 6px;
}

.titulo-secundario-limpo {
    font-size: 1.05rem;
    font-weight: 700;
    color: #143622;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.resumo-limpo {
    font-size: 0.9rem;
    color: #5c6b62;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rodape-limpo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #6c7a72;
    border-top: 1px solid #f0f4f1;
}

.ler-mais-limpo {
    color: #0b4f26;
    font-weight: 600;
}
</style>