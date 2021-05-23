<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="行政费用";			//需处理
$Log_Funtion="数据更新";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$upFileResult="Y";
switch($ActionId){
           case 1://新增数据
        $OtherId=$OtherId==""?0:$OtherId;
        $Property=$Property==""?0:$Property;
            $inRecode="INSERT INTO $DataIn.hzqksheet (Id,Mid,Content,Amount,Currency,Bill,ReturnReasons,Date,Estate,TypeId,OtherId,Property,Locks,Operator) VALUES (NULL,'0','$Content', '$Amount','$Currency','0','','$theDate','1','$TypeId','$OtherId','$Property','1','$Operator')";
            $inAction=@mysql_query($inRecode);
            if ($inAction){
                    $Log.="&nbsp;&nbsp; 新增行政费用成功! <br>";
                    $Id=mysql_insert_id();
                    //上传文件
                    $filename=$_FILES["fileinput"]["name"]; 
                    if($filename!=""){//有上传文件
                            $FileType=".jpg";
                            $FilePath="../download/cwadminicost/";
                            if(!file_exists($FilePath)){
                                    makedir($FilePath);
                                    }
                            $PreFileName="H".$Id.$FileType;	
                           $copymes=copy($_FILES["fileinput"]["tmp_name"],"$FilePath" . "$PreFileName"); 
                            if($copymes){
                                    $Log.="&nbsp;&nbsp;单据上传成功！<br>";
                                    //更新刚才的记录
                                    $sql = "UPDATE $DataIn.hzqksheet SET Bill='1' WHERE Id=$Id";
                                    $result = mysql_query($sql);
                                    }
                            else{
                                    $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
                                    $upFileResult="N";			
                                    }
                            }
                    }
            else{
                    $Log.="<div class=redB>&nbsp;&nbsp;  新增行政费用失败! $inRecode </div><br>";
                    $OperationResult="N";
                    }
     $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
    break;
	
    case 2://更新数据
       //上传文件
        $BillSTR="";
        $filename=$_FILES["fileinput"]["name"]; 
        if($filename!=""){//有上传文件
                $FileType=".jpg";
                $FilePath="../download/cwadminicost/";
                if(!file_exists($FilePath)){
                        makedir($FilePath);
                        }
                $PreFileName="H".$Id.$FileType;	
                $copymes=copy($_FILES["fileinput"]["tmp_name"],"$FilePath" . "$PreFileName"); 
                if($copymes){
                        $Log.="&nbsp;&nbsp;单据上传成功！<br>";
                        $BillSTR=",Bill='1'";
                        }
                else{
                        $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
                        $upFileResult="N";			
                        }
           }
           $SetStr="`TypeId`='$TypeId',`Date`='$theDate',`Amount`='$Amount',`Currency`='$Currency',`Content`='$Content' $BillSTR";
           $UptlSql="UPDATE $DataIn.hzqksheet SET  $SetStr  WHERE Id='$Id' AND Estate=1";
           $UptlResult = mysql_query($UptlSql);
           if ($UptlResult){
               $Log.="<div class=redB>&nbsp;&nbsp;  更新行政费用（$Id）记录成功! </div><br>";
           }
           else{
               $Log.="<div class=redB>&nbsp;&nbsp;  更新行政费用（$Id）记录失败! $inRecode </div><br>";
                $OperationResult="N"; 
           }
           $alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";
    break;
    
    case 3:
         $delSql="DELETE FROM $DataIn.hzqksheet WHERE Id='$Id' AND Estate=1";
	  $delResult=mysql_query($delSql);	
	if($delResult){
	        $Log.="<div class=greenB>$Log_Item (" . $Id . ")记录删除成功!</div><br>";
	     } 
         else{
  	     $Log.="<div class=redB>$Log_Item (" . $Id . ")记录删除失败!</div><br>";
	      $OperationResult="N";
	}
             $alertLog=$Log_Item . "记录删除成功";$alertErrLog=$Log_Item . "记录删除失败";
        break;
        
     case 4: 
          $SetStr="Estate=2,Locks=0";
          $UptlSql="UPDATE $DataIn.hzqksheet SET  $SetStr  WHERE Id='$Id' AND Estate=1";
           $UptlResult = mysql_query($UptlSql);
           if ($UptlResult){
               $Log.="<div class=redB>&nbsp;&nbsp;  行政费用（$Id）请款成功! </div><br>";
           }
           else{
               $Log.="<div class=redB>&nbsp;&nbsp;  行政费用（$Id）请款失败! $inRecode </div><br>";
                $OperationResult="N"; 
           }
           $alertLog=$Log_Item . "请款成功";$alertErrLog=$Log_Item . "请款失败";
         break;
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

        if ($upFileResult=="N")  echo "<SCRIPT LANGUAGE=JavaScript>alert('单据上传失败！');</script>";
        if ($OperationResult=="N" ){
                 echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
         }
         else{
               echo  "<SCRIPT LANGUAGE=JavaScript> alert('$alertLog'); parent.closeWinDialog();parent.ResetPage(8,3);</script>";
         }
?>