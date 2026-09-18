<?php

$id = filter_input(INPUT_GET, "noticia", FILTER_VALIDATE_INT);

if (!$id) {
    echo "<div class='container py-5'>";
    echo "<h2>Notícia não encontrada.</h2>";
    echo "</div>";
    return;
}

$sql = "SELECT * FROM noticias WHERE id_noticia = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$noticia = $resultado->fetch_assoc();

if (!$noticia) {
    echo "<div class='container py-5'>";
    echo "<h2>Notícia não encontrada.</h2>";
    echo "</div>";
    return;
}

?>

<div class="container noticia-detalhe py-5">

    <a href="index.php?id=noticias" class="btn btn-secondary mb-4">
        Voltar para notícias
    </a>

    <h1>
        <?= htmlspecialchars($noticia['titulo']) ?>
    </h1>

    <div class="noticia-info mb-4">

        <span>
            <?= date("d/m/Y H:i", strtotime($noticia['data_noticia'])) ?>
        </span>

        <span>
            <?= htmlspecialchars($noticia['autor']) ?>
        </span>

        <span>
            <?= htmlspecialchars($noticia['categoria']) ?>
        </span>

    </div>

    <?php if (!empty($noticia['imagem'])): ?>

        <img
            src="<?= htmlspecialchars($noticia['imagem']) ?>"
            class="img-fluid rounded mb-4 noticia-imagem"
        >

    <?php endif; ?>

    <?php if (!empty($noticia['resumo'])): ?>

        <p class="noticia-resumo">
            <?= htmlspecialchars($noticia['resumo']) ?>
        </p>

    <?php endif; ?>

    <div class="noticia-texto">

        <?= nl2br(htmlspecialchars($noticia['descricao'])) ?>

    </div>

</div>