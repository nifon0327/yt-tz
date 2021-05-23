<?php   
//电信-zxq 2012-08-01
/*
$DataIn.yw3_pisheet
$DataIn.yw3_piatt
分开已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
//$_SESSION["nowWebPage"]=$nowWebPage; 
$_SEESION["nowWebPage"] = $nowWebPage;
//步骤2：
$Log_Item="PI资料";		//需处理
$upDataSheet="$DataIn.yw3_pisheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 901://取消PI中的订单
		$Log_Funtion="取消";
		$delSql = "DELETE FROM $DataIn.yw3_pisheet WHERE oId='$oId'"; 
		$delRresult = mysql_query($delSql);
		if($delRresult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; 订单ID为 $oId 的 $Log_Item 取消操作成功.<br>";
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>&nbsp;&nbsp;订单ID为 $oId 的 $Log_Item 取消操作失败.</div><br>";
			}
		//重置PI档
		//include "yw_pi_reset.php";
		include "yw_piBlue_reset.php";
		break;
	case 902:
		$PI=$Id;
		$Date=date("Y-m-d");
		$Log_Funtion="附加项目更新";
		$upNums=count($Description);
		for($i=0;$i<$upNums;$i++){
			//检查是否已有记录，如果是，则更新，否则新增
			$DescriptionTemp=$Description[$i];
			$QtyTemp=$Qty[$i];
			$UnitTemp=$Unit[$i];
			if($OldAtt[$i]!=""){
				$IdTemp=$OldAtt[$i];
				$updateSQL="UPDATE $DataIn.yw3_piatt SET Description='$DescriptionTemp',Qty='$QtyTemp',Unit='$UnitTemp',Date='$Date',Operator='$Operator' WHERE Id=$IdTemp";
				$updateResult = mysql_query($updateSQL);
				if($updateResult && mysql_affected_rows()>0){
					$Log.="原附加项目 $IdTemp 更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>原附加项目 $IdTemp 无需更新或更新失败.</div><br>";
					$OperationResult="N";
					}
				}
			else{
				$inSql="INSERT INTO $DataIn.yw3_piatt (Id,PI,Description,Qty,Unit,Date,Operator) VALUES (NULL,'$Id','$DescriptionTemp','$QtyTemp','$UnitTemp','$Date','$Operator')";
				$inResult = mysql_query($inSql);
				if($inResult && mysql_affected_rows()>0){
					$Log.="附加项目 $DescriptionTemp 新增成功.<br>";
					}
				else{
					$Log.="<div class='redB'>附加项目 $DescriptionTemp 新增失败. $inSql </div><br>";
					$OperationResult="N";
					}
				}
			}
		//重置PI的PDF文档
		//include "yw_Pi_reset.php";
		include "yw_piBlue_reset.php";
		break;
	case 935://更新交货期
		$Log_Funtion="交货期更新";
		$SetStr="Leadtime='$NewLeadtime',Date='$DateTime',Operator='$Operator'";
		$updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE $upDataSheet.Id='$PIId'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; PI为 $Id 的 $Log_Item 更新成功. <br>";
			//更新未设置采购交期的已下采购单
			$checkResult = mysql_query("SELECT S.Id FROM $DataIn.yw3_pisheet I LEFT JOIN $DataIn.yw1_ordersheet S ON S.Id=I.oId WHERE I.Id='$PIId' LIMIT 1",$link_id);		
			if($checkRow=mysql_fetch_array($checkResult)){
			     $Ids=$checkRow["Id"];
			      include "yw_pi_setcgdate.php";
			}
		}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp; PI为 $Id 的 $Log_Item 更新失败. $updateSQL</div><br>";
			$OperationResult="N";
			}echo $Log_Funtion;
			
		//重置PI的PDF文档
		//include "yw_Pi_reset.php";
		include "yw_piBlue_reset.php";
		break;
		
	/*case 903://加入报价规则
	     $Log_Funtion="加入报价规则";
		 $delResult=mysql_query("DELETE FROM $DataIn.yw3_pirules WHERE Mid='$Mid'",$link_id);
		 $Rules="INSERT INTO $DataIn.yw3_pirules (Id,Mid,Operator)VALUES(NULL,'$Mid','$Operator')";
		 $Result=mysql_query($Rules,$link_id);
		 if($Result){
		         $Log.="报价规则加入PI成功";
			    }
			else{
			     $Log.="<div class='redB'>报价规则加入PI失败</div>";
			    }
	        break;
	case 904://删除加入报价规则
		 $delResult=mysql_query("DELETE FROM $DataIn.yw3_pirules WHERE Mid='$Mid'",$link_id);
		 if(delResult){
		         $Log.="报价规则删除成功";
			    }
			else{
			     $Log.="<div class='redB'>报价规则删除失败</div>";
			    }
	        break;*/
   case "PIRemark":
        $delResult=mysql_query("UPDATE  $DataIn.yw3_pisheet  SET Remark = '$tempRemark',modifier='$Operator',modified ='$DateTime' WHERE PI='$PI'",$link_id);
		 if($delResult && mysql_affected_rows()>0){
		         $Log.="报价规则删除成功";
			    }
			else{
			     $Log.="<div class='redB'>报价规则删除失败</div>";
			    }
        break;
   case 166://pipdfreback
	if($Attached!=""){//有上传文件
		$FileType=".pdf";
		$OldFile=$Attached;
		$FilePath="../download/pipdfreback/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="PIback".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
echo $Attached;
			$Log.="&nbsp;&nbsp;PI回传单上传成功！ <br>";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;PI回传单上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}

       break;
	   
	case 153:   //重置为新的PI //add by zx 2014-05-26
		$PI="$Id";
		//include "yw_piNew_reset.php";
		include "yw_piBlue_reset.php";
		$Log.="<div>PI:$PI 新PI重置完毕！！ </div><br>";
		break;
		
	default:
		$updateSQL = "DELETE FROM  $upDataSheet  WHERE $upDataSheet.PI='$PI'";
		$updateResult = mysql_query($updateSQL);
		$dataArray=explode("|",$SIdList);
		
        $Count=count($dataArray);
		for($i=0;$i<$Count;$i++){
		    $tempArray=explode("^^",$dataArray[$i]);
			$IdTemp=$tempArray[0];
			$LeadtimeTemp=$tempArray[1];
			$Remark=$tempArray[2];
			
			$InsertSql="INSERT INTO $upDataSheet(CompanyId,oId,PI,Leadtime,PaymentTerm,Notes,OtherNotes,Terms,ShipTo,SoldTo,`condition`,Remark,Date,Operator) VALUES('$CompanyId','$IdTemp','$PI','$LeadtimeTemp','$PaymentTerm','$Notes','$OtherNotes','$Terms','$ShipTo','$SoldTo','$condition','$Remark','$DateTime','$Operator')";
			$inRes=@mysql_query($InsertSql);
			  if($inRes){
			       $Log.="$i--PI添加成功!<br>";
			       $Ids=$IdTemp;
			       include "yw_pi_setcgdate.php";
			       }
			   else{
			       $Log.="<div>$i--PI添加失败! $InsertSql</div><br>";
			       }
			}
		//include "yw_pi_reset.php";
		include "yw_piBlue_reset.php";
		break;			
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>