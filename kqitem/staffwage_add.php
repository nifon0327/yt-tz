<?php 
//二合一已更新$DataIn.电信---yang 20120801
//代码共享-EWEN 2012-09-03
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
$myResult = mysql_query("SELECT Number,Name,BranchId,JobId,Grade,KqSign,ComeIn 
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
		$Dx=0;$Gljt=0;$Gwjt=0;$Jj=0;$Shbz=0;$Zsbz=0;$Jtbz=0;$Jbf=0;$Yxbz=0;$taxbz=0;$Jz=0;$Sb=0;$Kqkk=0;$dkfl=0;$RandP=0;$Amount=0;
		$sumY=0;$sumM=0;$sumD=0;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$BranchId=$myRow["BranchId"];
		$JobId=$myRow["JobId"];
		$Grade=$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$ComeIn=$myRow["ComeIn"];	
		//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
		//底薪、补助
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
		$Dx=$B_Result["Dx"];
		$Shbz=$B_Result["Shbz"];
		$Zsbz=$B_Result["Zsbz"];
		$Jtbz=$B_Result["Jtbz"];
		//津贴计算
		if($Grade>0){
			$jtResult = mysql_fetch_array(mysql_query("SELECT Subsidy FROM $DataPublic.gradesubsidy WHERE 1 and Grade=$Grade LIMIT 1",$link_id));
			$Gwjt=$jtResult["Subsidy"];
			}
		else{
			$Gwjt=0;
			}
		//预设奖金
			$jjResult = mysql_query("SELECT Jj,Jtbz FROM $DataPublic.paybase where 1 and Number=$Number LIMIT 1",$link_id);
			if($jjRow=mysql_fetch_array($jjResult)){
				$Jj=$jjRow["Jj"];
				
				}
		if($KqSign==1){
			//工龄津贴
			include "subprogram/staff_model_gl.php";		
			$Gljt=$sumY*$glAmount;			
			////补助重置:首月次月没有补助，第三月根据天数计算，第四月全部
			if($sumY==0 && $sumM<3){
				if($sumM==2){//已过试用期，开始计算补助，但未够足额补助的,按比例计算
					$Shbz=sprintf("%.0f",$Shbz*($sumD/$theMonthDays))<0?0:sprintf("%.0f",$Shbz*($sumD/$theMonthDays));
					$Zsbz=sprintf("%.0f",$Zsbz*($sumD/$theMonthDays))<0?0:sprintf("%.0f",$Zsbz*($sumD/$theMonthDays));
					$Jtbz=sprintf("%.0f",$Jtbz*($sumD/$theMonthDays))<0?0:sprintf("%.0f",$Jtbz*($sumD/$theMonthDays));
					}
				else{
					$Shbz=0;$Zsbz=0;$Jtbz=0;//不足刚好两个月，试用期没有补助
					}
				}
			include "kqcode/staffwage_jbf.php";//加班费
			}
		//借支:加入全部未还款项
		$ygjz_Result = mysql_query("SELECT SUM(Amount) AS Amount FROM $DataIn.cwygjz WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id);
		$Jz=mysql_result($ygjz_Result,0,"Amount");
		$Jz=$Jz==""?0:$Jz;

		//社保		
		$sbCheck= mysql_fetch_array(mysql_query("SELECT  mAmount FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=1 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
		$Sb=$sbCheck["mAmount"]==""?0:$sbCheck["mAmount"];		
		//住房公积金		
		$gjjCheck= mysql_fetch_array(mysql_query("SELECT  mAmount FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=2 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
		$Gjj=$gjjCheck["mAmount"]==""?0:$gjjCheck["mAmount"];		
        //餐费扣款 
        $ct_Result = mysql_query("SELECT  Amount FROM $DataPublic.ct_monthamount  WHERE Number=$Number and Month='$chooseMonth'",$link_id);
		if($dataRow = mysql_fetch_array($ct_Result)) {
			//$ct=mysql_result($ct_Result,0,"Amount");
			$ct=$dataRow["Amount"];
		}
		$ct=$ct==""?0:$ct;

		////有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,add by zx 20130529,一天74块钱扣,但应到工时，要加上它。 add by zx 20130529
		if($DKhours>0){
				$FLAmount=$Gljt+$Gwjt+$Shbz+$Zsbz+$Yxbz+$Jtbz;//假日加班费
				       //工龄津贴+岗位津贴+生活补助+住宿补助+夜宵补助+交通补助
				$dkfl=$SubdkAmount*($DKhours/8); //按天扣 
				if ($FLAmount<$dkfl){   //如果不够扣，则跟福利费用就一样了
					$dkfl=$FLAmount;	
				}
		}

		//个税计算
		$taxbz=0; //个税补助，>=175补100元
		include "kqcode/staffwage_gs.php";
		//数据入库
		//echo " $sumM 姓名：$Name 底：$Dx+工龄：$Gljt+岗位：$Gwjt+奖金：$Jj+生活：$Shbz+宿：$Zsbz+加班：$Jbf+夜：$Yxbz-借：$Jz-社保：$Sb-考勤扣：$Kqkk <br>";
		//数据写入薪资表			
		$inRecode="INSERT INTO $DataIn.cwxzsheet 
		(Id,Mid,Month,BranchId,JobId,Grade,KqSign,Number,Dx,Gljt,Gwjt,Jj,Shbz,Zsbz,Jtbz,Jbf,Yxbz,taxbz,Jz,Sb,Gjj,Ct,Kqkk,dkfl,RandP,Otherkk,Amount,Remark,Estate,Locks,Operator) VALUES 		
		(NULL,'0','$chooseMonth','$BranchId','$JobId','$Grade','$KqSign','$Number','$Dx','$Gljt','$Gwjt','$Jj','$Shbz','$Zsbz','$Jtbz','$Jbf','$Yxbz','$taxbz','$Jz','$Sb','$Gjj','$ct','$Kqkk','$dkfl','$RandP','0','$Amount','','1','0','$Operator')";
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
	$myResult = mysql_query("SELECT Number,Name,BranchId,JobId,Grade,KqSign,ComeIn 
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
			$Dx=0;$Gljt=0;$Gwjt=0;$Jj=0;$Shbz=0;$Zsbz=0;$Jtbz=0;$Jbf=0;$Yxbz=0;$taxbz=0;$Jz=0;$Sb=0;$Kqkk=0;$dkfl=0;$RandP=0;$Amount=0;
			$sumY=0;$sumM=0;$sumD=0;
			$Number=$myRow["Number"];
			$Name=$myRow["Name"];
			$BranchId=$myRow["BranchId"];
			$JobId=$myRow["JobId"];
			$Grade=$myRow["Grade"];
			$KqSign=$myRow["KqSign"];
			$ComeIn=$myRow["ComeIn"];	
			//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
			//底薪、补助
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
			$Dx=$B_Result["Dx"];
			$Shbz=$B_Result["Shbz"];
			$Zsbz=$B_Result["Zsbz"];
			$Jtbz=$B_Result["Jtbz"];
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
				$Gljt=$sumY*$glAmount;			
				//补助重置:首月次月没有补助，第三月根据天数计算，第四月全部
				if($sumY==0 && $sumM<3){
					if($sumM==2){//已过试用期，开始计算补助，但未够足额补助的,按比例计算
						$Shbz=sprintf("%.0f",$Shbz*($sumD/$theMonthDays));
						$Zsbz=sprintf("%.0f",$Zsbz*($sumD/$theMonthDays));
						$Jtbz=sprintf("%.0f",$Jtbz*($sumD/$theMonthDays));
						}
					else{
						$Shbz=0;$Zsbz=0;$Jtbz=0;//不足刚好一个月，试用期没有补助
						}
					}
				}
			//预设奖金
			$jjResult = mysql_query("SELECT Jj FROM $DataPublic.paybase where 1 and Number=$Number LIMIT 1",$link_id);
			if($jjRow=mysql_fetch_array($jjResult)){
				$Jj=$jjRow["Jj"];
				}
			
			if($KqSign==1){//加班费
				include "kqcode/staffwage_jbf.php";
				}
			//借支:加入全部未还款项
			$ygjz_Result = mysql_query("SELECT SUM(Amount) AS Amount FROM $DataIn.cwygjz WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id);
			$Jz=mysql_result($ygjz_Result,0,"Amount");
			$Jz=$Jz==""?0:$Jz;
			//社保		
		$sbCheck= mysql_fetch_array(mysql_query("SELECT  mAmount FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=1 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
		$Sb=$sbCheck["mAmount"]==""?0:$sbCheck["mAmount"];		
		//住房公积金		
		$gjjCheck= mysql_fetch_array(mysql_query("SELECT  mAmount FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=2 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
		$Gjj=$gjjCheck["mAmount"]==""?0:$gjjCheck["mAmount"];		
     //餐费扣款 
        $ct_Result = mysql_query("SELECT  Amount FROM $DataPublic.ct_monthamount  WHERE Number=$Number and Month='$chooseMonth'",$link_id);
		$ct=mysql_result($ct_Result,0,"Amount");
		$ct=$ct==""?0:$ct;

		////有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,add by zx 20130529,一天74块钱扣,但应到工时，要加上它。 add by zx 20130529
		if($DKhours>0){
				$FLAmount=$Gljt+$Gwjt+$Shbz+$Zsbz+$Yxbz+$Jtbz;//假日加班费
				       //工龄津贴+岗位津贴+生活补助+住宿补助+夜宵补助+交通补助
				$dkfl=$SubdkAmount*($DKhours/8); //按天扣 
				if ($FLAmount<$dkfl){   //如果不够扣，则跟福利费用就一样了
					$dkfl=$FLAmount;	
				}
		}

		//个税计算
		$taxbz=0; //个税补助，>=175补100元
		include "kqcode/staffwage_gs.php";
			//数据入库
			//echo " $sumM 姓名：$Name 底：$Dx+工龄：$Gljt+岗位：$Gwjt+奖金：$Jj+生活：$Shbz+宿：$Zsbz+加班：$Jbf+夜：$Yxbz-借：$Jz-社保：$Sb-考勤扣：$Kqkk <br>";
			//数据写入薪资表			
			$inRecode="INSERT INTO $DataIn.cwxzsheet 
			(Id,Mid,Month,BranchId,JobId,Grade,KqSign,Number,Dx,Gljt,Gwjt,Jj,Shbz,Zsbz,Jtbz,Jbf,Yxbz,taxbz,Jz,Sb,Gjj,Ct,Kqkk,dkfl,RandP,Otherkk,Amount,Remark,Estate,Locks,Operator) VALUES 		
			(NULL,'0','$chooseMonth','$BranchId','$JobId','$Grade','$KqSign','$Number','$Dx','$Gljt','$Gwjt','$Jj','$Shbz','$Zsbz','$Jtbz','$Jbf','$Yxbz','$taxbz','$Jz','$Sb','$Gjj','$ct','$Kqkk','$dkfl','$RandP','0','$Amount','','1','0','$Operator')";
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