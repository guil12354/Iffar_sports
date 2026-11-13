<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'administrador') {
    header('Location: ../index.php');
    exit;
}

$conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($conexao->connect_error) {
    die('Erro ao conectar ao banco.');
}
$conexao->set_charset('utf8mb4');

function limpar($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

$erro = '';
$modoCadastro = isset($_GET['novo']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cadastrar'])) {
        $titulo = trim($_POST['titulo'] ?? '');
        $resumo = trim($_POST['resumo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $autor = $_SESSION['nome'] ?? 'Administrador';
        $data = date('Y-m-d H:i:s');
        $imagem = '';
        $arquivoSalvo = '';

        $categoriasValidas = ['Esportes', 'Eventos', 'Competições', 'Campus'];

        if ($titulo == '' || $resumo == '' || $descricao == '' || !in_array($categoria, $categoriasValidas)) {
            $erro = 'Preencha todos os campos obrigatórios.';
        }

        if ($erro == '' && isset($_FILES['imagem']) && $_FILES['imagem']['error'] != UPLOAD_ERR_NO_FILE) {
            $arquivo = $_FILES['imagem'];
            $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if ($arquivo['error'] != UPLOAD_ERR_OK || $arquivo['size'] > 5 * 1024 * 1024 || $arquivo['size'] <= 0) {
                $erro = 'A imagem deve ter até 5 MB.';
            } elseif (!in_array($ext, $permitidas)) {
                $erro = 'Envie uma imagem JPG, PNG ou WEBP válida.';
            } else {
                $pasta = __DIR__ . '/../img/noticias/';
                if (!is_dir($pasta)) {
                    mkdir($pasta, 0755, true);
                }

                $nome = uniqid() . '.' . $ext;
                $arquivoSalvo = $pasta . $nome;

                if (move_uploaded_file($arquivo['tmp_name'], $arquivoSalvo)) {
                    $imagem = 'img/noticias/' . $nome;
                } else {
                    $erro = 'Erro ao salvar a imagem.';
                }
            }
        }

        if ($erro == '') {
            $sql = 'INSERT INTO noticias (titulo, resumo, descricao, autor, categoria, imagem, data_noticia) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param('sssssss', $titulo, $resumo, $descricao, $autor, $categoria, $imagem, $data);

            if ($stmt->execute()) {
                header('Location: noticiascrud.php?ok=1');
                exit;
            }

            if ($arquivoSalvo != '' && is_file($arquivoSalvo)) {
                unlink($arquivoSalvo);
            }
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

$noticias = null;
if (!$modoCadastro) {
    $noticias = $conexao->query('SELECT * FROM noticias ORDER BY id_noticia DESC');
}
?>

<?php $paginaAtual = 'noticias'; $tituloPagina = $modoCadastro ? 'Cadastrar notícia' : 'Gerenciar notícias'; $subtituloPagina = $modoCadastro ? 'Nova publicação do portal' : 'Publicações do portal'; $estilosExtras = '<style>.admin-main .painel{background:#fff;border:1px solid #dce4df;border-radius:14px;margin-top:22px;overflow:hidden}.admin-main .painel-titulo{padding:19px 22px;border-bottom:1px solid #e9eeea;font-weight:700;color:#005126}.admin-main .painel-corpo{padding:24px}.admin-main .capa{width:75px;height:55px;object-fit:cover;border-radius:7px}.admin-main .categoria{background:#eaf4ee;color:#005126;border-radius:20px;padding:6px 10px;font-size:12px}.admin-main .btn-verde,.admin-main .btn-green{background:#005126;color:white;border:0;border-radius:8px;padding:11px 18px}.admin-main .subtitulo{color:#748078;font-size:14px}.admin-main .page-title{color:#005126;font-size:2rem}.admin-main #preview{display:none;max-width:170px;max-height:120px;margin-top:12px;border-radius:8px}.admin-main .sport-card{background:white}</style><link rel="stylesheet" href="css/crud-padrao.css">'; include __DIR__ . '/include/head.php'; include __DIR__ . '/include/nav.php'; ?>
<main class="admin-main"><div class="container-fluid p-0">

        <div class="crud-topo">
            <div><h1><?= $modoCadastro ? 'Cadastrar notícia' : 'Gerenciar notícias' ?></h1><p class="subtitulo"><?= $modoCadastro ? 'Preencha os dados para publicar uma notícia.' : 'Crie, edite e exclua publicações do portal.' ?></p></div>
            <a href="<?= $modoCadastro ? 'noticiascrud.php' : 'index.php' ?>" class="crud-btn-outline"><i class="bi bi-arrow-left me-1"></i><?= $modoCadastro ? 'Voltar à lista' : 'Voltar ao painel' ?></a>
        </div>

        <?php if ($erro != ''): ?>
            <div class="alert alert-danger"><?= limpar($erro) ?></div>
        <?php elseif (isset($_GET['ok'])): ?>
            <div class="alert alert-success">Operação realizada com sucesso.</div>
        <?php endif; ?>

<?php if (!$modoCadastro): ?>
<div class="crud-acoes"><a class="crud-btn-primary" href="noticiascrud.php?novo=1"><i class="bi bi-plus-circle me-1"></i> Nova notícia</a></div>

        <section class="painel">
            <div class="painel-titulo"><i class="bi bi-newspaper me-2"></i>Notícias cadastradas (<?= $noticias->num_rows ?>)</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>ID</th><th>Imagem</th><th>Título</th><th>Categoria</th><th>Autor</th><th>Data</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php while ($noticia = $noticias->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= (int)$noticia['id_noticia'] ?></td>
                            <td>
                                <?php if ($noticia['imagem'] != ''): ?>
                                    <img class="capa" src="../<?= limpar($noticia['imagem']) ?>" alt="Capa da notícia">
                                <?php else: ?>Sem imagem<?php endif; ?>
                            </td>
                            <td><strong><?= limpar($noticia['titulo']) ?></strong></td>
                            <td><span class="categoria"><?= limpar($noticia['categoria']) ?></span></td>
                            <td><?= limpar($noticia['autor']) ?></td>
                            <td><?= $noticia['data_noticia'] ? date('d/m/Y H:i', strtotime($noticia['data_noticia'])) : '-' ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-warning" href="editar_noticia.php?id=<?= (int)$noticia['id_noticia'] ?>"><i class="bi bi-pencil"></i> Editar</a>
                                    <form method="POST" onsubmit="return confirm('Excluir esta notícia?')">
                                        <button class="btn btn-sm btn-danger" name="excluir" value="<?= (int)$noticia['id_noticia'] ?>"><i class="bi bi-trash"></i> Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($noticias->num_rows == 0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Nenhuma notícia cadastrada.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
<?php else: ?>

        <section class="painel">
            <div class="painel-titulo"><i class="bi bi-plus-circle me-2"></i>Cadastrar notícia</div>
            <div class="painel-corpo">
                <form method="POST" action="noticiascrud.php?novo=1" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label" for="titulo">Título</label>
                        <input id="titulo" name="titulo" class="form-control" value="<?= limpar($_POST['titulo'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="resumo">Resumo</label>
                        <textarea id="resumo" name="resumo" rows="3" class="form-control" required><?= limpar($_POST['resumo'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="6" class="form-control" required><?= limpar($_POST['descricao'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="categoria">Categoria</label>
                            <select id="categoria" name="categoria" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach (['Esportes', 'Eventos', 'Competições', 'Campus'] as $opcao): ?>
                                    <option value="<?= limpar($opcao) ?>" <?= ($_POST['categoria'] ?? '') == $opcao ? 'selected' : '' ?>><?= limpar($opcao) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="imagem">Imagem de capa (até 5 MB)</label>
                            <input id="imagem" type="file" name="imagem" accept="image/jpeg,image/png,image/webp" class="form-control">
                            <img id="preview" alt="Prévia da imagem">
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn-verde" name="cadastrar" value="1"><i class="bi bi-send me-2"></i>Publicar notícia</button>
                        <a href="noticiascrud.php" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </section>
<?php endif; ?>

</div></main>
<script>
const campo = document.getElementById('imagem');
const preview = document.getElementById('preview');
if (campo) {
    campo.addEventListener('change', function () {
        const arquivo = campo.files[0];
        if (arquivo && arquivo.type.startsWith('image/')) {
            preview.src = URL.createObjectURL(arquivo);
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
            preview.removeAttribute('src');
        }
    });
}
</script>

<?php include __DIR__ . '/include/foot.php'; ?>