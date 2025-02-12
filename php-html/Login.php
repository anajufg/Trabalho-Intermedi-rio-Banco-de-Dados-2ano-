<?php 
// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chama o arquivo só uma vez
require_once 'Cliente.php';
$c = new Cliente();

// Busca o usuario e verifica se ele esta no banco
$resultado = null; // Inicializa como nulo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    try {
        $resultado = $c->buscaUsuario($username);
        if ($resultado == false) {
            $usernameNaoEncontrado = true;
        } else {
            if (password_verify($senha, $resultado['senha'])) {
               switch ($resultado['acesso']) {
                case 'usuario':
                    session_start();
                    $_SESSION['id'] = $resultado['idUsuario'];
                    header('location: home.php');
                    exit();
                    break;
                case 'gerente':
                    header('location: homeGerente.php');
                    exit();
                    break;
                case 'administrador':
                    header('location: homeAdm.php');
                    exit();
                    break;
                default:
                    break;
               }
            } else {
                $senhaIncorreta = true;
            }
        }

    } catch (PDOException $e) {
        echo "ERRO: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Login</title>
</head>
<body>
    <!-- Tela principal -->
    <div class="main"> 

        <!-- Tela da esquerda -->
        <div class="esquerda"> 
            <div class="card"> 
                <form method="POST">
                    <h1>LOGIN</h1>
                    <div class="usuario"> 
                        <label for="usuario">Usuário</label>
                        <input type="text" name="username" placeholder="Usuário" required>
                        <?php if (isset($usernameNaoEncontrado) && $usernameNaoEncontrado) { ?>
                            <div class="aviso">Usuário não encontrado!</div>
                        <?php } ?>
                    </div>
                    <div class="usuario"> 
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" placeholder="Senha" required>
                        <?php if (isset($senhaIncorreta) && $senhaIncorreta) { ?>
                            <div class="aviso">Senha incorreta!</div>
                        <?php } ?>
                    </div>
                    <button class="botao" type="submit">LOGIN</button>
                    <a href="Cadastrar.php" target="_self" id="cadastrar">Não tem uma conta? Cadastre-se</a>
                </form>
            </div>
        </div>

        <!-- Tela da direita -->
        <div class="direita"> 
            <img src="../Imagens/imagemLogin.svg" class="img" alt="Login animação"> 
        </div>
    </div>
</body>
</html>
