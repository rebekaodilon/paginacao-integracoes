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

        $chuncked_array = array_chunk($file, 1000);
        $primeiras_mil_linhas = $chuncked_array[0];
        
        // processa as primeiras 1000 linhas
        echo json_encode($primeiras_mil_linhas);
        // termina o processo

        // remove as linhas processadas do arquivo
        unset($chuncked_array[0]);

        // re-transforma o chunk em array
        $array_processado = [];
        foreach($chuncked_array as $chunk){
            foreach($chunk as $array_chuncked){
                $array_processado[] = $array_chuncked;
            }
        }

        // transforma o array processado em arquivo novamente
        foreach($array_processado as $key_line => $line){$array_processado[$key_line] = implode(";", $line);}
        $array_processado = implode("\n", $array_processado);
        
        // salva o arquivo novamente sem o array processado
        if($ftp->putFromString($dir_file, $array_processado))
        {
            echo '<br/><br/>Arquivo processado com sucesso!';
        }
    }
}