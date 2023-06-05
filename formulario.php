<?php
// Configuração do banco de dados
$servidor = "127.0.0.1:3306";
$username = "root";
$password = "";

// Conexão com o banco de dados
$conn = new mysqli($servidor, $username, $password);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Criação do banco de dados
$sql = "CREATE DATABASE IF NOT EXISTS gameCrud";
    if ($conn->query($sql) !== true) {
        die("Erro ao criar banco: " . $conn->error);
    }
    // Seleciona o banco de dados
    $conn->select_db("gameCrud");

    // Criação da tabela
    $sql = "CREATE TABLE IF NOT EXISTS formulario (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        cpf VARCHAR(14) NOT NULL,
        titulacao VARCHAR(100) NOT NULL,
        curriculo VARCHAR(255) NOT NULL,
        rua VARCHAR(100) NOT NULL,
        bairro VARCHAR(100) NOT NULL,
        cidade VARCHAR(100) NOT NULL,
        linkedin VARCHAR(100)
    )";

    $sql = "CREATE TABLE IF NOT EXISTS verificacao (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(8) NOT NULL,
        nome_professor VARCHAR(255) NOT NULL
    )";

    if ($conn->query($sql) !== true) {
        die("Erro ao criar tabela verificacao: " . $conn->error);
    }

        // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST["nome"];
        $cpf = $_POST["cpf"];
        $titulacao = $_POST["titulacao"];
        $rua = $_POST["rua"];
        $bairro = $_POST["bairro"];
        $cidade = $_POST["cidade"];
        $linkedin = $_POST["linkedin"];

    // Gerar código aleatório
    $codigo = substr(md5(uniqid(rand(), true)), 0, 8);

    // Prepara a instrução SQL usando instruções preparadas
    $stmt = $conn->prepare("INSERT INTO formulario (nome, cpf, titulacao, rua, bairro, cidade, linkedin)
    VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Vincula os parâmetros aos valores
    $stmt->bind_param("sssssss", $nome, $cpf, $titulacao, $rua, $bairro, $cidade, $linkedin);

    // Verifica se um arquivo foi enviado
    // if(isset($_FILES["curriculo"])) {
    //     $curriculo = $_FILES["curriculo"]["name"];
    //     $curriculo_tmp = $_FILES["curriculo"]["tmp_name"];

    //     // Move o arquivo para o diretório desejado
    //     move_uploaded_file($curriculo_tmp, "Área de Trabalho/candidatos" . $curriculo);
    // }

    
    // Executa a instrução preparada
    if ($stmt->execute()) {
        // Insere os dados na tabela "verificacao"
        $stmt_verificacao = $conn->prepare("INSERT INTO verificacao (codigo, nome_professor) VALUES (?, ?)");
        $stmt_verificacao->bind_param("ss", $codigo, $nome);
        $stmt_verificacao->execute();

        echo "<script>
        alert('Cadastrado com sucesso!');
        window.location.href = 'index.html';
    </script>";
    } else {
        echo "Erro ao inserir os dados: " . $stmt->error;
    }

    // Fecha as instruções preparadas
    $stmt->close();
    $stmt_verificacao->close();
}

// Fecha a conexão com o banco de dados
$conn->close();
?>