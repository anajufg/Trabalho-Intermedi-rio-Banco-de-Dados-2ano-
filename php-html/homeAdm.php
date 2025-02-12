<?php
// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chama o arquivo só uma vez
require_once 'Cliente.php';
$c = new Cliente();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se está atualizando ou criando um novo usuário
    if (isset($_GET['id_up']) && !empty($_GET['id_up'])) {
        // Atualização
        $id_upd = $_GET['id_up'];
        $username = $_POST['username'];
        $senha = $_POST['senha'];
        $acesso = $_POST['acesso'];
        $email = $_POST['email'];

        if (!empty($username) && !empty($acesso) && !empty($email)) {
            try {
                $c->atualizaDadosUsuario($id_upd, $username, $senha, $acesso, $email);
                header("location: homeAdm.php");
                exit();
            } catch (PDOException $e) {
                echo "ERRO: " . $e->getMessage();
                exit();
            }
        }
    } else {
        // Cadastra
        $username = $_POST['username'];
        $senha = $_POST['senha'];
        $acesso = $_POST['acesso'];
        $email = $_POST['email'];

        if (!empty($username) && !empty($senha) && !empty($acesso) && !empty($email)) {
            try {
                if (!$c->cadastraUsuario($username, $senha, $acesso, $email)) {
                    $usernameExistente = true;
                } else {
                    header("location: homeAdm.php");
                    exit();
                }
            } catch (PDOException $e) {
                echo "ERRO: " . $e->getMessage();
                exit();
            }
        }
    }
}

// Busca dados de um usuário para edição
$resultado = null; // Inicializa como nulo
if (isset($_GET['id_up'])) {
    $id_update = $_GET['id_up'];
    try {
        $resultado = $c->buscaDadosUsuario($id_update);
    } catch (PDOException $e) {
        echo "ERRO: " . $e->getMessage();
        exit();
    }
}

// Altera status de um usuário
if (isset($_GET['id_status'])) {
    $id_stt = $_GET['id_status'];
    try {
        $c->alteraStatusUsuario($id_stt);
        header("location: homeAdm.php");
        exit();
    } catch (PDOException $e) {
        echo "ERRO: " . $e->getMessage();
        exit();
    }
}

// Excluir usuário
if (isset($_GET['id_usuario'])) {
    $id = $_GET['id_usuario'];
    try {
        $c->excluiUsuario($id);
        header("location: homeAdm.php");
        exit();
    } catch (PDOException $e) {
        echo "ERRO: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Home Adm</title>
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
            <div class="cardAdm">
                <form method="POST">
                    <h1><?php echo isset($resultado) ? "ATUALIZAR USUÁRIO" : "CADASTRAR USUÁRIO"; ?></h1>
                    <div class="usuario">
                        <label for="usuario">Usuário</label>
                        <input type="text" name="username" id="username" placeholder="Usuário" value="<?php echo isset($resultado) ? $resultado['username'] : ''; ?>" required>
                        <?php if (isset($usernameExistente) && $usernameExistente) { ?>
                            <div class="aviso">Este username já está em uso!</div>
                        <?php } ?>
                    </div>
                    <div class="usuario">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="E-mail" value="<?php echo isset($resultado) ? $resultado['email'] : ''; ?>" required>
                    </div>
                    <div class="usuario">
                        <label for="senha">Senha</label>
                        <input type="<?php echo isset($resultado) ? 'text' : 'password'; ?>" name="senha" id="senha" placeholder="Senha" value="<?php echo isset($resultado) ? 'Alterar senha (opcional)' : ''; ?>" <?php echo isset($resultado) ? '' : 'required'; ?>>
                    </div>
                    <div class="usuario">
                        <label for="acesso">Acesso</label>
                        <select name="acesso" id="acesso" required>
                            <option value="usuario" <?php echo isset($resultado) && $resultado['acesso'] == 'usuario' ? 'selected' : ''; ?>>Usuário</option>
                            <option value="gerente" <?php echo isset($resultado) && $resultado['acesso'] == 'gerente' ? 'selected' : ''; ?>>Gerente</option>
                            <option value="administrador" <?php echo isset($resultado) && $resultado['acesso'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                    <button class="botao" type="submit"><?php echo isset($resultado) ? "ATUALIZAR" : "CADASTRAR"; ?></button>
                </form>
            </div>
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
                        <?php
                        $dados = $c->listarUsuarios();
                        if (count($dados) > 0) {
                            foreach ($dados as $usuario) {
                                echo "<tr>";
                                echo "<td>{$usuario['username']}</td>";
                                echo "<td>{$usuario['acesso']}</td>";
                                echo "<td>" . ($usuario['status'] ? "Ativo" : "Inativo") . "</td>";
                                echo "<td>{$usuario['email']}</td>";
                                ?>
                                <td>
                                    <div class="botao-grupo">
                                        <a href="homeAdm.php?id_up=<?php echo $usuario['idUsuario']; ?>">Editar</a>
                                        <a href="homeAdm.php?id_usuario=<?php echo $usuario['idUsuario']; ?>">Excluir</a>
                                        <a href="homeAdm.php?id_status=<?php echo $usuario['idUsuario']; ?>">Status</a>
                                    </div>
                                </td>
                                <?php
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Não há registros!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

