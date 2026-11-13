
<?php

// Conexão com o banco de dados
$conexao = new mysqli(
    "localhost",
    "root",
    "",
    "iffar_sports3"
);

if ($conexao->connect_error) {
    die("Erro ao conectar ao banco de dados.");
}

$conexao->set_charset("utf8mb4");

// Buscar modalidades cadastradas
$esportes = $conexao->query("
    SELECT *
    FROM esportes
    ORDER BY nome ASC
");

// Imagem padrão do IFFar Sports
$imagemPadrao = "img/logo.png";

?>

<style>
/* CARDS DE ESPORTES */

.pagina-esportes .card-esporte {
    position: relative;
    display: block;
    width: 100%;
    height: 260px;
    overflow: hidden;
    border-radius: 12px;
    background: #eaf2ed;
    text-decoration: none;
}

/* Imagens reais dos esportes */

.pagina-esportes .card-esporte > img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s ease;
}

.pagina-esportes .card-esporte:hover > img {
    transform: scale(1.04);
}

/* Imagem padrão quando não há foto */

.pagina-esportes .card-esporte.sem-imagem {
    background: linear-gradient(
        135deg,
        #eaf3ed 0%,
        #d7e8dc 100%
    );
}

.pagina-esportes .card-esporte.sem-imagem > img {
    width: 110px;
    height: 110px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    object-fit: contain;
    opacity: 0.9;
}

.pagina-esportes .card-esporte.sem-imagem:hover > img {
    transform: translate(-50%, -50%) scale(1.04);
}

/* Escurecimento inferior para destacar o nome */

.pagina-esportes .overlay-esporte {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(0, 0, 0, 0.78) 0%,
        rgba(0, 0, 0, 0.08) 55%,
        transparent 100%
    );
    pointer-events: none;
}

/* Nome da modalidade */

.pagina-esportes .nome-esporte {
    position: absolute;
    left: 18px;
    right: 18px;
    bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    color: #ffffff;
    font-size: 19px;
    font-weight: 700;
    z-index: 2;
}

.pagina-esportes .nome-esporte span {
    overflow-wrap: anywhere;
}

.pagina-esportes .nome-esporte i {
    flex-shrink: 0;
    font-size: 18px;
}

/* Responsividade */

@media (max-width: 767px) {
    .pagina-esportes .card-esporte {
        height: 230px;
    }
}
</style>

<section class="pagina-esportes">

    <div class="container py-5">

        <!-- CABEÇALHO -->
        <div class="cabecalho-secao mb-4">

            <div>

                <span class="mini-titulo">
                    IFFAR SPORTS
                </span>

                <h1 class="titulo-pagina">
                    Esportes
                </h1>

                <p class="subtitulo-pagina">
                    Conheça as modalidades esportivas
                    oferecidas pelo IFFar.
                </p>

            </div>

        </div>

        <!-- LISTAGEM DOS ESPORTES -->
        <?php if ($esportes && $esportes->num_rows > 0): ?>

            <div class="row g-4">

                <?php while ($esporte = $esportes->fetch_assoc()): ?>

                    <?php

                    // Verificar se existe imagem cadastrada
                    $temImagem = !empty(
                        trim($esporte['imagem'] ?? '')
                    );

                    // Selecionar imagem real ou padrão
                    $imagem = $temImagem
                        ? $esporte['imagem']
                        : $imagemPadrao;

                    // Nome da modalidade
                    $nomeEsporte = htmlspecialchars(
                        $esporte['nome'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    ?>

                    <div class="col-12 col-sm-6 col-lg-4">

                        <a
                            href="index.php?id=esporte&esporte=<?= (int) $esporte['id_esporte'] ?>"
                            class="card-esporte <?= !$temImagem ? 'sem-imagem' : '' ?>"
                        >

                            <!-- Imagem da modalidade -->
                            <img
                                src="<?= htmlspecialchars(
                                    $imagem,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= $nomeEsporte ?>"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='img/logo.png'; this.parentElement.classList.add('sem-imagem');"
                            >

                            <!-- Gradiente -->
                            <div class="overlay-esporte"></div>

                            <!-- Nome do esporte -->
                            <div class="nome-esporte">

                                <span>
                                    <?= $nomeEsporte ?>
                                </span>

                                <i class="bi bi-arrow-up-right"></i>

                            </div>

                        </a>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <!-- ESTADO VAZIO -->
            <div class="estado-vazio">

                <i class="bi bi-trophy"></i>

                <h2>
                    Nenhuma modalidade cadastrada
                </h2>

                <p>
                    As modalidades esportivas aparecerão
                    aqui assim que forem cadastradas.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
