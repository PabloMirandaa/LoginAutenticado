<?php 
session_start();// iniciar a sessão
// Limpar o buffer de redirecionamento
ob_start();

// Incluir o arquivo para validar e recuperar os dados
include_once 'validarToken.php';

// Chamar a função validar o token, se a função retornar FALSE significa que o token é inválido e acessa o IF
if(!validarToken()){
    // cria a mensagem de erro e atribui para variavel global
    $_SESSION['msg'] = "<p style= 'color: #f00;'>Erro: Necessário realizar o login para acessar a página!</p>";

    // Redirieciona o usario para o index.php
    header("Location:index.php");

    // pausar o processamento da página
    exit();
};
echo "Bem vindos";