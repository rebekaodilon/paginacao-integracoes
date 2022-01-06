<?php

require_once '../zk_api/src/services/ftp/FtpClient.php';
require_once '../zk_api/src/services/ftp/FtpException.php';
require_once '../zk_api/src/services/ftp/FtpWrapper.php';

// conexão com o ftp
$ftp = new \FtpClient\FtpClient();
$ftp->connect('ftp.zukk.in');
$ftp->login('testes_zukkin', 'yC8peZbUuUdXQKuB');

// ativa o modo passivo do ftp
$ftp->pasv(true);

// lista a localização de todos os arquivos da pasta
$dir_files = $ftp->nlist("./arquivos/");

// se tiver achado algum arquivo
if(!empty($dir_files))
{
    // pega a localização do primeiro arquivo
    $dir_file = $dir_files[array_key_first($dir_files)];
    // pega o conteudo do primeiro arquivo
    $file = $ftp->getContent($dir_file);
    // se ele tiver conseguido pegar o conteudo e o mesmo for válido
    if($file != '' and $file != false and $file != null)
    {   
        // transforma o arquivo em array
        $file = explode("\n", $file);
        foreach($file as $key_line => $line){$file[$key_line] = explode(";", $line);}
        $fileArray = array_chunk($file, 1000);
        
        // loop
        for ($i=0; $i < count($fileArray); $i++) {
            // guarda as 1000 linhas
            $lines = $fileArray[$i];

            echo json_encode($lines); die;
        }
        
    }
}