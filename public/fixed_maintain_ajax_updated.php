<?php 
//电信-joseph
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

ChangeWtitle("$SubCompany 固定资产维护资料记录");
$Log_Funtion="更新";
//$Login_help="yw_order_ajax_updated";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

//$ALType="BuyerId=$BuyerId&cProviderId=$cProviderId&PayMode=$PayMode&Provider=$Provider&chooseDate=$chooseDate";


$Operator=$Login_P_Number;
include "../model/subprogram/staffname.php";   //把Operator 员工名字

$cSign=$_SESSION["Login_cSign"];
$cSignSTR=" AND D.cSign=$cSign ";

if($passvalue!=""){
	$pArray=explode("|",$passvalue);
	$FixedID=$pArray[0];
	$maitainID=$pArray[1];
	$DaysID=$pArray[2];
	$SubName=$pArray[3];
	$CycleDate=$pArray[4];
	$Days=$pArray[5];
}

if ($MaintainDate!=""){  //说明来自月/季/月等
	$CycleDate=$MaintainDate;
}
//echo "Action";
echo "$Action : $Operator: $Login_P_Number ";
$OperationResult="N";

switch ($Action){
	case "EveryDay":
		$Log_Item="每天维护数据";
		//写主表
		$count_Temp=mysql_query("SELECT ID FROM $DataPublic.fixed_m_main 
								 WHERE CycleDate='$CycleDate' and DaysID='$DaysID'  and Days='$Days' AND FixedID='$FixedID' ",$link_id);  //查找记录是否存在，不存在插入
		//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
		$Mid=mysql_result($count_Temp,0,"ID");
		if ($Mid<1){ 
			$inRecode="INSERT INTO $DataPublic.fixed_m_main 					  
			(Id,cSign,FixedID,DaysID,Days,CycleDate,Estate,Locks,Date,Operator)
			VALUES (NULL,'$cSign','$FixedID','$DaysID','$Days','$CycleDate','1','0','$Date','$Operator') ";
			//echo "$inRecode";
			$inResult=mysql_query($inRecode,$link_id);
			$Mid=mysql_insert_id();
		}

		
		//写入子表
		//if ($Question!=""){ //有问题描述才算有问题
			$count_Temp=mysql_query("SELECT count( * ) AS counts FROM $DataPublic.fixed_m_sheet 
									 WHERE CycleDate='$CycleDate' and Mid='$Mid' AND maitainID='$maitainID' ",$link_id);  //查找记录是否存在，不存在插入
			//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
			$counts=mysql_result($count_Temp,0,"counts");
			if ($counts<1){ 
				$inRecode="INSERT INTO $DataPublic.fixed_m_sheet 					  
				(Id,MID,cSign,maitainID,Question,Solution,CycleDate,Estate,Locks,Date,Operator)
				VALUES (NULL,'$Mid','$cSign','$maitainID','$Question','$Solution','$CycleDate','1','0','$Date','$Operator') ";
				//echo "$inRecode";
				$inResult=mysql_query($inRecode,$link_id);
			}
			else{
				$sql = "UPDATE $DataPublic.fixed_m_sheet  SET Question='$Question',Solution='$Solution',Operator='$Operator' WHERE CycleDate='$CycleDate' and Mid='$Mid' AND maitainID='$maitainID'";
				//echo "$sql ";
				$result = mysql_query($sql,$link_id);
			}
		//}
	break;
	
	case "SaveToday":
		
		$providerSql= mysql_query("select D.ID,D.CpName,D.Model,D.TypeId,D.BuyDate, B.Name AS Branch,M.Name as maintainer,S.DaysID,S.Days,K.CName 
				FROM 
				(
					SELECT * 
						FROM (
						SELECT * 
						FROM $DataPublic.fixed_userdata where UserType=2
						ORDER BY Sdate DESC 
						)A
						GROUP BY Mid
					)F
				LEFT JOIN $DataPublic.fixed_assetsdata D ON D.Id=F.Mid
				LEFT JOIN (select  distinct S.TypeId, S.DaysID,S.Days from $DataPublic.oa3_maitaintype S where S.DaysID=$DayID ) 
				S ON S.TypeId=D.TypeId
				LEFT JOIN $DataPublic.oa3_maitaindays K ON K.ID=S.DaysID
				LEFT JOIN $DataPublic.staffmain M ON M.Number=F.User
				LEFT JOIN $DataPublic.branchdata B ON B.Id=D.BranchId
				WHERE 1 AND D.Estate=1 $cSignSTR AND S.DaysID=$DayID AND F.User=$UserNumber    ",$link_id);  //S.DaysID=1 表示只是每天维护的
		/*
		echo "select D.ID,D.CpName,D.Model,D.TypeId,D.BuyDate, B.Name AS Branch,M.Name as maintainer,S.DaysID,S.Days,K.CName 
				FROM 
				(
					SELECT * 
						FROM (
						SELECT * 
						FROM $DataPublic.fixed_userdata where UserType=2
						ORDER BY Sdate DESC 
						)A
						GROUP BY Mid
					)F
				LEFT JOIN $DataPublic.fixed_assetsdata D ON D.Id=F.Mid
				LEFT JOIN (select  distinct S.TypeId, S.DaysID,S.Days from $DataPublic.oa3_maitaintype S where S.DaysID=$DayID ) 
				S ON S.TypeId=D.TypeId
				LEFT JOIN $DataPublic.oa3_maitaindays K ON K.ID=S.DaysID
				LEFT JOIN $DataPublic.staffmain M ON M.Number=F.User
				LEFT JOIN $DataPublic.branchdata B ON B.Id=D.BranchId
				WHERE 1 $cSignSTR AND S.DaysID=$DayID AND F.User=$UserNumber   ";
		*/
		$CycleDate=$Date;  //加入今天的
		
		if($providerRow = mysql_fetch_array($providerSql)){
			do{
				
				$FixedID=$providerRow["ID"];
				$Days=$providerRow["Days"];
				//写主表
				$count_Temp=mysql_query("SELECT ID FROM $DataPublic.fixed_m_main 
										 WHERE CycleDate='$CycleDate' and DaysID='$DayID' AND FixedID='$FixedID' ",$link_id);  //查找记录是否存在，不存在插入         

				
				//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
				$Mid=mysql_result($count_Temp,0,"ID");
				if ($Mid<1){ 
					$inRecode="INSERT INTO $DataPublic.fixed_m_main 					  
					(Id,cSign,FixedID,DaysID,Days,CycleDate,Estate,Locks,Date,Operator)
					VALUES (NULL,'$cSign','$FixedID','$DayID','$Days','$CycleDate','1','0','$Date','$Operator') ";
					//echo "$inRecode";
					$inResult=mysql_query($inRecode,$link_id);
					$Mid=mysql_insert_id();
				}				
				
				
			}while ($providerRow = mysql_fetch_array($providerSql));	
		
		}
		break;	
		
	case "SaveCheck":  //领导检查使用
		$count_Temp=mysql_query("SELECT ID FROM $DataPublic.fixed_m_check 
								 WHERE CycleDate='$Date' and DaysID='$DaysID' AND Days='$Days' AND FixedID='$FixedID' ",$link_id);  //查找记录是否存在，不存在插入
		//echo "SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId";
		$Mid=mysql_result($count_Temp,0,"ID");
		if ($Mid<1){ 	
			$inRecode="INSERT INTO $DataPublic.fixed_m_check 					  
			(Id,cSign,FixedID,DaysID,Days,CycleDate,Estate,Locks,Date,Operator)
			VALUES (NULL,'$cSign','$FixedID','$DaysID','$Days','$Date','1','0','$Date','$Operator') ";
			//echo "$inRecode";
			$inResult=mysql_query($inRecode,$link_id);
		}
		break;
			
	}
$Operator=$Login_P_Number;	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode);	
?>