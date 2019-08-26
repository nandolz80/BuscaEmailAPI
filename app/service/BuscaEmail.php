<?php

namespace App\service;

class BuscaEmail
{

    private $server;
    private $userName;
    private $password;

    public function __construct()
    {
        $this->server = env('MAIL_SERVER');
        $this->userName = env('MAIL_USERNAME');
        $this->password = env('MAIL_PASSWORD');

    }
    public function getMail()
    {

        $emailJson = $this->buscaEmail();

        if(empty($emailJson)){
            return response()->json([
                'erro' => 'Nenhum Email encontrado',
                'status' => 404
            ]);
        }
        return  response()->json($emailJson);
    }

    private function buscaEmail()
    {

        $mbox = imap_open($this->server, $this->userName, $this->password);
        $email = $this->montaJson($mbox);
        //marca a mensagem como lida
        imap_setflag_full($mbox, "2,5", "\\Seen", ST_UID);
        //Fecha a conexão com o MailServer
        imap_close($mbox);
        return  $email;
    }

    private function montaJson($mbox)
    {

        $aux = [];

        for($m = 1; $m <= imap_num_msg($mbox); $m++){

            $header = imap_headerinfo($mbox, $m);
            $body = imap_fetchbody ($mbox, $m, 1);

            $todos = explode('Nome:', $body);
            $nome = explode('Endereco:',$todos[1]);
            $endereco = explode('Valor:',$nome[1]);
            $valor = explode('Vencimento:',$endereco[1]);
            $vencimento = explode('Att.',$valor[1]);

            $resp = [
                "nome" => $this->dataReplace($nome[0]),
                "endereco" => $this->dataReplace($endereco[0]),
                "valor" => $this->dataReplace($valor[0]),
                "vencimento" => $this->dataReplace($vencimento[0]),
                "arquivo" => $this->getAnexo($mbox)
            ];

            array_push($aux, $resp);
        }

        return  $aux;
    }

    private function getAnexo($mbox)
    {
        $dados  = [];
        for($m = 1; $m <= imap_num_msg($mbox); $m++){
            //pegando o anexo e descriptografando
            $arquivo = imap_base64(imap_fetchbody($mbox, $m,2));
            array_push($dados, $arquivo);
        }
        return $dados;
    }

    private function dataReplace($value)
    {
        return preg_replace(['/\r\n+/m','/\t\t+/m'], '',$value);
    }

}
