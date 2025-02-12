<?php
// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chama o arquivo só uma vez
require_once 'Cliente.php';
$c = new Cliente();

session_start();
$resultado = null; // Inicializa como nulo
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    try {
        $resultado = $c->buscaDadosUsuario($id);
        
    } catch (PDOException $e) {
        echo "ERRO: " . $e->getMessage();
        exit();
    }
   
}

// Altera senha de um usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $senha = $_POST['senha'];
    $confirmaSenha = $_POST['confirmaSenha'];

    if ($senha == $confirmaSenha) {
        try {
            $c->alteraSenhaUsuario($resultado['idUsuario'], $senha);
            header("location: home.php");
            exit();
        } catch (PDOException $e) {
            echo "ERRO: " . $e->getMessage();
            exit();
        }
    } else {
        $senhaIncompativel = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Home</title>
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
            <?php 
                if (isset($_GET['alterarSenha']) == true) {
                    ?> 
                    <div class="cardAdm">
                        <form method="POST">
                            <h1>ALTERAR SENHA</h1>
                            <div class="usuario">
                                <label for="senha">Nova senha</label>
                                <input type="password" name="senha" id="senha" placeholder="Senha" required>
                            </div>
                            <div class="usuario">
                                <label for="senha">Confirme a senha</label>
                                <input type="password" name="confirmaSenha" id="confirmaSenha" placeholder="Confirme a senha" required>
                                <?php if (isset($senhaIncompativel) && $senhaIncompativel) { ?>
                                <div class="aviso">As senhas não coincidem. Tente novamente.</div>
                                <?php } ?>
                            </div>
                            <button class="botao" type="submit">ALTERAR</button>
                        </form>
                    </div>
                    <?php
                } else {
                    ?>
                    <h1>Seja Bem-vindo<?php if (isset($resultado)) { echo ", " .$resultado['username']; }?><h1>
                    <img src="../Imagens/imagemPaginaInicial.svg" class="img" alt="Pagina Inicial animação"> 
                    <?php
                }
            ?>
        </div>

        <!-- Tela da direita -->
        <div class="direita">
            <div class="table-container">
                <table>
                    <thead>
                        <tr class="titulo">
                            <th>USERNAME</th>
                            <th>ACESSO</th>
                            <th>STATUS</th>
                            <th colspan="2">EMAIL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php if (isset($resultado)) { echo $resultado['username']; } ?></td>
                            <td><?php if (isset($resultado)) { echo $resultado['acesso']; } ?></td>
                            <td><?php if (isset($resultado)) { echo $resultado['status'] ? "Ativo" : "Inativo"; } ?></td>
                            <td><?php if (isset($resultado)) { echo $resultado['email']; } ?></td>
                            <td>
                                <div class="botao-grupo">
                                    <a href="home.php?alterarSenha=true">Alterar senha</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

