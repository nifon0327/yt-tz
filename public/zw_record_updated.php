<?php 
//$DataPublic.net_cpdata 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="资产领用资料";		//需处理
$upDataSheet="$DataPublic.fixed_assetsdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$cSigntmp=$cSign_XY==""?"7":$cSign_XY;  //cSign=7 为研砼，5 为鼠宝， 3 为皮套
$cSign=$cSigntmp;
$x=1;
echo "ActionId:$ActionId";
switch($ActionId){
          case 37:
             $Log_Funtion="增加领用人";
		    $sheetInSql="INSERT INTO $DataPublic.fixed_userdata (Id,cSign,Mid,User,SDate,UserType,Remark,Date,Estate,Operator)
		    VALUES (NULL,'$cSign','$Mid','$User','$UserDate','1','$Remark','$DateTime','1','$Operator')";  //
		    //echo "$sheetInSql";
		    $sheetInAction=@mysql_query($sheetInSql);
		    if($sheetInAction && mysql_affected_rows()>0){
			      $Log.="加入领用人成功.<br>";
		         }
		   else{
			       $Log.="<div class='redB'>加入领用人失败 </div><br>";
		           $OperationResult="N";
		        }	
         break;
         case 34:
         $DelSql="DELETE FROM $DataPublic.fixed_userdata    WHERE Mid='$Id'  AND Estate=1 and UserType=1";//退回交接中的资料
         $DelResult=mysql_query($DelSql);
         if($DelResult&& mysql_affected_rows($DelResult)>0){
               $Log.="领用退回成功.<br>";
         }
         else{
	              $Log.="<div class='redB'>领用退回失败 $DelSql</div><br>";
		           $OperationResult="N";
         }
         break;
        case 41:
        $Log_Funtion="寄回";
        $updateSql="UPDATE $DataPublic.fixed_assetsdata  SET Estate=5 WHERE Id='$Id' AND Estate=1";
         $upResult=mysql_query($updateSql);
         if($upResult&& mysql_affected_rows($upResult)>0){
               $Log.="状态设置为: 寄回客户 成功.<br>";
         }
         else{
	              $Log.="<div class='redB'>状态设置为: 寄回客户 失败 $updateSql</div><br>";
		           $OperationResult="N";
         }
         break;

}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>