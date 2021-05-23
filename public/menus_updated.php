<?php 
//电信-joseph
//代码、数据库共享，加标识-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="系统菜单";		//需处理
$upDataSheet="$DataIn.ac_menus";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	default:
      if($level==1){
             $updateStr =",typeid=$typeid";
        }else{
              $updateStr =",parent_id=$parent_id";
           }
          $SetStr="cSign='$cSign',ModuleId='$ModuleId',name='$name',action='$action',callback='$callback',badges='$badges',icon_type='$icon_type',row='$row',`order`='$order',col='$col',abs='$abs',Date='$DateTime',Operator='$Operator' $updateStr";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&level=$level&Pagination=$Pagination";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>