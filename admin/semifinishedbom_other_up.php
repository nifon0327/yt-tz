<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="半成品BOM的其它功能";		//需处理
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
	$TypeIdSTR="";
	}
	
	
//******如果指定了操作对象
if($_POST['ListId']){
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$TypeIdSTR=" AND B.StuffId IN ($Ids)";
	}
	
$mStuffIds = "";
switch($Action){
	case "1"://配件替换
	   if($TypeIdSTR!=""){
			$Log_Funtion="半成品BOM中的配件替换";
			$up_Sql = "UPDATE $DataIn.semifinished_bom A
			LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.mStuffId
			SET A.StuffId='$newStuffId' 
			WHERE A.StuffId='$oldStuffId' $TypeIdSTR AND B.StuffId IS NOT NULL";
			$up_Result = mysql_query($up_Sql);	
			if($up_Result){
				$Log.="BOM关系表中的 $oldStuffId 替换为 $newStuffId 的替换操作成功执行!";
				}
			else{
				$Log.="<div class='redB'>BOM关系表中的 $oldStuffId 替换为 $newStuffId 的替换操作失败! $up_Sql </div>";
				$OperationResult="N";
				}
			}else{
				
				$Log.="<div class='redB'>未选择相关的半成品，不能做替换操作 </div>";
				$OperationResult="N";
			}
			
			//更新半成品价格NEW
			 $StuffId = $newStuffId;
			 include "../public/stuffdata_updated_costprice.php";
			  
		break;
	case "2"://从BOM表中清除配件
		$ClearIds=str_replace("``",",",$ClearIds);
		echo $ClearIds;
		$Log_Funtion="半成品BOM中的配件清除";
		
		$mStuffIds = "";
		$StuffResult = mysql_query("SELECT  GROUP_CONCAT(mStuffId) AS mStuffId  FROM $DataIn.semifinished_bom
									                   WHERE StuffId IN ($ClearIds)   AND mStuffId IN (
											           SELECT StuffId FROM $DataIn.stuffdata B WHERE 1 $TypeIdSTR 
											         )",$link_id);
		if($StuffMyrow=mysql_fetch_array($StuffResult)) {
		   $mStuffIds = $StuffMyrow['mStuffId'];
		}
		
	    if($TypeIdSTR!=""){
				$Del = "DELETE FROM $DataIn.semifinished_bom WHERE StuffId IN ($ClearIds) 
				        AND mStuffId IN (
				           SELECT StuffId FROM $DataIn.stuffdata B WHERE 1 $TypeIdSTR 
				        )"; 
				$Del_Result = mysql_query($Del);
				if($Del_Result){
					 $Log="半成品BOM数据库中的配件 $ClearIds 清除成功!";		
			    }
				else{
					$Log="<div class='redB'>BOM数据库中的配件 $ClearIds 清除失败! $Del</div>";
					$OperationResult="N";
				}
			}else{	
				$Log.="<div class='redB'>未选择相关的半成品，不能做替换操作 </div>";
				$OperationResult="N";
			}	
		break;
	case "3"://为防止人为错误，只对指定产品操作

			$StuffResult = mysql_query("SELECT * FROM $DataIn.semifinished_bom WHERE mStuffId=$modelmStuffId ORDER BY Id",$link_id);
			$i=0;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了半成品配件关系
				$BOMS=array();
				$BOMQ=array();
                $mStuffIds="";
                
				do{	
					$BOMS[]=$StuffMyrow["StuffId"];	
					$BOMQ[]=$StuffMyrow["Relation"];

					$i++;
					}while ($StuffMyrow=mysql_fetch_array($StuffResult));
					
				$Counts=count($_POST['ListId']);
				for($k=0;$k<$Counts;$k++){
					$StuffIdSTR=$_POST[ListId][$k];
					$Del = "DELETE FROM $DataIn.semifinished_bom WHERE mStuffId=$StuffIdSTR"; 
					$Del_Result = mysql_query($Del);
					
					$mStuffIds.=$mStuffIds==""?$StuffIdSTR:",$StuffIdSTR";
					//新增或更新版本
					$IN_recode3 = "INSERT INTO semifinished_bom_main(mStuffId,VersionNO,Remark,Estate,Locks,Date,Operator,creator,created) 
					                        VALUES('$StuffIdSTR','1.00','','1','0',CURDATE(),'$Operator','$Operator',NOW()) 
					                        ON DUPLICATE KEY UPDATE VersionNo=VersionNo+0.10,modifier='$Operator',modified=NOW()";
					$IN_res3=@mysql_query($IN_recode3);
			
					for($j=0;$j<$i;$j++){
						$m=$j+1;
						$StuffId=$BOMS[$j];
						$Relation=$BOMQ[$j];
						$In_Sql="INSERT INTO $DataIn.semifinished_bom (Id,mStuffId, StuffId, Relation, Date, Operator, creator, created) VALUES (NULL,'$StuffIdSTR','$StuffId','$Relation','$Date','$Operator','$Operator','$DateTime')";
      
						$result=@mysql_query($In_Sql);
						if($result){
							$Log1=$Log1."半成品 $StuffIdSTR BOM关系表中的第 $m 个配件成功加入BOM!<br>";
							}
						else{
							$Log2.="<span class='redB'> 半成品 $StuffIdSTR BOM关系表中的第 $m 个配件加入BOM失败!$In_Sql</span><br>";
							$OperationResult="N";
							}
						}
					}
                     				
				}else{
					$Log2.="<span class='redB'> 半成品 $modelmStuffId 未设置BOM关系</span><br>";
					$OperationResult="N";
				}
		$Log1=$Log1==""?"":"<p>".$Log1."</p>";
		$Log2=$Log2==""?"":"<p><div class=redB>".$Log2."</div></p>";
		$Log=$Log1.$Log2;
		$Log_Funtion="半成品BOM复制";
		break;
		
	case "4":
		$Log_Funtion="半成品BOM配件添加";
		$mStuffIds ="";
	    $SId_Array=explode("``",$AddStuffId);
		$Count=count($SId_Array);
		$Counts=count($_POST['ListId']);
		for($i=0;$i<$Counts;$i++){
			$mStuffIdSTR=$_POST[ListId][$i];
			$mStuffIds.=$mStuffIds==""?$mStuffIdSTR:",$mStuffIdSTR";
			$Log=$Log."<p>半成品 $mStuffIdSTR 的BOM添加配件：</p>";
			for($j=0;$j<$Count;$j++){
				$StuffId=$SId_Array[$j];
				//检查BOM表中是否已经存在
				$StuffResult = mysql_query("SELECT * FROM $DataIn.semifinished_bom WHERE mStuffId=$mStuffIdSTR and StuffId=$StuffId",$link_id);
				
				if($StuffMyrow=mysql_fetch_array($StuffResult)){
					$Log=$Log."<div class=redB>配件 $StuffId 已存在于半成品 $mStuffIdSTR 的BOM中，不再重复添加!</div><br>";
					}
				else{
					$IN_recodeN="INSERT INTO $DataIn.semifinished_bom (Id, mStuffId, StuffId, Relation, Date, Operator, creator, created) VALUES (NULL,'$mStuffIdSTR','$StuffId','$AddQty','$Date','$Operator','$Operator','$DateTime')";
					$resN = mysql_query($IN_recodeN);
					if($resN){
						$changeStuffBomState = "UPDATE $DataIn.stuffdata 
						SET bomEstate = 2 WHERE StuffId = $mStuffIdSTR";
						mysql_query($changeStuffBomState);
						$Log=$Log."配件 $StuffId 成功加入到半成品 $mStuffIdSTR 的BOM中!<br>";
						}
					else{
						$Log=$Log."<div class=redB>配件 $StuffId 加入半成品 $mStuffIdSTR 的BOM中时失败! $IN_recodeN </div><br>";
						$OperationResult="N";
						}
					}
				}
			}
		
		break;
	case "5"://改变配件的对应数量
		$Log_Funtion="半成品BOM配件对应数量更新";
	    $Counts=count($_POST['ListId']);
	   $mStuffIds = "";
		for($i=0;$i<$Counts;$i++){
			    $mStuffIdSTR=$_POST[ListId][$i];
			    $mStuffIds.=$mStuffIds==""?$mStuffIdSTR:",$mStuffIdSTR";
			    
			    $Log=$Log."<p>半成品 $mStuffIdSTR 的BOM配件对应数量更新：</p>";
				$StuffIdT=$upQtySID;
				//检查BOM表中是否已经存在
				$StuffResult = mysql_query("SELECT * FROM $DataIn.semifinished_bom WHERE mStuffId=$mStuffIdSTR and StuffId=$StuffIdT",$link_id);
				if($StuffMyrow=mysql_fetch_array($StuffResult)){
					$up_Sql = "UPDATE $DataIn.semifinished_bom SET Relation='$upQty',Date='$DateTime',Operator='$Operator'  WHERE StuffId=$StuffIdT and mStuffId=$mStuffIdSTR";
					$up_Result = mysql_query($up_Sql);
					if($up_Result){
					  $Log=$Log."半成品 $mStuffIdSTR BOM中的配件 $StuffIdT 对应数量成功更新为 $upQty!<br>";
						}
					else{
						$Log=$Log."<div class=redB>产品 $mStuffIdSTR BOM中的配件 $StuffIdT 对应数量更新为 $upQty 时失败!</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log=$Log."<div class=redB>配件 $StuffIdT 并不存在于半成品 $mStuffIdSTR 的BOM中，不做操作!</div><br>";
					}
			}
			
	break;
	}

 //更新半成品价格NEW
$mStuffIdArray=explode(',', $mStuffIds);
if (count($mStuffIdArray)>0){
         $MyPDOEnabled=1;
         include "../basic/parameter.inc";
		 $myPDO->query(" START TRANSACTION;");
		 for($n=0;$n=count($mStuffIdArray);$n++){
		        $mStuffId = $mStuffIdArray[$n];
			    $myResult=$myPDO->query("SELECT setNewStuffCostPrice($mStuffId) AS Counts");
		 }
         $myPDO->query(" COMMIT;");
 }
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
