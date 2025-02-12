<?php 
// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chama o arquivo só uma vez
require_once 'Cliente.php';
$c = new Cliente();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cadastra
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $email = $_POST['email'];

    if (!empty($username) && !empty($senha) && !empty($email)) {
        try {
            $resul = $c->cadastraUsuario($username, $senha, "usuario", $email);
            if (!$resul) {
                $usernameExistente = true;
            } else {
                session_start();
                $_SESSION['id'] = $resul;
                header('location: home.php');
                exit();
            }
        } catch (PDOException $e) {
            echo "ERRO: " . $e->getMessage();
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Cadastrar</title>
</head>
<body>
    <!-- Botão de Sair -->
    <div class="logout-container">
        <a href="Login.php" class="logout-button">Sair</a>
    </div>
    <!-- Tela principal -->
    <div class="main"> 

        <!-- Tela da esquerda -->
        <div class="esquerda"> 
            <img src="../Imagens/imagemCadastrar.svg" class="img" alt="Cadastrar animação"> 
        </div>

        <!-- Tela da direita -->
        <div class="direita"> 
            <div class="card"> 
                <form method="POST">
                    <h1>CADASTRE-SE</h1>
                    <div class="usuario"> 
                        <label for="usuario">Usuário</label>
                        <input type="text" name="username" id="username" placeholder="Usuário" required>
                        <?php if (isset($usernameExistente) && $usernameExistente) { ?>
                            <div class="aviso">Este username já está em uso!</div>
                        <?php } ?>
                    </div>
                    <div class="usuario"> 
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="E-mail" required>
                    </div>
                    <div class="usuario"> 
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" placeholder="Senha" required>
                    </div>
                    <button class="botao" type="submit">CADASTRAR</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
