<?php   
//步骤1 $DataPublic.info1_business 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="加工工序资料";		//需处理
$upDataSheet="$DataIn.process_data";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "subprogram/updated_model_3b.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "subprogram/updated_model_3b.php";		break;
	case 40: //图档上传
            $Type=1;
            $FilePath="../download/process/";
            if(!file_exists($FilePath)){
	           makedir($FilePath);
            }
            if ($Gfile!=""){
                $OldFile=$Gfile;
                $strFileName = $_FILES['Gfile']['name'];
		$extendFile=extend_3($strFileName);
		$PreFileName=$ProcessId . "-1." . $extendFile;  
           
                $CheckFileSql=mysql_query("SELECT Id FROM $DataIn.process_file WHERE ProcessId='$ProcessId' AND Type='$Type' LIMIT 1",$link_id);
	        if($CheckFileRow=mysql_fetch_array($CheckFileSql) && mysql_affected_rows()>0){//已经存在记录
                      //更新记录
                      $uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
                      if($uploadInfo!=""){
                          $updateSql = "UPDATE $DataIn.process_file SET FileName='$PreFileName' WHERE ProcessId='$ProcessId' AND Type='$Type'"; 
                          $updateResult = mysql_query($updateSql);
                       if ($updateResult){
                             $Log="更新图档文件成功.";
                        }else{
                            $Log="更新图档文件失败.";
                            $OperationResult="N";
                          }
                      }else{
                          $Log="更新图档文件失败.";
                          $OperationResult="N";
                      }
                }
                else{
                    $uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
		    if($uploadInfo!=""){//新上传文件
			$inRecode="INSERT INTO $DataIn.process_file (Id,ProcessId,Type,FileName,Date,Estate,Locks,Operator) VALUES (NULL,'$ProcessId','$Type','$PreFileName','$Date','1','0','$Operator')";
			$inAction=@mysql_query($inRecode);
			if($inAction){
				$Log="加工工序 $ProcessId 的图档文件添加成功.<br>";
				}
			else{
				$Log="<div class='redB'>加工工序 $ProcessId 的图档文件添加失败.</div><br>";
				$OperationResult="N";
			    }
			}
		    else{
			//上传失败
			$Log="新增图档文件失败.";
			}
                   }
               }
		
            break;
        default:
    $FilePath="../download/process/";
    if(!file_exists($FilePath)){
	  makedir($FilePath);
     }
    if($Picture!=""){
        $oldPicture=$Picture;
		$FileType=".jpg";
		$Newpicture=$ProcessId.$FileType;
	    $upInfo=UploadFiles($oldPicture,$Newpicture,$FilePath);
        $PictureInfo=$upInfo==""?"":" ,Picture='1'";
      }
     $Remark=FormatSTR($Remark);
     $SetStr="ProcessName='$ProcessName',gxTypeId='$gxTypeId',BassLoss='$BassLoss',Price='$Price',Remark='$Remark',Date='$Date',Operator='$Operator' $PictureInfo";
     include "../model/subprogram/updated_model_3a.php";
    break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>