<?php

//扫描配件图片并更新电信---add by zx 2013-05-30
//$Date=date("Y-m-d");
$Date = date("Y-m-d H:i:s");
$handle = opendir("../download/tmp_stuffpdf/");
$returnstr = "";  //(远程更新)
$stuffFilePath = "../download/stufffile/";
$TempFilePath = "../download/tmp_stuffpdf/";
//$d=anmaIn("download/upload/",$SinkOrder,$motherSTR);
while ($Gfile = readdir($handle)) {
    $FileType = substr($Gfile, -3, 3);
    //$returnstr.="(strtolower($FileType)=='pdf') && (Gfile=='125938.pdf') | ";
    if (($Gfile != ".") && ($Gfile != "..") && (strtolower($FileType) == "pdf" || strtolower($FileType) == "jpg") && (strlen($Gfile) >= 9) && (strlen($Gfile) <= 10)) {
        //$Id=trim(preg_replace("[^0-9]","",$Gfile));
        $StuffId = trim(preg_replace("/([^0-9]+)/i", "", $Gfile));

        if (strtolower($FileType) == "jpg") {
            $pdffile = "$StuffId" . '_s.jpg';
        } else {
            $pdffile = "$StuffId" . '.pdf';
        }

        $stuffile = $stuffFilePath . $pdffile;
        $TempFile = $TempFilePath . $Gfile;

        if ($StuffId != "") {
            if (copy($TempFile, $stuffile)) {  //拷贝成功，则删除临时文件
                unlink($TempFile);
                $isPDFTOJPG = 0;
                if (strtolower($FileType) == "pdf") {
                    //真空罩、不干胶、说明书、胶袋、不干胶、白盒、隔版、双面胶、条码标签、外箱 直接转成JPG
                    $StuffResult = mysql_query("SELECT TypeId from $DataIn.stuffdata  where  StuffId='$StuffId' AND TypeId in (9047,9109,9031,9002,9066,9103,9057,9120,9096,9033,9040)", $link_id);
                    if ($StuffMyrow = mysql_fetch_array($StuffResult)) {
                        $jpgFile = $stuffFilePath . $StuffId . "_s.jpg";
                        exec("$execImageMagick -colorspace sRGB -density 300 -transparent white -trim $stuffile $jpgFile");
                        if (file_exists($jpgFile)) {
                            $isPDFTOJPG = 1;
                            $stuffile = $jpgFile;
                        }

                    }

                    $returnstr .= "CopyOKpdf|";
                    $sql = "UPDATE $DataIn.stuffdata SET Picture=2 WHERE StuffId=$StuffId";  //这些更新可以返回到调用的执行，不在文件服务器执行！从2015-07-30日起重新启用配件图片审核
                    //$sql = "UPDATE $DataIn.stuffdata SET Picture=1 WHERE StuffId=$StuffId";  //这些更新可以返回到调用的执行，不在文件服务器执行！
                    $result = mysql_query($sql, $link_id);
                    if ($result) {
                        //$Log="StuffId号为 $StuffId 的图档删除成功.</br>";
                        $returnstr .= "1|$StuffId|";    //1表示更新表成功，0表示更新表失败
                    } else {
                        //$Log="StuffId号为 $StuffId 图档删除失败! $sql</br>";
                        //$OperationResult="N";
                        $returnstr .= "0|$StuffId|";    //1表示更新表成功，0表示更新表失败
                    }
                }  //if(strtolower($FileType)=="pdf"){

            }  //if(copy

        }//if(StuffId!="")
    } //
}
closedir($handle);
echo "^$returnstr";   //把执行成功的返回过去，这样容易排错

$Log = $returnstr;
$Log_Item = "配件资料";            //需处理
$Log_Funtion = "配件图片自动加载";
$Operator = $Login_P_Number;
$DateTime = date("Y-m-d H:i:s");
$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res = @mysql_query($IN_recode, $link_id);

?>