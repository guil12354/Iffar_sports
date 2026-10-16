
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

function limpar($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = $_SESSION['csrf_token'];

// CADASTRAR OU EXCLUIR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($token, $_POST['csrf_token'] ?? '')) {
        $erro = "Solicitação inválida. Atualize a página.";
    }

    // CADASTRAR
    elseif (isset($_POST['cadastrar'])) {

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

        if ($dados['nome'] === '') {
            $erro = "Informe o nome do esporte.";
        }

        // UPLOAD DA IMAGEM
        if (!$erro && isset($_FILES['imagem']) &&
            $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {

            $arquivo = $_FILES['imagem'];

            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                $erro = "Erro ao enviar a imagem.";
            } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
                $erro = "A imagem deve ter no máximo 5 MB.";
            } else {

                $tipo = (new finfo(FILEINFO_MIME_TYPE))
                    ->file($arquivo['tmp_name']);

                $extensoes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($extensoes[$tipo])) {
                    $erro = "Utilize JPG, PNG ou WEBP.";
                } else {

                    $pasta = "../img/esportes/";

                    if (!is_dir($pasta) &&
                        !mkdir($pasta, 0755, true)) {
                        $erro = "Não foi possível criar a pasta.";
                    } else {

                        $nomeArquivo = bin2hex(random_bytes(12))
                            . "." . $extensoes[$tipo];

                        if (move_uploaded_file(
                            $arquivo['tmp_name'],
                            $pasta . $nomeArquivo
                        )) {
                            $imagem = "img/esportes/" . $nomeArquivo;
                        } else {
                            $erro = "Não foi possível salvar a imagem.";
                        }
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

            $erro = "Não foi possível cadastrar o esporte.";
        }
    }

    // EXCLUIR
    elseif (isset($_POST['excluir'])) {

        $id = filter_var(
            $_POST['excluir'],
            FILTER_VALIDATE_INT
        );

        if ($id && $id > 0) {

            $stmt = $conexao->prepare(
                "DELETE FROM esportes WHERE id_esporte = ?"
            );

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

$esportes = $conexao->query(
    "SELECT * FROM esportes ORDER BY nome ASC"
);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Esportes | IFFar Sports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
:root {
    --verde: #004b2c;
    --escuro: #003b23;
    --laranja: #ffad14;
    --fundo: #f3f5f4;
}

body {
    background: var(--fundo);
    font-family: "Segoe UI", Arial, sans-serif;
    color: #172b22;
}

.sidebar {
    position: fixed;
    width: 223px;
    height: 100vh;
    background: var(--escuro);
    color: white;
    display: flex;
    flex-direction: column;
}

.brand {
    padding: 22px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #ffffff20;
}

.brand-icon {
    background: var(--laranja);
    color: var(--escuro);
    padding: 10px;
    border-radius: 11px;
    font-size: 20px;
}

.brand strong { font-size: 14px; }
.brand small { display: block; color: #afc7b9; font-size: 11px; }

.menu-label {
    color: #83a996;
    font-size: 11px;
    margin: 25px 22px 12px;
}

.menu-link {
    color: #dcece2;
    padding: 13px 15px;
    margin: 3px 10px;
    border-radius: 9px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
}

.menu-link:hover, .menu-link.active {
    background: #ffffff19;
    color: white;
}

.menu-link.active {
    border-left: 4px solid var(--laranja);
}

.sidebar-bottom {
    margin-top: auto;
    padding: 15px 0;
    border-top: 1px solid #ffffff20;
}

.main { margin-left: 223px; }

.topbar {
    background: white;
    border-bottom: 1px solid #e0e8e2;
    padding: 20px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.content {
    padding: 30px;
    max-width: 1450px;
    margin: auto;
}

.page-title {
    font-size: 27px;
    font-weight: 750;
    color: var(--escuro);
}

.panel {
    background: white;
    border: 1px solid #dfe8e2;
    border-radius: 14px;
    margin-top: 25px;
    overflow: hidden;
}

.panel-head {
    padding: 20px 24px;
    border-bottom: 1px solid #e9eeea;
}

.panel-head h2 {
    font-size: 17px;
    font-weight: 700;
    color: var(--escuro);
    margin: 0;
}

.panel-body { padding: 25px; }

.form-label {
    font-size: 13px;
    font-weight: 600;
}

.form-control {
    border-radius: 8px;
    padding: 11px;
}

.form-control:focus {
    border-color: #168456;
    box-shadow: 0 0 0 3px #16845618;
}

.btn-green {
    background: var(--verde);
    color: white;
    border: 0;
    padding: 11px 18px;
    border-radius: 8px;
    font-weight: 600;
}

.btn-green:hover {
    background: var(--escuro);
    color: white;
}

.table thead th {
    background: #f6f8f6;
    font-size: 12px;
    color: #697d70;
    padding: 15px;
}

.table td {
    padding: 15px;
    vertical-align: middle;
    font-size: 13px;
}

.sport-img {
    width: 75px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
}

.preview {
    display: none;
    width: 180px;
    max-height: 130px;
    object-fit: cover;
    border-radius: 8px;
    margin-top: 12px;
}

@media(max-width: 850px) {
    .sidebar { width: 65px; }
    .brand div:last-child, .menu-label, .menu-link span {
        display: none;
    }
    .menu-link { justify-content: center; }
    .main { margin-left: 65px; }
    .content { padding: 18px; }
}
</style>
</head>

<body>

<aside class="sidebar">

    <div class="brand">
        <div class="brand-icon">
            <i class="bi bi-trophy-fill"></i>
        </div>
        <div>
            <strong>IFFar Sports</strong>
            <small>Administração</small>
        </div>
    </div>

    <a href="index.php" class="menu-link mt-3">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
    </a>

    <div class="menu-label">CONTEÚDO</div>

    <a href="noticiascrud.php" class="menu-link">
        <i class="bi bi-newspaper"></i>
        <span>Notícias</span>
    </a>

    <a href="esportescrud.php" class="menu-link active">
        <i class="bi bi-dribbble"></i>
        <span>Esportes</span>
    </a>

    <div class="sidebar-bottom">
        <a href="../index.php" class="menu-link">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Ver portal</span>
        </a>

        <a href="../logout.php" class="menu-link">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>
    </div>

</aside>

<div class="main">

<header class="topbar">
    <strong>Gerenciamento de esportes</strong>
    <span>
        <i class="bi bi-person-circle"></i>
        <?= limpar($_SESSION['nome'] ?? 'Administrador') ?>
    </span>
</header>

<main class="content">

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Gerenciar esportes</h1>
            <p class="text-muted">
                Cadastre e gerencie as modalidades esportivas.
            </p>
        </div>

        <a href="index.php" class="btn btn-outline-success">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if ($erro): ?>
        <div class="alert alert-danger mt-3">
            <?= limpar($erro) ?>
        </div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="alert alert-success mt-3">
            <?= limpar($sucesso) ?>
        </div>
    <?php endif; ?>

    <section class="panel">

        <div class="panel-head">
            <h2>
                <i class="bi bi-plus-circle me-2"></i>
                Cadastrar esporte
            </h2>
        </div>

        <div class="panel-body">

            <form method="POST" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token"
                       value="<?= limpar($token) ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome"
                               class="form-control"
                               placeholder="Ex: Futsal" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Imagem de capa</label>
                        <input type="file" name="imagem" id="imagem"
                               class="form-control"
                               accept="image/jpeg,image/png,image/webp">
                        <img id="preview" class="preview"
                             alt="Prévia da imagem">
                    </div>

                </div>

                <div class="mb-4">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control"
                              rows="3"
                              placeholder="Descrição da modalidade"></textarea>
                </div>

                <?php foreach (['feminino', 'masculino'] as $categoria): ?>

                    <h5 class="mt-4 mb-3">
                        <i class="bi bi-calendar-week me-2"></i>
                        Treino <?= ucfirst($categoria) ?>
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
                                <label class="form-label">
                                    <?= $label ?>
                                </label>

                                <input type="text"
                                       name="<?= $categoria . '_' . $campo ?>"
                                       class="form-control">
                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endforeach; ?>

                <div class="text-end mt-3">
                    <button type="submit" name="cadastrar"
                            value="1" class="btn-green">
                        <i class="bi bi-plus-lg me-1"></i>
                        Cadastrar esporte
                    </button>
                </div>

            </form>

        </div>
    </section>

    <section class="panel">

        <div class="panel-head">
            <h2>
                <i class="bi bi-dribbble me-2"></i>
                Esportes cadastrados
            </h2>
        </div>

        <div class="table-responsive">

            <table class="table align-middle mb-0">

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

                            <td>
                                #<?= (int)$esporte['id_esporte'] ?>
                            </td>

                            <td>
                                <?php if (!empty($esporte['imagem'])): ?>
                                    <img class="sport-img"
                                         src="../<?= limpar($esporte['imagem']) ?>"
                                         alt="Imagem do esporte">
                                <?php else: ?>
                                    <span class="text-muted">Sem imagem</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong>
                                    <?= limpar($esporte['nome']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= limpar($esporte['descricao']) ?>
                            </td>

                            <td>
                                <div class="d-flex gap-2">

                                    <a href="editar_esporte.php?id=<?= (int)$esporte['id_esporte'] ?>"
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Editar
                                    </a>

                                    <form method="POST"
                                          onsubmit="return confirm('Deseja excluir este esporte?')">

                                        <input type="hidden" name="csrf_token"
                                               value="<?= limpar($token) ?>">

                                        <button type="submit" name="excluir"
                                                value="<?= (int)$esporte['id_esporte'] ?>"
                                                class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                            Excluir
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Nenhum esporte cadastrado.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>
</div>

<script>
const input = document.getElementById("imagem");
const preview = document.getElementById("preview");

input.addEventListener("change", function () {
    const arquivo = this.files[0];

    if (preview.dataset.url) {
        URL.revokeObjectURL(preview.dataset.url);
        delete preview.dataset.url;
    }

    if (arquivo && arquivo.type.startsWith("image/")) {
        const url = URL.createObjectURL(arquivo);
        preview.src = url;
        preview.dataset.url = url;
        preview.style.display = "block";
    } else {
        preview.removeAttribute("src");
        preview.style.display = "none";
    }
});
</script>

</body>
</html>
