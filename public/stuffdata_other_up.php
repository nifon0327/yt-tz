<?php 
/*
已更新$DataIn.电信---yang 20120801
*/
//步骤1
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件管理的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
if($uType!=""){
	$uTypeSTR="AND $DataIn.stuffdata.TypeId ='$uType'";
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
	$uTypeSTR="AND $DataIn.stuffdata.StuffId IN ($Ids) AND $DataIn.stuffdata.Estate>0";
	$uTypeSTR2=" AND S.StuffId IN ($Ids)  AND S.Estate>0";
	}
switch($Action){
	case "1":	//全部锁定或解锁
		if($Locks==0){
			$Log_Funtion="全部锁定";}
		else{
			$Log_Funtion="全部解锁";}
		$up_sql = "UPDATE $DataIn.stuffdata SET Locks='$Locks' WHERE 1 $uTypeSTR";
		$up_result = mysql_query($up_sql);
		if($up_result){
			$Log="<p>&nbsp;&nbsp;&nbsp;&nbsp;$Remark $Log_Funtion 的操作成功!</p></br>";
			}
		else{
			$Log="<p><div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;$Log_Funtion 的操作失败! $up_sql </div></p></br>";
			$OperationResult="N";
			}
		break;
	case "2":
		$Log_Funtion="配件名称字符替换";
		$Reason_Sql ="INSERT INTO $DataIn.stuffchange SELECT NULL,S.StuffId,S.StuffCname,Replace(StuffCname,'$Character_OLD','$Character_NEW'),S.Price,S.Price,B.CompanyId,B.CompanyId,'批量配件名称字符替换','$Date','$Operator','1','0','0','$Operator','$DateTime',null,null FROM $DataIn.stuffdata S
LEFT JOIN $DataIn.BPS B ON B.StuffId=S.StuffId WHERE S.StuffCname LIKE '%$Character_OLD%' $uTypeSTR2";
		  $Reason_Result = mysql_query($Reason_Sql);
		   $SetStr1=",Estate='2' ";
		   
		$up_sql = "UPDATE $DataIn.stuffdata SET StuffCname=replace(StuffCname,'$Character_OLD','$Character_NEW') $SetStr1 WHERE StuffCname LIKE '%$Character_OLD%'  $uTypeSTR";		
		$up_result = mysql_query($up_sql);
		if($up_result){
			$Log="<p>&nbsp;&nbsp;&nbsp;&nbsp;$Remark $Log_Funtion (将 $Character_OLD 替换为 $Character_NEW)的操作成功! $up_sql </p></br>";
			}
		else{
			$Log="<p><div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;$Log_Funtion (将 $Character_OLD 替换为 $Character_NEW)的操作失败! $up_sql </div></p></br>";
			$OperationResult="N";
			}
		break;
	case "3"://????
	
		$Log_Funtion="清除闲置配件";
		/*
		$DelSql ="DELETE $DataIn.stuffdata,$DataIn.bps,$DataIn.ck9_stocksheet 
		FROM $DataIn.stuffdata
		LEFT JOIN $DataIn.bps ON $DataIn.bps.StuffId=$DataIn.stuffdata.StuffId
		LEFT JOIN $DataIn.ck9_stocksheet ON $DataIn.ck9_stocksheet.StuffId=$DataIn.stuffdata.StuffId
		WHERE 1 $uTypeSTR
		AND $DataIn.stuffdata.StuffId NOT IN(
		SELECT StuffId FROM $DataIn.cg1_stocksheet GROUP BY StuffId UNION
		SELECT StuffId FROM $DataIn.pands GROUP BY StuffId UNION
		SELECT StuffId FROM $DataIn.ck7_bprk GROUP BY StuffId UNION
		SELECT StuffId FROM $DataIn.ck8_bfsheet GROUP BY StuffId)";
		$DelResult=mysql_query($DelSql);
		if($DelResult){//可删除				
			$Log.="$i -1:闲置配件清除操作成功!<br>";
			}
		else{
			$Log.="<div class=redB>$i -3:闲置配件清除操作失败!</div> $DelSql";$OperationResult="N";
			}
		$endCheck= mysql_query("OPTIMIZE TABLE stuffdata,bps,ck9_stocksheet",$link_id);*/
		break;
	case "4"://配件取代
		//拆分
		if(empty($_POST['ListId'])){
			$Log="没有指定配件,操作不成功！";
			}
		else{
			$Counts=count($_POST['ListId']);
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];			
				$Stockpile_Result = mysql_query("SELECT * FROM $DataIn.ck9_stocksheet WHERE StuffId=$thisId LIMIT 1",$link_id);
				if ($Stockpile_Row = mysql_fetch_array($Stockpile_Result)){
					$dStockQty=$Stockpile_Row["dStockQty"];$tStockQty=$Stockpile_Row["tStockQty"];$oStockQty=$Stockpile_Row["oStockQty"];
					$update_Sql1 = "UPDATE $DataIn.ck9_stocksheet SET dStockQty=dStockQty+$dStockQty,tStockQty=tStockQty+$tStockQty,oStockQty=oStockQty+$oStockQty  where StuffId=$newStuffId";
					$update_Result1 = mysql_query($update_Sql1);
					 if($update_Result1){
						$Log="$i -1：被取代的配件($thisId)库存已加入至取代的配件($newStuffId)库存中。<br> ";
						
						$update_Sql2 ="UPDATE $DataIn.pands SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：pands表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：pands表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.cg1_stocksheet SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：cg1_stocksheet表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：cg1_stocksheet表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.ck1_rksheet SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：ck1_rksheet表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：ck1_rksheet表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.ck5_llsheet SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：ck5_llsheet表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：ck5_llsheet表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.ck7_bprk SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：ck7_bprk表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：ck7_bprk表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.ck8_bfsheet SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：ck8_bfsheet表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：ck8_bfsheet表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.cw1_fkoutsheet SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：cw1_fkoutsheet表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：cw1_fkoutsheet表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						$update_Sql2 ="UPDATE $DataIn.gys_shsheet SET StuffId='$newStuffId' where StuffId='$thisId'";
						$update_Result2 = mysql_query($update_Sql2);
						if($update_Result2){
							$Log.="$i -2：gys_shsheet表:  被取代配件($thisId)的相关资料成功改为使用配件($newStuffId)。<br> ";
							}
						else{
							$Log.="<div class=redB>$i -2：gys_shsheet表:  被取代配件($thisId)的相关资料未能改为使用配件($newStuffId)。</div><br> ";
							}
						/*UPDATE 
							$DataIn.ck9_stocksheet K
							LEFT JOIN $DataIn.pands P ON P.StuffId=K.StuffId
							LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId
							LEFT JOIN $DataIn.ck1_rksheet R ON R.StuffId=K.StuffId
							LEFT JOIN $DataIn.ck5_llsheet L ON L.StuffId=K.StuffId
							LEFT JOIN $DataIn.ck7_bprk B ON B.StuffId=K.StuffId
							LEFT JOIN $DataIn.ck8_bfsheet F ON F.StuffId=K.StuffId
							LEFT JOIN $DataIn.cw1_fkoutsheet W ON W.StuffId=K.StuffId
							LEFT JOIN $DataIn.gys_shsheet A ON A.StuffId=K.StuffId
							SET K.dStockQty=0,K.tStockQty=0,K.oStockQty=0,
							P.StuffId='$newStuffId',G.StuffId='$newStuffId',R.StuffId='$newStuffId',
							L.StuffId='$newStuffId',B.StuffId='$newStuffId',
							F.StuffId='$newStuffId',W.StuffId='$newStuffId',
							A.StuffId='$newStuffId'
							where K.StuffId='$thisId'*/
							//删除A配件
							$DelSql= "DELETE $DataIn.stuffdata,$DataIn.bps,$DataIn.ck9_stocksheet,$DataIn.stuffimg
							FROM $DataIn.stuffdata 
							LEFT JOIN $DataIn.bps ON $DataIn.bps.StuffId=$DataIn.stuffdata.StuffId
							LEFT JOIN $DataIn.ck9_stocksheet ON $DataIn.ck9_stocksheet.StuffId=$DataIn.stuffdata.StuffId
							LEFT JOIN $DataIn.stuffimg ON $DataIn.stuffimg.StuffId=$DataIn.stuffdata.StuffId
							WHERE $DataIn.stuffdata.StuffId='$thisId'"; 
							$DelResult = mysql_query($DelSql);
						}//end if($update_Result1)
					 else{
						$Log.="<p><div class=redB>$j -1：被取代的配件($thisId)库存加入至取代的配件($newStuffId)库存中时失败。$update_Sql1  </div><br>";
						$OperationResult="N";
						}//end if($update_Result1)
					}//end if ($Stockpile_Row = mysql_fetch_array($Stockpile_Result)){
				else{
					$Log.="<p><div class=redB>$j ：读取被取代的配件($thisId)库存时失败。</div><br>";
					$OperationResult="N";
					}//end if ($Stockpile_Row = mysql_fetch_array($Stockpile_Result)){
				}//end for 
			}//END if (empty
		$Log_Funtion="配件取代";
		break;
	case 5://价格更新
		$Log_Funtion="批量更新配件单价";
		
		  $Reason_Sql ="INSERT INTO $DataIn.stuffchange SELECT NULL,S.StuffId,S.StuffCname,S.StuffCname,S.Price,'$NewPrice',B.CompanyId,B.CompanyId,'批量更新配件单价','$Date','$Operator','1','0','0','$Operator','$DateTime',null,null  FROM $DataIn.stuffdata S
LEFT JOIN $DataIn.BPS B ON B.StuffId=S.StuffId WHERE 1 $uTypeSTR2";
		  $Reason_Result = mysql_query($Reason_Sql);
		   $SetStr1=",Estate='2' ";
		      
		$upSql = "UPDATE $DataIn.stuffdata SET Price='$NewPrice' $SetStr1 WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件单价成功！$upSql <br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件单价失败！$upSql</div><br>";
			$OperationResult="N";
			}
	break;
	case 6://供应商更新
		$Log_Funtion="批量更新配件默认供应商";
		$Reason_Sql ="INSERT INTO $DataIn.stuffchange SELECT NULL,S.StuffId,S.StuffCname,S.StuffCname,S.Price,S.Price,B.CompanyId,'$CompanyId','批量更新配件默认供应商','$Date','$Operator','1','0','0','$Operator','$DateTime',null,null  FROM $DataIn.stuffdata S
         LEFT JOIN $DataIn.BPS B ON B.StuffId=S.StuffId WHERE 1 $uTypeSTR2";
		  $Reason_Result = mysql_query($Reason_Sql);
		   
		$upSql = "UPDATE $DataIn.bps SET CompanyId=$CompanyId  WHERE StuffId IN (SELECT StuffId FROM $DataIn.stuffdata WHERE 1 $uTypeSTR)";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件默认供应商成功！<br>";
			 $upSql2= "UPDATE $DataIn.stuffdata SET Estate='2' WHERE 1 $uTypeSTR";
		     $upResult2 = mysql_query($upSql2);
			}
		else{
			$Log="<div class='redB'>批量更新配件默认供应商失败！</div><br>";
			$OperationResult="N";
			}
	break;
	case 7://供应商更新
		$Log_Funtion="批量更新配件默认采购员";
		$upSql = "UPDATE $DataIn.bps,$DataIn.stuffdata SET $DataIn.bps.BuyerId='$BuyerId' WHERE $DataIn.bps.StuffId=$DataIn.stuffdata.StuffId $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1 && mysql_affected_rows()>0){
			$Log="批量更新配件默认采购员成功！<br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件默认采购员失败！$upSql </div><br>";
			$OperationResult="N";
			}
	break;
	case 8://批量更新配件的分类
		$Log_Funtion="批量更新配件的分类";
		$upSql = "UPDATE $DataIn.stuffdata SET TypeId='$NewTypeId' WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件所属分类成功!<br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件所属分类失败!$upSql</div><br>";
			$OperationResult="N";
			}

	break;
	case 9://批量更新配件的送货楼层
		$Log_Funtion="批量更新配件的送货楼层";
		$upSql = "UPDATE $DataIn.stuffdata SET SendFloor='$NewSendFloor' WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件的送货楼层成功!<br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件的送货楼层失败!$upSql</div><br>";
			$OperationResult="N";
			}
	  break;
case 10://批量更新配件的交货周期
		$Log_Funtion="批量更新配件的交货周期";
		$upSql = "UPDATE $DataIn.stuffdata SET jhDays='$NewJhDays' WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件的交货周期成功!<br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件的交货周期失败!$upSql</div><br>";
			$OperationResult="N";
			}
	break;
case 11://批量更新配件的属性
		$Log_Funtion="批量更新配件的属性";
     //配件属性
if($_POST['ListId']){//如果指定了配件
	   $Counts=count($_POST['ListId']);
	   for($i=0;$i<$Counts;$i++){
	     $StuffId=$_POST[ListId][$i];
         $tempCount=count($Property);
        $DelSql="DELETE FROM $DataIn.stuffproperty WHERE StuffId=$StuffId";
        $DelResult=@mysql_query($DelSql);
         for($k=0;$k<$tempCount;$k++){
                   $inSql3="INSERT INTO $DataIn.stuffproperty(Id,StuffId,Property)VALUES(NULL,'$StuffId','$Property[$k]')";
                   $inRes3=@mysql_query($inSql3);
                   if($inRes3&& mysql_affected_rows()>0){
		               	$Log.="更新配件$StuffId 的属性成功!<br>";
                       }
                      else{
                          $Log="<div class='redB'>更新配件$StuffId 的属性成功!</div><br>";
                         $OperationResult="N";
                      }
               }
		 }
	}
	break;
	case 12: //按百分比跟新价格
	{
		$Log_Funtion="批量更新配件单价";
		//$symbol
		
		 $Reason_Sql ="INSERT INTO $DataIn.stuffchange SELECT NULL,S.StuffId,S.StuffCname,S.StuffCname,S.Price,round(S.Price $symbol $NewPriceRate,4),B.CompanyId,B.CompanyId,'批量更新配件单价','$Date','$Operator','1','0','0','$Operator','$DateTime',null,null  FROM $DataIn.stuffdata S
LEFT JOIN $DataIn.BPS B ON B.StuffId=S.StuffId WHERE 1 $uTypeSTR2";

		  $Reason_Result = mysql_query($Reason_Sql);
		   $SetStr1=",Estate='2' ";
		      
		$upSql = "UPDATE $DataIn.stuffdata SET Price=round(Price $symbol $NewPriceRate,4) $SetStr1 WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件单价成功！$upSql <br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件单价失败！$upSql</div><br>";
			$OperationResult="N";
			}

	}
	break;
		case 13://批量更新配件的品捡方式
		$Log_Funtion="批量更新配件的品捡方式";
		$upSql = "UPDATE $DataIn.stuffdata SET CheckSign='$NewCheckSign' WHERE 1 $uTypeSTR";
		$upResult1 = mysql_query($upSql);
		if($upResult1){
			$Log="批量更新配件的品捡方式成功!<br>";
			}
		else{
			$Log="<div class='redB'>批量更新配件的品捡方式失败!$upSql</div><br>";
			$OperationResult="N";
			}
	  break;
	
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
