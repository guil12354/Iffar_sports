
<?php

// Buscar notícias no banco de dados
$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {

    $sqlNoticias = "
        SELECT *
        FROM noticias
        WHERE titulo LIKE ?
           OR resumo LIKE ?
        ORDER BY id_noticia DESC
    ";

    $stmt = $conexao->prepare($sqlNoticias);

    $termoBusca = "%" . $busca . "%";

    $stmt->bind_param(
        "ss",
        $termoBusca,
        $termoBusca
    );

    $stmt->execute();
    $noticias = $stmt->get_result();

} else {

    $sqlNoticias = "
        SELECT *
        FROM noticias
        ORDER BY id_noticia DESC
    ";

    $noticias = $conexao->query($sqlNoticias);
}

?>

<style>
/* Cards clicáveis */
.pagina-noticias .card-noticia-lista {
    display: block;
    color: inherit;
    text-decoration: none;
    cursor: pointer;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
}

.pagina-noticias .card-noticia-lista:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0, 65, 34, .12);
    color: inherit;
    text-decoration: none;
}

.pagina-noticias .card-noticia-lista:focus-visible {
    outline: 3px solid #e7a928;
    outline-offset: 3px;
}

.pagina-noticias .card-noticia-lista:hover h2 { color: #007a40; }

/* Imagens e placeholder */
.pagina-noticias .imagem-noticia-lista {
    width: 100%;
    overflow: hidden;
    background: #eaf2ed;
}

.pagina-noticias .imagem-noticia-lista img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.pagina-noticias .imagem-noticia-lista.sem-imagem {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #eaf3ed 0%, #d7e8dc 100%);
}

.pagina-noticias .imagem-noticia-lista.sem-imagem img {
    width: 105px;
    height: 105px;
    min-height: 0;
    max-width: 65%;
    object-fit: contain;
    opacity: .9;
}

.pagina-noticias .rodape-card-noticia .ler-noticia {
    color: #005126;
    font-weight: 600;
}

.pagina-noticias .card-noticia-lista:hover .ler-noticia { color: #007a40; }

/* Desktop: todos os cards têm a mesma altura */
@media (min-width: 768px) {
    .pagina-noticias .card-noticia-lista {
        height: 200px !important;
    }

    .pagina-noticias .card-noticia-lista > .row,
    .pagina-noticias .card-noticia-lista > .row > [class*="col-"] {
        height: 100% !important;
        min-height: 0 !important;
    }

    .pagina-noticias .imagem-noticia-lista,
    .pagina-noticias .imagem-noticia-lista:not(.sem-imagem) img {
        height: 200px !important;
        min-height: 0 !important;
    }

    .pagina-noticias .conteudo-noticia-lista {
        height: 200px !important;
        min-height: 0 !important;
        padding: 16px 20px;
        overflow: hidden;
    }

    .pagina-noticias .conteudo-noticia-lista p {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        overflow: hidden;
    }
}

/* Celular: imagem em cima, conteúdo abaixo */
@media (max-width: 767.98px) {
    .pagina-noticias .card-noticia-lista,
    .pagina-noticias .card-noticia-lista > .row,
    .pagina-noticias .card-noticia-lista > .row > [class*="col-"] {
        height: auto !important;
    }

    .pagina-noticias .imagem-noticia-lista,
    .pagina-noticias .imagem-noticia-lista:not(.sem-imagem) img {
        height: 210px !important;
        min-height: 0 !important;
    }

    .pagina-noticias .conteudo-noticia-lista {
        height: auto !important;
        min-height: 0 !important;
    }
}
</style>

<section class="pagina-noticias">

    <div class="container py-5">

        <!-- CABEÇALHO -->
        <div class="row align-items-center mb-4 gy-3">

            <div class="col-lg-8">

                <span class="mini-titulo">
                    IFFAR SPORTS
                </span>

                <h1 class="titulo-pagina">
                    Notícias
                </h1>

            </div>

            <!-- CAMPO DE BUSCA -->
            <div class="col-lg-4">

                <form
                    action="index.php"
                    method="GET"
                    class="busca-noticias"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="noticias"
                    >

                    <div class="input-group">

                        <input
                            type="search"
                            name="busca"
                            class="form-control"
                            placeholder="Buscar notícia"
                            value="<?= htmlspecialchars(
                                $busca,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <button
                            class="btn btn-busca"
                            type="submit"
                            aria-label="Buscar"
                        >
                            <i class="bi bi-search"></i>
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- RESULTADOS DA BUSCA -->
        <?php if ($busca !== ''): ?>

            <div class="mb-4">

                <span class="texto-busca">
                    Resultados para:

                    <strong>
                        <?= htmlspecialchars(
                            $busca,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                </span>

                <a
                    href="index.php?id=noticias"
                    class="ms-2 link-limpar-busca"
                >
                    Limpar busca
                </a>

            </div>

        <?php endif; ?>

        <!-- LISTAGEM -->
        <div class="lista-noticias">

            <?php if ($noticias && $noticias->num_rows > 0): ?>

                <?php while ($noticia = $noticias->fetch_assoc()): ?>

                    <?php

                    // Verificar se existe imagem
                    $temImagem = !empty(
                        trim($noticia['imagem'] ?? '')
                    );

                    $imagem = $temImagem
                        ? $noticia['imagem']
                        : 'img/logo.png';

                    // Formatar data
                    $data = !empty($noticia['data_noticia'])
                        ? strtotime($noticia['data_noticia'])
                        : false;

                    // Preparar resumo
                    $resumo = strip_tags(
                        $noticia['resumo'] ?? ''
                    );

                    if (mb_strlen($resumo) > 260) {
                        $resumo =
                            mb_substr($resumo, 0, 260)
                            . '...';
                    }

                    // Link da notícia
                    $linkNoticia = 'index.php?id=noticia&noticia='
                        . (int) $noticia['id_noticia'];

                    ?>

                    <!-- CARD INTEIRO CLICÁVEL -->
                    <a
                        href="<?= $linkNoticia ?>"
                        class="card-noticia-lista"
                        aria-label="Ler notícia: <?= htmlspecialchars(
                            $noticia['titulo'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                        <div class="row g-0">

                            <!-- IMAGEM -->
                            <div class="col-md-4 col-lg-3">

                                <div class="imagem-noticia-lista <?= !$temImagem ? 'sem-imagem' : '' ?>">

                                    <img
                                        src="<?= htmlspecialchars(
                                            $imagem,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt="<?= $temImagem
                                            ? htmlspecialchars(
                                                $noticia['titulo'] ?? 'Notícia',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Logo IFFar Sports'
                                        ?>"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.src='img/logo.png'; this.parentElement.classList.add('sem-imagem');"
                                    >

                                </div>

                            </div>

                            <!-- CONTEÚDO -->
                            <div class="col-md-8 col-lg-9">

                                <div class="conteudo-noticia-lista">

                                    <div>

                                        <!-- DATA -->
                                        <div class="data-noticia-lista">

                                            <?= $data
                                                ? date('d/m/Y', $data)
                                                : ''
                                            ?>

                                        </div>

                                        <!-- TÍTULO -->
                                        <h2>
                                            <?= htmlspecialchars(
                                                $noticia['titulo'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h2>

                                        <!-- RESUMO -->
                                        <p>
                                            <?= htmlspecialchars(
                                                $resumo,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                    </div>

                                    <!-- RODAPÉ -->
                                    <div class="rodape-card-noticia">

                                        <span>
                                            <i class="bi bi-person"></i>

                                            <?= htmlspecialchars(
                                                $noticia['autor'] ?? 'Administrador',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                        <span class="ler-noticia">
                                            Ler notícia
                                            <i class="bi bi-arrow-right"></i>
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </a>

                <?php endwhile; ?>

            <?php else: ?>

                <!-- SEM NOTÍCIAS -->
                <div class="estado-vazio">

                    <i class="bi bi-newspaper"></i>

                    <h2>
                        Nenhuma notícia encontrada
                    </h2>

                    <p>
                        Não encontramos notícias correspondentes
                        à sua busca.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>
