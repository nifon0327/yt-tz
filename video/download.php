<?php
/**
 * 下载文件
 * header函数
 *
 */
$file_path = 'system.mp4';
header('Content-Description: File Transfer');

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($file_path));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
?>