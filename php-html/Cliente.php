<?php
require_once 'Conexao.php';

class Cliente {
    private $pdo;
    private $tabela;

    public function __construct() {
        $conexao = new Conexao();
        $this->pdo = $conexao->getPdo(); 
        $this->tabela = "TrabalhoSemana29_Cliente";
    }

    /* Lista usuarios */
    public function listarUsuarios() {
        $stmt = $this->pdo->query("SELECT * FROM " . $this->tabela . " ORDER BY username");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Cadastra usuario */
    public function cadastraUsuario($username, $senha, $acesso, $email) {
        // Verifica se já existe
        $stmt = $this->pdo->prepare("SELECT idUsuario FROM " . $this->tabela . " WHERE username = :n");
        $stmt->bindValue(":n", $username);
        $stmt->execute();

        // Se o user ja existe retorna falso
        if ($stmt->rowCount() > 0) {
            return false;
        } else { // Caso contrario, cadastra a pessoa
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha
            $stmt = $this->pdo->prepare("INSERT INTO " . $this->tabela . " (username, senha, acesso, status, email) VALUES (:n, :s, :a, 1, :e)");
            $stmt->bindValue(":n", $username);
            $stmt->bindValue(":s", $senhaHash); // Armazena a senha hasheada
            $stmt->bindValue(":a", $acesso);
            $stmt->bindValue(":e", $email);
            $stmt->execute();

            // Retorna o id do usuario
            $stmt = $this->pdo->prepare("SELECT idUsuario FROM " . $this->tabela . " WHERE username = :n");
            $stmt->bindValue(":n", $username);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['idUsuario'];
        }
    }

    /* Exclui usuario */
    public function excluiUsuario($id) {
        $stmt = $this->pdo->prepare("DELETE FROM " . $this->tabela . " WHERE idUsuario = :id");
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }

    /* Busca dados de um usuario */
    public function buscaDadosUsuario($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM " . $this->tabela . " WHERE idUsuario = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* Busca usuario para login */
    public function buscaUsuario($username) {
        $stmt = $this->pdo->prepare("SELECT idUsuario, senha, acesso FROM " . $this->tabela . " WHERE username = :n && status = 1");
        $stmt->bindValue(":n", $username);
        $stmt->execute();

        // Se o user nao existe ou esta inatico retorna falso
        if ($stmt->rowCount() < 0) {
            return false;
        } else {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    /* Atualiza dados de um usuario */
    public function atualizaDadosUsuario($id, $username, $senha, $acesso, $email) {
        if ($senha == 'Alterar senha (opcional)') {
            $stmt = $this->pdo->prepare("SELECT senha FROM " . $this->tabela . " WHERE idUsuario = :id");
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $senhaHash = $resultado['senha'];
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha
        }
        $stmt = $this->pdo->prepare("UPDATE " . $this->tabela . " SET username = :n, senha = :s, acesso = :a, email = :e WHERE idUsuario = :id");
        $stmt->bindValue(":n", $username);
        $stmt->bindValue(":s", $senhaHash); // Armazena a senha hasheada
        $stmt->bindValue(":a", $acesso);
        $stmt->bindValue(":e", $email);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }

    /* Altera status de um usuario */
    public function alteraStatusUsuario($id) {
        // Obtem o status atual 
        $stmt = $this->pdo->prepare("SELECT status FROM " . $this->tabela . " WHERE idUsuario = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $statusAtual = $resultado['status'];

        // Atualiza o status
        if($statusAtual == 1) {
            $statusAtual = 0;
        } else {
            $statusAtual = 1;
        }

        // Atualiza no banco
        $stmt = $this->pdo->prepare("UPDATE " . $this->tabela . " SET status = :stt WHERE idUsuario = :id");
        $stmt->bindValue(":stt", $statusAtual);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }

    /* Alterar senha de um usuario */
    public function alteraSenhaUsuario($id, $senha) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha
        // Atualiza no banco
        $stmt = $this->pdo->prepare("UPDATE " . $this->tabela . " SET senha = :s WHERE idUsuario = :id");
        $stmt->bindValue(":s", $senhaHash);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }
}
?>

