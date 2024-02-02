<?php 
// Credenciais do BD
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "auth";

try{
    // Conexão com o BD
    $conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
    // echo "sucesso";
}catch(PDOException $err){
    echo "Erro: Conexão com o banco de dados nao realizado com sucesso. Erro gerado" . $err->getMessage();
}
?>