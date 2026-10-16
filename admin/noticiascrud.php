<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}
if (($_SESSION['nivel'] ?? '') !== 'administrador') {
    header('Location: ../index.php');
    exit;
}

$conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($conexao->connect_error) die('Erro ao conectar ao banco.');
$conexao->set_charset('utf8mb4');

function limpar($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['token_noticias'])) {
    $_SESSION['token_noticias'] = bin2hex(random_bytes(16));
}
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token_noticias'], $_POST['token'] ?? '')) {
        die('Solicitação inválida.');
    }

    if (isset($_POST['cadastrar'])) {
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $autor = $_SESSION['nome'] ?? 'Administrador';
        $data = date('Y-m-d H:i:s');
        $imagem = '';
        $arquivoSalvo = '';

        if (!$titulo || !$resumo || !$descricao || !in_array($categoria, ['Esportes','Eventos','Competições','Campus'], true)) {
            $erro = 'Preencha todos os campos obrigatórios.';
        }

        if (!$erro && isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $arquivo = $_FILES['imagem'];
            $tipos = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
            if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] > 5 * 1024 * 1024 || $arquivo['size'] <= 0) {
                $erro = 'A imagem deve ter até 5 MB.';
            } else {
                $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
                if (!isset($tipos[$tipo]) || !getimagesize($arquivo['tmp_name'])) {
                    $erro = 'Envie uma imagem JPG, PNG ou WEBP válida.';
                } else {
                    $pasta = __DIR__ . '/../img/noticias/';
                    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                        $erro = 'Não foi possível criar a pasta de imagens.';
                    } else {
                        $nome = bin2hex(random_bytes(12)) . '.' . $tipos[$tipo];
                        $arquivoSalvo = $pasta . $nome;
                        if (move_uploaded_file($arquivo['tmp_name'], $arquivoSalvo)) {
                            $imagem = 'img/noticias/' . $nome;
                        } else {
                            $erro = 'Erro ao salvar a imagem.';
                        }
                    }
                }
            }
        }

        if (!$erro) {
            $sql = 'INSERT INTO noticias (titulo, resumo, descricao, autor, categoria, imagem, data_noticia) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param('sssssss', $titulo, $resumo, $descricao, $autor, $categoria, $imagem, $data);
            if ($stmt->execute()) {
                header('Location: noticiascrud.php?ok=1');
                exit;
            }
            if ($arquivoSalvo && is_file($arquivoSalvo)) unlink($arquivoSalvo);
            $erro = 'Erro ao cadastrar notícia.';
        }
    }

    if (isset($_POST['excluir'])) {
        $id = filter_var($_POST['excluir'], FILTER_VALIDATE_INT);
        if ($id && $id > 0) {
            $stmt = $conexao->prepare('DELETE FROM noticias WHERE id_noticia = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            header('Location: noticiascrud.php?ok=2');
            exit;
        }
        $erro = 'ID inválido.';
    }
}

$noticias = $conexao->query('SELECT * FROM noticias ORDER BY id_noticia DESC');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Notícias | IFFar Sports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root { --verde:#004b2c; --escuro:#003b23; --laranja:#ffad14; }
body { background:#f3f5f4; font-family:'Segoe UI',Arial,sans-serif; }
.sidebar { width:223px; height:100vh; position:fixed; background:var(--escuro); color:white; display:flex; flex-direction:column; }
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
.table th { background:#f6f8f6; color:#687b70; font-size:12px; }
.table td,.table th { padding:14px; vertical-align:middle; }
.capa { width:75px; height:55px; object-fit:cover; border-radius:7px; }
.categoria { background:#e8f2ed; color:var(--verde); border-radius:20px; padding:6px 10px; font-size:12px; }
#preview { display:none; max-width:170px; max-height:120px; margin-top:12px; border-radius:8px; }
@media(max-width:750px) { .sidebar { width:65px; } .marca span,.menu span { display:none; } .menu { padding:14px; } .principal { margin-left:65px; } .conteudo { padding:16px; } }
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
            <div><h1>Gerenciar notícias</h1><p class="subtitulo">Crie, edite e exclua publicações do portal.</p></div>
            <a href="index.php" class="btn btn-outline-success">Voltar ao painel</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= limpar($erro) ?></div>
        <?php elseif (isset($_GET['ok'])): ?>
            <div class="alert alert-success">Operação realizada com sucesso.</div>
        <?php endif; ?>

        <section class="painel">
            <div class="painel-titulo"><i class="bi bi-plus-circle me-2"></i>Cadastrar notícia</div>
            <div class="painel-corpo">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= limpar($_SESSION['token_noticias']) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="titulo">Título</label>
                        <input id="titulo" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="resumo">Resumo</label>
                        <textarea id="resumo" name="resumo" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="6" class="form-control" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="categoria">Categoria</label>
                            <select id="categoria" name="categoria" class="form-select" required>
                                <option value="">Selecione</option>
                                <option>Esportes</option><option>Eventos</option>
                                <option>Competições</option><option>Campus</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="imagem">Imagem de capa (até 5 MB)</label>
                            <input id="imagem" type="file" name="imagem" accept="image/jpeg,image/png,image/webp" class="form-control">
                            <img id="preview" alt="Prévia da imagem">
                        </div>
                    </div>
                    <button class="btn-verde" name="cadastrar" value="1"><i class="bi bi-send me-2"></i>Publicar notícia</button>
                </form>
            </div>
        </section>

        <section class="painel">
            <div class="painel-titulo"><i class="bi bi-newspaper me-2"></i>Notícias cadastradas (<?= $noticias->num_rows ?>)</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>ID</th><th>Imagem</th><th>Título</th><th>Categoria</th><th>Autor</th><th>Data</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php while ($noticia = $noticias->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= (int)$noticia['id_noticia'] ?></td>
                            <td>
                                <?php if ($noticia['imagem']): ?>
                                    <img class="capa" src="../<?= limpar($noticia['imagem']) ?>" alt="Capa da notícia">
                                <?php else: ?>Sem imagem<?php endif; ?>
                            </td>
                            <td><strong><?= limpar($noticia['titulo']) ?></strong></td>
                            <td><span class="categoria"><?= limpar($noticia['categoria']) ?></span></td>
                            <td><?= limpar($noticia['autor']) ?></td>
                            <td><?= $noticia['data_noticia'] ? date('d/m/Y H:i', strtotime($noticia['data_noticia'])) : '-' ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-warning" href="editar_noticia.php?id=<?= (int)$noticia['id_noticia'] ?>">Editar</a>
                                    <form method="POST" onsubmit="return confirm('Excluir esta notícia?')">
                                        <input type="hidden" name="token" value="<?= limpar($_SESSION['token_noticias']) ?>">
                                        <button class="btn btn-sm btn-danger" name="excluir" value="<?= (int)$noticia['id_noticia'] ?>">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($noticias->num_rows === 0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Nenhuma notícia cadastrada.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script>
const campo = document.getElementById('imagem');
const preview = document.getElementById('preview');
let urlAnterior;
campo.addEventListener('change', () => {
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
