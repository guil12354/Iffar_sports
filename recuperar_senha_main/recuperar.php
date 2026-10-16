<?php
require_once __DIR__ . '/config_smtp.php';

// Diagnóstico local: deixe false quando publicar o sistema.
$modoTeste = true;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: recuperar_senha.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Informe um e-mail válido.');
}

try {
  $db = new mysqli("localhost", "root", "", "iffar_sports3");

if ($db->connect_error) {
    throw new Exception("Erro na conexão com o banco de dados.");
}

$db->set_charset("utf8mb4");
    $stmt = $db->prepare('SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $existe = $stmt->get_result()->fetch_assoc();

    if ($existe) {
        // Procura a biblioteca nos dois locais comuns do projeto.
        $pastas = [
            __DIR__ . '/../phpmailer/src/',
            __DIR__ . '/../PHPMailer/src/',
            __DIR__ . '/PHPMailer/src/',
            __DIR__ . '/phpmailer/src/'
        ];
        $pastaLib = null;
        foreach ($pastas as $pasta) {
            if (is_file($pasta . 'PHPMailer.php') &&
                is_file($pasta . 'SMTP.php') &&
                is_file($pasta . 'Exception.php')) {
                $pastaLib = $pasta;
                break;
            }
        }
        if ($pastaLib === null) {
            throw new RuntimeException(
                'PHPMailer não encontrado. Coloque os arquivos PHPMailer.php, SMTP.php e Exception.php em TCC3/phpmailer/src/.'
            );
        }

        require_once $pastaLib . 'Exception.php';
        require_once $pastaLib . 'PHPMailer.php';
        require_once $pastaLib . 'SMTP.php';

        $config = require __DIR__ . '/config_email.php';
        if (empty($config['usuario']) || empty($config['senha'])) {
            throw new RuntimeException(
                'SMTP não configurado: defina IFFAR_SMTP_USER e IFFAR_SMTP_PASS no ambiente do Apache/WampServer.'
            );
        }

        $token = bin2hex(random_bytes(32));
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config['usuario'];
        $mail->Password = $config['senha'];
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($config['remetente'] ?: $config['usuario'], 'IFFar Sports');
        $mail->addAddress($email);

        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $protocolo = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $caminho = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $link = $protocolo . '://' . $host . $caminho . '/nova_senha.php?email=' .
            rawurlencode($email) . '&token=' . rawurlencode($token);

        $mail->Subject = 'Recuperação de senha - IFFar Sports';
        $mail->Body = "Para redefinir sua senha, acesse:\n" . $link . "\n\nEste link expira em 24 horas.";
       $mail->send();

// Salvar o token no banco
$stmt = $db->prepare(
    'INSERT INTO recuperar_senha (email, token, usado, data)
     VALUES (?, ?, 0, NOW())'
);

$stmt->bind_param('ss', $email, $token);
$stmt->execute();

        // Grava o token somente depois do envio bem-sucedido.
        $stmt = $db->prepare('INSERT INTO recuperar_senha (email, token, usado, data) VALUES (?, ?, 0, NOW())');
        $stmt->bind_param('ss', $email, $token);
        $stmt->execute();
    }
} catch (Throwable $e) {
    error_log('Recuperação IFFar Sports: ' . $e->getMessage());
    http_response_code(500);
    if ($modoTeste) {
        exit('<h3>Erro no ambiente local:</h3><pre>' .
            htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>');
    }
    exit('Não foi possível processar a recuperação. Tente novamente mais tarde.');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recuperação solicitada | IFFar Sports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="estilo.css">
</head>
<body>
<main class="card">
<h3>Confira seu e-mail</h3>
<p>Se houver uma conta cadastrada, você receberá as instruções para recuperar sua senha.</p>
<a href="../login.php">Voltar ao login</a>
</main>
</body>
</html>
