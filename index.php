<?php

require_once '../zk_api/src/services/ftp/FtpClient.php';
require_once '../zk_api/src/services/ftp/FtpException.php';
require_once '../zk_api/src/services/ftp/FtpWrapper.php';

// $ftp = new \FtpClient\FtpClient();
// $ftp->connect('ftp.zukk.in');
// $ftp->login('testes_zukkin', 'yC8peZbUuUdXQKuB');
// $ftp->pasv(true);

// $file = $ftp->getContent("/arquivos/arquivo1.txt");
// var_dump($file); die;


$ftp_server = "ftp.zukk.in";
$ftp_conn = ftp_connect($ftp_server);
$login = ftp_login($ftp_conn, 'testes_zukkin', 'yC8peZbUuUdXQKuB');
ftp_pasv($ftp_conn, true);

$file = ftp_nlist($ftp_conn, "/arquivos");
var_dump($file); die;

for ($i=0; $i < count($listaArquivos); $i++) {

    $nomeArquivo = $listaArquivos[$i];
    $separadorLinha = ";\r\n";

    if (file_exists($nomeArquivo)) 
    {
        $tamanhoArquivo = filesize($nomeArquivo);
        $arquivo = fopen($nomeArquivo, 'r+');
        $totalLinhasArquivo = count(file($nomeArquivo));
        $contaLinhas = 0;
        $maxLinhas = 1000;
        $resultado = array();
        $cont = 0;

        while(!feof($arquivo))
        {
            if ($contaLinhas == $maxLinhas)
            {
                unset($resultado);
                $resultado = array();
                $contaLinhas = 0;
            }
            else if ($contaLinhas < $maxLinhas)
            {
                $resultado[] = explode($separadorLinha, fgets($arquivo, $tamanhoArquivo));
                // Faz o processamento do array
            }
            else
            {
                break;
            }
            $contaLinhas++;
            $cont++;
        }
        echo json_encode($resultado);
        fclose($arquivo);
    } 
    else 
    {
        exit('ERRO => Arquivo vazio ou inexistente.');
    }
}

ftp_close($ftp_conn);