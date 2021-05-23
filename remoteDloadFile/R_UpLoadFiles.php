<?php
include_once "../basic/parameter.inc";   //加放数据库访问
include_once "../remoteDloadFile/model/modelfunction.php";

//获取用户姓名
/*
$staffResult=mysql_fetch_array(mysql_query("SELECT Name,JobId FROM $DataPublic.staffmain WHERE Number='$Login_P_Number'",$link_id));
$UserDir=$staffResult["Name"];
$UserJobId=$staffResult["JobId"];
*/

//if ($UserJobId==35){
switch ($UpFileSign) {  //$UpFileSign:
    case "stuffG":   //stuffG: 表示配件图档文件
        include_once "../remoteDloadFile/R_stuffimg_GfileUpLoad.php";
        break;

    case "stuffP":   //stuffP: 表示配件图片自动上传，add by zx 2012-05-30
        include_once "../remoteDloadFile/R_stuffimg_PfileUpLoad.php";
        break;

    case "stuffPDF":   //stuffG: 表示配件图片文件PDF  //stuffdata_updated.php Action=40

        include_once "../remoteDloadFile/R_stuffimg_pdffileUpLoad.php";
    break;

    case "productFile":   //产品原图
        include_once "../remoteDloadFile/R_productimg_fileUpLoad.php";
        break;

    case "QCFile":   //QC原图
        //echo "^ $FileRemark 0|$ProductId|$originalPicture|";
        include_once "../remoteDloadFile/R_QCimg_fileUpLoad.php";
        break;
}
//}
?>