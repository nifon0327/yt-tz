<?php 
//$DataIn.电信---yang 20120801
//代码共享-EWEN 2012-09-03
$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE ValueCode='101' OR ValueCode='102' and Estate=1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	do{
		$ValueCode=$checkRow["ValueCode"];
		switch($ValueCode){
			case "101"://工龄
				$glAmount=$checkRow["Value"];
				break;
			case "102"://1.5倍时薪
				$jbAmount=$checkRow["Value"];
				break;
			}
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
$glAmount=$glAmount==""?0:$glAmount;
$jbAmount=$jbAmount==""?0:$jbAmount;

//如果是需要考勤的，则还需要有考勤统计数据
$myResult = mysql_query("SELECT X.Number,X.Month,M.Name,M.BranchId,M.JobId,M.Grade,M.KqSign,M.ComeIn 
FROM $DataIn.cwxzsheet X
LEFT JOIN $DataPublic.staffmain M ON X.Number=M.Number WHERE X.Id='$Id' LIMIT 1",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		//初始化
		$Dx=0;$Gljt=0;$Gwjt=0;$Jj=0;$Shbz=0;$Zsbz=0;$Jtbz=0;$Jbf=0;$Yxbz=0;$taxbz=0;$Jz=0;$Sb=0;$Kqkk=0;$RandP=0;$Amount=0;
		$sumY=0;$sumM=0;$sumD=0;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$BranchId=$myRow["BranchId"];
		$JobId=$myRow["JobId"];
		$Grade=$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$ComeIn=$myRow["ComeIn"];	
		$chooseMonth=$myRow["Month"];	
		//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
		//底薪、补助
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
		$Dx=$B_Result["Dx"];
		$Shbz=$B_Result["Shbz"];
		$Zsbz=$B_Result["Zsbz"];
		$Jtbz=$B_Result["Jtbz"];
		//津贴计算
		if($Grade>0){
			$jtResult = mysql_fetch_array(mysql_query("SELECT Subsidy FROM $DataPublic.gradesubsidy WHERE 1 AND Grade=$Grade LIMIT 1",$link_id));
			$Gwjt=$jtResult["Subsidy"];
			}
		else{
			$Gwjt=0;
			}
		//预设奖金
		$jjResult = mysql_fetch_array(mysql_query("SELECT Jj FROM $DataPublic.paybase where 1 and Number=$Number LIMIT 1",$link_id));
		$Jj=$jjResult["Jj"];
		if($KqSign==1){
			//工龄津贴
			include "subprogram/staff_model_gl.php";		
			$Gljt=$sumY*$glAmount;			
			//补助重置:首月没有补助，次月根据天数计算，第三月全部
			if($sumY==0 && $sumM<2){
				if($sumM==1){//已过试用期，开始计算补助，但未够足额补助的,按比例计算
					$Shbz=sprintf("%.0f",$Shbz*($sumD/$theMonthDays));
					$Zsbz=sprintf("%.0f",$Zsbz*($sumD/$theMonthDays));
					$Jtbz=sprintf("%.0f",$Jtbz*($sumD/$theMonthDays));
					}
				else{
					$Shbz=$Jtbz=$Zsbz=0;//不足刚好一个月，试用期没有补助
					}
				}
			include "kqcode/staffwage_jbf.php";//加班费
			}
		
		//借支:加入全部未还款项
		$ygjz_Result = mysql_query("SELECT SUM(Amount) AS Amount FROM $DataIn.cwygjz WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id);
		$Jz=mysql_result($ygjz_Result,0,"Amount");
		$Jz=$Jz==""?0:$Jz;
		//社保		
		$sbCheck= mysql_fetch_array(mysql_query("SELECT mAmount FROM $DataIn.sbpaysheet WHERE 1 and Number=$Number and Month='$chooseMonth' ORDER BY Id DESC LIMIT 1",$link_id));
		$Sb=$sbCheck["mAmount"]==""?0:$sbCheck["mAmount"];		
		//个税计算
		$taxbz=0; //个税补助，>=175补100元
		include "kqcode/staffwage_gs.php";
		//数据入库
		//echo " $sumM 姓名：$Name 底：$Dx+工龄：$Gljt+岗位：$Gwjt+奖金：$Jj+生活：$Shbz+宿：$Zsbz+加班：$Jbf+夜：$Yxbz-借：$Jz-社保：$Sb-考勤扣：$Kqkk <br>";
		//数据写入薪资表			
		$upRecode="UPDATE $DataIn.cwxzsheet SET BranchId='$BranchId',JobId='$JobId',Grade='$Grade',
		KqSign='$KqSign',Dx='$Dx',Gljt='$Gljt',Gwjt='$Gwjt',Jj='$Jj',Shbz='$Shbz',
		Zsbz='$Zsbz',Jtbz='$Jtbz',Jbf='$Jbf',Yxbz='$Yxbz',taxbz='$taxbz',Jz='$Jz',Sb='$Sb',Kqkk='$Kqkk',
		RandP='$RandP',Amount='$Amount',Locks='0',Operator='$Operator'	WHERE Id='$Id' LIMIT 1";
		$upAction=@mysql_query($upRecode);
		$Mid=$Id;
		if($upAction){ 
			$Log.="$Name $chooseMonth 的薪资重置成功! $Operator <br>";			
			//借支数据连接主ID
			$update_SQL = mysql_query("UPDATE $DataIn.cwygjz SET Mid='$Mid',Locks='0' WHERE Number=$Number and left(PayDate,7)='$Month'",$link_id);
			$update_Result = mysql_query($update_SQL);
			//奖惩数据连接主ID
			$update_SQL = mysql_query("UPDATE $DataIn.staffrandp SET Mid='$Mid',Locks='0' WHERE Number=$Number and left(Date,7)='$Month'",$link_id);
			$update_Result = mysql_query($update_SQL);
			} 
		else{
			$Log.="<div class=redB>$Name $chooseMonth 的薪资重置失败!</div><br>";
			$OperationResult="N";
			}
		}while($myRow = mysql_fetch_array($myResult));
	}
?>