<?php 
//EWEN 2013-08-04 加入餐费、加班奖金
include "../model/modelhead.php";
//步骤2：
$Log_Item="追加 $chooseMonth 薪资记录";			//需处理
$fromWebPage=$funFrom."_m";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//追加情况，如果是当前月，只追加离职人员
$nowMonth=date("Y-m");
if($nowMonth==$chooseMonth){//只生成当月有效且已当月离职的员工薪资
	$EstateSTR=" and Estate=0";
	}
//读取加班时薪资料
$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE Estate=1",$link_id);
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

//有效员工计算:入职必须<=当前计薪月份；或离职月份>=当前计薪月份；且当月没有薪资数据的员工
//如果是需要考勤的，则还需要有考勤统计数据
$myResult = mysql_query("SELECT Number,Currency,Name,BranchId,JobId,GroupId,Grade,KqSign,ComeIn 
FROM $DataPublic.staffmain 
WHERE left(ComeIn,7)<='$chooseMonth' 
AND cSign='$Login_cSign'
AND Number NOT IN (SELECT Number FROM $DataPublic.dimissiondata WHERE left(outDate,7)<'$chooseMonth')
AND Number NOT IN(SELECT Number FROM $DataIn.cwxzsheet WHERE Month='$chooseMonth') 
AND (KqSign>1 OR (Number IN(SELECT Number FROM $DataIn.kqdata WHERE Month='$chooseMonth')))
$EstateSTR
",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		//初始化
		$Dx=$Gljt=$Gwjt=$Jj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Jbjj=$Yxbz=$taxbz=$Jz=$Sb=$Gjj=$Ct=$Kqkk=$dkfl=$RandP=$Otherkk=$Amount=0;
		$sumY=0;$sumM=0;$sumD=0;
		$Currency=$myRow["Currency"];//加入员工薪资支付的货币
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$BranchId=$myRow["BranchId"];
		$JobId=$myRow["JobId"];
		$GroupId=$myRow["GroupId"];
		$Grade=$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$ComeIn=$myRow["ComeIn"];	
		//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
		//底薪、补助
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
		$Dx=$B_Result["Dx"];
		$Shbz=$B_Result["Shbz"];
		$Zsbz=$B_Result["Zsbz"];
		//津贴计算
		if($Grade>0){
			$jtResult = mysql_fetch_array(mysql_query("SELECT Subsidy FROM $DataPublic.gradesubsidy WHERE 1 and Grade=$Grade LIMIT 1",$link_id));
			$Gwjt=$jtResult["Subsidy"];
			}
		else{
			$Gwjt=0;
			}
		if($KqSign==1){//加班费
			include "kqcode/staffwage_jbf.php";
			}
		if($KqSign==1){
			//工龄津贴
			include "subprogram/staff_model_gl.php";		
			$Gljt=$sumY*$glAmount;			
			//补助重置:首月没有补助，次月根据天数计算，第三月全部
			if($sumY==0 && $sumM<2){
				if($sumM==1){//已过试用期，开始计算补助，但未够足额补助的,按比例计算
					$Shbz=sprintf("%.0f",$Shbz*($sumD/$theMonthDays));
					$Zsbz=sprintf("%.0f",$Zsbz*($sumD/$theMonthDays));
					}
				else{
					$Shbz=0;$Zsbz=0;//不足刚好一个月，试用期没有补助
					}
				}
			}
		//预设奖金
			$jjResult = mysql_query("SELECT Jj FROM $DataPublic.paybase where 1 and Number=$Number LIMIT 1",$link_id);
			if($jjRow=mysql_fetch_array($jjResult)){
				$Jj=$jjRow["Jj"];
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
		//个税计算
		include "kqcode/staffwage_gs.php";
		//数据入库
		//echo " $sumM 姓名：$Name 底：$Dx+工龄：$Gljt+岗位：$Gwjt+奖金：$Jj+生活：$Shbz+宿：$Zsbz+加班：$Jbf+夜：$Yxbz-借：$Jz-社保：$Sb-考勤扣：$Kqkk <br>";
		//数据写入薪资表			
		$inRecode="INSERT INTO $DataIn.cwxzsheet 
		(Id,Mid,Currency,Month,BranchId,JobId,GroupId,Grade,KqSign,Number,Dx,Gljt,Gwjt,Jj,Shbz,Zsbz,Jtbz,Jbf,Jbjj,Yxbz,taxbz,Jz,Sb,Gjj,Ct,Kqkk,dkfl,RandP,Otherkk,Amount,Remark,Estate,Locks,Operator) VALUES 		
		(NULL,'0','$Currency','$chooseMonth','$BranchId','$JobId','$GroupId','$Grade','$KqSign','$Number','$Dx','$Gljt','$Gwjt','$Jj','$Shbz','$Zsbz','$Jtbz','$Jbf','$Jbjj','$Yxbz','$taxbz','$Jz','$Sb','$Gjj','$Ct','$Kqkk','$dkfl','$RandP','0','$Amount','','1','0','$Operator')";
		$inAction=@mysql_query($inRecode);
		$Mid=mysql_insert_id();
		if($inAction){ 
			$Log.="$Name $chooseMonth 的 $TitleSTR 成功!<br>";			
			//借支数据连接主ID
			$update_SQL = mysql_query("UPDATE $DataIn.cwygjz SET Mid='$Mid',Locks='0' WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id);
			$update_Result = mysql_query($update_SQL);
			//奖惩数据连接主ID
			$update_SQL = mysql_query("UPDATE $DataIn.staffrandp SET Mid='$Mid',Locks='0' WHERE Number=$Number and left(Date,7)='$chooseMonth'",$link_id);
			$update_Result = mysql_query($update_SQL);
			} 
		else{
			$Log.="<div class=redB>$Name $chooseMonth 的 $TitleSTR 失败! $inRecode </div><br>";
			$OperationResult="N";
			}
		}while($myRow = mysql_fetch_array($myResult));
	}
else{
	$chooseMonth=date("Y-m");
	if($nowMonth==$chooseMonth){//只生成当月有效且已当月离职的员工薪资
		$EstateSTR=" and Estate=0";
		}
	//有效员工计算:入职必须<=当前计薪月份；或离职月份>=当前计薪月份；且当月没有薪资数据的员工
	//如果是需要考勤的，则还需要有考勤统计数据
	$myResult = mysql_query("SELECT Number,Currency,Name,BranchId,JobId,GroupId,Grade,KqSign,ComeIn 
	FROM $DataPublic.staffmain 
	WHERE left(ComeIn,7)<='$chooseMonth' 
	AND cSign='$Login_cSign'
	AND  Number NOT IN (SELECT Number FROM $DataPublic.dimissiondata WHERE left(outDate,7)<'$chooseMonth')
	AND Number NOT IN(SELECT Number FROM $DataIn.cwxzsheet WHERE Month='$chooseMonth') 
	AND (KqSign>1 OR (Number IN(SELECT Number FROM $DataIn.kqdata WHERE Month='$chooseMonth')))
	$EstateSTR
	",$link_id);
	if($myRow = mysql_fetch_array($myResult)){
		do{
			//初始化
			$Dx=$Gljt=$Gwjt=$Jj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Jbjj=$Yxbz=$taxbz=$Jz=$Sb=$Gjj=$Ct=$Kqkk=$dkfl=$RandP=$Otherkk=$Amount=0;
			$sumY=0;$sumM=0;$sumD=0;
			$Currency=$myRow["Currency"];//加入员工薪资支付的货币
			$Number=$myRow["Number"];
			$Name=$myRow["Name"];
			$BranchId=$myRow["BranchId"];
			$JobId=$myRow["JobId"];
			$GroupId=$myRow["GroupId"];
			$Grade=$myRow["Grade"];
			$KqSign=$myRow["KqSign"];
			$ComeIn=$myRow["ComeIn"];	
			//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
			//底薪、补助
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
			$Dx=$B_Result["Dx"];
			$Shbz=$B_Result["Shbz"];
			$Zsbz=$B_Result["Zsbz"];
			//津贴计算
			if($Grade>0){
				$jtResult = mysql_fetch_array(mysql_query("SELECT Subsidy FROM $DataPublic.gradesubsidy where 1 and Grade=$Grade LIMIT 1",$link_id));
				$Gwjt=$jtResult["Subsidy"];
				}
			else{
				$Gwjt=0;
				}
			if($KqSign==1){
				//工龄津贴
				include "subprogram/staff_model_gl.php";		
				$Gljt=$sumY*50;			
				//补助重置:首月没有补助，次月根据天数计算，第三月全部
				if($sumY==0 && $sumM<2){
					if($sumM==1){//已过试用期，开始计算补助，但未够足额补助的,按比例计算
						$Shbz=sprintf("%.0f",$Shbz*($sumD/$theMonthDays));
						$Zsbz=sprintf("%.0f",$Zsbz*($sumD/$theMonthDays));
						}
					else{
						$Shbz=0;$Zsbz=0;//不足刚好一个月，试用期没有补助
						}
					}
				}
			if($KqSign==1){//加班费
				include "kqcode/staffwage_jbf.php";
				}
			//预设奖金
			$jjResult = mysql_query("SELECT Jj FROM $DataPublic.paybase where 1 and Number=$Number LIMIT 1",$link_id);
			if($jjRow=mysql_fetch_array($jjResult)){
				$Jj=$jjRow["Jj"];
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
		//个税计算
		include "kqcode/staffwage_gs.php";
			//数据入库
			//echo " $sumM 姓名：$Name 底：$Dx+工龄：$Gljt+岗位：$Gwjt+奖金：$Jj+生活：$Shbz+宿：$Zsbz+加班：$Jbf+夜：$Yxbz-借：$Jz-社保：$Sb-考勤扣：$Kqkk <br>";
			//数据写入薪资表			
			$inRecode="INSERT INTO $DataIn.cwxzsheet 
		(Id,Mid,Currency,Month,BranchId,JobId,GroupId,Grade,KqSign,Number,Dx,Gljt,Gwjt,Jj,Shbz,Zsbz,Jtbz,Jbf,Jbjj,Yxbz,taxbz,Jz,Sb,Gjj,Ct,Kqkk,dkfl,RandP,Otherkk,Amount,Remark,Estate,Locks,Operator) VALUES 		
		(NULL,'0','$Currency','$chooseMonth','$BranchId','$JobId','$GroupId','$Grade','$KqSign','$Number','$Dx','$Gljt','$Gwjt','$Jj','$Shbz','$Zsbz','$Jtbz','$Jbf','Jbjj','$Yxbz','$taxbz','$Jz','$Sb','$Gjj','$Ct','$Kqkk','$dkfl','$RandP','0','$Amount','','1','0','$Operator')";
			$inAction=@mysql_query($inRecode);
			$Mid=mysql_insert_id();
			if($inAction){ 
				$Log.="$Name $chooseMonth 的 $TitleSTR 成功!<br>";			
				//借支数据连接主ID
				$update_SQL = mysql_query("UPDATE $DataIn.cwygjz SET Mid='$Mid',Locks='0' WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id);
				$update_Result = mysql_query($update_SQL);
				//奖惩数据连接主ID
				$update_SQL = mysql_query("UPDATE $DataIn.staffrandp SET Mid='$Mid',Locks='0' WHERE Number=$Number and left(Date,7)='$chooseMonth'",$link_id);
				$update_Result = mysql_query($update_SQL);
				} 
			else{
				$Log.="<div class=redB>$Name $chooseMonth 的 $TitleSTR 失败! $inRecode </div><br>";
				$OperationResult="N";
				}
			}while($myRow = mysql_fetch_array($myResult));
		}
	}
//步骤4：
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&chooseMonth=$chooseMonth&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>