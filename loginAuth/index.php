<?php 
session_start();// iniciar a sessão
// Limpar o buffer de redirecionamento
ob_start();

// inlcusao com o arquivo de conexao
include_once 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php 
    //Exemplo de criptografar a senha
    // echo password_hash('123456', PASSWORD_DEFAULT);
    ?>

    <h1>Login</h1>

    <?php 
        // Receber os dados do formulário
        $dados=filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Acessa o if quando o usuário clicou no botão "Acessar" do form
        if(!empty($dados['sendLogin'])){
            

            // Query para recuperar o usuario do BD
            $queryUsuario= "SELECT id, nome, usuario, email, senha 
                FROM usuarios
                WHERE usuario =:usuario
                Limit 1";

                // Preparar a query
                $resultUsuario=$conn->prepare($queryUsuario);

                // Substitui o link ":usuario" pelo valor que vem do fomrulario
                $resultUsuario->bindParam(':usuario', $dados['usuario']);

                // Executar a query
                $resultUsuario->execute();

                // Acessa o if quando encontrou o usuário no BD
                if(($resultUsuario)and ($resultUsuario->rowCount()!=0)){
                    //ler o resultado retornado
                    $rowUsuario = $resultUsuario->fetch(PDO::FETCH_ASSOC);
                    // var_dump($rowUsuario);

                    // Verificar se a senha digitada pelo usuario é igual a salva no BD
                    if(password_verify($dados['senha'], $rowUsuario['senha'])){
                        // o jWT é dividdido em 3 partes separadas por ponto ".": um header, um payload e uma signature

                        //header indica o tipo do token "JWT", e o algoritimo utilizado "HS256"
                        $header = [
                            'alg' => 'HS256',
                            'typ' => 'JWT'                            
                        ];
                        // var_dump($header);
                        // coverter array em um objeto
                        $header=json_encode($header);
                        // var_dump($header);

                        // codificar dados em base64
                        $header = base64_encode($header);
                        // var_dump($header);

                        // O payload é o corpo do JWT, recebe as informações que precisa armazenar
                        // iss - O domnínio da aplicação que gera o token
                        // aud - Define o domnínio que pode usar o token
                        // exp - Data de vencimento do token (7 days;24 hours; 60mins; 60secs)
                        $duracao = time() + (7*24*60*60);

                        $payload = [
                            'iss' => 'localhost',
                            'aud' => 'localhost',
                            'exp' => $duracao,
                            'id' => $rowUsuario['id'],
                            'nome' => $rowUsuario['nome'],
                            'email' => $rowUsuario['email']
                        ];
                        // coverter array em um objeto
                        $payload=json_encode($payload);
                        // var_dump($payload);

                        // codificar dados em base64
                        $payload = base64_encode($payload);
                        // var_dump($payload);

                        // signature é a assinatura 
                        // chave secreta é única
                        $secret = "D1265TDF37G73FDD6D7F";
                        
                        // Pegar o header e o payload e codificar com o algoritmo sha 256, junto com a chave
                        // Gera um valor de hash com chave usando o método HMAC 
                        $signature = hash_hmac('sha256',"$header.$payload", $secret, true);

                        // codificar dados em base64
                        $signature = base64_encode($signature);
                        // var_dump($signature);

                        // echo "Token: $header.$payload.$signature <br>";

                        // Salvar o token em cookies
                        // cria o cookie com duração de 7 dias
                        setcookie('token',"$header.$payload.$signature", (time()+(7*24*60*60)));

                        // redirecionar o usuario para página dashboard
                        header("Location: dashboard.php");

                    }else{
                    // criar mensagem de erro e atribui a uma variavel global msg
                    $_SESSION['msg'] = "<p style='color:#f00'>Erro: Usuário ou senha inválida!</p>";
                    }

                }else{
                    // criar mensagem de erro e atribui a uma variavel global msg
                    $_SESSION['msg'] = "<p style='color:#f00'>Erro: Usuário ou senha inválida!</p>";
                }
        }
        // VErificar se existe a variavel global msg e acessa o if
        if(isset($_SESSION['msg'])){
            // imprime o valor da variavel global msg
            echo $_SESSION['msg'];
            // Destroi a variavel global msg
            unset ($_SESSION['msg']);
        }

    ?>
    <!-- Início do Form de Login -->
    <form method="POST" action="">
        <?php 
            $usuario="";
            if(isset($dados['usuario'])){
                $usuario=$dados['usuario'];
            }
        ?>
        <label>Usuário: </label>
        <input type="text" name="usuario" placeholder="Digite o Usuario" value="<?php echo $usuario;?>"><br><br>

        <?php 
            $senha="";
            if(isset($dados['senha'])){
                $senha=$dados['senha'];
            }
        ?>
        <label>Senha: </label>
        <input type="password" name="senha" placeholder="Digite a Senha" value="<?php echo $senha;?>"><br><br>

        <input type="submit" name="sendLogin" value="Acessar"><br><br>
    </form>
    <!-- Fim do Form de Login -->

</body>
</html>