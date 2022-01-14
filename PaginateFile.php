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
     * Array de chunks do arquivo
     *
     * @var array
     */
    private $chuncked_array = [];

    /**
     * Guarda o nome do arquivo que foi lido
     *
     * @var array
     */
    private $dir_file;

    /**
     * Linhas processadas
     *
     * @var array
     */
    private $linhas;

    /**
     * Separador de linhas
     *
     * @var string
     */
    private $separador_linha;

    /**
     * Separador de colunas
     *
     * @var string
     */
    private $separador_coluna;

    /**
     * Construtor
     * 
     * @param int $totalLinhas
     * @param \FtpClient\FtpClient $ftp
     * @param string file_path
     * @param string trash_path
     * @param string $separador_linha
     * @param string $separador_coluna
     * 
     * @return void
     */
    public function __construct(int $totalLinhas, FtpClient\FtpClient $ftp, string $file_path, string $trash_path, string $separador_linha, string $separador_coluna)
    {
        $this->totalLinhas = $totalLinhas;
        $this->ftp = $ftp;
        $this->file_path = $file_path;
        $this->trash_path = $trash_path;
        $this->separador_linha = $separador_linha;
        $this->separador_coluna = $separador_coluna;
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
            $this->dir_file = $dir_files[array_key_first($dir_files)];
            $trash_file = $this->trash_path . basename($this->dir_file);

            // pega o conteudo do primeiro arquivo
            $file = $this->ftp->getContent($this->dir_file);
            
            // salva todo o conteudo do arquivo em uma variável
            $file_content = $file;

            // se não existir na lixeira cria uma copia na lixeira
            if(empty($this->ftp->getContent($this->trash_path . basename($this->dir_file))))
            {
                // Move arquivo para lixeira
                $this->ftp->rename($this->dir_file, $this->trash_path . basename($this->dir_file));

                // salva o conteudo no arquivo
                $this->ftp->putFromString($trash_file, $file_content);
            }
            
            // se ele tiver conseguido pegar o conteudo e o mesmo for válido
            if($file != '' and $file != false and $file != null)
            {   
                // transforma o arquivo em array
                $file = explode("$this->separador_linha", $file);
                foreach($file as $key_line => $line){$file[$key_line] = explode($this->separador_coluna, $line);}

                $this->chuncked_array= array_chunk($file, $this->totalLinhas);
                $this->linhas = $this->chuncked_array[0];

                return $this->linhas;
            }
        }
    }

    /**
     * Apaga as linhas do arquivo
     * 
     * @return void
     */
    public function deleteFileLines()
    {
        // pega o conteudo do primeiro arquivo
        $file = $this->ftp->getContent($this->dir_file);

        // se tiver achado algum arquivo
        if($file != '' and $file != false and $file != null)
        {
            $arquivo_processado = $this->chuncked_array[0];
            unset($this->chuncked_array[0]);

            // re-transforma o chunk em array
            $array_processado = [];
            foreach($this->chuncked_array as $chunk){
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
            if($this->ftp->putFromString($this->dir_file, $array_processado))
            {
                $message = '<br/><br/>Arquivo processado com sucesso!';
            }
        }
        else{
            // Remove arquivo da pasta de origem
            $this->ftp->delete($this->dir_file);
            
            $message = '<br/><br/>Arquivo processado e enviado pra a lixeira!';
        }

        return $message;
    }
}