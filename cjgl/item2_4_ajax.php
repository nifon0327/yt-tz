<?php
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

$Log_Item="来料品检记录";			//需处理
$Log_Funtion="生成退换数据";
//步骤4：
switch($ActionId){
  case 1://生成物料退换数据 调用item5_j_ajax.php
        $upId=$Id;
        $qcResult= mysql_query("SELECT StuffId,Qty FROM $DataIn.qc_badrecord WHERE  Id='$upId' AND Estate=1 ",$link_id);
        if ($qcRow = mysql_fetch_array($qcResult)){
	          $thQTY[0]=$qcRow["Qty"];
              $thStuffId[0]=$qcRow["StuffId"];
              $cause_Result=mysql_query("SELECT T.Cause,B.CauseId,B.Reason FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  AND T.Type=1 WHERE B.Mid='$upId'",$link_id);
              while ( $cause_row = mysql_fetch_array($cause_Result)){
                        $CauseId=$cause_row["CauseId"];
                        if ($CauseId=="-1"){
                            if ($Reason!="") $Reason.=" / ";
                            $Reason.=$cause_row["Reason"];
                        }else{
                            if ($Reason!="") $Reason.=" / ";
                            $Reason.=$cause_row["Cause"];
                        }

               }
	          $thRemark[0]=$Reason;
              $FromPage="item2_4";
              $checkMid=mysql_query("SELECT Id FROM $DataIn.ck2_thmain WHERE CompanyId='$CompanyId' AND Date='$Date'",$link_id);
              if ($checkMidRow = mysql_fetch_array($checkMid)){
                 $oldMid=$checkMidRow["Id"];
              }
              $EstateSTR=1;
              include "item5_7_th_ajax.php";
              if ($OperationResult=="Y"){ //更新状态
               $upSql="UPDATE $DataIn.qc_badrecord SET Estate=0 WHERE Id='$upId' AND Estate=1 LIMIT 1";
	       $upAction=@mysql_query($upSql);
              }
         }
     break;
  case 3:
      //更新主表

       $upSql="UPDATE $DataIn.qc_badrecord SET Qty='$sumQty' WHERE Id='$Id' AND Estate=1 LIMIT 1";
       $upAction=@mysql_query($upSql);

       $Mid=$Id;
        //删除原有的品检明细表
      $delSql="DELETE FROM $DataIn.qc_badrecordsheet WHERE Mid='$Mid'";
      $delResult=mysql_query($delSql);

      if ($sumQty>0){  //有不良品
                     $FileType=".jpg";
                     $FilePath="../download/qcbadpicture/";
                     if(!file_exists($FilePath)){
                          makedir($FilePath);
                      }
                     $counts=count($badQty);
                       for ($i=0;$i<$counts;$i++)
                        {
                            if ($badQty[$i]>0){
                                 //生成明细表
                                $Picture=$PictureName[$i]==""?0:1;

                                $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQty[$i]', '','$Picture')";
                                $insheetAction=@mysql_query($insheetSql,$link_id);
                                if (!$insheetAction){
                                         $qcError=1;break;
                                 }
                                 else{
                                       $Sid=mysql_insert_id();
                                       //上传不良图片
                                        $PreFileName="Q".$Sid.$FileType;
                                        if ($fileinput[$i]){
                                          $copymes=copy($fileinput[$i],"$FilePath" . "$PreFileName");
                                          if($copymes){
                                                //更新刚才的记录
                                               if ($Picture==0){
                                                   $sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id=$Sid";
                                                   $result = mysql_query($sql);
                                               }
                                             }
                                        else{
                                             if ($Picture==1){
                                                   $sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='0' WHERE Id=$Sid";
                                                   $result = mysql_query($sql);
                                               }
                                              $qcResult.="\n 不良图片上传失败！";
                                            }
                                       }
                                       else{
                                           //更名
                                           if ($Picture==1){
                                               $oldPictureName=$FilePath . $PictureName[$i];
                                               rename($oldPictureName,"$FilePath" . "$PreFileName");
                                           }
                                       }
                                  }
                            }
                        }//end for
                        //有其它不良原因
                        if ($otherbadQty>0){
                            //生成明细表
                            $Picture=$otherPictureName==""?0:1;
                            $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '-1', '$otherbadQty', '$otherCause','$Picture')";
                            $insheetAction=@mysql_query($insheetSql,$link_id);
                            if (!$insheetAction){
                                 $qcError=1;break;
                             }
                             else{
                                        $Sid=mysql_insert_id();
                                       //上传不良图片
                                        $PreFileName="Q".$Sid.$FileType;
                                        if ($otherfileinput){
                                              $copymes=copy($_FILES["otherfileinput"]["tmp_name"],"$FilePath" . "$PreFileName");
                                             if($copymes){
                                                //更新刚才的记录
                                                 if ($Picture==0){
                                                     $sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id=$Sid";
                                                    $result = mysql_query($sql);
                                                 }
                                             }
                                        else{
                                            if ($Picture==1){
                                                     $sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='0' WHERE Id=$Sid";
                                                    $result = mysql_query($sql);
                                                 }
                                             $qcResult.="\n不良图片上传失败！";
                                            }
                                       }
                                       else{
                                         //更名
                                           if ($Picture==1){
                                               $oldPictureName=$FilePath . $otherPictureName;
                                               rename($oldPictureName,"$FilePath" . "$PreFileName");
                                           }
                                       }
                             }
                        }

                         if  ($qcError==1) $qcResult.="\n来料品检不良明细记录保存失败！"; else $qcResult.="\n来料品检不良明细记录保存成功！";
                }
            echo "<SCRIPT LANGUAGE=JavaScript>alert('$qcResult');</script>";
      /*
      $arrData=explode("|",unescape($Id));
      $Mid=$arrData[0];
      $badQty=$arrData[1];

      $upSql="UPDATE $DataIn.qc_badrecord SET Qty='$badQty' WHERE Id='$Mid' AND Estate=1 LIMIT 1";
      $upAction=@mysql_query($upSql);

      //删除原有的品检明细表
      $delSql="DELETE FROM $DataIn.qc_badrecordsheet WHERE Mid='$Mid'";
      $delResult=mysql_query($delSql);
        if ($badQty>0){  //有不良品
                  $CauseId=explode(",",$arrData[2]);
                  $badQtys=explode(",",$arrData[3]);
                  $sLen=count($CauseId);
                  for ($i=0;$i<$sLen;$i++){
                      if  ($CauseId[$i]=="-1") $Reason=$arrData[4]; else $Reason="";
                       //生成明细表
                       $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQtys[$i]', '$Reason')";
                       $insheetAction=@mysql_query($insheetSql,$link_id);
                       if (!$insheetAction){
                           $qcError=1;break;
                       }
                   }
                   if  ($qcError==1) $OperationResult="N"; else $OperationResult="Y";

             }
       *
       */
      break;

     case 5:
         //多张不良图片上传
         $qcResult=" 不良图片上传成功！";
          $FileType=".jpg";
          $FilePath="../download/qcbadpicture/";
          $counts=count($SelCauseId);
          $upCount=0;
          for ($i=0;$i<$counts;$i++)
          {

               if ($Pictures[$i] && $SelCauseId[$i]>0){
                   $PreFileName=$SelCauseId[$i] . "_" . date("Ymd") . $i . $FileType;
                   $copymes=copy($Pictures[$i],"$FilePath" . "$PreFileName");
                   if($copymes){
                        $insql = "INSERT INTO $DataIn.qc_badrecordfile (Id, Mid,Picture,Date,Operator) VALUES (NULL, '$SelCauseId[$i]', '$PreFileName', '$Date', '$Operator');";
                        $result = mysql_query($insql);
                        $upCount++;
                    }
                    else{
                         $qcResult="不良图片上传失败！已成功上传$upCount 张！";
                         break;
                    }
                }
          }
          echo "<SCRIPT LANGUAGE=JavaScript>alert('$qcResult');</script>";
      break;
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;

function unescape($str){
   $ret = '';
   $len = strlen($str);
   for ($i = 0; $i < $len; $i++){
       if ($str[$i] == '%' && $str[$i+1] == 'u'){
          $val = hexdec(substr($str, $i+2, 4));
          if ($val < 0x7f) $ret .= chr($val);
             else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
                  else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
          $i += 5;
          }
       else if ($str[$i] == '%'){
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
                }
            else $ret .= $str[$i];
   }
   return $ret;
}
?>
