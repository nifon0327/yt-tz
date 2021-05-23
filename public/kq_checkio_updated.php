<?php 
//mc 内部模式:OK ewen2013-08-03
include "../model/modelhead.php";
$funFrom="kq_checkio";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="考勤记录";		//需处理
$upDataSheet="$DataIn.checkinout";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$Date=date("Y-m-d");
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
	    
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";			break;
	case 17:
		$Log_Funtion="审核";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Check7Sql=mysql_query("SELECT Id FROM $upDataSheet WHERE Id='$Id' AND Estate='0'",$link_id);
				if($Check7Row=mysql_fetch_array($Check7Sql)){//取消审核
					$SetStr="Estate=1,Locks=1";
					$Log_Funtion="取消审核";
					}
				else{//审核
					$SetStr="Estate=0,Locks=0";		
					}
				$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id=$Id";
				$result = mysql_query($sql);
				if($result){
					$Log="ID号在 $Ids 的记录$Log_Funtion 成功.</br>";
					}
				else{
					$Log="ID号为 $Ids 的记录$Log_Funtion 失败!</br>";
					$OperationResult="N";
					}
				}
			}
		break;		
		
	case "UpdateTime":
		$Log_Funtion="更新出勤时间";	$SetStr=" CheckTime=CONCAT(substring(CheckTime,1,11),'$NewTime')  ";		include "../model/subprogram/updated_model_3d.php";		break;
		
	case 902://出勤状态更新	
		if($CheckType=="K"){
			$CheckType="O";
			$KRSignSTR=",KRSign='1'";
			}			
		$Date=date("Y-m-d");
		$SetStr="CheckType='$CheckType',Operator='$Operator',Locks='0' $KRSignSTR";
		include "../model/subprogram/updated_model_3a.php";
		
		$checkStatus = mysql_query("SELECT * FROM $upDataSheet WHERE  Id>$Id AND Number= (SELECT Number FROM $upDataSheet WHERE  Id=$Id) ",$link_id);
		if (mysql_num_rows($checkStatus)<=0){
			$checkSign = $CheckType=="O"?0:1;
			 $upSql="UPDATE staff_workstatus SET CheckType=$checkSign,Date=CURDATE() WHERE Number=(SELECT Number FROM $upDataSheet WHERE  Id=$Id)";
		     $upResult=mysql_query($upSql);
		}

		
		break;
	case "kq":
	      $Field=explode(",",$kqList);
          $Count=count($Field);
          for($i=1;$i<$Count;$i++){
		  $RowData=explode("^^",$Field[$i]);
		  $Number=$RowData[0];
		  $SdTime=is_numeric($RowData[1])==false?0:$RowData[1];
		  $JbTime=is_numeric($RowData[2])==false?0:$RowData[2];
		  $JbTime2=is_numeric($RowData[3])==false?0:$RowData[3];
		  $JbTime3=is_numeric($RowData[4])==false?0:$RowData[4];
		  
		  $kqDel="DELETE FROM $DataIn.kqdaytj WHERE Number='$Number' and Date='$CheckDate'";
		  $kqResult=mysql_query($kqDel);
		  
		  $GroupResult=mysql_query("SELECT GroupId From $DataPublic.staffmain Where Number='$Number'",$link_id);
		  if($GroupRow=mysql_fetch_array($GroupResult)){
		   $GroupId=$GroupRow["GroupId"];
		   }
		  $kqdaySql="INSERT INTO  $DataIn.kqdaytj(Id, GroupId, Number, SdTime, JbTime, JbTime2, JbTime3, Date, Locks)
		             Values(NULL,'$GroupId','$Number','$SdTime','$JbTime','$JbTime2','$JbTime3','$CheckDate','0')";
		  //echo $kqdaySql;
		  $kqdayRow=mysql_query($kqdaySql,$link_id);
		  if($kqdayRow){
		               $Log="$CheckDate 日考勤统计数据保存成功！.</br>";
					}
				else{
				       $Log="<div class=redB>$CheckDate 日考勤统计数据保存失败！</div>";
					 }
		  }
       break;
  default:
	   break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CheckDate=$CheckDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
  ?>