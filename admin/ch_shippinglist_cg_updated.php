<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="CG出货标签";		//需处理
$upDataSheet="$DataIn.cg_order";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
    $inRecod="INSERT INTO $DataIn.cg_order(Id, POrderId, ItemNo, PONo, Description) VALUES (NULL,'$POrderId','$ItemNo','$PONo','$Description')";
	echo $inRecod;
     $inAction=mysql_query($inRecod,$link_id);
     if ($inAction){ 
	                $Log="CG出货标签添加成功!<br>";
 	               } 
     else{ 

			 $inRecod1="UPDATE $DataIn.cg_order SET ItemNo='$ItemNo',PONo='$PONo',Description='$Description' WHERE POrderId='$POrderId'";
	        $inAction1=@mysql_query($inRecod1);
            if ($inAction1){ 
	              $Log="CG出货标签更新成功,添加失败!<br>";
 	             } 
            else{ 
	              $Log="<div class=redB>CG出货标签更新失败,添加失败!</div><br>"; 
	              $OperationResult="N";
	            }	  
	     } 

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
