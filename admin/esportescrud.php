<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

if (($_SESSION['nivel'] ?? '') !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");

if ($conexao->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

$conexao->set_charset("utf8mb4");

$erro = "";
$sucesso = "";
$modoCadastro = isset($_GET["novo"]);

function limpar($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// CADASTRAR OU EXCLUIR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CADASTRAR
    if (isset($_POST['cadastrar'])) {

        $campos = [
            'nome', 'descricao',
            'feminino_dias', 'feminino_horario',
            'feminino_local', 'feminino_professor',
            'masculino_dias', 'masculino_horario',
            'masculino_local', 'masculino_professor'
        ];

        $dados = [];
        foreach ($campos as $campo) {
            $dados[$campo] = trim($_POST[$campo] ?? '');
        }

        $imagem = "";
        $arquivoSalvo = "";

        if ($dados['nome'] === '') {
            $erro = "Informe o nome do esporte.";
        }

        // UPLOAD DA IMAGEM
        if (!$erro && isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $arquivo = $_FILES['imagem'];
            $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                $erro = "Erro ao enviar a imagem.";
            } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
                $erro = "A imagem deve ter no máximo 5 MB.";
            } elseif (!in_array($ext, $permitidas, true)) {
                $erro = "Utilize JPG, PNG ou WEBP.";
            } else {
                $pasta = "../img/esportes/";
                if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                    $erro = "Não foi possível criar a pasta.";
                } else {
                    $nomeArquivo = uniqid() . "." . $ext;
                    if (move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeArquivo)) {
                        $imagem = "img/esportes/" . $nomeArquivo;
                        $arquivoSalvo = $pasta . $nomeArquivo;
                    } else {
                        $erro = "Não foi possível salvar a imagem.";
                    }
                }
            }
        }

        if (!$erro) {
            $sql = "INSERT INTO esportes (
                nome, imagem, descricao,
                feminino_dias, feminino_horario,
                feminino_local, feminino_professor,
                masculino_dias, masculino_horario,
                masculino_local, masculino_professor
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conexao->prepare($sql);
            $stmt->bind_param(
                "sssssssssss",
                $dados['nome'],
                $imagem,
                $dados['descricao'],
                $dados['feminino_dias'],
                $dados['feminino_horario'],
                $dados['feminino_local'],
                $dados['feminino_professor'],
                $dados['masculino_dias'],
                $dados['masculino_horario'],
                $dados['masculino_local'],
                $dados['masculino_professor']
            );

            if ($stmt->execute()) {
                header("Location: esportescrud.php?sucesso=1");
                exit;
            }

            if ($arquivoSalvo && is_file($arquivoSalvo)) unlink($arquivoSalvo);
            $erro = "Não foi possível cadastrar o esporte.";
        }
    }

    // EXCLUIR
    elseif (isset($_POST['excluir'])) {
        $id = filter_var($_POST['excluir'], FILTER_VALIDATE_INT);

        if ($id && $id > 0) {
            $stmt = $conexao->prepare("DELETE FROM esportes WHERE id_esporte = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                header("Location: esportescrud.php?sucesso=2");
                exit;
            }

            $erro = "Não foi possível excluir o esporte.";
        } else {
            $erro = "Esporte inválido.";
        }
    }
}

if (isset($_GET['sucesso'])) {
    $sucesso = $_GET['sucesso'] == 1
        ? "Esporte cadastrado com sucesso!"
        : "Esporte excluído com sucesso!";
}

$esportes = $conexao->query("SELECT * FROM esportes ORDER BY nome ASC");
?>

<?php $paginaAtual = 'esportes'; $tituloPagina = $modoCadastro ? 'Cadastrar esporte' : 'Gerenciar esportes'; $subtituloPagina = 'Modalidades esportivas'; $estilosExtras = '<style>.admin-main .painel{background:#fff;border:1px solid #dce4df;border-radius:14px;margin-top:22px;overflow:hidden}.admin-main .painel-titulo{padding:19px 22px;border-bottom:1px solid #e9eeea;font-weight:700;color:#005126;font-size:14px}.admin-main .painel-corpo{padding:24px}.admin-main .capa{width:75px;height:55px;object-fit:cover;border-radius:7px}.admin-main .categoria{background:#eaf4ee;color:#005126;border-radius:20px;padding:6px 10px;font-size:12px}.admin-main .btn-verde,.admin-main .btn-green{background:#005126;color:white;border:0;border-radius:8px;padding:11px 18px}.admin-main .subtitulo{color:#748078;font-size:14px}.admin-main .page-title{color:#005126;font-size:2rem}.admin-main #preview{display:none;max-width:170px;max-height:120px;margin-top:12px;border-radius:8px}.admin-main .sport-card{background:white}</style><link rel="stylesheet" href="css/crud-padrao.css">'; include __DIR__ . '/include/head.php'; include __DIR__ . '/include/nav.php'; ?>
<main class="admin-main"><div class="container-fluid p-0">

    <div class="crud-topo">
        <div>
            <h1><?= $modoCadastro ? 'Cadastrar esporte' : 'Gerenciar esportes' ?></h1>
            <p><?= $modoCadastro ? 'Preencha os dados da modalidade esportiva.' : 'Cadastre e gerencie as modalidades esportivas.' ?></p>
        </div>
        <a href="<?= $modoCadastro ? 'esportescrud.php' : 'index.php' ?>" class="crud-btn-outline">
            <i class="bi bi-arrow-left"></i> <?= $modoCadastro ? 'Voltar à lista' : 'Voltar ao painel' ?>
        </a>
    </div>

    <?php if ($erro): ?>
        <div class="alert alert-danger mt-3"><?= limpar($erro) ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="alert alert-success mt-3"><?= limpar($sucesso) ?></div>
    <?php endif; ?>

<?php if (!$modoCadastro): ?>
    <div class="crud-acoes"><a class="crud-btn-primary" href="esportescrud.php?novo=1"><i class="bi bi-plus-circle"></i> Novo esporte</a></div>

    <section class="painel">
        <div class="painel-titulo"><i class="bi bi-dribbble me-2"></i>Esportes cadastrados (<?= $esportes ? $esportes->num_rows : 0 ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($esportes && $esportes->num_rows > 0): ?>
                    <?php while ($esporte = $esportes->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= (int)$esporte['id_esporte'] ?></td>
                            <td>
                                <?php if (!empty($esporte['imagem'])): ?>
                                    <img class="sport-img" src="../<?= limpar($esporte['imagem']) ?>" alt="Imagem do esporte">
                                <?php else: ?>
                                    <span class="text-muted">Sem imagem</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= limpar($esporte['nome']) ?></strong></td>
                            <td><?= limpar($esporte['descricao']) ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="editar_esporte.php?id=<?= (int)$esporte['id_esporte'] ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Deseja excluir este esporte?')">
                                        <button type="submit" name="excluir" value="<?= (int)$esporte['id_esporte'] ?>" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhum esporte cadastrado.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php else: ?>

    <section class="painel">
        <div class="painel-titulo"><i class="bi bi-plus-circle me-2"></i>Cadastrar esporte</div>
        <div class="painel-corpo">
            <form method="POST" action="esportescrud.php?novo=1" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Futsal" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Imagem de capa</label>
                        <input type="file" name="imagem" id="imagem" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <img id="preview" class="preview" alt="Prévia da imagem">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3" placeholder="Descrição da modalidade"></textarea>
                </div>

                <?php foreach (['feminino', 'masculino'] as $categoria): ?>
                    <h5 class="mt-4 mb-3">
                        <i class="bi bi-calendar-week me-2"></i> Treino <?= ucfirst($categoria) ?>
                    </h5>
                    <div class="row">
                        <?php
                        $camposTreino = [
                            'dias' => 'Dias',
                            'horario' => 'Horário',
                            'local' => 'Local',
                            'professor' => 'Professor'
                        ];
                        ?>
                        <?php foreach ($camposTreino as $campo => $label): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= $label ?></label>
                                <input type="text" name="<?= $categoria . '_' . $campo ?>" class="form-control">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <div class="crud-form-actions">
                    <button type="submit" name="cadastrar" value="1" class="crud-btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Cadastrar esporte
                    </button>
                </div>
            </form>
        </div>
    </section>
<?php endif; ?>

</div></main>
<script>
const input = document.getElementById("imagem");
const preview = document.getElementById("preview");

input?.addEventListener("change", function () {
    const arquivo = this.files[0];
    if (arquivo && arquivo.type.startsWith("image/")) {
        preview.src = URL.createObjectURL(arquivo);
        preview.style.display = "block";
    } else {
        preview.removeAttribute("src");
        preview.style.display = "none";
    }
});
</script>

<?php include __DIR__ . '/include/foot.php'; ?>