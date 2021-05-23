<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//步骤1  $DataIn.productdata 二合一已更新
include "../model/modelhead.php";
header("Content-Type: text/html; charset=gb2312");

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_imageload";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="刀模图档上传";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

//步骤3：需处理，更新操作
switch($ActionId){
    case 92:
            $Log_Funtion="图档上传";
            $FilePath="../download/cut_drawing/";
		    if(!file_exists($FilePath)){
			    makedir($FilePath);
			}
          $count  = count($docFile);
		  for( $i = 0 ; $i < $count; $i++){
				$strFileName = $_FILES['docFile']['name'][$i];
                $CutId   =  $CutIdArray[$i];
                 if($strFileName){
						    $FileType=extend_3($strFileName);
							$Newpicture=$StuffId."_".$CutId."_".$i.".".$FileType;
						    $uploadInfo=UploadFiles($docFile[$i],$Newpicture,$FilePath);
							if($uploadInfo!=""){
							     $checkResult=mysql_query("SELECT Id  FROM $DataIn.slice_cutdie WHERE StuffId='$StuffId'  AND CutId='$CutId'",$link_id);
							     if($checkRow=mysql_fetch_array($checkResult)){
							             $upId=$checkRow["Id"];
							             $upFileSql="UPDATE  $DataIn.slice_cutdie SET Estate=2,Picture='$Newpicture' WHERE Id='$upId' ";
							     }
							     else{
								         $upFileSql="INSERT INTO  $DataIn.slice_cutdie (Id, StuffId, CutId, Picture,Estate,Date, Operator) VALUES (NULL,'$StuffId','$CutId','$Newpicture','2','$Date','$Operator')";
							          }
							    
									 $upAction=mysql_query($upFileSql);
									 if($upAction){
									              $Log.="配件$StuffId 的刀模(ID:  $CutId )图档 $Newpicture 上传成功.<br>";
											     }
										     else{
											      $Log.="<div class='redB'>配件$StuffId 的刀模(ID:  $CutId )图档 $Newpicture 上传失败. </div>$upFileSql<br>";
											      $OperationResult="N";
											     }
									}
                          }
				}
        break;	
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>