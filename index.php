<?php

$nomeArquivo = 'arquivo1.txt';
$separadorLinha = ";\r\n";

if (file_exists($nomeArquivo)) 
{
    $tamanhoArquivo = filesize($nomeArquivo);
    $arquivo = fopen($nomeArquivo, 'r+');
    $contaLinhas = 0;
    $maxLinhas = 1000;
    $resultado = array();
    
    
    while(!feof($arquivo))
    {
        if ($contaLinhas == $maxLinhas)
        {
            // var_dump(array_chunk($resultado , $maxLinhas));
            var_dump('conta linhas igual a max linhas');
            unset($resultado);
            $resultado = array();
            $contaLinhas = 0;
        }
        else if ($contaLinhas < $maxLinhas)
        {
            var_dump('conta linhas menor que max linhas');
            $resultado[] = explode($separadorLinha, fgets($arquivo, $tamanhoArquivo));
            // echo $resultado[$contaLinhas][0] . '<br>';
            
        }
        else
        {
            var_dump('conta linhas maior que max linhas');
            break;
        }
        $contaLinhas++;
    }
    // var_dump($resultado);
    fclose($arquivo);
} 
else 
{
    exit('ERRO => Arquivo vazio ou inexistente.');
}