<?php 
//EWEN 2013-08-04 加入餐费、加班奖金
$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE  Estate=1",$link_id);
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
			case "103"://2倍时薪
				$jbAmount2=$checkRow["Value"];
				break;
			case "104"://3倍时薪
				$jbAmount3=$checkRow["Value"];
				break;
			case "108":// 有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,add by zx 20130529
				$SubdkAmount=$checkRow["Value"];
				break;				
			}
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
$glAmount=$glAmount==""?0:$glAmount;
$jbAmount=$jbAmount==""?0:$jbAmount;

//如果是需要考勤的，则还需要有考勤统计数据
$myResult = mysql_query("SELECT X.Number,X.Month,M.Name,M.BranchId,M.JobId,M.GroupId,M.Grade,M.KqSign,M.ComeIn FROM $DataIn.cwxzsheet X LEFT JOIN $DataPublic.staffmain M ON X.Number=M.Number WHERE X.Id='$Id' LIMIT 1",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		//初始化
		$Dx=$Gljt=$Gwjt=$Jj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Jbjj=$Yxbz=$taxbz=$Jz=$Sb=$Gjj=$Ct=$Kqkk=$dkfl=$RandP=$Otherkk=$Amount=0;
		$sumY=0;$sumM=0;$sumD=0;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$BranchId=$myRow["BranchId"];
		$JobId=$myRow["JobId"];
		$GroupId=$myRow["GroupId"];
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
		$Jj=$jjResult["Jj"] == ""?"0.00":$jjResult["Jj"];
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
		$JzRow= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS Jz FROM $DataIn.cwygjz WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id));
		$Jz=$JzRow["Jz"]==""?0:$JzRow["Jz"];
		//社保		
		$SbRow= mysql_fetch_array(mysql_query("SELECT IFNULL(mAmount,0) AS Sb FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=1 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
		$Sb=$SbRow["Sb"]==""?0:$SbRow["Sb"];
		//住房公积金		
		$GjjRow= mysql_fetch_array(mysql_query("SELECT IFNULL(mAmount,0) AS Gjj FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=2 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
		$Gjj=$GjjRow["Gjj"]==""?0:$GjjRow["Gjj"];
		//餐费扣款 
        $CtRow = mysql_fetch_array(mysql_query("SELECT IFNULL(Amount,0) AS Ct FROM $DataPublic.ct_monthamount  WHERE Number=$Number and Month='$chooseMonth' LIMIT 1",$link_id));
		$Ct=$CtRow["Ct"]==""?0:$CtRow["Ct"];
		////有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,add by zx 20130529,一天74块钱扣,但应到工时，要加上它。 add by zx 20130529
		if($DKhours>0){
				$FLAmount=$Gljt+$Gwjt+$Shbz+$Zsbz+$Yxbz+$Jtbz;
				       //工龄津贴+岗位津贴+生活补助+住宿补助+夜宵补助+交通补助
				$dkfl=$SubdkAmount*($DKhours/8); //按天扣 
				if ($FLAmount<$dkfl){   //如果不够扣，则跟福利费用就一样了
					$dkfl=$FLAmount;	
				}
		}
		$dkfl = $dkfl + ($InLates+$OutEarlys)*10;
		//工资签收逾期扣款
		if($chooseMonth == "2013-09")
		{
			$searchOverTime = "Month in ('2013-07','2013-08')";
		}
		else
		{
			$targetMonth = date('Y-m',strtotime('last month', strtotime($month)));
			$searchOverTime = "Month = '$targetMonth'";
		}
		$remark = "";
		$wageSignOverSql = "Select Sum(PayMent) as Pay From $DataPublic.wage_sign_overtime Where Number = '$Number' and Estate = '1' and $searchOverTime";
		$wageSignResult = mysql_query($wageSignOverSql);
		$wageSignOverRow = mysql_fetch_assoc($wageSignResult);
		$wageKk = $wageSignOverRow["Pay"];
		if($wageKk != "")
		{
			if($chooseMonth == "2013-09")
			{
				$remark = "2013-07、2013-08薪资签收逾期扣款";
			}
			else
			{
				$remark = "$chooseMonth薪资签收逾期扣款";
			}
			
			$remark .= "$wageKk 元";
		}
		
		$dkfl += $wageKk;
		//个税计算
		$taxbz=0; //个税补助，>=175补100元
		include "kqcode/staffwage_gs.php";
		//数据入库
		//echo " $sumM 姓名：$Name 底：$Dx+工龄：$Gljt+岗位：$Gwjt+奖金：$Jj+生活：$Shbz+宿：$Zsbz+加班：$Jbf+夜：$Yxbz-借：$Jz-社保：$Sb-考勤扣：$Kqkk <br>";
		//数据写入薪资表			
		$upRecode="UPDATE $DataIn.cwxzsheet SET BranchId='$BranchId',JobId='$JobId',Grade='$Grade',
		KqSign='$KqSign',Dx='$Dx',Gljt='$Gljt',Gwjt='$Gwjt',Jj='$Jj',Shbz='$Shbz',
		Zsbz='$Zsbz',Jtbz='$Jtbz',Jbf='$Jbf',Jbjj='$Jbjj',Yxbz='$Yxbz',taxbz='$taxbz',Jz='$Jz',Sb='$Sb',Kqkk='$Kqkk',Gjj='$Gjj',Ct='$Ct',dkfl='$dkfl',
		RandP='$RandP',Amount='$Amount',Locks='0',Operator='$Operator', Otherkk='$Otherkk',Remark='$remark'	WHERE Id='$Id' LIMIT 1";
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