<?php 

/**
* FunctionName: down
* Description: PHP下载提供端, 支持断点续传, 支持文件过期验证, 支持多点下载
* Param: string    $fileName        资源名
* Param: string    $downFileName    下载时的文件名, 默认为资源名
* Param: integer    $speed            下载速度
* Param: string    $contentType    资源类型
* Author: RoO
* Date: 2012-11-09 15:33:41
* */
function down($fileName, $downFileName = null, $speed = 1024, $contentType = '') {
    /*
      $isReload    判断文件是否过期或者更改
      $isPart        判断请求是否是请求某一段资源
     */
    $isReload = false;
    $isPart   = false;
 
    //判断资源是否存在并且可读
    if (!is_file($fileName) || !is_readable($fileName)) {
        header("HTTP/1.1 404 NOT FOUND");
        exit;
    }
    //获取文件信息
    $fileInfo  = stat($fileName);
    //组装etag标签内容
    $etag      = md5("{$fileInfo['ino']}{$fileInfo['mtime']}{$fileInfo['size']}");
    $etag .= '-' . crc32($etag);
 
    //获取客户端传递过来的实体标签(对应ETag头);
    $clientTag = false;
    if (isset($_SERVER['HTTP_IF_RANGE'])) {
        $clientTag = $_SERVER['HTTP_IF_RANGE'];
    }
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        $clientTag = $_SERVER['HTTP_IF_NONE_MATCH'];
    }
 
    //如果客户端传递过来的资源实体标签和文件ETag标签不相符,则认为资源过期
    if ($clientTag && $clientTag != $etag) {
        $isReload = true;
    }
    //通过if-Unmodified标签判断文件是过期
    if (isset($_SERVER['HTTP_IF_UNMODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_UNMODIFIED_SINCE']) < $fileInfo['mtime']) {
        $isReload = true;
    }
    //通过if-Modified标签判断文件是否过期
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) != $fileInfo['mtime']) {
        $isReload = true;
    }
    /*
      是否定义保存文件名,如果未定义,则使用服务起上的文件名作为保存文件名
      文件名通过rawurlencode处理,确保文件名不存在乱码
     */
    if (!$downFileName) {
        $downFileName = $fileName;
    }
    $filePathInfo = pathinfo($downFileName);
    $extension    = isset($filePathInfo['extension']) ? "." . $filePathInfo['extension'] : false;
    $downFileName = rawurlencode($filePathInfo['filename']) . $extension;
 
    /*
      通过Range头判断请求是否是部分内容请求
      如果是,则处理range,取出内容开始地址和结束地址,并计算内容长度
      如果否,则返回整个资源,内容长度为整个资源的长度
     */
    if (isset($_SERVER['HTTP_RANGE'])) {
        if (preg_match('/^bytes=(\d*)-(\d*)$/i', $_SERVER['HTTP_RANGE'], $rangeArray)) {
            $start  = $end    = 0;
            list(, $start, $end) = $rangeArray;
            if ($start == '' && $end == '') {
                header('HTTP/1.1 404 NOT FOUND');
                exit;
            }
            if ($start == '') {
                $start = $fileInfo['size'] - $end;
                $end   = $fileInfo['size'] - 1;
            } elseif ($end == '') {
                $end = $fileInfo['size'] - 1;
            }
 
            $start         = $start < 0 ? 0 : $start;
            $end           = $end > $fileInfo['size'] ? $fileInfo['size'] - 1 : $end;
            $contentLength = $end - $start + 1;
            if ($contentLength < 0) {
                header("HTTP/1.1 404 NOT FOUND");
                exit;
            }
            $isPart = true;
        }
    }
 
    //如果不是部分资源请求或者文件已经过期,则返回全部内容
    if (!$isPart || $isReload) {
        $contentLength = $fileInfo['size'];
        $start         = 0;
        $end           = $fileInfo['size'] - 1;
    }
 
    //默认资源类型
    if (!$contentType) {
        $contentType = "application/octet-stream";
    }
 
    //获取GMT时间,并拼装处理
    $gmdate = gmdate('D, d M Y H:i:s', $fileInfo['mtime']) . ' GMT';
    //发送http响应头
    header("Date: $gmdate");
    header("ETag: $etag");
    header("Last-Modified: $gmdate");
    header("Content-Length: $contentLength");
    header("Content-Type: $contentType");
    header("Accept-Ranges: bytes");
    header("Content-Transfer-Encoding: binary");
    header("Content-Control: no-cache");
    header("Content-Disposition: attachment; filename=\"$downFileName\"");
 
    /*
      如果是部分资源,则发送206状态,并发送内容范围
      如果是全部资源,则发送200状态
     */
    if ($isPart) {
        header("HTTP/1.1 206 Partial Content");
        header("Content-Range: bytes $start-$end/{$fileInfo['size']}");
    } else {
        header("HTTP/1.1 200 OK");
    }
 
    //定义每次读取文件的长度
    $readSize  = 2048;
    //如果有速度限制, 则计算休眠时间,以此来限制速度
    $sleepTime = 0;
    if ($speed > 0) {
        $sleepTime = floor($readSize * 100 / $speed);
    }
 
    //打开文件
    $fileHandle      = fopen($fileName, 'rb');
    //移动指针到请求对应的点
    fseek($fileHandle, $start);
    //本次请求已读取的文件长度
    $alreadyReadSize = 0;
    //开启缓冲输出
    ob_start();
    /*
      循环读取资源内容,并输出
      1.判断以读内容是否已经超过请求的长度
      2.判断文件已经结束(碰到EOF标识)
      3.判断连接是否依然保持正常
     */
    while ($alreadyReadSize < $contentLength && !feof($fileHandle) && connection_status() == 0) {
        //计算下次循环要读取的长度,作用是为了防止最后一次读取超长的字符
        $readSize        = $alreadyReadSize + $readSize > $contentLength ? $contentLength - $alreadyReadSize : $readSize;
        $alreadyReadDate = fread($fileHandle, $readSize);
        //输出内容,并马上刷新缓冲区,另内容可以即时到达客户端
        echo $alreadyReadDate;
        ob_flush();
        flush();
        $alreadyReadSize += $readSize;
        //休眠,以达到控制下载速度
        if ($sleepTime > 0) {
            usleep($sleepTime);
        }
    }
}

?>
