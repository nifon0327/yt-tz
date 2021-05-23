<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：y
$Log_Item="社保缴费的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$tempDateTime = date("YmdHis");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

switch($Action){
       case 200:
		$Log_Funtion="上传单据";
        $DateTime=date("YmdHis");
		$Date=date("Y-m-d");
		$tempArray = explode("|", $upId);
		if($Attached!="" ){//如果指定了操作对象,并且选择文件
              $OldFile=$Attached;
              $FilePath="../download/sbjf_List/";
              if(!file_exists($FilePath)){
                     makedir($FilePath);
                     }
              $PreFileName=$tempDateTime.".jpg";
              
              $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
              if($Attached){
                  $Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
              }
              else{
                   $Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
                 }
                    
           for( $k = 0 ;$k < count($tempArray); $k++){     
                    
                $Mid = $tempArray[$k];
                
	            $DelSql="DELETE FROM $DataIn.rs_sbjf_picture WHERE Mid='$Mid'";
				$DelResult=mysql_query($DelSql);
				$chooseMonth = $chooseMonth==""?"0000-00-00":$chooseMonth;
				$TypeId = $TypeId==""?"0":$TypeId;
				
				$PictureSql="INSERT INTO  $DataIn.rs_sbjf_picture(`Id`, `Mid`,`chooseMonth`
				, `TypeId`, `Picture`, `Date`, `Operator`)VALUES(NULL,'$Mid',
				'$chooseMonth','$TypeId','$PreFileName','$Date','$Operator')";
	            $PictureResult=@mysql_query($PictureSql);
	         }

          }
        break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
