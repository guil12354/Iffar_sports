<?php
session_start();
if (($_SESSION['nivel'] ?? '') !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

function limpar($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$erro = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: noticiascrud.php');
    exit;
}

$conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($conexao->connect_error) {
    die('Erro na conexão com o banco de dados.');
}
$conexao->set_charset('utf8mb4');

$stmt = $conexao->prepare('SELECT * FROM noticias WHERE id_noticia = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$noticia = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$noticia) {
    header('Location: noticiascrud.php');
    exit;
}

$valores = $noticia;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    foreach (['titulo', 'resumo', 'descricao', 'autor', 'categoria'] as $campo) {
        $valores[$campo] = trim((string)($_POST[$campo] ?? ''));
        if ($valores[$campo] === '') {
            $erro = 'Preencha todos os campos obrigatórios.';
        }
    }

    $categorias = ['Esportes', 'Eventos', 'Competições', 'Campus'];
    if (!$erro && !in_array($valores['categoria'], $categorias, true)) {
        $erro = 'Selecione uma categoria válida.';
    }

    $imagem = $noticia['imagem'] ?? '';
    $arquivoSalvo = null;

    if (!$erro && isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
        $arquivo = $_FILES['imagem'];
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] <= 0 || $arquivo['size'] > 5 * 1024 * 1024) {
            $erro = 'A imagem deve ter até 5 MB.';
        } elseif (!in_array($ext, $permitidas, true)) {
            $erro = 'Envie uma imagem JPG, PNG ou WEBP válida.';
        } else {
            $pasta = __DIR__ . '/../img/noticias/';
            if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                $erro = 'Não foi possível criar a pasta de imagens.';
            } else {
                $nome = uniqid() . '.' . $ext;
                $arquivoSalvo = $pasta . $nome;
                if (move_uploaded_file($arquivo['tmp_name'], $arquivoSalvo)) {
                    $imagem = 'img/noticias/' . $nome;
                } else {
                    $arquivoSalvo = null;
                    $erro = 'Não foi possível salvar a imagem.';
                }
            }
        }
    }

    if (!$erro) {
        $stmt = $conexao->prepare('UPDATE noticias SET titulo = ?, resumo = ?, descricao = ?, autor = ?, categoria = ?, imagem = ? WHERE id_noticia = ?');
        $stmt->bind_param('ssssssi', $valores['titulo'], $valores['resumo'], $valores['descricao'], $valores['autor'], $valores['categoria'], $imagem, $id);

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: noticiascrud.php?ok=1');
            exit;
        }

        if ($arquivoSalvo && is_file($arquivoSalvo)) {
            unlink($arquivoSalvo);
        }
        $erro = 'Erro ao salvar as alterações.';
    }
}
?>

<?php $paginaAtual = 'noticias'; $tituloPagina = 'Gerenciar notícias'; $subtituloPagina = 'Publicações do portal'; $estilosExtras = '<style>.admin-main .painel{background:#fff;border:1px solid #dce4df;border-radius:14px;margin-top:22px;overflow:hidden}.admin-main .painel-titulo{padding:19px 22px;border-bottom:1px solid #e9eeea;font-weight:700;color:#005126}.admin-main .painel-corpo{padding:24px}.admin-main .capa{width:75px;height:55px;object-fit:cover;border-radius:7px}.admin-main .categoria{background:#eaf4ee;color:#005126;border-radius:20px;padding:6px 10px;font-size:12px}.admin-main .btn-verde,.admin-main .btn-green{background:#005126;color:white;border:0;border-radius:8px;padding:11px 18px}.admin-main .subtitulo{color:#748078;font-size:14px}.admin-main .page-title{color:#005126;font-size:2rem}.admin-main #preview{display:none;max-width:170px;max-height:120px;margin-top:12px;border-radius:8px}.admin-main .sport-card{background:white}</style>'; include __DIR__ . '/include/head.php'; include __DIR__ . '/include/nav.php'; ?>
<main class="admin-main"><div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><h1>Editar notícia #<?= (int)$id ?></h1><p class="subtitulo">Atualize os dados da publicação cadastrada.</p></div>
            <a href="noticiascrud.php" class="btn btn-outline-success"><i class="bi bi-arrow-left me-1"></i>Voltar às notícias</a>
        </div>
        <?php if ($erro): ?>
            <div class="alert alert-danger mt-3" role="alert"><?= limpar($erro) ?></div>
        <?php endif; ?>
        <section class="painel">
            <div class="painel-titulo"><i class="bi bi-pencil-square me-2"></i>Informações da notícia</div>
            <div class="painel-corpo">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3"><label for="titulo" class="form-label">Título</label><input id="titulo" name="titulo" class="form-control" value="<?= limpar($valores['titulo']) ?>" required></div>
                    <div class="mb-3"><label for="resumo" class="form-label">Resumo</label><textarea id="resumo" name="resumo" rows="3" class="form-control" required><?= limpar($valores['resumo']) ?></textarea></div>
                    <div class="mb-3"><label for="descricao" class="form-label">Descrição</label><textarea id="descricao" name="descricao" rows="6" class="form-control" required><?= limpar($valores['descricao']) ?></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="autor" class="form-label">Autor</label><input id="autor" name="autor" class="form-control" value="<?= limpar($valores['autor']) ?>" required></div>
                        <div class="col-md-6 mb-3"><label for="categoria" class="form-label">Categoria</label><select id="categoria" name="categoria" class="form-select" required>
                            <?php foreach (['Esportes', 'Eventos', 'Competições', 'Campus'] as $categoria): ?>
                                <option value="<?= limpar($categoria) ?>" <?= ($valores['categoria'] ?? '') === $categoria ? 'selected' : '' ?>><?= limpar($categoria) ?></option>
                            <?php endforeach; ?>
                        </select></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Imagem atual</label><div>
                        <?php if (!empty($noticia['imagem'])): ?><img class="capa" src="../<?= limpar($noticia['imagem']) ?>" alt="Imagem atual da notícia">
                        <?php else: ?><span class="text-muted">Sem imagem cadastrada.</span><?php endif; ?>
                    </div></div>
                    <div class="mb-4"><label for="imagem" class="form-label">Trocar imagem (JPG, PNG ou WEBP, até 5 MB)</label><input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp" class="form-control"><img id="preview" alt="Prévia da nova imagem"></div>
                    <div class="d-flex gap-2 flex-wrap"><button type="submit" name="editar" value="1" class="btn-verde"><i class="bi bi-check-circle me-2"></i>Salvar alterações</button><a href="noticiascrud.php" class="btn btn-outline-secondary px-4 py-2">Cancelar</a></div>
                </form>
            </div>
        </section>
    
</div></main>
<script>
const campo = document.getElementById('imagem');
const preview = document.getElementById('preview');
if (campo && preview) campo.addEventListener('change', () => {
    const arquivo = campo.files[0];
    if (arquivo && arquivo.type.startsWith('image/')) {
        preview.src = URL.createObjectURL(arquivo);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
        preview.removeAttribute('src');
    }
});
</script>

<?php include __DIR__ . '/include/foot.php'; ?>