<?php   
//2014-01-08 ewen 更新
$checkLen=strlen($CheckTheDay);
$SearchDay3="";
switch($checkLen){
	case 4://年
		$DivTitle=$CheckTheDay."年<span class='yellowB'> -> </span>";
		$OnclickSTR="onclick='ViewMonths(this,\"$CheckTheDay\");return false;' style='cursor:pointer;'";
		$CheckTheDaySTR="AND DATE_FORMAT(S.Date,'%Y')='$CheckTheDay'";
		break;
	default:
		$TEST=$TYT==""?"":" AND S.Number=$TYT";
		$DivTitle=$CheckTheDay;
		$CheckTheDaySTR="AND DATE_FORMAT(S.Date,'%Y-%m')='$CheckTheDay'";
		$XZSign=0;//薪资表是否已经生成
		//注意：时薪初始化，由于非考勤在职员工会计入统计，所当月无工资的员工（请长假的情况）需做时薪初始化置0值，即此类员工将不计算实际支出
		$checkQJSql=mysql_query("SELECT Number FROM $DataPublic.kqqjsheet WHERE StartDate LIKE '$CheckTheDay%' OR EndDate LIKE '$CheckTheDay%' GROUP BY Number",$link_id);
		if($checkQJRow=mysql_fetch_array($checkQJSql)){
			do{
				$sNumber=$checkQJRow["Number"];
				$TempSXSTR="SX".strval($sNumber); 
				$$TempSXSTR=0;								//初始化请假员工的时薪
				}while ($checkQJRow=mysql_fetch_array($checkQJSql));
			}
		/*
		员工时薪计算:已验证OK
		考勤员工时薪=考勤工资(=实发+借支扣款+个人社保+个税扣款+公积金扣款+餐费扣款+其他扣款)/上班工时(=工作日上班工时+工作日标准加班工时+工作日超时加班工时+工作日直落工时  +周未上班工时+周未标准加班工时+周未超时加班工时+周未直落工时 +假日上班工时+假日标准加班工时+假日超时加班工时+假日直落工时)
		*/
		if ($CheckTheDay>'2013-04'){//只有当月工资以：6品检、7仓库、8车间三个部门来计算的员工才做统计
			$checkKqSqlB=mysql_query("
				SELECT A.Number,IFNULL(A.Amount+A.Jz+A.Sb+A.RandP+A.Gjj+A.Ct+A.Otherkk,0)/(IFNULL(B.Whours,0)+IFNULL((B.Ghours+B.GOverTime+B.GDropTime)*1.5,0)+IFNULL((B.xHours+B.XOverTime+B.XDropTime)*2,0)+IFNULL((B.FHours+B.FOverTime+B.FDropTime)*3,0)) AS SX
					FROM $DataIn.cwxzsheet A 
					LEFT JOIN $DataIn.kqdata B ON B.Number=A.Number AND B.Month='$CheckTheDay'
					WHERE A.Month='$CheckTheDay' AND A.Kqsign=1 AND A.BranchId>5
				UNION ALL
				SELECT A.Number,IFNULL(A.Amount+A.Jz+A.Sb+A.RandP+A.Gjj+A.Ct+A.Otherkk,0)/( $checkDays*10) AS SX
					FROM $DataIn.cwxzsheet A 
					WHERE A.Month='$CheckTheDay' AND A.KqSign>1 AND A.BranchId>5
				",$link_id);
			}
		else{//2013-04之前的需加上假日加班费
			$checkKqSqlB=mysql_query("
				SELECT X.Number,(IFNULL(X.Amount+X.Jz+X.Sb+X.RandP+X.RandP+X.Gjj+X.Ct+X.Otherkk,0)+IFNULL(H.Amount,0))/(IFNULL(K.Whours,0)+IFNULL(K.Ghours*1.5,0)+IFNULL(H.xHours*2,0)+IFNULL(H.fHours*3,0)) AS SX
				FROM $DataIn.cwxzsheet X 
				LEFT JOIN $DataIn.hdjbsheet H ON H.Number=X.Number AND H.Month='$CheckTheDay'
				LEFT JOIN $DataIn.kqdata K ON K.Number=X.Number AND K.Month='$CheckTheDay'
				WHERE X.Month='$CheckTheDay' AND X.Kqsign=1  ORDER BY X.Number",$link_id);
			}
		if($checkKqRowB=mysql_fetch_array($checkKqSqlB)){
			$XZSign=1;
			do{
				$sNumber=$checkKqRowB["Number"];
				$TempSXSTR="SX".strval($sNumber); 
				$$TempSXSTR=$checkKqRowB["SX"];	//记录每位员工的时薪
				}while($checkKqRowB=mysql_fetch_array($checkKqSqlB));
			}
		break;
	}
//已结付的情况下，将结果存入数据库
?>
<table width="200" border="0" cellpadding="0" cellspacing="0">
  <tr class=''>
    <td colspan="3" align="center" class="A1101" <?php    echo $OnclickSTR?>  style="height:20px"><div class=rmbB><?php    echo $DivTitle?></div></td>
	</tr>
  <tr class=''>
    <td width="60" align="center" class="A0101" style="height:20px">需求支出</td>
    <td width="80" align="center" class="A0101" style="height:20px">预估支出</td>
    <td width="60" align="center" class="A0101" style="height:20px">实际支出</td>
  </tr>
<?php   
	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	$CheckFirstDay=$CheckTheDay."-01";			//当月首日
	$days=date("t",strtotime($CheckFirstDay));	//当月天数
	//读取小组信息
  	$checkSqlA=mysql_query("
	SELECT G.GroupId,G.GroupName,G.GroupLeader 
	FROM $DataIn.staffgroup G
	WHERE 1 AND G.TypeId>0 AND G.Estate=1 ORDER BY G.GroupId",$link_id);
	$m=1;
	$xqAmountSUM=0;
	$ygAmountSUM=0;
	$sjAmountSUM=0;
	if($checkRowA=mysql_fetch_array($checkSqlA)){
		//当前日期的前两天
		$Pre2Days=date("Y-m-d",strtotime ("-2 day"));
		do{//读取小组资料
			$DateNow=$CheckTheDay."-01";					//当月首日初始化
			$Leader=$checkRowA["GroupLeader"];			//小组组长
			$GroupName=$checkRowA["GroupName"];	//小组名称
			$GroupId=$checkRowA["GroupId"];				//小组ID
			$MonthWorkHours=0;									//该班长当月小组总工时数
			$MonthFactPay=0;										//该班长当月工资预估支出
			$xqAmount=0;											//该班长的小组当月生产的需求支出
			$sjAmount=$ygAmount=$sjValue=$ygValue=$TTT=0;//初始化
			
			//**********计算小组的需求、预估和实际支出
			$checkZCSQL=mysql_query("SELECT * FROM $DataIn.sc2_sctj WHERE scMonth='$CheckTheDay' AND GroupId='$GroupId' AND Date<='$Pre2Days' LIMIT 1",$link_id); //存入数据并提取，是为了加快页面速度
			
			
			if($checkZCROW=mysql_fetch_array($checkZCSQL)){//有保存资料时，目前没有资料
				$xqAmount_Before=$checkZCROW["XQZC"];			//之前记录的需求
				$ygAmount_Before=$checkZCROW["YGZC"];			//之前记录的预估
				$sjAmount_Before=$checkZCROW["SJZC"];			//之前记录的实际：目前按默认小姐人员计算
				$OneHourSalaryt=$checkZCROW["OneHourSalaryt"];	//之前记录的预估时薪
				$Day_Before=$checkZCROW["Date"];				//之前记录的最后日期
				}
			else{//没有保存资料时
				//1-		计算需求支出：该小组生产记录中的生产数量的总额：小组对应的加工分类，该分类记录的生产金额
				$xqAmountRow= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*C.Price),0) AS xqAmount FROM $DataIn.sc1_cjtj S LEFT JOIN $DataIn.cg1_stocksheet C ON C.POrderId=S.POrderId LEFT JOIN $DataIn.stuffdata A ON A.StuffId=C.StuffId WHERE 1 AND S.GroupId='$GroupId' $CheckTheDaySTR AND S.Date<NOW() AND A.TypeId=S.TypeId",$link_id));
				$xqAmount=sprintf("%.0f",$xqAmountRow["xqAmount"]); 
				
				// 2-		计算预估和实际支出
			  	$SearchDay=" AND DATE_FORMAT(S.Date,'%Y-%m')='$CheckTheDay' AND S.GroupId='$GroupId' ";//当天小组设置时间在当月的且小组ID等于查询的小组
			  	include "desk_cjrtj_worktime.php" ;
				$ygAmount+=$group1_ygAmount+$group2_ygAmount;	//预估支出
			 	//$sjAmount+=$group1_sjAmount+$group2_sjAmount;		//实际支出
				
				$newSQL = mysql_query("select sum(d.Dx+d.Jbf+d.Gljt+d.Gwjt+d.Jj+d.Jbjj+d.Shbz+d.Zsbz+d.Jtbz+d.Yxbz+d.taxbz-d.Kqkk-d.dkfl) as AllAmount from $DataIn.cwxzsheet d where  d.month='$CheckTheDay' AND d.GroupId='$GroupId'",$link_id);
				if ($newSQLRs = mysql_fetch_array($newSQL)) {
					$sjAmount += $newSQLRs["AllAmount"];
				}
				$newSQL = mysql_query("select sum(d.cAmount) as AllAmount from $DataIn.sbpaysheet d
				
				left join $DataPublic.staffmain m on d.Number=m.Number
				
		  where d.TypeId in (1,2) and d.month='$CheckTheDay' AND m.GroupId='$GroupId'",$link_id);
				if ($newSQLRs = mysql_fetch_array($newSQL)) {
					$sjAmount += $newSQLRs["AllAmount"];
				}
				
				
				
				
				}
			//**********
			
			//纵向累加
			$xqAmountSUM+=$xqAmount;
			$ygAmountSUM+=$ygAmount;
			$sjAmountSUM+=$sjAmount;
			
			//横向累加
			$sumAmount[$GroupId]["xq"]=$sumAmount[$GroupId]["xq"]+$xqAmount;
			$sumAmount[$GroupId]["yg"]=$sumAmount[$GroupId]["yg"]+$ygAmount;
			$sumAmount[$GroupId]["sj"]=$sumAmount[$GroupId]["sj"]+$sjAmount;
			
			//需求预估百分比分析
			$TrendsImg="<div>&nbsp;</div>";
			$ygValue="<div>&nbsp;</div>";
			$sjValue="<div>&nbsp;</div>";
			if($xqAmount>0){//需求值为0时重置
			   if ($ygAmount!=0)
				$ygValue=round(($xqAmount-$ygAmount)/$ygAmount*100);
			  else
			    $ygValue=0;
				//值区域判断
				if($xqAmount-$ygAmount>0){//高估
					$TrendsImg="<div class='redB'>▲</div>";
					$ygValue="<div class='redB'>$ygValue%</div>";
					}
				else{////低估
					$TrendsImg="<div class='greenB'>▼</div>";
					$ygValue=$ygValue*(-1);
					$ygValue="<div class='greenB'>$ygValue%</div>";
					}
				if($sjAmount>0){
					$sjValue=round(($xqAmount-$sjAmount)/$sjAmount*100);
					if($xqAmount-$sjAmount>0){//高估
						$TrendsImg="<div class='redB'>▲</div>";
						$sjValue="<div class='redB'>$sjValue%</div>";
						}
					else{////低估
						$TrendsImg="<div class='greenB'>▼</div>";
						$sjValue=$sjValue*(-1);
						$sjValue="<div class='greenB'>$sjValue%</div>";
						}
					}
				}
			
			/*/写入数据表,加入当月
			if($CheckTheDay='2011-04'){
				$inRecode="INSERT INTO $DataIn.sc1_sczctj (Id,scMonth,GroupId,XQZC,YGZC,SJZC,OneHourSalaryt) VALUES (NULL,'$CheckTheDay','$GroupId','$xqAmount','$ygAmount','$sjAmount','$OneHourSalaryt')";
				$inResult=@mysql_query($inRecode);
				}
			*/
			
			//输出值
			$xqAmount=$xqAmount==0?"&nbsp;":number_format($xqAmount);
			$ygAmount=$ygAmount==0?"&nbsp;":number_format($ygAmount);
			$sjAmount=$sjAmount==0?"&nbsp;":number_format($sjAmount);
			$bgcolor=$m%2==0?"bgcolor='#cccccc'":"";
			echo"<tr $bgcolor align='right'>
			<td class='A0101' style='height:35px;'>$xqAmount$TrendsImg</td>
			<td class='A0101'>$ygAmount<a href='desk_cjrtj_read.php?chooseMonth=$CheckTheDay&GroupId=$GroupId' target='_blank'>[+]</a>$ygValue</td>
			<td class='A0101'>$sjAmount$sjValue</td></tr>";
			$m++;
			}while($checkRowA=mysql_fetch_array($checkSqlA));
		}
	/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($_SESSION["Login_cSign"]!=3){
		//其他辅助人员计算：不管是否考勤
		$SearchDay=" AND DATE_FORMAT(S.Date,'%Y-%m')='$CheckTheDay'  AND S.GroupId>600 AND S.GroupId<802 ";	//辅助组
		$SearchDay3=" AND DATE_FORMAT(S.Date,'%Y-%m')='$CheckTheDay'  AND S.GroupId>'801' ";								//车间组
		include "desk_cjrtj_worktime.php" ;
		$Orther_ygAmount+=$group1_ygAmount+$group2_ygAmount;
		$Orther_sjAmount+=$group1_sjAmount+$group2_sjAmount;
		//纵向统计
		$ygAmountSUM+=$Orther_ygAmount;
		$sjAmountSUM+=$Orther_sjAmount;
		//横向统计
		$sumAmount["other"]["yg"]=$sumAmount["other"]["yg"]+$Orther_ygAmount;
		$sumAmount["other"]["sj"]=$sumAmount["other"]["sj"]+$Orther_sjAmount;
		
		$Orther_ygAmount=$Orther_ygAmount==0?"&nbsp;":number_format($Orther_ygAmount);
		$Orther_sjAmount=$Orther_sjAmount==0?"&nbsp;":number_format($Orther_sjAmount);
				
		$bgcolor=$m%2==0?"bgcolor='#cccccc'":"";
		echo"<tr $bgcolor align='right'>
		<td class='A0101'>&nbsp;</td>
		<td class='A0101'>$Orther_ygAmount<a href='desk_cjrtj_read.php?chooseMonth=$CheckTheDay&GroupId=0' target='_blank'>[+]</a></td>
		<td class='A0101'>$Orther_sjAmount</td></tr>";
		}
	///////其它费用统计:公司支付的社保费用部分+行政费用622,635
	$gspaySql=mysql_fetch_array(mysql_query("SELECT SUM( A.Amount ) AS Amount FROM (
		SELECT SUM(cAmount) AS Amount FROM $DataIn.sbpaysheet WHERE Month='$CheckTheDay' AND (BranchId=6 OR BranchId=8)     
		UNION ALL 
		SELECT SUM(L.Amount) AS Amount  FROM $DataIn.hzqksheet L WHERE DATE_FORMAT(L.Date,'%Y-%m')='$CheckTheDay' AND (L.TypeId=622 OR L.TypeId=635))A
		",$link_id));
	$gspayAmount=sprintf("%.0f",$gspaySql["Amount"]);
	$sjAmountSUM+=$gspayAmount;
	$sumAmount["gs"]["sj"]=$sumAmount["gs"]["sj"]+$gspayAmount;
	$gspayAmount=$gspayAmount==0?"&nbsp;":number_format($gspayAmount);
	$m++;
	$bgcolor=$m%2==0?"bgcolor='#cccccc'":"";
	echo"<tr $bgcolor align='right'>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td class='A0101'>$gspayAmount</td></tr>";
	*/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//小计百分比计算
	$TrendsImgSUM="<div>&nbsp;</div>";
	$ygValueSUM="<div>&nbsp;</div>";
	$sjValueSUM="<div>&nbsp;</div>";
	if($xqAmountSUM>0){//需求值为0时重置
		$ygValueSUM=round(($xqAmountSUM-$ygAmountSUM)/$ygAmountSUM*100);
		//值区域判断
		if($xqAmountSUM-$ygAmountSUM>0){//高估
			$TrendsImgSUM="<div class='redB'>▲</div>";
			$ygValueSUM="<div class='redB'>$ygValueSUM%</div>";
			}
		else{////低估
			$TrendsImgSUM="<div class='greenB'>▼</div>";
			$ygValueSUM=$ygValueSUM*(-1);
			$ygValueSUM="<div class='greenB'>$ygValueSUM%</div>";
			}
		if($sjAmountSUM>0){
			$sjValueSUM=round(($xqAmountSUM-$sjAmountSUM)/$sjAmountSUM*100);
			if($xqAmountSUM-$sjAmountSUM>0){//高估
				$TrendsImgSUM="<div class='redB'>▲</div>";
				$sjValueSUM="<div class='redB'>$sjValueSUM%</div>";
				}
			else{////低估
				$TrendsImgSUM="<div class='greenB'>▼</div>";
				$sjValueSUM=$sjValueSUM*(-1);
				$sjValueSUM="<div class='greenB'>$sjValueSUM%</div>";
				}
			}
		}
	
	$xqAmountSUM=$xqAmountSUM==0?"&nbsp;":number_format($xqAmountSUM);
	$ygAmountSUM=$ygAmountSUM==0?"&nbsp;":number_format($ygAmountSUM);
	$sjAmountSUM=$sjAmountSUM==0?"&nbsp;":number_format($sjAmountSUM);
echo"
<tr align='right'>
    <td class='A0101'>$xqAmountSUM $TrendsImgSUM</td>
    <td class='A0101'>$ygAmountSUM $ygValueSUM</td>
    <td class='A0101'>$sjAmountSUM $sjValueSUM</td>
  </tr>";
  ?>
</table>