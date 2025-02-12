<?php
// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chama o arquivo só uma vez
require_once 'Cliente.php';
$c = new Cliente();

// Altera status de um usuário
if (isset($_GET['id_status'])) {
    $id_stt = $_GET['id_status'];
    try {
        $c->alteraStatusUsuario($id_stt);
        header("location: homeGerente.php");
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
    <title>Home Gerente</title>
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
            <img src="../Imagens/imagemPaginaInicial.svg" class="img" alt="Pagina Inicial animação"> 
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
                                        <a href="homeGerente.php?id_status=<?php echo $usuario['idUsuario']; ?>">Status</a>
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

