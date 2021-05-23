<?php 
/*电信---yang 20120801
$DataPublic.my3_exadd
$DataPublic.my3_express
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="我的快递单";		//需处理
$upDataSheet="$DataPublic.my3_express";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$PayerNo=FormatSTR($PayerNo);
		$Receiver=FormatSTR($Receiver);
		$Company=FormatSTR($Company);
		$Country=FormatSTR($Country);
		$ZIP=$ZIP;
		$Address=FormatSTR($Address);
		$Tel=$Tel;
		$Mobile=$Mobile;
		$Pieces=$Pieces;
		$Contents=FormatSTR($Contents);
                $BoxSize=FormatSTR($BoxSize);
		$Date=date("Y-m-d");
		//检查通讯录
		$checkLinkSql= mysql_query("SELECT * FROM $DataPublic.my3_exadd WHERE Name LIKE '$Receiver' AND Company LIKE '$Company' AND Address LIKE '$Address'",$link_id);
		if($checkLinkRow = mysql_fetch_array($checkLinkSql)){//有记录
			$IdTemp=$checkLinkRow["Id"];
			}
		else{	//没有记录
			$LinkmanRecode="INSERT INTO $DataPublic.my3_exadd 
			(Id,Name,Company,PayerNo,Address,ZIP,Country,Tel,Mobile,Email,Estate,Locks,Date,Operator) VALUES 
			(NULL,'$Receiver','$Company','','$Address','$ZIP','$Country','$Tel','$Mobile','$Email','1','0','$Date','$Operator')";
			$Linkman_res=@mysql_query($LinkmanRecode);
			$IdTemp=mysql_insert_id();
			}
		if($IdTemp!=""){
			$InsertRecode="UPDATE $DataPublic.my3_express SET
			Receiver='$IdTemp',CompanyId='$CompanyId',expressType='$expressType',PayType='$PayType',PayerNo='$PayerNo',Contents='$Contents',SendContent='$SendContent',Length='$Length',Width='$Width',Height='$Height',cWeight='$cWeight',Pieces='$Pieces' WHERE Id='$Id'";
			$InsertRow=@mysql_query($InsertRecode);
			if($InsertRow){
				$Log=$TitleSTR."成功.<br>";
				}
			else{
				$Log="<div class='redB'>".$TitleSTR."失败. $InsertRecode </div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log="<div class='redB'>联系人 $Receiver 资料添加或读取失败！</div>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>