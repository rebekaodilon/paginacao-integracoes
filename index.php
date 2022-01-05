<?php

$ftp_server = "ftp.zukk.in";
$ftp_conn = ftp_connect($ftp_server);
$login = ftp_login($ftp_conn, 'testes_zukkin', 'yC8peZbUuUdXQKuB');

$file_list = ftp_nlist($ftp_conn, ".");
var_dump($file_list);

ftp_close($ftp_conn);

$nomeArquivo = 'arquivos/arquivo1.txt';
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
    // echo json_encode($resultado);
    fclose($arquivo);
} 
else 
{
    exit('ERRO => Arquivo vazio ou inexistente.');
}