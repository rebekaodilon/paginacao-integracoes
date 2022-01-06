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

for ($i=0; $i < count($file); $i++) {
    
    $nomeArquivo = $file[$i];
    $arquivo = explode('/', $nomeArquivo);
    // var_dump($nomeArquivo); die;
    $separadorLinha = ";\r\n";

    if ($arquivo[2] != '' and $arquivo[2] != false and $arquivo[2] != null) 
    {
        // $tamanhoArquivo = filesize($arquivo[2]);
        $arquivo = fopen($arquivo[2], 'r+');
        $contaLinhas = 0;
        $maxLinhas = 1000;
        $resultado = array();

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