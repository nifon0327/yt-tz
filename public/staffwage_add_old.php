<?php 
//ewen 2013-08-03 加入加班奖金
include "../model/modelhead.php";
//步骤2：
$Log_Item="追加 $chooseMonth 薪资记录";			//需处理
//$fromWebPage=$funFrom."_m";
$fromWebPage=$funFrom."_read";
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
$myResult = mysql_query("SELECT Number,Currency,Name,BranchId,JobId,GroupId,Grade,KqSign,ComeIn,WorkAdd  
FROM $DataPublic.staffmain 
WHERE left(ComeIn,7)<='$chooseMonth' 
AND cSign='$Login_cSign'
AND Number NOT IN (SELECT Number FROM $DataPublic.dimissiondata WHERE left(outDate,7)<'$chooseMonth')  
AND Number NOT IN(SELECT Number FROM $DataIn.cwxzsheet WHERE Month='$chooseMonth') 
AND (KqSign>1 OR (Number IN(SELECT Number FROM $DataIn.kqdata WHERE Month='$chooseMonth')))
$EstateSTR  
",$link_id);

//echo "cSign: $cSign, Month='$chooseMonth' ";
/*if($cSign==3){ // add by zx 2015-11-02  
	//echo "$donwloadFileIP";
	$url="http://10.0.10.2/public/staffwage_addToMC.php?chooseMonth=$chooseMonth&Login_cSign=$cSign&Operator=$Operator";
	//echo "$url";
	$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
	//$content= str_replace("\"","'",$str);
	$content=$str; 
	$start="^";
	$strP=strpos($content,$start);
	$tempStr=substr($content,$strP+1);	
	//echo "成功:$tempStr"; 
	$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&chooseMonth=$chooseMonth&Estate=$Estate";
    $Log=$tempStr;
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";

	return false;
}*/

//return false;

//

if($myRow = mysql_fetch_array($myResult)){
	do{
		//初始化
		$Dx=$Gljt=$Gwjt=$Jj=$Shbz=$Zsbz=$Jtbz=$Jbf=$Jbjj=$Yxbz=$taxbz=$Studybz=$Housebz=$Jz=$Sb=$Gjj=$Ct=$Kqkk=$dkfl=$RandP=$Otherkk=$Amount=0;
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
		$WorkAdd=$myRow["WorkAdd"];	
		//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
		//底薪、补助
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
		$Dx=$B_Result["Dx"];
		$Shbz=$B_Result["Shbz"];
		$Shbz=($WorkAdd==2 && $KqSign==1)?$Shbz+100:$Shbz;
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
		$baseResult = mysql_fetch_array(mysql_query("SELECT Dx,Jj,Jtbz,Sbkk,Taxkk FROM $DataPublic.paybase where  Number='$Number' LIMIT 1",$link_id));

		$Jj=$baseResult["Jj"] == ""?"0.00":$baseResult["Jj"];
		
		if ($Currency==1){  //人民币工资计算
					$InLates=$OutEarlys=0;
					if($KqSign==1){
						include "subprogram/staff_model_gl.php";		//工龄津贴
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
					$JzRow= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS Jz FROM $DataIn.cwygjz WHERE Number=$Number and left(PayDate,7)='$chooseMonth'",$link_id));
					$Jz=$JzRow["Jz"]==""?0:$JzRow["Jz"];
					if ($Jz==0){
					    include "kqcode/staffwage_jz.php";
			   	    }

					//社保		
					$SbRow= mysql_fetch_array(mysql_query("SELECT IFNULL(mAmount,0) AS Sb FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=1 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
					$Sb=$SbRow["Sb"]==""?0:$SbRow["Sb"];
					//住房公积金		
					$GjjRow= mysql_fetch_array(mysql_query("SELECT IFNULL(mAmount,0) AS Gjj FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=2 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
					$Gjj=$GjjRow["Gjj"]==""?0:$GjjRow["Gjj"];
					//餐费扣款 
			        $CtRow = mysql_fetch_array(mysql_query("SELECT IFNULL(Amount,0) AS Ct FROM $DataPublic.ct_monthamount  WHERE Number=$Number and Month='$chooseMonth' LIMIT 1",$link_id));
					$Ct=$CtRow["Ct"]==""?0:$CtRow["Ct"];
					
			        
			        include "kqcode/staffwage_study.php"; //就学补助
			        include "kqcode/staffwage_house.php";//购房补助
			        
					
					if($DKhours>0){
						$FLAmount=$Gljt+$Gwjt+$Shbz+$Zsbz+$Yxbz+$Jtbz;
						//工龄津贴+岗位津贴+生活补助+住宿补助+夜宵补助+交通补助
						$dkfl=$SubdkAmount*($DKhours/8); //按天扣 
						if ($FLAmount<$dkfl){   //如果不够扣，则跟福利费用就一样了
							$dkfl=$FLAmount;	
							}
						}
					//echo "$Number $InLates $OutEarlys";
					if ($chooseMonth<="2015-10"){
						if ($chooseMonth>="2015-09"){
							$dkfl = $dkfl + ($InLates+$OutEarlys)*20; //迟到、早退次数扣款
						}
						else{
							$dkfl = $dkfl + ($InLates+$OutEarlys)*10; //迟到、早退次数扣款
						}
					
					
						//工资签收逾期扣款
						if($chooseMonth == "2013-09")
						{
							$searchOverTime = "Month in ('2013-07','2013-08')";
						}
						else
						{
							$targetMonth = date('Y-m',strtotime('last month', strtotime($chooseMonth)));
							$searchOverTime = "Month = '$targetMonth'";
						}
						$remark = "";
						$wageSignOverSql = "Select Sum(PayMent) as Pay From $DataPublic.wage_sign_overtime Where Number = '$Number' and Estate = '1' and $searchOverTime";
						
						$wageSignResult = mysql_query($wageSignOverSql);
						$wageSignOverRow = mysql_fetch_assoc($wageSignResult);
						$wageKk = $wageSignOverRow["Pay"]==''?0:$wageSignOverRow["Pay"];
						//echo $wageKk.'  '.$wageSignOverSql.'<br>';
						if($wageKk != "")
						{
							if($chooseMonth == "2013-09")
							{
								$remark = "2013-07、2013-08薪资签收逾期扣款";
							}
							else
							{
								$remark = "$chooseMonth 薪资签收逾期扣款";
							}
							
							$remark .= "$wageKk 元";
						}
						$dkfl += $wageKk;
					}
					//个税计算
					$taxbz=0; //个税补助
					//include "kqcode/staffwage_gs.php";
					if ($Number==10001 || $Number==11880){
						include "kqcode/staffwage_gs_tw.php";  // 新的扣税方式
					}
					else{
					    include "kqcode/staffwage_gsnew.php";  // 新的扣税方式	
					}
                    
		}
		else{
			    $Jtbz=$baseResult["Jtbz"];
			    $Sb=$baseResult["Sbkk"];
			    $RandP=$baseResult["Taxkk"];
			    $Dx=$baseResult["Dx"];
			    
			    $Shbz=0;$Zsbz=0;$Gwjt=0;$Gljt=0;
			    $Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$taxbz-$Sb-$Kqkk-$dkfl-$RandP-$Otherkk;
		}
		
		//$jbRecode 来自kqcode/staffwage_jbf.php
		if ($jbRecode!="") {
	        $del_Result="DELETE  FROM $DataIn.hdjbsheet WHERE Month='$chooseMonth' and Number='$Number'";
	        $delAction=@mysql_query($del_Result);
	        
	        $inAction2=@mysql_query($jbRecode);
		    if ($inAction2){ 
			     $Log.="员工 $Name $chooseMonth 的假日加班费保存成功!<br>";
			     $Jbjj=0;
			     if ($oRandP>0 && $chooseMonth>'2015-10'){
				    $UP_Result="UPDATE $DataIn.hdjbsheet SET oRandP=$oRandP,Amount=Amount-$oRandP WHERE Month='$chooseMonth' and Number='$Number'";
				    $upAction=@mysql_query($UP_Result);
				    if (!$UP_Result){
				         $Log.="<div class=redB>更新员工 $Name $chooseMonth 的假日加班费税款失败!</div><br>$UP_Result<br>";
			             $OperationResult="N";
				    }
				    $oRandP=0;
			     }
			} 
		     else{
			    $Log.="<div class=redB>员工 $Name $chooseMonth 的假日加班费保存失败!</div><br>$jbRecode<br>";
			    $OperationResult="N";
			}
		}
		//数据写入薪资表			
		$inRecode="INSERT INTO $DataIn.cwxzsheet 		(Id,Mid,Currency,Month,BranchId,JobId,GroupId,Grade,KqSign,Number,Dx,Gljt,Gwjt,Jj,Shbz,Zsbz,Jtbz,Studybz,Housebz,Jbf,Jbjj,Yxbz,taxbz,Jz,Sb,Gjj,Ct,Kqkk,dkfl,RandP,Otherkk,Amount,Remark,Estate,Locks,Operator) VALUES 		
		(NULL,'0','$Currency','$chooseMonth','$BranchId','$JobId','$GroupId','$Grade','$KqSign','$Number','$Dx','$Gljt','$Gwjt','$Jj','$Shbz','$Zsbz','$Jtbz','$Studybz','$Housebz','$Jbf','$Jbjj','$Yxbz','$taxbz','$Jz','$Sb','$Gjj','$Ct','$Kqkk','$dkfl','$RandP','$Otherkk','$Amount','$remark','1','0','$Operator')";
		
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
	$myResult = mysql_query("SELECT Number,Currency,Name,BranchId,JobId,GroupId,Grade,KqSign,ComeIn,WorkAdd  
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
			$Currency=$myRow["Currency"];
			$Number=$myRow["Number"];
			$Name=$myRow["Name"];
			$BranchId=$myRow["BranchId"];
			$JobId=$myRow["JobId"];
			$GroupId=$myRow["GroupId"];
			$Grade=$myRow["Grade"];
			$KqSign=$myRow["KqSign"];
			$ComeIn=$myRow["ComeIn"];
			$WorkAdd=$myRow["WorkAdd"];		
			//计算当月所在部门、职位、等级、考勤状态？或生成薪资前先设定好
			//底薪、补助
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset where KqSign='$KqSign' LIMIT 1",$link_id));
			$Dx=$B_Result["Dx"];
			$Shbz=$B_Result["Shbz"];
			$Shbz=($WorkAdd==2 && $KqSign==1)?$Shbz+100:$Shbz;
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
		$baseResult = mysql_fetch_array(mysql_query("SELECT Dx,Jj,Jtbz,Sbkk,Taxkk FROM $DataPublic.paybase where  Number='$Number' LIMIT 1",$link_id));
		$Jj=$baseResult["Jj"];
		
		if ($Currency==1){  //人民币工资计算
			    if($KqSign==1){//加班费
				include "kqcode/staffwage_jbf.php";
				}
			   //借支:加入全部未还款项
			   
				$JzRow= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS Jz FROM $DataIn.cwygjz WHERE Number='$Number' and left(PayDate,7)='$chooseMonth'",$link_id));
				$Jz=$JzRow["Jz"]==""?0:$JzRow["Jz"];
				
				if ($Jz==0){
					include "kqcode/staffwage_jz.php";
				}
				
				//社保		
				$SbRow= mysql_fetch_array(mysql_query("SELECT IFNULL(mAmount,0) AS Sb FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=1 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
				$Sb=$SbRow["Sb"]==""?0:$SbRow["Sb"];
				//住房公积金		
				$GjjRow= mysql_fetch_array(mysql_query("SELECT IFNULL(mAmount,0) AS Gjj FROM $DataIn.sbpaysheet WHERE 1 AND TypeId=2 and Number=$Number and Month='$chooseMonth'  ORDER BY  Id DESC LIMIT 1",$link_id));
				$Gjj=$GjjRow["Gjj"]==""?0:$GjjRow["Gjj"];
				//餐费扣款 
		        $CtRow = mysql_fetch_array(mysql_query("SELECT IFNULL(Amount,0) AS Ct FROM $DataPublic.ct_monthamount  WHERE Number=$Number and Month='$chooseMonth' LIMIT 1",$link_id));
				$Ct=$CtRow["Ct"]==""?0:$CtRow["Ct"];
		        
			        
				////有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,一天74块钱扣,但应到工时，要加上它。 
				if($DKhours>0){
						$FLAmount=$Gljt+$Gwjt+$Shbz+$Zsbz+$Yxbz+$Jtbz;//假日加班费
						       //工龄津贴+岗位津贴+生活补助+住宿补助+夜宵补助+交通补助
						$dkfl=$SubdkAmount*($DKhours/8); //按天扣 
						if ($FLAmount<$dkfl){   //如果不够扣，则跟福利费用就一样了
							$dkfl=$FLAmount;	
						}
				}		
				
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
				$Otherkk = $wageSignOverRow["Pay"];
				if($Otherkk != "")
				{
					if($chooseMonth == "2013-09")
					{
						$remark = "2013-07、2013-08薪资签收逾期扣款";
					}
					else
					{
						$remark = "$chooseMonth薪资签收逾期扣款";
					}
					
					$remark .= "$Otherkk 元";
				}
				else
				{
					$Otherkk = "0";
				}
		
				//个税计算
				$taxbz=0; //个税补助
				//include "kqcode/staffwage_gs.php";
                include "kqcode/staffwage_gsnew.php";  // 新的扣税方式
	}
	else{
			    $Jtbz=$baseResult["Jtbz"];
			    $Sb=$baseResult["Sbkk"];
			    $RandP=$baseResult["Taxkk"];
			    $Dx=$baseResult["Dx"];
			    
			    $Shbz=0;$Zsbz=0;$Gwjt=0;$Gljt=0;
			   
			    $Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$taxbz-$Sb-$Kqkk-$dkfl-$RandP-$Otherkk;
	}
	
	    //$jbRecode 来自kqcode/staffwage_jbf.php
		if ($jbRecode!="") {
	        $del_Result="DELETE  FROM $DataIn.hdjbsheet WHERE Month='$chooseMonth' and Number='$Number'";
	        $delAction=@mysql_query($del_Result);
	        
	        $inAction2=@mysql_query($jbRecode);
		    if ($inAction2){ 
			     $Log.="员工".$Number.$chooseMonth."的假日加班费保存成功!<br>";
			     $Jbjj=0;
			     if ($oRandP>0 && $chooseMonth>'2015-10'){
				    $UP_Result="UPDATE $DataIn.hdjbsheet SET oRandP=$oRandP,Amount=Amount-$oRandP WHERE Month='$chooseMonth' and Number='$Number'";
				    $upAction=@mysql_query($UP_Result);
				    if (!$UP_Result){
				         $Log.="<div class=redB>更新员工 $Name $chooseMonth 的假日加班费税款失败!</div><br>$UP_Result<br>";
			             $OperationResult="N";
				    }
				    $oRandP=0;
			     }
			} 
		     else{
			    $Log.="<div class=redB>员工".$Number.$chooseMonth."的的假日加班费保存失败!</div><br>";
			    $OperationResult="N";
			}
		}
		
		//数据写入薪资表			
		$inRecode="INSERT INTO $DataIn.cwxzsheet 
			(Id,Mid,Currency,Month,BranchId,JobId,GroupId,Grade,KqSign,Number,Dx,Gljt,Gwjt,Jj,Shbz,Zsbz,Jtbz,Jbf,Jbjj,Yxbz,taxbz,Jz,Sb,Gjj,Ct,Kqkk,dkfl,RandP,Otherkk,Amount,Remark,Estate,Locks,Operator) VALUES 		
			(NULL,'0','$Currency','$chooseMonth','$BranchId','$JobId','$GroupId','$Grade','$KqSign','$Number','$Dx','$Gljt','$Gwjt','$Jj','$Shbz','$Zsbz','$Jtbz','$Jbf','$Jbjj','$Yxbz','$taxbz','$Jz','$Sb','$Gjj','$Ct','$Kqkk','$dkfl','$RandP','$Otherkk','$Amount','$remark','1','0','$Operator')";
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