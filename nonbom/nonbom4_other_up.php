<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件管理的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
if($uType!=""){
	$uTypeSTR="AND $DataPublic.nonbom4_goodsdata.TypeId ='$uType'";
	$uTypeSTR2=" AND S.TypeId ='$uType'";
	$Remark="分类ID为 $uType 的";
	}
else{
	$uTypeSTR="";
	}
if($_POST['ListId']){//如果指定了配件
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$uTypeSTR="AND $DataPublic.nonbom4_goodsdata.GoodsId IN ($Ids)";
	$uTypeSTR2=" AND S.GoodsId IN ($Ids) ";
	}
switch($Action){
	case "1":	//全部锁定或解锁
		if($Locks==0){
			$Log_Funtion="全部锁定";}
		else{
			$Log_Funtion="全部解锁";}
		$up_sql = "UPDATE $DataPublic.nonbom4_goodsdata SET Locks='$Locks' WHERE 1 $uTypeSTR";
		$up_result = mysql_query($up_sql);
		if($up_result){
			$Log="<p>&nbsp;&nbsp;&nbsp;&nbsp;$Remark $Log_Funtion 的操作成功!</p></br>";
			}
		else{
			$Log="<p><div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;$Log_Funtion 的操作失败! $up_sql </div></p></br>";
			$OperationResult="N";
			}
		break;
		case 2://价格更新
		$Log_Funtion="批量更新配件单价";
		  if($DataIn !== 'ac'){
		    $Reason_Sql ="INSERT INTO $DataPublic.nonbom4_goodchange SELECT NULL,S.GoodsId,S.GoodsName,S.GoodsName,S.Price,'$NewPrice',B.CompanyId,B.CompanyId,'批量更新配件单价','$Date','$Operator','1'  FROM $DataPublic.nonbom4_goodsdata S
						LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=S.GoodsId WHERE 1 $uTypeSTR2";
		  }else{
		  	$Reason_Sql ="INSERT INTO $DataPublic.nonbom4_goodchange SELECT NULL,S.GoodsId,S.GoodsName,S.GoodsName,S.Price,'$NewPrice',B.CompanyId,B.CompanyId,'批量更新配件单价','$Date','$Operator','1', 0, 0, '$Operator', NOW(), '$Operator', NOW()  FROM $DataPublic.nonbom4_goodsdata S
						LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=S.GoodsId WHERE 1 $uTypeSTR2";
		  }
		  $Reason_Result = mysql_query($Reason_Sql);
		   $SetStr1=",Estate='2' ";
		      
		$upSql = "UPDATE $DataPublic.nonbom4_goodsdata SET Price='$NewPrice' $SetStr1 WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件单价成功！$upSql <br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件单价失败！$upSql</div><br>";
			$OperationResult="N";
			}
	break;
	case 3://供应商更新
		$Log_Funtion="批量更新配件默认供应商";
		if($DataIn !== 'ac'){
		    $Reason_Sql ="INSERT INTO $DataPublic.nonbom4_goodchange SELECT NULL,S.GoodsId,S.GoodsName,S.GoodsName,S.Price,S.Price,B.CompanyId,'$CompanyId','批量更新配件默认供应商','$Date','$Operator','1'  FROM $DataPublic.nonbom4_goodsdata S
            LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=S.GoodsId WHERE 1 $uTypeSTR2";
		 }else{
		 	$Reason_Sql ="INSERT INTO $DataPublic.nonbom4_goodchange SELECT NULL,S.GoodsId,S.GoodsName,S.GoodsName,S.Price,S.Price,B.CompanyId,'$CompanyId','批量更新配件默认供应商','$Date','$Operator','1', 0, 0, '$Operator', NOW(), '$Operator', NOW()  FROM $DataPublic.nonbom4_goodsdata S
            LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=S.GoodsId WHERE 1 $uTypeSTR2";
		 }
		 $Reason_Result = mysql_query($Reason_Sql);
		   
		$upSql = "UPDATE $DataPublic.nonbom5_goodsstock SET CompanyId=$CompanyId  WHERE GoodsId IN (SELECT GoodsId FROM $DataPublic.nonbom4_goodsdata WHERE 1 $uTypeSTR)";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件默认供应商成功！<br>";
			 $upSql2= "UPDATE $DataPublic.nonbom4_goodsdata SET Estate='2' WHERE 1 $uTypeSTR";
		     $upResult2 = mysql_query($upSql2);
			}
		else{
			$Log="<div class='redB'>批量更新配件默认供应商失败！</div><br>";
			$OperationResult="N";
			}
	break;

	case 4://批量更新配件的分类
		$Log_Funtion="批量更新配件的分类";
		$upSql = "UPDATE  $DataPublic.nonbom4_goodsdata SET TypeId='$NewTypeId' WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新非BOM配件所属分类成功!<br>";
			}
		else{
			$Log="<div class='redB'>批量更新非BOM配件所属分类失败!$upSql</div><br>";
			$OperationResult="N";
			}

	break;

case 5://批量更新配件的属性
		$Log_Funtion="批量更新配件的属性";
     //配件属性
   if($_POST['ListId']){//如果指定了配件
	   $Counts=count($_POST['ListId']);
	   for($i=0;$i<$Counts;$i++){
	     $GoodsId=$_POST[ListId][$i];
         $tempCount=count($Property);
        $DelSql="DELETE FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId";
        $DelResult=@mysql_query($DelSql);
         for($k=0;$k<$tempCount;$k++){
                   $inSql3="INSERT INTO $DataPublic.nonbom4_goodsproperty(Id,GoodsId,Property)VALUES(NULL,'$GoodsId','$Property[$k]')";
                   $inRes3=@mysql_query($inSql3);
                   if($inRes3&& mysql_affected_rows()>0){
		               	$Log.="更新非BOM配件$GoodsId 的属性成功!<br>";
                       }
                      else{
                          $Log="<div class='redB'>更新非BOM配件$GoodsId 的属性成功!</div><br>";
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
