

<?php

session_start();



$conexao = new mysqli("localhost", "root", "", "iffar_sports3");



if ($conexao->connect_error) {

    die("Erro ao conectar ao banco de dados.");

}



$conexao->set_charset("utf8mb4");



$erro = "";



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["entrar"])) {



    $email = trim($_POST["email"] ?? "");

    $senha = $_POST["senha"] ?? "";



    if ($email === "" || $senha === "") {

        $erro = "Preencha o e-mail e a senha.";

    } else {



        $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";

        $stmt = $conexao->prepare($sql);



        $stmt->bind_param("s", $email);

        $stmt->execute();



        $resultado = $stmt->get_result();

        $usuario = $resultado->fetch_assoc();



        if ($usuario && password_verify($senha, $usuario["senha"])) {



            session_regenerate_id(true);



            $_SESSION["id_usuario"] = $usuario["id_usuario"];

            $_SESSION["nome"] = $usuario["nome"];

            $_SESSION["email"] = $usuario["email"];

            $_SESSION["nivel"] = $usuario["nivel"];



            if ($usuario["nivel"] === "administrador") {

                header("Location: admin/index.php");

            } else {

                header("Location: index.php");

            }



            exit;



        } else {

            $erro = "E-mail ou senha incorretos.";

        }



        $stmt->close();

    }

}



$conexao->close();

?>



<!DOCTYPE html>

<html lang="pt-BR">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <title>Entrar | IFFar Sports</title>



    <link

        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"

        rel="stylesheet"

    >



    <link

        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"

        rel="stylesheet"

    >



    <style>

        :root {

            --verde: #004b2c;

            --verde-escuro: #003b23;

            --verde-claro: #e8f2ed;

            --laranja: #ffad14;

            --fundo: #f3f5f4;

            --texto: #172b22;

            --cinza: #75847c;

        }



        * {

            box-sizing: border-box;

        }



        body {

            margin: 0;

            min-height: 100vh;

            min-height: 100dvh;

            background: var(--fundo);

            font-family: "Segoe UI", Arial, sans-serif;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 24px;

        }



        .login-container {

            width: 100%;

            max-width: 440px;

        }



        .login-logo {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 12px;

            margin-bottom: 28px;

        }



        .logo-icon {

            width: 52px;

            height: 52px;

            border-radius: 14px;

            background: var(--laranja);

            color: var(--verde-escuro);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 26px;

        }



        .logo-text h1 {

            color: var(--verde-escuro);

            font-size: 24px;

            font-weight: 750;

            margin: 0;

        }



        .logo-text span {

            display: block;

            color: var(--cinza);

            font-size: 13px;

            margin-top: 3px;

        }



        .login-card {

            background: #ffffff;

            border: 1px solid #e0e8e2;

            border-radius: 16px;

            padding: 36px;

            box-shadow: 0 12px 35px rgba(0, 50, 25, 0.055);

        }



        .login-header {

            text-align: center;

            margin-bottom: 30px;

        }



        .login-header h2 {

            font-size: 25px;

            font-weight: 750;

            color: var(--verde-escuro);

            margin-bottom: 8px;

        }



        .login-header p {

            font-size: 14px;

            color: var(--cinza);

            margin: 0;

            line-height: 1.6;

        }



        .form-label {

            font-size: 13px;

            font-weight: 600;

            color: var(--texto);

            margin-bottom: 8px;

        }



        .input-group {

            position: relative;

            margin-bottom: 20px;

        }



        .input-icon {

            position: absolute;

            left: 15px;

            top: 50%;

            transform: translateY(-50%);

            color: #899b90;

            font-size: 17px;

            z-index: 5;

            pointer-events: none;

        }



        .form-control {

            height: 48px;

            border-radius: 9px !important;

            border: 1px solid #dae3dd;

            padding-left: 44px;

            font-size: 14px;

            color: var(--texto);

            background: #ffffff;

            transition: all 0.2s;

        }



        .form-control:focus {

            border-color: #168456;

            box-shadow: 0 0 0 3px rgba(22, 132, 86, 0.12);

        }



        .form-control::placeholder {

            color: #a1ada5;

        }



        .senha-input {

            padding-right: 46px;

        }



        .toggle-password {

            position: absolute;

            right: 13px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #819087;

            font-size: 18px;

            cursor: pointer;

            z-index: 5;

            padding: 4px;

        }



        .toggle-password:hover {

            color: var(--verde);

        }



        .btn-entrar {

            width: 100%;

            height: 48px;

            background: var(--verde);

            border: 1px solid var(--verde);

            color: white;

            font-size: 14px;

            font-weight: 600;

            border-radius: 9px;

            margin-top: 5px;

            transition: all 0.2s;

        }



        .btn-entrar:hover,

        .btn-entrar:focus {

            background: var(--verde-escuro);

            color: white;

            border-color: var(--verde-escuro);

            box-shadow: 0 4px 12px rgba(0, 75, 44, 0.16);

        }



        .btn-entrar i {

            margin-right: 7px;

        }



        .login-footer {

            text-align: center;

            border-top: 1px solid #edf0ee;

            margin-top: 28px;

            padding-top: 22px;

        }



        .login-footer a {

            text-decoration: none;

            color: var(--verde);

            font-size: 13px;

            font-weight: 600;

            transition: color 0.2s;

        }



        .login-footer a:hover {

            color: #008452;

        }



        .login-footer i {

            margin-right: 6px;

        }



        .copyright {

            text-align: center;

            color: #89958c;

            font-size: 12px;

            margin-top: 24px;

        }



        .alert {

            border-radius: 9px;

            font-size: 13px;

            margin-bottom: 22px;

        }



        @media (max-width: 480px) {

            body {

                padding: 16px;

            }



            .login-card {

                padding: 26px 22px;

            }



            .login-header h2 {

                font-size: 22px;

            }

        }


        /* Ajustes de alinhamento e bordas do formulário */
        .login-card {
            border-radius: 18px;
            padding: 34px 36px;
        }
        .login-header { margin-bottom: 28px; }
        .login-card form { width: 100%; }
        .login-card .input-group {
            display: block;
            width: 100%;
            position: relative;
            margin-bottom: 20px;
        }
        .login-card .input-group > .form-control {
            display: block;
            width: 100%;
            min-width: 0;
            height: 50px;
            border: 1px solid #d6e1da;
            border-radius: 10px !important;
            padding: 0 46px;
            box-shadow: none;
        }
        .login-card .input-group > .form-control:focus {
            border-color: #168456;
            box-shadow: 0 0 0 3px rgba(22,132,86,.13);
        }
        .login-card .input-icon { left: 16px; z-index: 2; }
        .login-card .toggle-password {
            right: 10px;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            z-index: 3;
        }
        .login-card .toggle-password:focus-visible {
            outline: 2px solid #168456;
            outline-offset: 2px;
        }
        .login-card .btn-entrar {
            width: 100%;
            height: 50px;
            border-radius: 10px;
        }
        @media (max-width: 480px) {
            .login-card { padding: 28px 22px; }
        }

    </style>



                

           
</head>



<body>



    <main class="login-container">



        <div class="login-logo">



            <div class="logo-icon">

                <i class="bi bi-trophy-fill"></i>

            </div>



            <div class="logo-text">

                <h1>IFFar Sports</h1>

                <span>Portal de atividades esportivas</span>

            </div>



        </div>



        <section class="login-card">



            <div class="login-header">

                <h2>Bem-vindo de volta!</h2>

                <p>

                    Entre com suas credenciais para acessar

                    o sistema IFFar Sports.

                </p>

            </div>



            <?php if (!empty($erro)): ?>

                <div class="alert alert-danger" role="alert">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>

                </div>

            <?php endif; ?>



            <form method="POST" action="">



                <label for="email" class="form-label">

                    E-mail

                </label>



                <div class="input-group">

                    <i class="bi bi-envelope input-icon"></i>



                    <input

                        type="email"

                        id="email"

                        name="email"

                        class="form-control"

                        placeholder="Digite seu e-mail"

                        autocomplete="username"

                        maxlength="254"

                        required

                    >

                </div>



                <label for="senha" class="form-label">

                    Senha

                </label>



                <div class="input-group">

                    <i class="bi bi-lock input-icon"></i>



                    <input

                        type="password"

                        id="senha"

                        name="senha"

                        class="form-control senha-input"

                        placeholder="Digite sua senha"

                        autocomplete="current-password"

                        required

                    >



                    <button

                        type="button"

                        class="toggle-password"

                        id="toggleSenha"

                        aria-label="Mostrar senha"

                        aria-pressed="false"

                    >

                        <i class="bi bi-eye"></i>

                    </button>

                </div>



                <button

                    type="submit"

                    name="entrar"

                    value="1"

                    class="btn btn-entrar"

                >

                    <i class="bi bi-box-arrow-in-right"></i>

                    Entrar no sistema

                </button>



            </form>
            <div class="text-center mt-3"><a href="recuperar_senha_main/recuperar_senha.php" style="color:#004b2c;font-size:13px;font-weight:600;text-decoration:none"><i class="bi bi-key"></i> Esqueci minha senha</a></div>
            <div class="login-footer">

                <a href="index.php">

                    <i class="bi bi-arrow-left"></i>

                    Voltar para o portal

                </a>

            </div>



        </section>



        <div class="copyright">

            &copy; <?= date("Y") ?> IFFar Sports

            &bull; Campus Uruguaiana

        </div>



    </main>



    <script>

        const toggleSenha = document.getElementById("toggleSenha");

        const campoSenha = document.getElementById("senha");



        toggleSenha.addEventListener("click", function () {



            const mostrar = campoSenha.type === "password";



            campoSenha.type = mostrar ? "text" : "password";



            this.querySelector("i").className = mostrar

                ? "bi bi-eye-slash"

                : "bi bi-eye";



            this.setAttribute(

                "aria-label",

                mostrar ? "Ocultar senha" : "Mostrar senha"

            );



            this.setAttribute("aria-pressed", String(mostrar));



        });

    </script>



</body>

</html>
