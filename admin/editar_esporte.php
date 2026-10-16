<?php
session_start();
if (($_SESSION['nivel'] ?? '') !== 'administrador') {
    header('Location: ../login.php');
    exit;
}
function limpar($valor) { return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8'); }
$erro = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { header('Location: esportescrud.php'); exit; }
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');
    $conexao->set_charset('utf8mb4');
    $stmt = $conexao->prepare('SELECT * FROM esportes WHERE id_esporte = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $esporte = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$esporte) { throw new Exception('Esporte não encontrado.'); }
    if (empty($_SESSION['token_esportes'])) { $_SESSION['token_esportes'] = bin2hex(random_bytes(16)); }
    $valores = $esporte;
    $campos = ['nome','descricao','feminino_dias','feminino_horario','feminino_local','feminino_professor','masculino_dias','masculino_horario','masculino_local','masculino_professor'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
        if (!hash_equals($_SESSION['token_esportes'], $_POST['token'] ?? '')) { throw new Exception('Solicitação inválida. Atualize a página.'); }
        foreach ($campos as $campo) { $valores[$campo] = trim($_POST[$campo] ?? ''); }
        if ($valores['nome'] === '') { $erro = 'Informe o nome do esporte.'; }
        $imagem = $esporte['imagem'] ?? '';
        $arquivoSalvo = null;
        if (!$erro && isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $arquivo = $_FILES['imagem'];
            if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] <= 0 || $arquivo['size'] > 5 * 1024 * 1024) {
                $erro = 'A imagem deve ter até 5 MB.';
            } else {
                $tipos = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
                if (!isset($tipos[$tipo]) || !getimagesize($arquivo['tmp_name'])) {
                    $erro = 'Envie uma imagem JPG, PNG ou WEBP válida.';
                } else {
                    $pasta = __DIR__ . '/../img/esportes/';
                    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                        $erro = 'Não foi possível criar a pasta de imagens.';
                    } else {
                        $nomeArquivo = bin2hex(random_bytes(12)) . '.' . $tipos[$tipo];
                        $arquivoSalvo = $pasta . $nomeArquivo;
                        if (move_uploaded_file($arquivo['tmp_name'], $arquivoSalvo)) {
                            $imagem = 'img/esportes/' . $nomeArquivo;
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
                $sql = 'UPDATE esportes SET nome=?, imagem=?, descricao=?, feminino_dias=?, feminino_horario=?, feminino_local=?, feminino_professor=?, masculino_dias=?, masculino_horario=?, masculino_local=?, masculino_professor=? WHERE id_esporte=?';
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param('sssssssssssi', $valores['nome'], $imagem, $valores['descricao'], $valores['feminino_dias'], $valores['feminino_horario'], $valores['feminino_local'], $valores['feminino_professor'], $valores['masculino_dias'], $valores['masculino_horario'], $valores['masculino_local'], $valores['masculino_professor'], $id);
                $stmt->execute();
                $stmt->close();
                header('Location: esportescrud.php?ok=1');
                exit;
            } catch (Throwable $ex) {
                if ($arquivoSalvo && is_file($arquivoSalvo)) { unlink($arquivoSalvo); }
                throw $ex;
            }
        }
    }
} catch (Throwable $ex) {
    http_response_code(500);
    echo '<div style="font:16px Arial;padding:24px"><h2>Erro ao editar esporte</h2><p>' . limpar($ex->getMessage()) . '</p></div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar esporte | IFFar Sports</title>
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
.capa { width:170px; height:110px; object-fit:cover; border-radius:8px; }
#preview { display:none; max-width:170px; max-height:120px; margin-top:12px; border-radius:8px; }
@media(max-width:750px) { .sidebar { width:65px; } .marca span,.menu span { display:none; } .menu { padding:14px; } .principal { margin-left:65px; } .conteudo { padding:16px; } .topo { padding:18px 14px; } }
</style>
</head>
<body>
<aside class="sidebar">
    <div class="marca"><i class="bi bi-trophy-fill"></i><span>IFFar Sports</span></div>
    <a class="menu mt-3" href="index.php"><i class="bi bi-grid"></i><span>Dashboard</span></a>
    <a class="menu" href="noticiascrud.php"><i class="bi bi-newspaper"></i><span>Notícias</span></a>
    <a class="menu ativo" href="esportescrud.php"><i class="bi bi-dribbble"></i><span>Esportes</span></a>
    <div class="rodape-menu">
        <a class="menu" href="../index.php"><i class="bi bi-box-arrow-up-right"></i><span>Ver portal</span></a>
        <a class="menu" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a>
    </div>
</aside>
<div class="principal">
    <header class="topo">Gerenciamento de esportes <span class="float-end"><i class="bi bi-person-circle"></i> Administrador</span></header>
    <main class="conteudo">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><h1>Editar esporte</h1><p class="subtitulo">Atualize as informações e horários de treino de <?= limpar($esporte['nome']) ?>.</p></div>
            <a href="esportescrud.php" class="btn btn-outline-success"><i class="bi bi-arrow-left me-1"></i>Voltar aos esportes</a>
        </div>
        <?php if ($erro): ?><div class="alert alert-danger mt-3"><?= limpar($erro) ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= limpar($_SESSION['token_esportes']) ?>">
            <section class="painel">
                <div class="painel-titulo"><i class="bi bi-pencil-square me-2"></i>Informações do esporte</div>
                <div class="painel-corpo">
                    <div class="mb-3"><label class="form-label" for="nome">Nome do esporte</label><input id="nome" type="text" name="nome" class="form-control" value="<?= limpar($valores['nome']) ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="descricao">Descrição</label><textarea id="descricao" name="descricao" rows="5" class="form-control"><?= limpar($valores['descricao']) ?></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Imagem atual</label>
                        <div><?php if (!empty($esporte['imagem'])): ?><img class="capa" src="../<?= limpar($esporte['imagem']) ?>" alt="Imagem atual do esporte"><?php else: ?><span class="text-muted">Sem imagem cadastrada.</span><?php endif; ?></div>
                    </div>
                    <div class="mb-2"><label class="form-label" for="imagem">Trocar imagem (até 5 MB)</label><input id="imagem" type="file" name="imagem" accept="image/jpeg,image/png,image/webp" class="form-control"><small class="text-muted">Se não selecionar outra imagem, a atual será mantida.</small><div><img id="preview" alt="Prévia da nova imagem"></div></div>
                </div>
            </section>
            <?php foreach (['feminino' => 'Feminino', 'masculino' => 'Masculino'] as $prefixo => $titulo): ?>
            <section class="painel">
                <div class="painel-titulo"><i class="bi bi-calendar-week me-2"></i>Treino <?= $titulo ?></div>
                <div class="painel-corpo"><div class="row">
                    <?php foreach (['dias'=>'Dias', 'horario'=>'Horário', 'local'=>'Local', 'professor'=>'Professor'] as $sufixo => $rotulo): $campo = $prefixo . '_' . $sufixo; ?>
                    <div class="col-md-6 mb-3"><label class="form-label" for="<?= $campo ?>"><?= $rotulo ?></label><input type="text" id="<?= $campo ?>" name="<?= $campo ?>" class="form-control" value="<?= limpar($valores[$campo]) ?>"></div>
                    <?php endforeach; ?>
                </div></div>
            </section>
            <?php endforeach; ?>
            <div class="d-flex gap-2 mt-4 mb-4 flex-wrap">
                <button type="submit" name="editar" value="1" class="btn-verde"><i class="bi bi-check-circle me-2"></i>Salvar alterações</button>
                <a href="esportescrud.php" class="btn btn-outline-secondary px-4 py-2">Cancelar</a>
            </div>
        </form>
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
