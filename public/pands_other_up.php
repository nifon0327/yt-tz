<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="BOM的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
if($uType!=""){
	$TypeIdSTR="AND B.TypeId='$uType'";
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
	$TypeIdSTR=" AND A.ProductId IN ($Ids)";
	}
switch($Action){
	case "1"://配件替换
		$Log_Funtion="BOM中的配件替换";
		$up_Sql = "UPDATE $DataIn.pands A
		LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId
		SET A.StuffId='$newStuffId',A.Date='$DateTime',A.Operator='$Operator' 
		WHERE A.StuffId='$oldStuffId' $TypeIdSTR AND B.ProductId IS NOT NULL";
		$up_Result = mysql_query($up_Sql);
		
		
		$chargePandsInsertSql = "Insert Into $DataIn.pandscharge From
								Select NULL, ProductId,'$newStuffId', '$oldStuffId', '1', '', '0', '0', '', '$DateTime', '1', '$Operator' 
								From $DataIn.productdata
								Where ProductId IN ($Ids)";
		mysql_query($chargePandsInsertSql);
		
		if($up_Result){
			$Log.="BOM关系表中的 $oldStuffId 替换为 $newStuffId 的替换操作成功执行!";
			setProductsToJudge($TypeIdSTR);
			}
		else{
			$Log.="<div class='redB'>BOM关系表中的 $oldStuffId 替换为 $newStuffId 的替换操作失败! $up_Sql </div>";
			$OperationResult="N";
			}
		break;
	case "2"://从BOM表中清除配件
		$ClearIds=str_replace("",",",$ClearIds);//将替换为,
		$Log_Funtion="BOM中的配件清除";		
		$Del = "DELETE FROM $DataIn.pands WHERE StuffId IN ($ClearIds) AND ProductId IN (SELECT A.ProductId FROM $DataIn.productdata A WHERE 1 $TypeIdSTR ORDER BY Id)"; 
		$Del_Result = mysql_query($Del);
		if($Del_Result){
			$Log="BOM数据库中的配件 $ClearIds 清除成功!";
			setProductsToJudge($TypeIdSTR);
			
			}
		else{
			$Log="<div class='redB'>BOM数据库中的配件 $ClearIds 清除失败! $Del</div>";
			$OperationResult="N";
			}		
		break;
	case "3"://为防止人为错误，只对指定产品操作
		//读取模板BOM
			$StuffResult = mysql_query("SELECT * FROM $DataIn.pands WHERE ProductId=$modelPId ORDER BY Id",$link_id);
			$i=0;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				$BOMS=array();
				$BOMQ=array();
				$CUTS=array();
				$CUTR=array();
                $DPRA=array();
				do{	
					$BOMS[]=$StuffMyrow["StuffId"];	
					$BOMQ[]=$StuffMyrow["Relation"];
					$CUTS[]=$StuffMyrow["Diecut"];	
					$CUTR[]=$StuffMyrow["Cutrelation"];
                    $DPRA[]=$StuffMyrow["bpRate"];
					$i++;
					}while ($StuffMyrow=mysql_fetch_array($StuffResult));
				//
				$Counts=count($_POST['ListId']);
				for($k=0;$k<$Counts;$k++){
					$ProductIdSTR=$_POST[ListId][$k];
					$Del = "DELETE FROM $DataIn.pands WHERE ProductId=$ProductIdSTR"; 
					$Del_Result = mysql_query($Del);
					for($j=0;$j<$i;$j++){
						$m=$j+1;
						$StuffIdT=$BOMS[$j];
						$StuffIdQ=$BOMQ[$j];
						$Diecut=$CUTS[$j];
						$Cutrelation=$CUTR[$j];
                        $bpRate=$DPRA[$j];
						$IN_recodeN="INSERT INTO $DataIn.pands (Id,ProductId,StuffId,Relation,Diecut,Cutrelation,bpRate,Date,Operator) VALUES (NULL,'$ProductIdSTR','$StuffIdT','$StuffIdQ','$Diecut','$Cutrelation','$bpRate','$Date','$Operator')";
                     // echo $IN_recodeN."<br>";
						$resN=@mysql_query($IN_recodeN);
						if($resN){
							$Log1=$Log1."产品 $ProductIdSTR BOM关系表中的第 $m 个配件成功加入BOM!<br>";
							setProductToJudge($ProductIdSTR);
							}
						else{
							$Log2.="<span class='redB'> 产品 $ProductIdSTR BOM关系表中的第 $m 个配件加入BOM失败!</span><br>";
							$OperationResult="N";
							}
						}
					}
					
									
				}
		//开始复制
		$Log1=$Log1==""?"":"<p>".$Log1."</p>";
		$Log2=$Log2==""?"":"<p><div class=redB>".$Log2."</div></p>";
		$Log=$Log1.$Log2;
		$Log_Funtion="BOM复制";
		break;
	case "4":
		$Log_Funtion="BOM配件添加";
		echo "$AddStuffId <br>";
		$SId_Array=explode("``",$AddStuffId);
		$Count=count($SId_Array);
		$Counts=count($_POST['ListId']);
		for($i=0;$i<$Counts;$i++){
			$ProductIdSTR=$_POST[ListId][$i];
			$Log=$Log."<p>产品 $ProductIdSTR 的BOM添加配件：</p>";
			for($j=0;$j<$Count;$j++){
				$StuffIdT=$SId_Array[$j];
				//检查BOM表中是否已经存在
				$StuffResult = mysql_query("SELECT * FROM $DataIn.pands where ProductId=$ProductIdSTR and StuffId=$StuffIdT",$link_id);
				//echo "SELECT * FROM $DataIn.pands where ProductId=$ProductIdSTR and StuffId=$StuffIdT";
				if($StuffMyrow=mysql_fetch_array($StuffResult)){
					$Log=$Log."<div class=redB>配件 $StuffIdT 已存在于产品 $ProductIdSTR 的BOM中，不再重复添加!</div><br>";
					}
				else{
					$IN_recodeN="INSERT INTO $DataIn.pands (Id,ProductId,StuffId,Relation,Diecut,Cutrelation,Date,Operator) VALUES (NULL,'$ProductIdSTR','$StuffIdT','$AddQty','','0','$Date','$Operator')";
					$resN=@mysql_query($IN_recodeN);
					if($resN){
						$changeProdcutState = "UPDATE $DataIn.productdata SET Estate = 2 WHERE ProductId = $ProductIdSTR";
						mysql_query($changeProdcutState);
						$Log=$Log."配件 $StuffIdT 成功加入到产品 $ProductIdSTR 的BOM中!<br>";
						setProductToJudge($ProductIdSTR);
						$addPandsChargeSql = "INSERT INTO $DataIn.pandscharge (Id,ProductId,newStuffId,oldStuffId,Relation,Diecut,Cutrelation,unitStuffs,ChargeDate, Estate,Operator) VALUES (NULL,'$ProductIdSTR','$StuffIdT','0','$AddQty','','0', '','$DateTime', '1','$Operator')";
						mysql_query($addPandsChargeSql);
						}
					else{
						$Log=$Log."<div class=redB>配件 $StuffIdT 加入产品 $ProductIdSTR 的BOM中时失败! $IN_recodeN </div><br>";
						$OperationResult="N";
						}
					}
				}
			}
		
		break;
	case "5"://改变配件的对应数量
		$Log_Funtion="BOM配件对应数量更新";
		$Counts=count($_POST['ListId']);
		for($i=0;$i<$Counts;$i++){
			    $ProductIdSTR=$_POST[ListId][$i];
			    $Log=$Log."<p>产品 $ProductIdSTR 的BOM配件对应数量更新：</p>";
				$StuffIdT=$upQtySID;
				//检查BOM表中是否已经存在
				$StuffResult = mysql_query("SELECT * FROM $DataIn.pands where ProductId=$ProductIdSTR and StuffId=$StuffIdT",$link_id);
				if($StuffMyrow=mysql_fetch_array($StuffResult)){
					$up_Sql = "UPDATE $DataIn.pands SET Relation='$upQty',Date='$DateTime',Operator='$Operator'  WHERE StuffId=$StuffIdT and ProductId=$ProductIdSTR";
					$up_Result = mysql_query($up_Sql);
					if($up_Result){
						$Log=$Log."产品 $ProductIdSTR BOM中的配件 $StuffIdT 对应数量成功更新为 $upQty!<br>";
						setProductToJudge($ProductIdSTR);
						$addPandsChargeSql = "INSERT INTO $DataIn.pandscharge (Id,ProductId,newStuffId,oldStuffId,Relation,Diecut,Cutrelation,unitStuffs,ChargeDate, Estate,Operator) VALUES (NULL,'$ProductIdSTR','$StuffIdT','$StuffIdT','$upQty','','0', '','$DateTime', '1','$Operator')";

						}
					else{
						$Log=$Log."<div class=redB>产品 $ProductIdSTR BOM中的配件 $StuffIdT 对应数量更新为 $upQty 时失败!</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log=$Log."<div class=redB>配件 $StuffIdT 并不存在于产品 $ProductIdSTR 的BOM中，不做操作!</div><br>";
					}
			}
		
	break;
	}
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

<?php
	
	function setProductsToJudge($TypeIdSTR)
	{
		$updateProductSql = "Update $DataIn.productdata A Set A.Estate = '2' Where $TypeIdSTR";
		$updateProductResult = mysql_query($updateProductSql);
	}
	
	function setProductToJudge($ProductIdSTR)
	{
		$updateProductSql = "Update $DataIn.productdata A Set A.Estate = '2' Where A.ProductId = $ProductIdSTR";
		$updateProductResult = mysql_query($updateProductSql);
	}
	
?>
