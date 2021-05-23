<?php
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../basic/chksession.php";
include "../cjgl/phpqrcode/phpqrcode.php";
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");
// 获取相关请求头
$cname_str = isset($cname_str) ? $cname_str : '';

// 处理相关数据
function trimAll($str)
{
    $reg = array(" ", "　", "\t", "\n", "\r");
    return str_replace($reg, ',', $str);
}

$cname_arr = array_chunk(array_values(array_filter(explode(',', trimAll($cname_str)))),3);
$ret = '';
foreach ($cname_arr as $cname) {
   $cnameTemp = implode('-',$cname);
   $cname = $cnameTemp . '%';
   // 领料表拥有此构件POrderId, 获取相应的构件id
   $sql = 'SELECT B.Id,D.POrderId,A.cName FROM productdata A 
           LEFT JOIN yw1_ordersheet B ON A.ProductId = B.ProductId 
           LEFT JOIN ck5_llsheet C ON C.POrderId = B.POrderId 
           LEFT JOIN yw1_scsheet D ON D.POrderId = B.POrderId 
           WHERE 1 
           AND A.cname LIKE "'.$cname.'"
           AND B.POrderId is not null 
           GROUP BY C.POrderId';

    $data = $myPDO->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($data) {
        // 批量生成二维码
        foreach ($data as $v) {
            $ret .= '|' . $v['Id'];
            $cName = $v['cName'];
            $POrderId = $v['POrderId'];
            $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'QRCode'.DIRECTORY_SEPARATOR;
            $PNG_WEB_DIR = 'QRCode/';
            if (!file_exists($PNG_TEMP_DIR)){
                mkdir($PNG_TEMP_DIR,0777);
            }
            $errorCorrectionLevel = 'L';
            $matrixPointSize = 6;
            $filename = $PNG_TEMP_DIR.$POrderId.".png";
            if (!file_exists($filename)) {
                QRcode::png($cName, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
            }
        }
    }else{
        continue;
    }

}
echo json_encode(array(
    'rlt' => ltrim($ret,'|')
));




