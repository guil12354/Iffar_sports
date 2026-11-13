
<?php

// Identificar a notícia pela URL
$id = filter_input(
    INPUT_GET,
    "noticia",
    FILTER_VALIDATE_INT
);

if (!$id) {
    echo "<div class='container py-5'>";
    echo "<h2>Notícia não encontrada.</h2>";
    echo "</div>";
    return;
}

// Buscar notícia no banco de dados
$sql = "SELECT * FROM noticias WHERE id_noticia = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$noticia = $resultado->fetch_assoc();

$stmt->close();

if (!$noticia) {
    echo "<div class='container py-5'>";
    echo "<h2>Notícia não encontrada.</h2>";
    echo "</div>";
    return;
}

// Preparar os dados
$titulo = htmlspecialchars(
    $noticia['titulo'],
    ENT_QUOTES,
    'UTF-8'
);

$autor = htmlspecialchars(
    $noticia['autor'] ?? 'IFFar Sports',
    ENT_QUOTES,
    'UTF-8'
);

$categoria = htmlspecialchars(
    $noticia['categoria'] ?? 'Notícias',
    ENT_QUOTES,
    'UTF-8'
);

$dataPublicacao = !empty($noticia['data_noticia'])
    ? strtotime($noticia['data_noticia'])
    : false;

$imagem = !empty($noticia['imagem'])
    ? htmlspecialchars(
        $noticia['imagem'],
        ENT_QUOTES,
        'UTF-8'
    )
    : '';

?>

<style>
/* CONTAINER PRINCIPAL */

.noticia-detalhe {
    max-width: 1000px;
    margin: 0 auto;
}

/* BOTÃO VOLTAR */

.noticia-voltar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #005126;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 28px;
}

.noticia-voltar:hover {
    color: #007d42;
}

/* CARD DA NOTÍCIA */

.noticia-artigo {
    background: #ffffff;
    border: 1px solid #e1e8e3;
    border-radius: 16px;
    padding: 35px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.04);
}

/* CATEGORIA */

.noticia-categoria {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #e8f4ec;
    color: #005126;
    padding: 8px 15px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 18px;
}

/* TÍTULO */

.noticia-titulo {
    font-size: clamp(28px, 4vw, 42px);
    line-height: 1.2;
    font-weight: 750;
    color: #123a29;
    margin-bottom: 25px;
    overflow-wrap: anywhere;
}

/* DATA, HORÁRIO E AUTOR */

.noticia-metadados {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px 24px;
    padding: 18px 0;
    border-top: 1px solid #e7ede8;
    border-bottom: 1px solid #e7ede8;
    margin-bottom: 28px;
}

.noticia-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #66756d;
    font-size: 14px;
}

.noticia-meta-item i {
    color: #006838;
    font-size: 16px;
}

/* IMAGEM PRINCIPAL */

.noticia-imagem-container {
    position: relative;
    margin-bottom: 30px;
}

.noticia-imagem {
    display: block;
    width: 100%;
    max-height: 480px;
    object-fit: cover;
    border-radius: 12px;
}

/* LUPA SOBRE A IMAGEM */

.noticia-lupa {
    position: absolute;
    top: 15px;
    right: 15px;

    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: rgba(0, 65, 34, 0.9);
    color: #ffffff;

    font-size: 19px;
    cursor: pointer;

    transition: background 0.2s, transform 0.2s;
    z-index: 2;
}

.noticia-lupa:hover {
    background: #007a40;
    color: #ffffff;
    transform: scale(1.08);
}

.noticia-lupa:focus-visible {
    outline: 3px solid #e7a928;
    outline-offset: 3px;
}

/* IMAGEM AMPLIADA */

.noticia-modal .modal-content {
    background: #151515;
    border: none;
    border-radius: 14px;
}

.noticia-modal .modal-body {
    text-align: center;
    padding: 0 20px 20px;
}

.noticia-imagem-completa {
    display: block;
    width: auto;
    max-width: 100%;
    max-height: 80vh;
    height: auto;
    object-fit: contain;
    margin: 0 auto;
}

/* RESUMO */

.noticia-resumo {
    font-size: 19px;
    line-height: 1.8;
    font-weight: 500;
    color: #3c4e43;
    padding-left: 18px;
    border-left: 4px solid #e7a928;
    margin-bottom: 30px;
}

/* DESCRIÇÃO */

.noticia-texto {
    color: #303c34;
    font-size: 17px;
    line-height: 1.9;
    overflow-wrap: anywhere;
}

/* RESPONSIVIDADE */

@media (max-width: 768px) {

    .noticia-artigo {
        padding: 22px 18px;
    }

    .noticia-metadados {
        gap: 12px 18px;
    }

    .noticia-resumo {
        font-size: 17px;
    }

    .noticia-texto {
        font-size: 16px;
    }

    .noticia-lupa {
        width: 38px;
        height: 38px;
        font-size: 17px;
    }

}
</style>

<!-- CONTEÚDO DA NOTÍCIA -->

<div class="container noticia-detalhe py-5">

    <!-- Voltar para notícias -->
    <a
        href="index.php?id=noticias"
        class="noticia-voltar"
    >
        <i class="bi bi-arrow-left"></i>
        Voltar para notícias
    </a>

    <article class="noticia-artigo">

        <!-- Categoria -->
        <div class="noticia-categoria">
            <i class="bi bi-tag"></i>
            <?= $categoria ?>
        </div>

        <!-- Título -->
        <h1 class="noticia-titulo">
            <?= $titulo ?>
        </h1>

        <!-- Informações da publicação -->
        <div class="noticia-metadados">

            <?php if ($dataPublicacao): ?>

                <!-- Data -->
                <span class="noticia-meta-item">
                    <i class="bi bi-calendar3"></i>
                    <?= date('d/m/Y', $dataPublicacao) ?>
                </span>

                <!-- Horário -->
                <span class="noticia-meta-item">
                    <i class="bi bi-clock"></i>
                    <?= date('H:i', $dataPublicacao) ?>
                </span>

            <?php endif; ?>

            <!-- Autor -->
            <span class="noticia-meta-item">
                <i class="bi bi-person-circle"></i>
                <?= $autor ?>
            </span>

        </div>

        <!-- IMAGEM PRINCIPAL -->
        <?php if ($imagem !== ''): ?>

            <div class="noticia-imagem-container">

                <img
                    src="<?= $imagem ?>"
                    class="noticia-imagem"
                    alt="<?= $titulo ?>"
                >

                <!-- Botão da lupa -->
                <button
                    type="button"
                    class="noticia-lupa"
                    data-bs-toggle="modal"
                    data-bs-target="#modalImagemNoticia"
                    title="Ver imagem completa"
                    aria-label="Ver imagem completa"
                >
                    <i class="bi bi-search"></i>
                </button>

            </div>

        <?php endif; ?>

        <!-- RESUMO -->
        <?php if (!empty($noticia['resumo'])): ?>

            <p class="noticia-resumo">
                <?= nl2br(
                    htmlspecialchars(
                        $noticia['resumo'],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ) ?>
            </p>

        <?php endif; ?>

        <!-- DESCRIÇÃO -->
        <div class="noticia-texto">

            <?= nl2br(
                htmlspecialchars(
                    $noticia['descricao'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                )
            ) ?>

        </div>

    </article>

</div>

<!-- MODAL DA IMAGEM COMPLETA -->
<?php if ($imagem !== ''): ?>

    <div
        class="modal fade noticia-modal"
        id="modalImagemNoticia"
        tabindex="-1"
        aria-label="Imagem completa da notícia"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content">

                <!-- Botão fechar -->
                <div class="modal-header border-0">

                    <button
                        type="button"
                        class="btn-close btn-close-white ms-auto"
                        data-bs-dismiss="modal"
                        aria-label="Fechar imagem"
                    ></button>

                </div>

                <!-- Imagem sem cortes -->
                <div class="modal-body">

                    <img
                        src="<?= $imagem ?>"
                        class="noticia-imagem-completa"
                        alt="<?= $titulo ?>"
                    >

                </div>

            </div>

        </div>

    </div>

<?php endif; ?>
