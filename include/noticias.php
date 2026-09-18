<?php

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

<section class="pagina-noticias">

    <div class="container py-5">

        <div class="row align-items-center mb-4 gy-3">

            <div class="col-lg-8">

                <span class="mini-titulo">
                    IFFAR SPORTS
                </span>

                <h1 class="titulo-pagina">
                    Notícias
                </h1>

            </div>

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
                            value="<?= htmlspecialchars($busca ?? '') ?>"
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

        <?php if ($busca !== ''): ?>

            <div class="mb-4">

                <span class="texto-busca">
                    Resultados para:
                    <strong><?= htmlspecialchars($busca ?? '') ?></strong>
                </span>

                <a
                    href="index.php?id=noticias"
                    class="ms-2 link-limpar-busca"
                >
                    Limpar busca
                </a>

            </div>

        <?php endif; ?>

        <div class="lista-noticias">

            <?php if ($noticias && $noticias->num_rows > 0): ?>

                <?php while ($noticia = $noticias->fetch_assoc()): ?>

                    <article class="card-noticia-lista">

                        <div class="row g-0">

                            <div class="col-md-4 col-lg-3">

                                <div class="imagem-noticia-lista">

                                    <?php

$imagem = !empty($noticia['imagem'])
    ? $noticia['imagem']
    : 'img/logo.png';

?>

<img
    src="<?= htmlspecialchars($imagem) ?>"
    alt="<?= htmlspecialchars($noticia['titulo'] ?? 'Notícia') ?>"
>

                                </div>

                            </div>

                            <div class="col-md-8 col-lg-9">

                                <div class="conteudo-noticia-lista">

                                    <div>

                                        <div class="data-noticia-lista">

                                            <?php

                                            $data = !empty($noticia['data_noticia'])
                                                ? strtotime($noticia['data_noticia'])
                                                : false;

                                            echo $data
                                                ? date('d/m/Y', $data)
                                                : '';

                                            ?>

                                        </div>

                                        <h2>
                                            <?= htmlspecialchars($noticia['titulo'] ?? '') ?>
                                        </h2>

                                        <p>

                                            <?php

                                            $resumo = strip_tags($noticia['resumo'] ?? '');

                                            if (mb_strlen($resumo) > 260) {
                                                $resumo =
                                                    mb_substr($resumo, 0, 260)
                                                    . "...";
                                            }

                                            echo htmlspecialchars($resumo);

                                            ?>

                                        </p>

                                    </div>

                                    <div class="rodape-card-noticia">

                                        <span>
                                            <i class="bi bi-person"></i>

                                            <?= htmlspecialchars($noticia['autor'] ?? 'Administrador') ?>
                                        </span>

                                        <a
                                            href="index.php?id=noticia&noticia=<?= (int)$noticia['id_noticia'] ?>"
                                        >
                                            Ler notícia
                                            <i class="bi bi-arrow-right"></i>
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </article>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="estado-vazio">

                    <i class="bi bi-newspaper"></i>

                    <h2>
                        Nenhuma notícia encontrada
                    </h2>

                    <p>
                        Não encontramos notícias correspondentes à sua busca.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>