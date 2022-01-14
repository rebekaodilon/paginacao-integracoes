<?php

class PaginateFile
{
    /** 
     * Variável que recebe a instancia do FTP com suas configurações
     * 
     * @var \FtpClient\FtpClient
     */
    private $ftp;

    /**
     * Váriavel de controle de tamanho de paginação
     *
     * @var int
     */
    private $totalLinhas;

    /**
     * Path do arquivo que será lido
     *
     * @var string
     */
    private $file_path;

    /**
     * Path do arquivo que será salvo na lixeira
     * 
     * @var string
     */
    private $trash_path;

    /**
     * Array com as linhas do arquivo separadas
     * 
     * @var array
     */
    private $chuncked_array;

    /**
     * Construtor
     * 
     * @param int $totalLinhas
     * @param \FtpClient\FtpClient $ftp
     * @param string file_path
     * @param string trash_path
     * 
     * @return void
     */
    public function __construct(int $totalLinhas, FtpClient $ftp, string $file_path, string $trash_path, array $chuncked_array)
    {
        $this->totalLinhas = $totalLinhas;
        $this->ftp = $ftp;
        $this->file_path = $file_path;
        $this->trash_path = $trash_path;
        $this->chuncked_array = $trash_path;
    }

    /**
     * Retorna as linhas do arquivo
     * 
     */
    public function getFileLines()
    {
        // lista a localização de todos os arquivos da pasta
        $dir_files = $this->ftp->nlist($this->file_path);

        // se tiver achado algum arquivo
        if(!empty($dir_files))
        {
            // pega a localização do primeiro arquivo
            $dir_file = $dir_files[array_key_first($dir_files)];
            $trash_file = $this->trash_path . basename($dir_file);

            // pega o conteudo do primeiro arquivo
            $file = $this->ftp->getContent($dir_file);
            
            // salva todo o conteudo do arquivo em uma variável
            $file_content = $file;

            // se não existir na lixeira cria uma copia na lixeira
            if(empty($this->ftp->getContent($this->trash_path . basename($dir_file))))
            {
                // Move arquivo para lixeira
                $this->ftp->rename($dir_file, $this->trash_path . basename($dir_file));

                // salva o conteudo no arquivo
                $this->ftp->putFromString($trash_file, $file_content);
            }
            
            // se ele tiver conseguido pegar o conteudo e o mesmo for válido
            if($file != '' and $file != false and $file != null)
            {   
                // transforma o arquivo em array
                $file = explode("\n", $file);
                foreach($file as $key_line => $line){$file[$key_line] = explode(";", $line);}

                $chuncked_array = array_chunk($file, $this->totalLinhas);
                $linhas = $chuncked_array[0];

                return $linhas;
            }
        }
    }

    /**
     * Apaga as linhas do arquivo
     * 
     * @param int $linhas
     * @param array $chuncked_array
     */
    public function deleteFileLines($linhas)
    {
        // lista a localização de todos os arquivos da pasta
        $dir_files = $ftp->nlist($this->file_path);

        // pega a localização do primeiro arquivo
        $dir_file = $dir_files[array_key_first($dir_files)];

        // pega o conteudo do primeiro arquivo
        $file = $ftp->getContent($dir_file);

        // se tiver achado algum arquivo
        if(!empty($dir_files))
        {
            $dir_file = $dir_files[array_key_first($dir_files)];

            if($file != '' and $file != false and $file != null)
            {
                $arquivo_processado = $this->chuncked_array[0];
                unset($this->chuncked_array[0]);
                unset($linhas[0]);
    
                // re-transforma o chunk em array
                $array_processado = [];
                foreach($chuncked_array as $chunk){
                    foreach($chunk as $array_chuncked){
                        $array_processado[] = $array_chuncked;
                    }
                }
    
                // transforma o array processado em arquivo novamente
                foreach($array_processado as $key_line => $line){$array_processado[$key_line] = implode(";", $line);}
                foreach($arquivo_processado as $key_line => $line){$arquivo_processado[$key_line] = implode(";", $line);}
                $arquivo_processado = implode("\n", $arquivo_processado);
                $array_processado = implode("\n", $array_processado);
    
                // salva o arquivo novamente sem o array processado
                if($ftp->putFromString($dir_file, $array_processado))
                {
                    $message = '<br/><br/>Arquivo processado com sucesso!';
                }
            }
            else{
                // Remove arquivo da pasta de origem
                $ftp->delete($dir_file);
                
                $message = '<br/><br/>Arquivo processado e enviado pra a lixeira!';
            }
        }

        return $message;
    }
}