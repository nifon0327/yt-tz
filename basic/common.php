<?php
define('IN_COMMON', true);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
define('SITE_PATH', substr(dirname(__FILE__), 0, -6)); // 末尾没有“/”


// 模拟 register_globals = On
if (!ini_get('register_globals')) {

    $superglobals = array($_SERVER, $_ENV, $_FILES, $_COOKIE, $_POST, $_GET);

    if (isset($_SESSION)) {
        array_unshift($superglobals, $_SESSION);
    }

    foreach ($superglobals as $superglobal) {
        extract($superglobal, EXTR_SKIP);
    }
}





/**
 * 写文件
 * 【注意：临时调试用而已，不要用于正式代码中！】
 *
 * @param string $filename 文件名
 * @param string $text   要写入的文本字符串
 * @param string $openmod  文本写入模式（'w':覆盖重写，'a':文本追加）
 * @return bool
 * @author qianyunlai.com
 */
function file_write_3($filename = '', $text = '', $openmod = 'w') {
    if (@$fp = fopen($filename, $openmod)) {
        flock($fp, 2);
        fwrite($fp, $text);
        fclose($fp);
        return true;
    } else {
        return false;
    }
}

/**
 * 写对象（包括 数字、字符串、数组）
 * 【注意：临时调试用而已，不要用于正式代码中！】
 *
 * @param string $text      要写入的文本字符串
 * @param string $type      文本写入类型（'w':覆盖重写，'a':文本追加）
 * @param bool   $isVarExport 是否变量导出
 * @param string $logFile     日志文件
 * @return bool
 * @author qianyunlai.com
 */
function write3($text = '', $type = 'a', $isVarExport = false, $logFile = 'write3.txt') {
    $filename = __DIR__ . '/../' . $logFile;

    $text = (is_array($text) && $isVarExport) ? var_export($text, true) : print_r($text, true);
    $text = "++++++++++++++++++++++++++++++++++++++++++\r\n"
        . date('Y-m-d H:i:s') . "\r\n"
        . $text . "\r\n";

    return file_write_3($filename, $text, $type);
}

/**
 * 写对象（包括 数字、字符串、数组）
 * 【注意：临时调试用而已，不要用于正式代码中！】
 *
 * @param string $text      要写入的文本字符串
 * @param string $type      文本写入类型（'w':覆盖重写，'a':文本追加）
 * @param bool   $isVarExport 是否变量导出
 * @return bool
 */
function write4($text = '', $type = 'a', $isVarExport = false) {
    return write3($text, $type, $isVarExport, 'write4.txt');
}

/**
 * 创建文件夹
 *
 * @param string $path      文件夹路径
 * @param int    $mode      访问权限
 * @param bool   $recursive 是否递归创建
 * @return bool
 */
function dir_mkdir($path = '', $mode = 0777, $recursive = true) {
    clearstatcache();
    if (!is_dir($path)) {
        mkdir($path, $mode, $recursive);
        return chmod($path, $mode);
    }

    return true;
}

/**
 * 清空/删除 文件夹
 *
 * @param string $dirname 文件夹路径
 * @param bool   $self    是否删除当前文件夹
 * @return bool
 */
function dir_rmdir($dirname = '', $self = true) {
    if (!file_exists($dirname)) {
        return false;
    }

    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }

    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            dir_rmdir($dirname . '/' . $entry);
        }
    }
    $dir->close();

    return $self && rmdir($dirname);
}