<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工体检费用操作的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

switch($Action){
       case 2:
		$Log_Funtion="上传单据凭证";
       $DateTime=date("YmdHis");
		$Date=date("Y-m-d");
         $FilePath="../download/tjfile/";
	     if(!file_exists($FilePath)){
			  makedir($FilePath);
			}
         if($Attached!=""){
	          $OldFile=$Attached;
	          $FileType=substr("$Attached_name", -4, 4);
	          $PreFileName=date("YmdHis").$FileType;
	          $uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
             }
	    	if($PassData && $uploadInfo!=""){//如果指定了操作对象,并且选择文件
		            $DataArray=explode("|",$PassData);
					$Counts=count($DataArray);
					for($i=0;$i<$Counts;$i++){
                              $Id =  $DataArray[$i];
                              $upSql = "UPDATE  $DataIn.cw17_tjsheet  SET  Attached = '$uploadInfo'  WHERE  Id =$Id";        
                              $upResult = @mysql_query($upSql);
						 }
                }
            else{
                     $Log.="<div class='redB'>体检凭证 $uploadInfo 上传失败. </div><br>";
               }
        break;
		}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
