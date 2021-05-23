<?php

function del0($s) {

    if ($s == '') {
        return '';
    }
    $s = trim(strval($s));
    if (preg_match('#^-?\d+?\.0+$#', $s)) {
        return preg_replace('#^(-?\d+?)\.0+$#', '$1', $s);
    }
    if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $s)) {
        return preg_replace('#^(-?\d+\.[0-9]+?)0+$#', '$1', $s);
    }
    return $s;
}

//==========================颜色转换成RGB
function hex2rgb($hexColor) {
    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
    } else {
        $color = str_replace('#', '', $hexColor);
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        );
    }
    return $rgb;
}

//获取ＩＰ地址
function convertip($ip) {
    //IP数据文件路径
    $dat_path = '../model/QQWry.Dat';
    //检查IP地址
    //if(!preg_match("/^d{1,3}.d{1,3}.d{1,3}.d{1,3}$/", $ip)) {
    //    return 'IP Address Error';
    //}
    //打开IP数据文件
    if (!$fd = @fopen($dat_path, 'rb')) {
        return 'IP date file not exists or access denied';
    }
    //分解IP进行运算，得出整形数
    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
    //获取IP数据索引开始和结束位置
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if ($ipbegin < 0) {
        $ipbegin += pow(2, 32);
    }
    $ipend = implode('', unpack('L', $DataEnd));
    if ($ipend < 0) {
        $ipend += pow(2, 32);
    }
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
    $BeginNum = 0;
    $EndNum = $ipAllNum;
    //使用二分查找法从索引记录中搜索匹配的IP记录
    while ($ip1num > $ipNum || $ip2num < $ipNum) {
        $Middle = intval(($EndNum + $BeginNum) / 2);
        //偏移指针到索引位置读取4个字节
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if (strlen($ipData1) < 4) {
            fclose($fd);
            return 'System Error';
        }
        //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
        $ip1num = implode('', unpack('L', $ipData1));
        if ($ip1num < 0) {
            $ip1num += pow(2, 32);
        }
        //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
        if ($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }
        //取完上一个索引后取下一个索引
        $DataSeek = fread($fd, 3);
        if (strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if (strlen($ipData2) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if ($ip2num < 0) {
            $ip2num += pow(2, 32);
        }
        //没找到提示未知
        if ($ip2num < $ipNum) {
            if ($Middle == $BeginNum) {
                fclose($fd);
                return 'Unknown';
            }
            $BeginNum = $Middle;
        }
    }
    $ipFlag = fread($fd, 1);
    if ($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if (strlen($ipSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }
    if ($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if (strlen($AddrSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while (($char = fread($fd, 1)) != chr(0)) {
            $ipAddr2 .= $char;
        }
        $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
        fseek($fd, $AddrSeek);
        while (($char = fread($fd, 1)) != chr(0)) {
            $ipAddr1 .= $char;
        }
    } else {
        fseek($fd, -1, SEEK_CUR);
        while (($char = fread($fd, 1)) != chr(0)) {
            $ipAddr1 .= $char;
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if (strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while (($char = fread($fd, 1)) != chr(0)) {
            $ipAddr2 .= $char;
        }
    }
    fclose($fd);
    //最后做相应的替换操作后返回结果
    if (preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
    $ipaddr = "$ipAddr1 $ipAddr2";
    $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
        $ipaddr = 'Unknown';
    }
    $ipaddr = iconv('gbk', 'utf-8//IGNORE', $ipaddr); //转换编码，如果网页的gbk可以删除此行
    return $ipaddr;
}

//判断是否为FirFox
function isFireFox() { //add by zx 2011-0326 是否为火狐调整宽度
    if (stripos($_SERVER["HTTP_USER_AGENT"], "firefox")) {
        return 1;
    } else {
        return 0;
    }
}

function isSafari() { //add by zx 2011-0326 是否为Safari
    if (stripos($_SERVER["HTTP_USER_AGENT"], "Safari")) {
        return 1;
    } else {
        return 0;
    }
}

function isSafari6() { //add by 2012-0726 是否为Safari6
    if (stripos($_SERVER["HTTP_USER_AGENT"], "Safari")) {//&& (stripos($_SERVER["HTTP_USER_AGENT"],"Version/6") ||  stripos($_SERVER["HTTP_USER_AGENT"],"Version/7"))
        return 1;
    } else {
        return 0;
    }
}

function isGoogleChrome() { //add by 2012-0726 是否为Chrome
    if (stripos($_SERVER["HTTP_USER_AGENT"], "Chrome")) {
        return 1;
    } else {
        return 0;
    }
}

function extend_1($file_name) {
    $retval = "";
    $pt = strrpos($file_name, ".");
    if ($pt) {
        $retval = substr($file_name, $pt + 1, strlen($file_name) - $pt);
    }
    return ($retval);
}

//方法二
function extend_2($file_name) {
    $extend = pathinfo($file_name);
    $extend = strtolower($extend["extension"]);
    return $extend;
}

//方法三
function extend_3($file_name) {
    $extend = explode(".", $file_name);
    $va = count($extend) - 1;
    return $extend[$va];
}

//独立已更新 EWEN 2009-12-17 17:50
//读取用户端IP
function GetIP() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
        $ip = getenv("REMOTE_ADDR");
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = "unknown";
    }
    return ($ip);
}

function SetMaskDiv() {//遮罩初始化
    echo "<div id='divShadow' class='divShadow' style='display:none;'><div class='divInfo' id='divInfo'></div></div>
    <div id='divPageMask' class='divPageMask' style='display:none;'>
        <iframe scrolling='no' height='100%' width='100%' marginwidth='0' marginheight='0' src='../model/MaskBgColor.htm'></iframe>
    </div>";
}

function num2rmb($num) {
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    $step = 3;                //注意：UTF格式是，中文占3个字符,GB2312为2个字符
    $num = round($num, 2);
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "oh,sorry,the  number  is  too  long!";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            $n = substr($num, strlen($num) - 1, 1);
        } else {
            $n = $num % 10;
        }
        $p1 = substr($c1, $step * $n, $step);
        $p2 = substr($c2, $step * $i, $step);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        $num = $num / 10;
        $num = (int)$num;
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        $m = substr($c, $j, $step * 2);
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + $step);
            $c = $left . $right;
            $j = $j - $step;
            $slen = $slen - $step;
        }
        $j = $j + $step;
    }
    if (substr($c, strlen($c) - $step, $step) == '零') {
        $c = substr($c, 0, strlen($c) - $step);
    }
    return $c . "整";
}

//不足位前面补0
function AddedZero($Len, $Str) {
    if ($Len > count($Str)) {
        $Str = "0" . $Str;
    }
    return $Str;
}

//工时计算
function WorkTime($sTime, $eTime, $Action) {
    $HoursTemp = abs(strtotime($eTime) - strtotime($sTime)) / 3600;    //允许0.5小时，不向上取整
    return $HoursTemp;
}

//创建目录
function makedir($dir, $mode = "0777") {
    if (!$dir) {
        return 0;
    }
    $dir = str_replace("\\", "/", $dir);
    $mdir = "";
    foreach (explode("/", $dir) as $val) {
        $mdir .= $val . "/";
        if ($val == ".." || $val == ".") {
            continue;
        }
        if (!file_exists($mdir)) {
            if (!@mkdir($mdir, $mode)) {
                echo "创建目录 [" . $mdir . "]失败.";
                exit;
            }
        }
    }
    return true;
}

function outDateDiff($part, $date1, $date2) {
    $year1 = date("Y", strtotime($date1));
    $year2 = date("Y", strtotime($date2));
    $month2 = date("m", strtotime($date2));
    $month1 = date("m", strtotime($date1));
    $day2 = date("d", strtotime($date2));
    $day1 = date("d", strtotime($date1));
    $hour2 = date("d", strtotime($date2));
    $hour1 = date("d", strtotime($date1));
    $min2 = date("i", strtotime($date2));
    $min1 = date("i", strtotime($date1));
    $sec2 = date("s", strtotime($date2));
    $sec1 = date("s", strtotime($date1));
    $part = strtolower($part);
    $ret = 0;
    switch ($part) {
        case "year":
            $ret = $year2 - $year1;
            break;
        case "month"://离职工龄月份计算
            $ret = ($year2 - $year1) * 12 + $month2 - $month1;
            if ($day2 > $day1) {
                $ret = $ret * 1 + 1;
            }
            break;
        case "day":
            $ret = (mktime(0, 0, 0, $month2, $day2, $year2) - mktime(0, 0, 0, $month1, $day1, $year1)) / (3600 * 24);
            break;
        case "hour":
            $ret = (mktime($hour2, 0, 0, $month2, $day2, $year2) - mktime($hour1, 0, 0, $month1, $day1, $year1)) / 3600;
            break;
        case "min":
            $ret = (mktime($hour2, $min2, 0, $month2, $day2, $year2) - mktime($hour1, $min1, 0, $month1, $day1, $year1)) / 60;
            break;
        case "sec":
            $ret = $date2 - $date1;
            break;
        default:
            return $ret;
            break;
    }
    return $ret;
}

function noRowInfo($tableWidth, $Language = 0) {
    $Info = "没有相关资料";
    if ($Language == 1) {
        $Info = "&nbsp";
    }
    echo "<table width='$tableWidth' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;border: 1px solid #E2E8E8' bgcolor='#F5F5F5' >
        <tr><td colspan='9' scope='col' height='60' class='' align='center'>$Info</td></tr></table>";
}

//条件集合
//解密
function anmaOut($RuleStr, $EncryptStr = "", $Type = 1) {
    $oldStr = "";
    $SinkOrder = "xacdefghijklmbnopqrstuvwyz";
    $RuleLen = strlen($RuleStr);                    //渗透码长度，隔1取1
    for ($i = 1; $i < $RuleLen; $i++) {
        $inChar = substr($RuleStr, $i, 1);                //取出渗透码字符
        $inNum = strpos($SinkOrder, $inChar);            //将 渗透码字母 转为数字
        $oldStr .= substr($EncryptStr, $inNum, 1);        //从加密码中读取原文字符
        $i++;
    }

    //测试代码
    if ($oldStr == "") {
        $oldStr = $EncryptStr;
    }

    return $oldStr;
}

//加密开始
//随机码（载体）
$ReferenceMark = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
for ($i = 0; $i < 32; $i++) {
    $motherSTR[$i] = $ReferenceMark[rand(0, 60)];
}
//渗透序号码(不变)
$SinkOrder = "xacdefghijklmbnopqrstuvwyz";
function anmaIn($oldStr, $SinkOrder, $motherSTR) {
    $len = strlen($oldStr);
    //渗透过程
    $SinkOrderTemp = $SinkOrder;
    $RuleStr = "";
    for ($i = 0; $i < $len; $i++) {
        $tl = strlen($SinkOrderTemp) - 1;                    //渗透码长度
        $DisturbChar = rand(0, 9);                            //随机干扰码(数字0-9)
        $inChar = $SinkOrderTemp[rand(0, $tl)];            //渗透码字母
        if ($inChar != "") {
            $inNum = strpos($SinkOrder, $inChar);
        }                //将 渗透码字母 转为数字
        $motherSTR[$inNum] = substr($oldStr, $i, 1);        //原文字符替代随机码某位置的字符
        $RuleStr .= $DisturbChar . $inChar;                    //完整渗透码
        $SinkOrderTemp = str_replace($inChar, "", $SinkOrderTemp);//新的渗透序号码
    }
    //渗透结果
    $EncryptStr = "";
    for ($i = 0; $i < 32; $i++) {
        $EncryptStr .= $motherSTR[$i];
    }
    $reValue = $RuleStr . "|" . $EncryptStr;
    return $reValue;
}

//加密结束
//====================================================
//                FileName:download.class.php
//                Summary: 文件下载类
//                Author: feifengxlq
//      Email:feifengxlq@sohu.com
//                CreateTime: 2005-7-19
//                LastModifed:
//                copyright (c)2005 xlq.100steps.net  [email]feifengxlq@sohu.com[/email]
//   使用范例：
// $download=new download('php,exe,html',false);
//  if(!$download->downloadfile($filename))
//  {
//    echo $download->geterrormsg();
//  }
//====================================================
class download {
    var $debug = true;
    var $errormsg = '';
    var $Filter = array();
    var $filename = '';
    var $mineType = 'text/plain';
    var $xlq_filetype = array();

    function download($fileFilter = '', $isdebug = true) {
        $this->setFilter($fileFilter);
        $this->setdebug($isdebug);
        $this->setfiletype();
    }

    function setFilter($fileFilter) {
        if (empty($fileFilter)) {
            return;
        }
        $this->Filter = explode(',', strtolower($fileFilter));
    }

    function setdebug($debug) {
        $this->debug = $debug;
    }

    function setfilename($filename) {
        $this->filename = $filename;
    }

    function downloadfile($filename) {
        $this->setfilename($filename);
        if ($this->filecheck()) {
            $fn = array_pop(explode('/', strtr($this->filename, '', '/')));
            header("Pragma: public");
            header("Expires: 0"); // set expiration time
            header("Cache-Component: must-revalidate, post-check=0, pre-check=0");
            header("Content-type:" . $this->mineType);
            header("Content-Length: " . filesize($this->filename));
            header("Content-Disposition: attachment; filename=" . $fn . "");
            header('Content-Transfer-Encoding: binary');
            readfile($this->filename);
            return true;
        } else {
            return false;
        }
    }

    function geterrormsg() {
        return $this->errormsg;
    }

    function filecheck() {
        $filename = $this->filename;
        if (file_exists($filename)) {
            $filetype = strtolower(array_pop(explode('.', $filename)));
            if (in_array($filetype, $this->Filter)) {
                $this->errormsg .= $filename . '不允许下载！';
                if ($this->debug) {
                    exit($filename . '不允许下载！');
                }
                return false;
            } else {
                if (function_exists("mime_content_type")) {
                    $this->mineType = mime_content_type($filename);
                }
                if (empty($this->mineType)) {
                    if (isset($this->xlq_filetype[$filetype])) {
                        $this->mineType = $this->xlq_filetype[$filetype];
                    }
                }
                if (!empty($this->mineType)) {
                    return true;
                } else {
                    $this->errormsg .= '获取' . $filename . '文件类型时候发生错误，或者不存在预定文件类型内';
                    if ($this->debug) {
                        exit('获取文件类型出错');
                    }
                    return false;
                }
            }
        } else {
            $this->errormsg .= $filename . '不存在!';
            if ($this->debug) {
                exit($filename . '不存在!');
            }
            return false;
        }
    }

    function setfiletype() {
        $this->xlq_filetype['chm'] = 'application/octet-stream';
        $this->xlq_filetype['ppt'] = 'application/vnd.ms-powerpoint';
        $this->xlq_filetype['xls'] = 'application/vnd.ms-excel';
        $this->xlq_filetype['doc'] = 'application/msword';
        $this->xlq_filetype['exe'] = 'application/octet-stream';
        $this->xlq_filetype['rar'] = 'application/octet-stream';
        $this->xlq_filetype['js'] = "javascript/js";
        $this->xlq_filetype['css'] = "text/css";
        $this->xlq_filetype['hqx'] = "application/mac-binhex40";
        $this->xlq_filetype['bin'] = "application/octet-stream";
        $this->xlq_filetype['oda'] = "application/oda";
        $this->xlq_filetype['pdf'] = "application/pdf";
        $this->xlq_filetype['ai'] = "application/postsrcipt";
        $this->xlq_filetype['eps'] = "application/postsrcipt";
        $this->xlq_filetype['es'] = "application/postsrcipt";
        $this->xlq_filetype['rtf'] = "application/rtf";
        $this->xlq_filetype['mif'] = "application/x-mif";
        $this->xlq_filetype['csh'] = "application/x-csh";
        $this->xlq_filetype['dvi'] = "application/x-dvi";
        $this->xlq_filetype['hdf'] = "application/x-hdf";
        $this->xlq_filetype['nc'] = "application/x-netcdf";
        $this->xlq_filetype['cdf'] = "application/x-netcdf";
        $this->xlq_filetype['latex'] = "application/x-latex";
        $this->xlq_filetype['ts'] = "application/x-troll-ts";
        $this->xlq_filetype['src'] = "application/x-wais-source";
        $this->xlq_filetype['zip'] = "application/zip";
        $this->xlq_filetype['bcpio'] = "application/x-bcpio";
        $this->xlq_filetype['cpio'] = "application/x-cpio";
        $this->xlq_filetype['gtar'] = "application/x-gtar";
        $this->xlq_filetype['shar'] = "application/x-shar";
        $this->xlq_filetype['sv4cpio'] = "application/x-sv4cpio";
        $this->xlq_filetype['sv4crc'] = "application/x-sv4crc";
        $this->xlq_filetype['tar'] = "application/x-tar";
        $this->xlq_filetype['ustar'] = "application/x-ustar";
        $this->xlq_filetype['man'] = "application/x-troff-man";
        $this->xlq_filetype['sh'] = "application/x-sh";
        $this->xlq_filetype['tcl'] = "application/x-tcl";
        $this->xlq_filetype['tex'] = "application/x-tex";
        $this->xlq_filetype['texi'] = "application/x-texinfo";
        $this->xlq_filetype['texinfo'] = "application/x-texinfo";
        $this->xlq_filetype['t'] = "application/x-troff";
        $this->xlq_filetype['tr'] = "application/x-troff";
        $this->xlq_filetype['roff'] = "application/x-troff";
        $this->xlq_filetype['shar'] = "application/x-shar";
        $this->xlq_filetype['me'] = "application/x-troll-me";
        $this->xlq_filetype['ts'] = "application/x-troll-ts";
        $this->xlq_filetype['gif'] = "image/gif";
        $this->xlq_filetype['jpeg'] = "image/pjpeg";
        $this->xlq_filetype['jpg'] = "image/pjpeg";
        $this->xlq_filetype['jpe'] = "image/pjpeg";
        $this->xlq_filetype['ras'] = "image/x-cmu-raster";
        $this->xlq_filetype['pbm'] = "image/x-portable-bitmap";
        $this->xlq_filetype['ppm'] = "image/x-portable-pixmap";
        $this->xlq_filetype['xbm'] = "image/x-xbitmap";
        $this->xlq_filetype['xwd'] = "image/x-xwindowdump";
        $this->xlq_filetype['ief'] = "image/ief";
        $this->xlq_filetype['tif'] = "image/tiff";
        $this->xlq_filetype['tiff'] = "image/tiff";
        $this->xlq_filetype['pnm'] = "image/x-portable-anymap";
        $this->xlq_filetype['pgm'] = "image/x-portable-graymap";
        $this->xlq_filetype['rgb'] = "image/x-rgb";
        $this->xlq_filetype['xpm'] = "image/x-xpixmap";
        $this->xlq_filetype['txt'] = "text/plain";
        $this->xlq_filetype['c'] = "text/plain";
        $this->xlq_filetype['cc'] = "text/plain";
        $this->xlq_filetype['h'] = "text/plain";
        $this->xlq_filetype['html'] = "text/html";
        $this->xlq_filetype['htm'] = "text/html";
        $this->xlq_filetype['htl'] = "text/html";
        $this->xlq_filetype['rtx'] = "text/richtext";
        $this->xlq_filetype['etx'] = "text/x-setext";
        $this->xlq_filetype['tsv'] = "text/tab-separated-values";
        $this->xlq_filetype['mpeg'] = "video/mpeg";
        $this->xlq_filetype['mpg'] = "video/mpeg";
        $this->xlq_filetype['mpe'] = "video/mpeg";
        $this->xlq_filetype['avi'] = "video/x-msvideo";
        $this->xlq_filetype['qt'] = "video/quicktime";
        $this->xlq_filetype['mov'] = "video/quicktime";
        $this->xlq_filetype['moov'] = "video/quicktime";
        $this->xlq_filetype['movie'] = "video/x-sgi-movie";
        $this->xlq_filetype['au'] = "audio/basic";
        $this->xlq_filetype['snd'] = "audio/basic";
        $this->xlq_filetype['wav'] = "audio/x-wav";
        $this->xlq_filetype['aif'] = "audio/x-aiff";
        $this->xlq_filetype['aiff'] = "audio/x-aiff";
        $this->xlq_filetype['aifc'] = "audio/x-aiff";
        $this->xlq_filetype['swf'] = "application/x-shockwave-flash";
    }
}

function PassParameter($Parameter) {
    $pStr = explode(",", $Parameter);
    for ($i = 0; $i < count($pStr); $i++) {
        $pName = $pStr[$i];
        $i++;
        $pValue = $pStr[$i];
        echo "<input name=$pName type='hidden' id=$pName value='$pValue'>";
        //echo"<input name=$pName type='text' id=$pName value='$pValue'>";
    }
}

function Usable_Currency($link_id) {
    echo "<select name='Currency' id='Currency' onchange='changeCurrency();' style='width:200px'>";
    $currency_result = mysql_query("SELECT * FROM $DataPublic.currencydata WHERE 1 and Estate=1", $link_id);
    if ($currency_row = mysql_fetch_array($currency_result)) {
        do {
            $Symbol = $currency_row["Symbol"];
            $Name = $currency_row["Name"];
            if ($Symbol == "RMB") {
                $selected = "selected";
            } else {
                $selected = "";
            }
            echo "<option value='$Symbol' $selected>$Name</option>";
        } while ($currency_row = mysql_fetch_array($currency_result));
    }
    echo "</select>";
}

//0-->空
function SpaceValue0($STR) {
    if ($STR == 0) {
        return "&nbsp;";
    } else {
        return floor($STR);
    }
}

function zerotospace($STR) {
    if ($STR == 0) {
        return "&nbsp;";
    } else {
        return $STR;
    }
}

function SpaceValue($STR) {
    if ($STR == "") {
        return "&nbsp;";
    } else {
        return $STR;
    }
}

//汉字转拼音
class chinese {
    var $d = array(
        array("A", -20319),
        array("Ai", -20317),
        array("An", -20304),
        array("Ang", -20295),
        array("Ao", -20292),
        array("Ba", -20283),
        array("Bai", -20265),
        array("Ban", -20257),
        array("Bang", -20242),
        array("Bao", -20230),
        array("Bei", -20051),
        array("Ben", -20036),
        array("Beng", -20032),
        array("Bi", -20026),
        array("Bian", -20002),
        array("Biao", -19990),
        array("Bie", -19986),
        array("Bin", -19982),
        array("Bing", -19976),
        array("Bo", -19805),
        array("Bu", -19784),
        array("Ca", -19775),
        array("Cai", -19774),
        array("Can", -19763),
        array("Cang", -19756),
        array("Cao", -19751),
        array("Ce", -19746),
        array("Ceng", -19741),
        array("Cha", -19739),
        array("Chai", -19728),
        array("Chan", -19725),
        array("Chang", -19715),
        array("Chao", -19540),
        array("Che", -19531),
        array("Chen", -19525),
        array("Cheng", -19515),
        array("Chi", -19500),
        array("Chong", -19484),
        array("Chou", -19479),
        array("Chu", -19467),
        array("Chuai", -19289),
        array("Chuan", -19288),
        array("Chuang", -19281),
        array("Chui", -19275),
        array("Chun", -19270),
        array("Chuo", -19263),
        array("Ci", -19261),
        array("Cong", -19249),
        array("Cou", -19243),
        array("Cu", -19242),
        array("Cuan", -19238),
        array("Cui", -19235),
        array("Cun", -19227),
        array("Cuo", -19224),
        array("Da", -19218),
        array("Dai", -19212),
        array("Dan", -19038),
        array("Dang", -19023),
        array("Dao", -19018),
        array("De", -19006),
        array("Deng", -19003),
        array("Di", -18996),
        array("Dian", -18977),
        array("Diao", -18961),
        array("Die", -18952),
        array("Ding", -18783),
        array("Diu", -18774),
        array("Dong", -18773),
        array("Dou", -18763),
        array("Du", -18756),
        array("Duan", -18741),
        array("Dui", -18735),
        array("Dun", -18731),
        array("Duo", -18722),
        array("E", -18710),
        array("En", -18697),
        array("Er", -18696),
        array("Fa", -18526),
        array("Fan", -18518),
        array("Fang", -18501),
        array("Fei", -18490),
        array("Fen", -18478),
        array("Feng", -18463),
        array("Fo", -18448),
        array("Fou", -18447),
        array("Fu", -18446),
        array("Ga", -18239),
        array("Gai", -18237),
        array("Gan", -18231),
        array("Gang", -18220),
        array("Gao", -18211),
        array("Ge", -18201),
        array("Gei", -18184),
        array("Gen", -18183),
        array("Geng", -18181),
        array("Gong", -18012),
        array("Gou", -17997),
        array("Gu", -17988),
        array("Gua", -17970),
        array("Guai", -17964),
        array("Guan", -17961),
        array("Guang", -17950),
        array("Gui", -17947),
        array("Gun", -17931),
        array("Guo", -17928),
        array("Ha", -17922),
        array("Hai", -17759),
        array("Han", -17752),
        array("Hang", -17733),
        array("Hao", -17730),
        array("He", -17721),
        array("Hei", -17703),
        array("Hen", -17701),
        array("Heng", -17697),
        array("Hong", -17692),
        array("Hou", -17683),
        array("Hu", -17676),
        array("Hua", -17496),
        array("Huai", -17487),
        array("Huan", -17482),
        array("Huang", -17468),
        array("Hui", -17454),
        array("Hun", -17433),
        array("Huo", -17427),
        array("Ji", -17417),
        array("Jia", -17202),
        array("Jian", -17185),
        array("Jiang", -16983),
        array("Jiao", -16970),
        array("Jie", -16942),
        array("Jin", -16915),
        array("Jing", -16733),
        array("Jiong", -16708),
        array("Jiu", -16706),
        array("Ju", -16689),
        array("Juan", -16664),
        array("Jue", -16657),
        array("Jun", -16647),
        array("Ka", -16474),
        array("Kai", -16470),
        array("Kan", -16465),
        array("Kang", -16459),
        array("Kao", -16452),
        array("Ke", -16448),
        array("Ken", -16433),
        array("Keng", -16429),
        array("Kong", -16427),
        array("Kou", -16423),
        array("Ku", -16419),
        array("Kua", -16412),
        array("Kuai", -16407),
        array("Kuan", -16403),
        array("Kuang", -16401),
        array("Kui", -16393),
        array("Kun", -16220),
        array("Kuo", -16216),
        array("La", -16212),
        array("Lai", -16205),
        array("Lan", -16202),
        array("Lang", -16187),
        array("Lao", -16180),
        array("Le", -16171),
        array("Lei", -16169),
        array("Leng", -16158),
        array("Li", -16155),
        array("Lia", -15959),
        array("Lian", -15958),
        array("Liang", -15944),
        array("Liao", -15933),
        array("Lie", -15920),
        array("Lin", -15915),
        array("Ling", -15903),
        array("Liu", -15889),
        array("Long", -15878),
        array("Lou", -15707),
        array("Lu", -15701),
        array("Lv", -15681),
        array("Luan", -15667),
        array("Lue", -15661),
        array("Lun", -15659),
        array("Luo", -15652),
        array("Ma", -15640),
        array("Mai", -15631),
        array("Man", -15625),
        array("Mang", -15454),
        array("Mao", -15448),
        array("Me", -15436),
        array("Mei", -15435),
        array("Men", -15419),
        array("Meng", -15416),
        array("Mi", -15408),
        array("Mian", -15394),
        array("Miao", -15385),
        array("Mie", -15377),
        array("Min", -15375),
        array("Ming", -15369),
        array("Miu", -15363),
        array("Mo", -15362),
        array("Mou", -15183),
        array("Mu", -15180),
        array("Na", -15165),
        array("Nai", -15158),
        array("Nan", -15153),
        array("Nang", -15150),
        array("Nao", -15149),
        array("Ne", -15144),
        array("Nei", -15143),
        array("Nen", -15141),
        array("Neng", -15140),
        array("Ni", -15139),
        array("Nian", -15128),
        array("Niang", -15121),
        array("Niao", -15119),
        array("Nie", -15117),
        array("Nin", -15110),
        array("Ning", -15109),
        array("Niu", -14941),
        array("Nong", -14937),
        array("Nu", -14933),
        array("Nv", -14930),
        array("Nuan", -14929),
        array("Nue", -14928),
        array("Nuo", -14926),
        array("O", -14922),
        array("Ou", -14921),
        array("Pa", -14914),
        array("Pai", -14908),
        array("Pan", -14902),
        array("Pang", -14894),
        array("Pao", -14889),
        array("Pei", -14882),
        array("Pen", -14873),
        array("Peng", -14871),
        array("Pi", -14857),
        array("Pian", -14678),
        array("Piao", -14674),
        array("Pie", -14670),
        array("Pin", -14668),
        array("Ping", -14663),
        array("Po", -14654),
        array("Pu", -14645),
        array("Qi", -14630),
        array("Qia", -14594),
        array("Qian", -14429),
        array("Qiang", -14407),
        array("Qiao", -14399),
        array("Qie", -14384),
        array("Qin", -14379),
        array("Qing", -14368),
        array("Qiong", -14355),
        array("Qiu", -14353),
        array("Qu", -14345),
        array("Quan", -14170),
        array("Que", -14159),
        array("Qun", -14151),
        array("Ran", -14149),
        array("Rang", -14145),
        array("Rao", -14140),
        array("Re", -14137),
        array("Ren", -14135),
        array("Reng", -14125),
        array("Ri", -14123),
        array("Rong", -14122),
        array("Rou", -14112),
        array("Ru", -14109),
        array("Ruan", -14099),
        array("Rui", -14097),
        array("Run", -14094),
        array("Ruo", -14092),
        array("Sa", -14090),
        array("Sai", -14087),
        array("San", -14083),
        array("Sang", -13917),
        array("Sao", -13914),
        array("Se", -13910),
        array("Sen", -13907),
        array("Seng", -13906),
        array("Sha", -13905),
        array("Shai", -13896),
        array("Shan", -13894),
        array("Shang", -13878),
        array("Shao", -13870),
        array("She", -13859),
        array("Shen", -13847),
        array("Sheng", -13831),
        array("Shi", -13658),
        array("Shou", -13611),
        array("Shu", -13601),
        array("Shua", -13406),
        array("Shuai", -13404),
        array("Shuan", -13400),
        array("Shuang", -13398),
        array("Shui", -13395),
        array("Shun", -13391),
        array("Shuo", -13387),
        array("Si", -13383),
        array("Song", -13367),
        array("Sou", -13359),
        array("Su", -13356),
        array("Suan", -13343),
        array("Sui", -13340),
        array("Sun", -13329),
        array("Suo", -13326),
        array("Ta", -13318),
        array("Tai", -13147),
        array("Tan", -13138),
        array("Tang", -13120),
        array("Tao", -13107),
        array("Te", -13096),
        array("Teng", -13095),
        array("Ti", -13091),
        array("Tian", -13076),
        array("Tiao", -13068),
        array("Tie", -13063),
        array("Ting", -13060),
        array("Tong", -12888),
        array("Tou", -12875),
        array("Tu", -12871),
        array("Tuan", -12860),
        array("Tui", -12858),
        array("Tun", -12852),
        array("Tuo", -12849),
        array("Wa", -12838),
        array("Wai", -12831),
        array("Wan", -12829),
        array("Wang", -12812),
        array("Wei", -12802),
        array("Wen", -12607),
        array("Weng", -12597),
        array("Wo", -12594),
        array("Wu", -12585),
        array("Xi", -12556),
        array("Xia", -12359),
        array("Xian", -12346),
        array("Xiang", -12320),
        array("Xiao", -12300),
        array("Xie", -12120),
        array("Xin", -12099),
        array("Xing", -12089),
        array("Xiong", -12074),
        array("Xiu", -12067),
        array("Xu", -12058),
        array("Xuan", -12039),
        array("Xue", -11867),
        array("Xun", -11861),
        array("Ya", -11847),
        array("Yan", -11831),
        array("Yang", -11798),
        array("Yao", -11781),
        array("Ye", -11604),
        array("Yi", -11589),
        array("Yin", -11536),
        array("Ying", -11358),
        array("Yo", -11340),
        array("Yong", -11339),
        array("You", -11324),
        array("Yu", -11303),
        array("Yuan", -11097),
        array("Yue", -11077),
        array("Yun", -11067),
        array("Za", -11055),
        array("Zai", -11052),
        array("Zan", -11045),
        array("Zang", -11041),
        array("Zao", -11038),
        array("Ze", -11024),
        array("Zei", -11020),
        array("Zen", -11019),
        array("Zeng", -11018),
        array("Zha", -11014),
        array("Zhai", -10838),
        array("Zhan", -10832),
        array("Zhang", -10815),
        array("Zhao", -10800),
        array("Zhe", -10790),
        array("Zhen", -10780),
        array("Zheng", -10764),
        array("Zhi", -10587),
        array("Zhong", -10544),
        array("Zhou", -10533),
        array("Zhu", -10519),
        array("Zhua", -10331),
        array("Zhuai", -10329),
        array("Zhuan", -10328),
        array("Zhuang", -10322),
        array("Zhui", -10315),
        array("Zhun", -10309),
        array("Zhuo", -10307),
        array("Zi", -10296),
        array("Zong", -10281),
        array("Zou", -10274),
        array("Zu", -10270),
        array("Zuan", -10262),
        array("Zui", -10260),
        array("Zun", -10256),
        array("Zuo", -10254)
    );

    function g($num) {
        if ($num > 0 && $num < 160) {
            return chr($num);
        } elseif ($num < -20319 || $num > -10247) {
            return "";
        } else {
            for ($i = count($this->d) - 1; $i >= 0; $i--) {
                if ($this->d[$i][1] <= $num) {
                    break;
                }
            }
            return $this->d[$i][0];
        }
    }

    function c($str) {
        $ret = "";
        $str = iconv("UTF-8", "GB2312", $str);//uft-8转gb2312
        for ($i = 0; $i < strlen($str); $i++) {
            $p = ord(substr($str, $i, 1));
            if ($p > 160) {
                $q = ord(substr($str, ++$i, 1));
                $p = $p * 256 + $q - 65536;
            }
            $ret .= $this->g($p);
        }
        return $ret;
    }
}

//结束汉字转拼音
//转英文日期
function toenglishdate($Date_temp) {
    $OutDate = date("j-M-Y", strtotime($Date_temp));
    $DateStr = explode("-", $OutDate); //日
    $DayStr = $DateStr[0] . "th" . " " . $DateStr[1] . " " . $DateStr[2];
    if (($DateStr[0] == "1") or ($DateStr[0] == "21") or ($DateStr[0] == "31")) {
        $DayStr = $DateStr[0] . "st" . " " . $DateStr[1] . " " . $DateStr[2];
    }
    if (($DateStr[0] == "2") or ($DateStr[0] == "22")) {
        $DayStr = $DateStr[0] . "nd" . " " . $DateStr[1] . " " . $DateStr[2];
    }
    if (($DateStr[0] == "3") or ($DateStr[0] == "23")) {
        $DayStr = $DateStr[0] . "rd" . " " . $DateStr[1] . " " . $DateStr[2];
    }
    return $DayStr;
}

//自动转码
function getSafeCode($string) {
    $string_1 = $string;   //原码
    $string_2 = iconv("UTF-8", "gb2312", $string_1);//转换为汉字编码
    $string_3 = iconv("gb2312", "UTF-8", $string_2);//转换为UTF-8
    if (strlen($string_1) == strlen($string_3)) {
        return $string_2;
    } else {
        return $string_1;
    }
}

//转码
function utf2gb($string) {
    $out = iconv("UTF-8", "gb2312", $string);
    return $out;
}

function FormatSTR($String) {//首字母大写
    $String = trim($String);//去前后空格
    $String = Chop($String);//去除连续空格
    $matches = array();
    preg_match('/^(.{1})(.*)$/us', $String, $matches);
    $String = strtr($matches[1], 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') . $matches[2];
    return $String;
}

function Capitalize($String) {//每一段首字母大写
    $String = trim($String);//去前后空格
    $tokens = explode(' ', $String);
    for ($i = 0, $n = count($tokens); $i < $n; $i++) {
        $matches = array();
        preg_match('/^(.{1})(.*)$/us', $tokens[$i], $matches);
        $tokens[$i] = strtr($matches[1], 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') . $matches[2];
    }
    $String = implode(' ', $tokens);
    return $String;
}

//上传图片
function UploadPictures($OldFile, $PreFileName, $FileDir) {
    //产品图片路径
    $FilePath = "../download/" . $FileDir . "/";
    $FileName = $FilePath . $PreFileName;

    //$copymes = copy($OldFile, $FileName);
    mkdir(dirname($FileName), 0777, true);
    $copymes = move_uploaded_file($OldFile['tmp_name'], $FileName);

    if ($copymes) {
        $FileName = $PreFileName;
    } else {
        $FileName = "";
    }
    return $FileName;
}

//二合一使用
function UploadFiles($OldFile, $PreFileName, $FilePath) {
    //产品图片路径
    $FileName = $FilePath . $PreFileName;
    //$copymes = copy($OldFile,$FileName);
    mkdir(dirname($FileName), 0777, true);
    $copymes = move_uploaded_file($OldFile['tmp_name'], $FileName);
    if ($copymes) {
        $FileName = $PreFileName;
    } else {
        $FileName = "";
    }
    return $FileName;
}

function newGetDateSTR() {
    $array_date = getdate();
    if ($array_date[mon] < 10) {
        $array_date[mon] = "0" . $array_date[mon];
    }
    if ($array_date[mday] < 10) {
        $array_date[mday] = "0" . $array_date[mday];
    }
    if ($array_date[hours] < 10) {
        $array_date[hours] = "0" . $array_date[hours];
    }
    if ($array_date[minutes] < 10) {
        $array_date[minutes] = "0" . $array_date[minutes];
    }
    if ($array_date[seconds] < 10) {
        $array_date[seconds] = "0" . $array_date[seconds];
    }
    $temptime = $array_date[year] . $array_date[mon] . $array_date[mday] . $array_date[hours] . $array_date[minutes] . $array_date[seconds];
    return $temptime;
}

function toSpace($STR) {
    if ($STR == "") {
        return "&nbsp;";
    } else {
        return $STR;
    }
}

//日期差：天数
function CountDays($TempTime, $Action = "") {
    //$d1=substr($TempTime,17,2);//秒
    //$d2=substr($TempTime,14,2);//分
    //$d3=substr($TempTime,11,2);// 时
    $d1 = 0;
    $d2 = 0;
    $d3 = 0;
    $d4 = substr($TempTime, 8, 2); //日
    $d5 = substr($TempTime, 5, 2); //月
    $d6 = substr($TempTime, 0, 4); //年
    $now_T = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
    $now_S = mktime("$d3", "$d2", "$d1", "$d5", "$d4", "$d6");
    $end_TS = intval(($now_T - $now_S) / (60 * 60 * 24));   //计算  剩余分钟
    if ($Action == 0) {
        switch ($end_TS) {
            case 0:
                $end_TS = "today";
                break;
            case 1:
                $end_TS = "1day";
                break;
            default:
                $end_TS = $end_TS . "days";
                break;
        }
    } else {
        if ($Action == 2) {
            $end_TS = $end_TS == 0 ? "今天" : $end_TS . "天";
        } else {
            if ($Action == 5) {
                $end_TS = $end_TS;
            } else {
                $end_TS = $TempTime . ":" . $end_TS . "天";
            }
        }
    }
    return $end_TS;
}

function AskDay($TempTime) {//判断是否大于30天，是返回1，否返回0
    $d1 = 0;
    $d2 = 0;
    $d3 = 0;
    $RebackTS = "";
    $d4 = substr($TempTime, 8, 2); //日
    $d5 = substr($TempTime, 5, 2); //月
    $d6 = substr($TempTime, 0, 4); //年
    $now_T = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
    $now_S = mktime("$d3", "$d2", "$d1", "$d5", "$d4", "$d6");
    $end_TS = intval(($now_T - $now_S) / (60 * 60 * 24));   //计算  剩余分钟
    if ($end_TS >= 30) {
        $RebackTS = "daysgreen.gif";
    }
    if ($end_TS >= 60) {
        $RebackTS = "daysyellow.gif";
    }
    if ($end_TS >= 90) {
        $RebackTS = "daysred.gif";
    }
    return $RebackTS;
}

function DateDiff($TempTime) {//判断是否大于30天，是返回1，否返回0
    $d1 = 0;
    $d2 = 0;
    $d3 = 0;
    $RebackTS = "";
    $d4 = substr($TempTime, 8, 2); //日
    $d5 = substr($TempTime, 5, 2); //月
    $d6 = substr($TempTime, 0, 4); //年
    $now_T = mktime("$d3", "$d2", "$d1", date("m"), date("d"), date("Y"));
    $now_S = mktime("$d3", "$d2", "$d1", "$d5", "$d4", "$d6");
    $end_TS = intval(($now_S - $now_T) / (60 * 60 * 24));   //计算  剩余分钟
    return $end_TS;
}

function ChangeWtitle($Title) {
    echo "<SCRIPT type='text/javascript'>top.document.title=\"$Title\";</script>";
}

//旧表尾函数，将淘汰
function Page_Bottom($RecordToTal, $i, $Page, $Page_count, $timer, $TypeSTR, $Login_WebStyle, $tableWidth) {
    //    document.getElementById("tbl").style.width
    echo "<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
          <tr><td height='5' colspan='6' class='A0011'>&nbsp;</td></tr>
        <tr>
            <td id='menuB1' class='A1000' $td_bgcolor>";
    $timer->stop();
    echo "执行时间：" . $timer->spent();
    echo "</td>
            <td class='' id='menuB2' align='center'>
            <table border='0' align='center' cellspacing='0'>
               <tr>
                <td class='readlink'>
                    <nobr>";
    if ($Page_count != "") {
        echo "第";
        echo "<select name='Page' id='Page' onchange='document.form1.submit()'>";
        for ($j = 1; $j <= $Page_count; $j++) {
            if ($j == $Page) {
                echo "<option value='$j' selected>$j</option>";
            } else {
                echo "<option value='$j'>$j</option>";
            }
        }
        echo "</select>页" . $i . "条记录,共" . $Page_count . "页" . $RecordToTal . "条记录&nbsp;";
    } else {
        echo "共1页,记录总数: $i 条&nbsp;";
    }
    echo "</nobr></tr></table>
        </td>
    </tr>
</table>";
    echo "<SCRIPT LANGUAGE='JavaScript'>
console.log(document.all.menuB1);
document.all.menuB1.style.width=menuT1.clientWidth;
document.all.menuB2.style.width=menuT2.clientWidth;
</script>";
    echo "<br>";
    if ($Form == "Yes") {
        echo "</form>";
    }
    echo "</body>
</html>
";
}

//新的表尾函数:总记录数，当前页记录数，当前页码，是否分页，页默认记录数，页面执行起始时间，页面CSS目录，表格宽度
function pBottom($RecordToTal, $i, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth) {
    echo "<div id='menuB2' align='center' STYLE='width: 300px;position: fixed;bottom: 30px; right: 50px;opacity: 0.2;transition:all .5s' onmouseover='mouseOver(this)' onmouseout='mouseOut(this)'><nobr>";
    if ($Pagination == 1) {//分页
        if ($RecordToTal <= $Page_Size) {
            $Page_count = 1;
        } else {
            if ($RecordToTal % $Page_Size) {
                $Page_count = (int)($RecordToTal / $Page_Size) + 1;
            } else {
                $Page_count = $RecordToTal / $Page_Size;
            }
        }
        echo "第<select name='Page' id='Page' onchange='openLoading();document.form1.submit()'>";//增加loading，by ckt 2017-12-26
        for ($j = 1; $j <= $Page_count; $j++) {
            if ($j == $Page) {
                echo "<option value='$j' selected>$j</option>";
            } else {
                echo "<option value='$j'>$j</option>";
            }
        }
        echo "</select>页" . $i . "条记录,共" . $Page_count . "页" . $RecordToTal . "条记录&nbsp;";
    } else {
        echo "共1页,记录总数: $RecordToTal 条&nbsp;";
    }
    echo "</nobr></div>";
    echo "<SCRIPT LANGUAGE='JavaScript'>
if (document.all.menuB1 !== undefined && document.all.menuB2 !== undefined){
    document.all.menuB1.style.width=menuT1.clientWidth;
    document.all.menuB2.style.width=menuT2.clientWidth;
}
</script>";
    echo "<input name='IdCount' type='hidden' id='IdCount' value='$i'></form></body></html><br>";
}

function List_Title($Th_Col, $Sign, $Height, $special) {
    if ($Height == 1) {        //高度自动
        $HeightSTR = "height='25'";
    } else {
        $HeightSTR = "height='30'";
    }
    $Field = explode("|", $Th_Col);
    $Count = count($Field);
    if ($Sign == 1) {
        $tId = "id='TableHead'";
    }
    $tableWidth = 0;
    for ($i = 0; $i < $Count; $i = $i + 2) {
        $j = $i;
        $k = $j + 1;
        $tableWidth += $Field[$k];
    }
    if ($special == 1) {
        $tableWidth = $tableWidth + ceil($Count * 1.42);
    }else{
        if (isFireFox() == 1) {
            $tableWidth = $tableWidth + $Count * 2;
        }
        if (isSafari6() == 1) {
            $tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
        }
        if (isGoogleChrome() == 1) {
            $tableWidth = $tableWidth + ceil($Count * 1.5);
        }
    }
    for ($i = 0; $i < $Count; $i = $i + 2) {
        if ($Sign == 1) {
            $Class_Temp = $i == 0 ? "" : "";
        } else {
            $Class_Temp = $i == 0 ? "" : "";
        }
        $j = $i;
        $k = $j + 1;
        if (isSafari6() == 0) {
            if ($k == ($Count - 1)) {
                $Field[$k] = "";
            }
        }
        $h = $j + 2;
        if (($Field[$j] == "中文名" && $Field[$h] == "&nbsp;") || $Field[$j] == "&nbsp;") {
            $Class_Temp = $Sign == 1 ? "" : "";
        }
        $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'>$Field[$j]</td>";
    }
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;background-color:#F0F5F8' $tId><tr $HeightSTR class='' align='center'>" . $TableStr . "</tr></table>";
    if ($Sign == 0) {
        echo "<iframe name=\"download\" style=\"display:none\"></iframe>";
    }
}

function get_currentDir($level) {
    switch ($level) {
        case 1:
            $DirArray = explode('/', $_SERVER['PHP_SELF']);
            $DirArray = array_reverse($DirArray);
            $FromDir = $DirArray['1'];
            break;
    }
    return $FromDir;
}

//获取第几周的开始、结束时间
function GetWeekToDate($Weeks, $dateFormat) {
    $year = substr($Weeks, 0, 4);
    $week = substr($Weeks, 4, 2);
    $timestamp = mktime(1, 0, 0, 1, 1, $year);
    $firstday = date("N", $timestamp);
    if ($firstday > 4) {
        $firstweek = strtotime('+' . (8 - $firstday) . ' days', $timestamp);
    } else {
        $firstweek = strtotime('-' . ($firstday - 1) . ' days', $timestamp);
    }
    $monday = strtotime('+' . ($week - 1) . ' week', $firstweek);
    $sunday = strtotime('+6 days', $monday);
    $start = date("$dateFormat", $monday);
    $end = date("$dateFormat", $sunday);
    return array($start, $end);
}

function GetWeek($Leadtime, $link_id) {
    if ($Leadtime != "" && $Leadtime != "&nbsp;") {
        if ($curWeeks == "") {
            $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek", $link_id));
            $curWeeks = $dateResult["CurWeek"];
        }
        $Leadtime = str_replace("*", "", $Leadtime);
        $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek", $link_id));
        $PIWeek = $dateResult["PIWeek"];
        if ($PIWeek > 0) {
            $week = substr($PIWeek, 4, 2);
            //$dateArray= GetWeekToDate($PIWeek,"m/d");
            $weekName = "Week " . $week;
            //$dateSTR=$dateArray[0] . "-" .  $dateArray[1];
        }
    }
    return $weekName;
}

function getRandIndex() {
    $time = explode(" ", microtime());
    $timetemp = substr($time [1], 2) . substr($time [0], 2, 4) . rand(100, 999);
    return $timetemp;
}

?>

