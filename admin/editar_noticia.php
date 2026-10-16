<?php
// IFFar Sports - edição de notícias
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

function limpar($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

// O projeto usa o nível de acesso para distinguir administradores.
// Exibimos um aviso em vez de uma página branca caso a sessão não esteja válida.
if (($_SESSION['nivel'] ?? '') !== 'administrador') {
    http_response_code(403);
    exit('<!doctype html><html lang="pt-BR"><meta charset="utf-8"><title>Acesso restrito</title><div style="font:16px Arial;max-width:650px;margin:70px auto;padding:25px;border:1px solid #ddd;border-radius:12px"><h2>Acesso administrativo necessário</h2><p>Sua sessão não está identificada como administrador. Faça login novamente.</p><a href="../login.php">Ir para login</a></div></html>');
}

$erro = '';
$noticia = null;
$valores = [];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    if (!$id || $id < 1) {
        throw new RuntimeException('ID da notícia inválido ou ausente na URL.');
    }
    $conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');
    $conexao->set_charset('utf8mb4');
    $stmt = $conexao->prepare('SELECT * FROM noticias WHERE id_noticia = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$noticia) {
        throw new RuntimeException('Notícia não encontrada (ID ' . $id . ').');
    }
    if (empty($_SESSION['token_noticias'])) {
        $_SESSION['token_noticias'] = bin2hex(random_bytes(16));
    }
    $valores = $noticia;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
        if (!hash_equals($_SESSION['token_noticias'], (string)($_POST['token'] ?? ''))) {
            throw new RuntimeException('Token de segurança inválido. Atualize a página e tente novamente.');
        }
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
            if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] <= 0 || $arquivo['size'] > 5 * 1024 * 1024) {
                $erro = 'A imagem deve ter até 5 MB.';
            } else {
                $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
                if (!isset($tipos[$tipo]) || getimagesize($arquivo['tmp_name']) === false) {
                    $erro = 'Envie uma imagem JPG, PNG ou WEBP válida.';
                } else {
                    $pasta = __DIR__ . '/../img/noticias/';
                    if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
                        $erro = 'Não foi possível criar a pasta de imagens.';
                    } else {
                        $nome = bin2hex(random_bytes(12)) . '.' . $tipos[$tipo];
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
        }
        if (!$erro) {
            try {
                $stmt = $conexao->prepare('UPDATE noticias SET titulo = ?, resumo = ?, descricao = ?, autor = ?, categoria = ?, imagem = ? WHERE id_noticia = ?');
                $stmt->bind_param('ssssssi', $valores['titulo'], $valores['resumo'], $valores['descricao'], $valores['autor'], $valores['categoria'], $imagem, $id);
                $stmt->execute();
                $stmt->close();
                header('Location: noticiascrud.php?ok=1');
                exit;
            } catch (Throwable $ex) {
                if ($arquivoSalvo && is_file($arquivoSalvo)) {
                    unlink($arquivoSalvo);
                }
                throw $ex;
            }
        }
    }
} catch (Throwable $ex) {
    $erro = 'Erro: ' . $ex->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar notícia | IFFar Sports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root { --verde:#004b2c; --escuro:#003b23; --laranja:#ffad14; }
body { background:#f3f5f4; font-family:'Segoe UI',Arial,sans-serif; }
.sidebar { width:223px; height:100vh; position:fixed; top:0; left:0; background:var(--escuro); color:white; display:flex; flex-direction:column; z-index:10; }
.marca { padding:22px 15px; border-bottom:1px solid #ffffff25; font-weight:bold; }
.marca i { background:var(--laranja); color:var(--escuro); padding:12px; border-radius:10px; margin-right:8px; }
.menu { color:#dcece2; text-decoration:none; display:block; padding:14px 20px; margin:4px 10px; border-radius:9px; }
.menu i { margin-right:10px; }
.menu:hover,.menu.ativo { background:#ffffff20; color:white; }
.menu.ativo { border-left:4px solid var(--laranja); }
.rodape-menu { margin-top:auto; border-top:1px solid #ffffff25; padding:12px 0; }
.principal { margin-left:223px; }
.topo { background:white; border-bottom:1px solid #e1e8e3; padding:20px 28px; color:var(--escuro); font-weight:600; }
.conteudo { padding:30px; max-width:1450px; margin:auto; }
h1 { color:var(--escuro); font-size:27px; font-weight:750; }
.subtitulo { color:#78877e; font-size:14px; }
.painel { background:white; border:1px solid #e0e8e2; border-radius:14px; margin-top:24px; overflow:hidden; box-shadow:0 8px 25px #003b2308; }
.painel-titulo { padding:20px 24px; border-bottom:1px solid #e9eeea; font-size:17px; font-weight:700; color:var(--escuro); }
.painel-corpo { padding:24px; }
.form-label { font-size:13px; font-weight:600; }
.form-control,.form-select { border-radius:8px; padding:10px 12px; }
.form-control:focus,.form-select:focus { border-color:#168456; box-shadow:0 0 0 3px #16845618; }
.btn-verde { background:var(--verde); color:white; border:0; border-radius:8px; padding:11px 18px; }
.btn-verde:hover { background:var(--escuro); color:white; }
.capa { width:190px; height:125px; object-fit:cover; border-radius:8px; border:1px solid #e0e8e2; }
#preview { display:none; width:190px; height:125px; object-fit:cover; margin-top:12px; border-radius:8px; }
@media(max-width:750px) { .sidebar { width:65px; } .marca span,.menu span { display:none; } .marca { padding:22px 10px; } .marca i { padding:9px; margin:0; } .menu { padding:14px; } .principal { margin-left:65px; } .conteudo { padding:16px; } .topo { padding:17px; } }
</style>
</head>
<body>
<aside class="sidebar">
    <div class="marca"><i class="bi bi-trophy-fill"></i><span>IFFar Sports</span></div>
    <a class="menu mt-3" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a>
    <a class="menu ativo" href="noticiascrud.php"><i class="bi bi-newspaper"></i><span>Notícias</span></a>
    <a class="menu" href="esportescrud.php"><i class="bi bi-dribbble"></i><span>Esportes</span></a>
    <div class="rodape-menu">
        <a class="menu" href="../index.php"><i class="bi bi-box-arrow-up-right"></i><span>Ver portal</span></a>
        <a class="menu" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a>
    </div>
</aside>
<div class="principal">
    <header class="topo">Gerenciamento de notícias <span class="float-end"><i class="bi bi-person-circle"></i> Administrador</span></header>
    <main class="conteudo">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><h1>Editar notícia<?= $id ? ' #' . (int)$id : '' ?></h1><p class="subtitulo">Atualize os dados da publicação cadastrada.</p></div>
            <a href="noticiascrud.php" class="btn btn-outline-success"><i class="bi bi-arrow-left me-1"></i>Voltar às notícias</a>
        </div>
        <?php if ($erro): ?>
            <div class="alert alert-danger mt-3" role="alert"><?= limpar($erro) ?></div>
        <?php endif; ?>
        <?php if ($noticia): ?>
        <section class="painel">
            <div class="painel-titulo"><i class="bi bi-pencil-square me-2"></i>Informações da notícia</div>
            <div class="painel-corpo">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= limpar($_SESSION['token_noticias'] ?? '') ?>">
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
        <?php endif; ?>
    </main>
</div>
<script>
const campo = document.getElementById('imagem');
const preview = document.getElementById('preview');
let urlAnterior = null;
if (campo && preview) campo.addEventListener('change', () => {
    if (urlAnterior) URL.revokeObjectURL(urlAnterior);
    const arquivo = campo.files[0];
    if (arquivo && arquivo.type.startsWith('image/')) {
        urlAnterior = URL.createObjectURL(arquivo);
        preview.src = urlAnterior;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
        preview.removeAttribute('src');
    }
});
</script>
</body>
</html>
