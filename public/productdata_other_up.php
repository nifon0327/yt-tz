<?php 
//电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品管理的其它功能操作";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
//操作对象处理
if($uType!=""){
	$TypeIdSTR="and  TypeId='$uType'";
	$Remark="分类ID为 $uType 的";}
else{
	$TypeIdSTR="";}
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$TypeIdSTR="and ProductId IN ($Ids)";
	}

switch($Action){
	case "1":	//全部锁定或解锁
		if($Locks==0){
			$Log_Funtion="产品锁定";}
		else{
			$Log_Funtion="产品解锁";
			}
		$up_sql = "UPDATE $DataIn.productdata SET Locks='$Locks' WHERE 1 $TypeIdSTR";
		$up_result = mysql_query($up_sql);
		if($up_result){
			$Log="<p>&nbsp;&nbsp;&nbsp;&nbsp;$Remark $Log_Funtion 的操作成功!</p></br>";
			}
		else{
			$Log="<p><div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;$Log_Funtion 的操作失败!</div></p></br>";
			$OperationResult="N";
			}
		break;
	case "2":
		$Log_Funtion="产品名称字符替换";
		$up_sql = "UPDATE $DataIn.productdata SET cName=replace(cName,'$Character_OLD','$Character_NEW') WHERE 1 $TypeIdSTR";		
		$up_result = mysql_query($up_sql);
		if($up_result){
			$Log="<p>&nbsp;&nbsp;&nbsp;&nbsp;$Remark $Log_Funtion (将 $Character_OLD 替换为 $Character_NEW)的操作成功! $up_sql </p></br>";
			}
		else{
			$Log="<p><div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;$Log_Funtion (将 $Character_OLD 替换为 $Character_NEW)的操作失败! $up_sql </div></p></br>";
			$OperationResult="N";
			}
		break;
	case "3"://单价更新
		$Log_Funtion="产品单价更新";
		$upSql = "UPDATE $DataIn.productdata SET Price='$NewPrice' WHERE 1 $TypeIdSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件单价成功！$upSql <br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件单价失败！$upSql</div><br>";
			$OperationResult="N";
			}
		break;
	case "4":
		$Log_Funtion="产品所属分类更新";
		$upSql = "UPDATE $DataIn.productdata SET TypeId='$NewTypeId' WHERE 1 $TypeIdSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件分类成功！$upSql <br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件分类失败！$upSql</div><br>";
			$OperationResult="N";
			}
		break;
		case 6:
		    $Counts=count($_POST['ListId']);
			$graphResult=mysql_query("SELECT * FROM $DataIn.doc_standarddrawing WHERE ProductType='$ProductId'",$link_id);
			if($graphRow=mysql_fetch_array($graphResult)){
			   $FileName=$graphRow["FileName"];
			   }
			for($i=0;$i<$Counts;$i++){
		    $Pid=$_POST[ListId][$i];
			$inRecode="INSERT INTO $DataIn.doc_standarddrawing 
			SELECT NULL,'1', cName, '$FileName', CompanyId, '$Pid', '1', '0', '$Date', '$Operator',0,'$Operator',NOW(),'$Operator',NOW() FROM $DataIn.productdata WHERE ProductId='$Pid'";
			$inAction=@mysql_query($inRecode);
			if($inRecode){
			       $Log.="产品 $Pid 的标准图原件 $FileName 复制成功.<br>";
			     }
			else{
			        $Log.="<div class='redB'>产品 $Pid 的标准图原件 $FileName 复制失败. $inRecode </div><br>";
					$OperationResult="N";
			     }
		    }
		   break;
		   
		   case 7:
		      if(count($_POST['ListId'])>0 && $NewCompanyId!=""){
		            $Log_Funtion="产品客户更新";
					$up_sql = "UPDATE $DataIn.productdata SET CompanyId='$NewCompanyId'  WHERE 1 $TypeIdSTR";		
					$up_result = mysql_query($up_sql);
					if($up_result){
						$Log="批量更新产品客户成功！$up_sql <br>";
						}
					else{
						$Log="<div class='redB'>批量更新产品客户失败！$up_sql</div><br>";
						$OperationResult="N";
						}
		      }
		      break;

		case 8:
			$Log_Funtion="产品购买分类更新";
			$upSql = "UPDATE $DataIn.productdata SET buySign='$NewbuySign' WHERE 1 $TypeIdSTR";
			$upResult1 = mysql_query($upSql);
			if($upResult1){
				$Log="批量更新产品购买分类成功！$upSql <br>";
			}
			else{
				$Log="<div class='redB'>批量更新产品购买分类失败！$upSql</div><br>";
				$OperationResult="N";
			}
		break;
		}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
