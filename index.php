<?php

require_once '../zk_integracao/src/services/ftp/FtpClient.php';
require_once '../zk_integracao/src/services/ftp/FtpException.php';
require_once '../zk_integracao/src/services/ftp/FtpWrapper.php';
require_once 'PaginateFile.php';

$ftp_conn = [
    'host' => 'ftp.zukk.in',
    'user' => 'testes_zukkin',
    'password' => 'yC8peZbUuUdXQKuB',
];

// conexão com o ftp
$ftp = new \FtpClient\FtpClient();
$ftp->connect($ftp_conn['host']);
$ftp->login($ftp_conn['user'], $ftp_conn['password']);

// ativa o modo passivo do ftp
$ftp->pasv(true);

$file_path = "./arquivos/";
$trash_path = "./lixeira/";

$paginate = new PaginateFile(100, $ftp, $file_path, $trash_path);

$lines = $paginate->getFileLines();

var_dump($lines);