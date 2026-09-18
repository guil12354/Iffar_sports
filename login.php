<?php

session_start();

$conexao = new mysqli("localhost", "root", "", "iffar_sports3");
$conexao->set_charset("utf8mb4");

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$erro = "";

if (isset($_POST['entrar'])) {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nivel'] = $usuario['nivel'];

            if ($usuario['nivel'] == 'administrador') {

                header("Location: admin/index.php");
                exit;

            } else {

                header("Location: index.php");
                exit;

            }

        } else {

            $erro = "E-mail ou senha incorretos.";

        }

    } else {

        $erro = "E-mail ou senha incorretos.";

    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - IFFar Sports</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card mt-5">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4">
                        IFFar Sports
                    </h2>

                    <h4 class="text-center mb-4">
                        Entrar
                    </h4>

                    <?php if (!empty($erro)): ?>

                        <div class="alert alert-danger">
                            <?= $erro ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                E-mail
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Senha
                            </label>

                            <input
                                type="password"
                                name="senha"
                                class="form-control"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            name="entrar"
                            class="btn btn-success w-100"
                        >
                            Entrar
                        </button>

                    </form>

                    <div class="text-center mt-3">

                        <a href="index.php">
                            Voltar para o site
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>