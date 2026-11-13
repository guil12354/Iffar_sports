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
    header('Location: esportescrud.php');
    exit;
}

$conexao = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($conexao->connect_error) {
    die('Erro na conexão com o banco de dados.');
}
$conexao->set_charset('utf8mb4');

$stmt = $conexao->prepare('SELECT * FROM esportes WHERE id_esporte = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$esporte = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$esporte) {
    header('Location: esportescrud.php');
    exit;
}

$valores = $esporte;
$campos = ['nome', 'descricao', 'feminino_dias', 'feminino_horario', 'feminino_local', 'feminino_professor', 'masculino_dias', 'masculino_horario', 'masculino_local', 'masculino_professor'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    foreach ($campos as $campo) {
        $valores[$campo] = trim($_POST[$campo] ?? '');
    }

    if ($valores['nome'] === '') {
        $erro = 'Informe o nome do esporte.';
    }

    $imagem = $esporte['imagem'] ?? '';
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
            $pasta = __DIR__ . '/../img/esportes/';
            if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                $erro = 'Não foi possível criar a pasta de imagens.';
            } else {
                $nomeArquivo = uniqid() . '.' . $ext;
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

    if (!$erro) {
        $sql = 'UPDATE esportes SET nome=?, imagem=?, descricao=?, feminino_dias=?, feminino_horario=?, feminino_local=?, feminino_professor=?, masculino_dias=?, masculino_horario=?, masculino_local=?, masculino_professor=? WHERE id_esporte=?';
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('sssssssssssi', $valores['nome'], $imagem, $valores['descricao'], $valores['feminino_dias'], $valores['feminino_horario'], $valores['feminino_local'], $valores['feminino_professor'], $valores['masculino_dias'], $valores['masculino_horario'], $valores['masculino_local'], $valores['masculino_professor'], $id);

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: esportescrud.php?ok=1');
            exit;
        }

        if ($arquivoSalvo && is_file($arquivoSalvo)) {
            unlink($arquivoSalvo);
        }
        $erro = 'Erro ao salvar as alterações.';
    }
}
?>

<?php $paginaAtual = 'esportes'; $tituloPagina = 'Gerenciar esportes'; $subtituloPagina = 'Modalidades esportivas'; $estilosExtras = '<style>.admin-main .painel{background:#fff;border:1px solid #dce4df;border-radius:14px;margin-top:22px;overflow:hidden}.admin-main .painel-titulo{padding:19px 22px;border-bottom:1px solid #e9eeea;font-weight:700;color:#005126}.admin-main .painel-corpo{padding:24px}.admin-main .capa{width:75px;height:55px;object-fit:cover;border-radius:7px}.admin-main .categoria{background:#eaf4ee;color:#005126;border-radius:20px;padding:6px 10px;font-size:12px}.admin-main .btn-verde,.admin-main .btn-green{background:#005126;color:white;border:0;border-radius:8px;padding:11px 18px}.admin-main .subtitulo{color:#748078;font-size:14px}.admin-main .page-title{color:#005126;font-size:2rem}.admin-main #preview{display:none;max-width:170px;max-height:120px;margin-top:12px;border-radius:8px}.admin-main .sport-card{background:white}</style>'; include __DIR__ . '/include/head.php'; include __DIR__ . '/include/nav.php'; ?>
<main class="admin-main"><div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><h1>Editar esporte</h1><p class="subtitulo">Atualize as informações e horários de treino de <?= limpar($esporte['nome']) ?>.</p></div>
            <a href="esportescrud.php" class="btn btn-outline-success"><i class="bi bi-arrow-left me-1"></i>Voltar aos esportes</a>
        </div>
        <?php if ($erro): ?><div class="alert alert-danger mt-3"><?= limpar($erro) ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
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
    
</div></main>
<script>
const campo = document.getElementById('imagem');
const preview = document.getElementById('preview');
campo.addEventListener('change', () => {
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