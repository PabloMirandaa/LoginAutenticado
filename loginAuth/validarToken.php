<?php 

function validarToken(){
    $token = $_COOKIE['token'];
    var_dump($token);

    // Converter token em array
    $tokenArray = explode(".", $token);
    var_dump($tokenArray);
    $header = $tokenArray[0];
    $payload = $tokenArray[1];
    $signature = $tokenArray[2];

    // chave secreta é única
    $secret = "D1265TDF37G73FDD6D7F";

    // Usar o header e o payload e codificar com o algoritmo sha256
    $validarAssinatura = hash_hmac('sha256',"$header.$payload", $secret, true);

    // Codificar dados em base64
    $validarAssinatura = base64_encode(($validarAssinatura));

    // Comparar a asssinatura do token recebido com a assinatura gerada.
    // Acessa o if quando o token é valid
    if($signature == $validarAssinatura){
        // decodificar os dados de base64
        $dadosToken = base64_decode($payload);

        // converter o objeto em array
        $dadosToken = json_decode(($dadosToken));

        // Comprarar a data de vencimento do token com a data atual
        // Acessa o if quando a adata do token é maior do que a data atual
        if($dadosToken->exp > time()){
            return true;
        }else{
            // Acessa o else quando a data do token é menor ou igual a data atual
            // Retorna False indicando que o token é inválido
            return false;
        }

    }else{
        // Acessa o else quando o token é invalido
        // Retorna false indicando que o token é inválido
        return false;
    }

    return true;
}