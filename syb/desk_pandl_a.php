<?php 
//电信
//代码共享-EWEN 2012-08-19
//汇率参数
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
//检查可用的项目数
$checkItemNum=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums FROM $DataPublic.sys8_pandlsheet A LEFT JOIN $DataPublic.sys8_pandlmain B ON B.Id=A.Mid WHERE A.Estate=1 AND B.Estate=1",$link_id));
$ItemNums=$checkItemNum["Nums"];
//初始化数组
$Value_Y=array();unset($Value_Y);
$Value_W=array();unset($Value_W);
$Value_A=array();unset($Value_A);
$SumType_Y=array();unset($SumType_Y);
$SumType_W=array();unset($SumType_W);
$SumType_A=array();unset($SumType_A);
$SumCol=array();unset($SumCol);
$SumOut=array();unset($SumOut);
$DataCheck1A=array();unset($DataCheck1A);
$DataCheck1B=array();unset($DataCheck1B);
$DataCheck2A=array();unset($DataCheck2A);
$DataCheck2B=array();unset($DataCheck2B);
$DataCheck3A=array();unset($DataCheck3A);
$DataCheck3B=array();unset($DataCheck3B);
$DataCheck4A=array();unset($DataCheck4A);
$DataCheck4B=array();unset($DataCheck4B);
$DataCheck0A=array();unset($DataCheck0A);
$DataCheck0B=array();unset($DataCheck0B);
$Subscript=0;			//数组起始下标
$NowMonth=$checkMonth==""?date("Y-m-01"):$checkMonth."-01";	//起始月份：默认为当前月

$MonthCount=$MonthCount==""?6:$MonthCount;										//要显示的月份数：默认为6个月
$MonthCount=$checkMonth==""?$MonthCount:1;										//如果已指定月份，则要显示的月份数为1
for($Subscript=0;$Subscript<=$MonthCount;$Subscript++){
	if($Subscript==0){
		$TempPayDatetj="";
		$TempDatetj="";
		$TempMonthtj="";
		$TempSendDatetj="";
		$TempqkDatetj="";
		$TempDateTax="";
		$TempDeliveryDate="";
		}
	else{
		$StepM=$Subscript-1;
		$CheckTime=date("Y-m",strtotime("$NowMonth -$StepM month"));
		$TempPayDatetj=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$CheckTime'";
		$TempDatetj=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
		$TempMonthtj="  AND M.Month='$CheckTime'";
		$TempSendDatetj=" AND DATE_FORMAT(M.SendDate,'%Y-%m')='$CheckTime'";
		$TempqkDatetj=" AND DATE_FORMAT(M.qkDate,'%Y-%m')='$CheckTime'";
		$TempDateTax=" AND DATE_FORMAT(M.TaxDate,'%Y-%m')='$CheckTime'";
        $TempDateModelf=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
		$TempDeliveryDate=" AND DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$CheckTime'";
		}
	include "desk_pandl_data.php";
	}
?>
<table id="DataList" border="0" cellspacing="0" >
  <tr bgcolor="#999999" align="right">
   	<td width="80" class="A1111" style="height:25px; "  align="center" rowspan="2">项目</td>
    <td width="150" class="A1101"  align="center" rowspan="2">明细</td>
    <td width="90" class="A1101" align="right" rowspan="2">已结付金额</td>
    <td width="90"class="A1101" align="right" rowspan="2">未结付金额</td>
    <td width="90" class="A1101" align="right" rowspan="2">小计</td>
   <td width="100" class="A1101" align="center" colspan="2">百分比</td>
    <?php 
//月份输出
$Marray=array();
unset($Marray);
//主要参数：根据实际表格需求手动设定
$unMonthTitleLastCol=5;								//标题非月份最后列序号
$MonthStartCol=$unMonthTitleLastCol+1;		//起始月的列序号
$unMonthDataLastCol=6;								//数据行非月份最后列序号
$UnionCol=3;												//月份合并的列数

 //隐藏行需合并的列数：此参数是
 $colspanHide=$unMonthDataLastCol+$MonthCount*$UnionCol;//即总列数
 //隐藏列处理
 $HideColCode1= $HideColCode2=$HideColCode3="";
for($i=0;$i<$MonthCount;$i++){
	$CheckTime=date("Y-m",strtotime("$NowMonth -$i month"));
	$Marray[$i+1]=$CheckTime;
	$CheckNum=date("Ym",strtotime("$NowMonth -$i month"));
	echo "<td style=\"CURSOR: pointer;width:80px;background:#6cf\" class=\"A1101\" rowspan=\"2\" colspan=\"1\" onclick=\"hideOrShowCol($MonthStartCol)\" title='显示或隐藏月份百分比数据列' onmouseover=\"this.style.background='#6C6'; \" onmouseout =\"this.style.background='#6cf'; \">$CheckTime</td>";
	$MonthStartCol++;
	}
	?>
  </tr>
   <tr bgcolor="#999999" align="right">
    <td width="50" align="right" class="A0101">分类</td>
    <td width="50" align="right" class="A0101">总支出</td>
  </tr>
  <?php 
  //计算支出的总额
  
  //数据输出
  $rateResult = mysql_query("SELECT * FROM $DataPublic.sys8_pandlmain WHERE Estate=1 ORDER BY SortId ",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	$m=0;//全部项目计数，从0开始
	$T=0;//分类和数组下标
	do{
		$flag=0;
		$ItemMid=$T+1;
		$Id=$rateRow["Id"];							//主项目ID
		$ItemName=$ItemMid." ".$rateRow["ItemName"];//主项目名称
		$ColorCode=$rateRow["ColorCode"];//行底色
		$checkSubSql=mysql_query("SELECT * FROM  $DataPublic.sys8_pandlsheet WHERE Mid='$Id' AND Estate=1 ORDER BY SortId",$link_id);
		if($checkSubRow=mysql_fetch_array($checkSubSql)){
			$j=1;//分类内子项目计数，从1开始计
			
			do{//每一子项目数据处理
				if($Value_A[0][$m]!=0){
			    $SubItemId=$checkSubRow["Id"];						//子项目ID
				$SubItemName=$j." ".$checkSubRow["ItemName"];	//子项目名称
				$SubItemRemark=$checkSubRow["Remark"];		//子项目备注
				$AjaxView=$checkSubRow["AjaxView"];			//是否显示子项目明细的标志
				$TitleStr=$SubItemRemark!=""?"title='$SubItemRemark'":"";//鼠标移至行时，显示备注内容
				$Sign=$checkSubRow["Sign"]=="1"?"<span style=\"color:#ff9933\">★</span>":"";//ff9933
				$Estate=$checkSubRow["Estate"];
				//$EstateSTR=$Estate==0?"style='display:none';":"";
				//底色处理和非数据列处理
				$BgColor=$j%2==1?$ColorCode:"#FFFFFF";
				
				
				if($Estate==1)
					$flag++;
				
				if($flag==1)
					echo"<tr align=\"right\" bgcolor=\"$BgColor\" $TitleStr onmouseover=\"this.style.background='#FF6633'; \" onmouseout =\"this.style.background='$BgColor'; \" $EstateSTR><td class=\"A0011\" height=\"18\" align=\"left\" bgcolor=\"$ColorCode\">$ItemName&nbsp;&nbsp;&nbsp;$Sign</td>";	
				else
					echo"<tr align=\"right\" bgcolor=\"$BgColor\" $TitleStr onmouseover=\"this.style.background='#FF6633'; \" onmouseout =\"this.style.background='$BgColor'; \" $EstateSTR><td class=\"A0011\"  height=\"18\" bgcolor=\"$ColorCode\">&nbsp;$Sign</td>";
				
				$RateInAll=$RateInType="";
				if($SumType_A[0][$T]>0 && $Value_A[0][$m]>0){//项目和需大于0,且项目金额大于0
					$TempRateInType=sprintf("%.2f",$Value_A[0][$m]/$SumType_A[0][$T]*100);
					if($TempRateInType<0.01){
						$RateInType="< 0.01%";
						if($T>0){$RateInAll="< 0.01%";}
						}
					else{
						$RateInType=$TempRateInType."%";
						if($T>0){
							//相对于总支出的百分比
							$TempRateInAll=sprintf("%.2f",$Value_A[0][$m]/$SumOut_A[0]*100);
							if($TempRateInAll<0.01){
								$RateInAll="< 0.01%";
								}
							else{
								$RateInAll=$TempRateInAll."%";
								}
							}
						}
					}
				$ValueTemp_Y=$Value_Y[0][$m]==0?"&nbsp;":($Value_Y[0][$m]>0?number_format($Value_Y[0][$m]):"<span class='redN'>(".number_format(-$Value_Y[0][$m]).")</span>");
				$ValueTemp_W=$Value_W[0][$m]==0?"&nbsp;":($Value_W[0][$m]>0?number_format($Value_W[0][$m]):"<span class='redN'>(".number_format(-$Value_W[0][$m]).")</span>");
				$ValueTemp_A=$Value_A[0][$m]==0?"&nbsp;":($Value_A[0][$m]>0?number_format($Value_A[0][$m]):"<span class='redN'>(".number_format(-$Value_A[0][$m]).")</span>");
				//是否可以点击查看明细
				if($AjaxView==1){
					echo"<td class=\"A0001\" align=\"left\" >$SubItemName</td><td class=\"A0001\" align=\"right\" style=\"CURSOR: pointer\" onClick=ShowSheet('TrShow$m','DivShow$m','','Y','$SubItemId')>$ValueTemp_Y</td><td class=\"A0001\" style=\"CURSOR: pointer\" onClick=ShowSheet('TrShow$m','DivShow$m','','W','$SubItemId')>$ValueTemp_W</td><td class=\"A0001\" style=\"CURSOR: pointer\" onClick=ShowSheet('TrShow$m','DivShow$m','','A','$SubItemId')>$ValueTemp_A</td>";
					}
				else{
					echo"<td class=\"A0001\" align=\"left\" >$SubItemName</td><td class=\"A0001\" align=\"right\">$ValueTemp_Y</td><td class=\"A0001\">$ValueTemp_W</td><td class=\"A0001\">$ValueTemp_A</td>";
					}
				echo"<td class=\"A0001\">$RateInType</td><td class=\"A0001\">$RateInAll</td>";
				for($i=1;$i<=$MonthCount;$i++){//按月份数输出月份数据
					//月分百分比计算
					////////////////////////
					$RateInAll=$RateInType="";
					if($SumType_A[$i][$T]>0 && $Value_A[$i][$m]>0){//项目和需大于0,且项目金额大于0
					$TempRateInType=sprintf("%.2f",$Value_A[$i][$m]/$SumType_A[$i][$T]*100);
					if($TempRateInType<0.01){
						$RateInType="< 0.01%";
						if($T>0){$RateInAll="< 0.01%";}
						}
					else{
						$RateInType=$TempRateInType."%";
						if($T>0){
							//相对于总支出的百分比
							$TempRateInAll=sprintf("%.2f",$Value_A[$i][$m]/$SumOut_A[$i]*100);
							if($TempRateInAll<0.01){
								$RateInAll="< 0.01%";
								}
							else{
								$RateInAll=$TempRateInAll."%";
								}
							}
						}
					}
					////////////////////////
					$ValueTemp=$Value_A[$i][$m]==0?"&nbsp;":($Value_A[$i][$m]>0?number_format($Value_A[$i][$m]):"<span class='redN'>(".number_format(-$Value_A[$i][$m]).")</span>");
					echo"<td class=\"A0001\" style=\"CURSOR: pointer;width:80px\" onClick=ShowSheet('TrShow$m','DivShow$m','$Marray[$i]','A','$SubItemId')>$ValueTemp</td>
					<td class=\"A0001\"  style='display:none;width:50px;background:#6cf'>$RateInType</td>
					<td class=\"A0001\"  style='display:none;width:50px;background:#6cf'>$RateInAll</td>";
					}
				echo"</tr><tr id='TrShow$m' style='display:none;background:#000;'><td colspan='$colspanHide' style=\"height:500px\" valign=\"top\"><div id='DivShow$m' style='display:none;'></div></td></tr>";//隐藏的行
				$j++;		//分类内子项目计数，从1开始计
				}
				$m++;	//全部项目计数，从0开始
				}while($checkSubRow=mysql_fetch_array($checkSubSql));
			}
		//支出分类分百比处理
		$RateT="&nbsp;";
		if($SumType_A[0][$T]>0 && $T>0){
				//计算分类所占支出百分比=分类和/总支出
				$RateTypeTemp=sprintf("%.2f",$SumType_A[0][$T]/$SumOut_A[0]*100);
				if($RateTypeTemp<0.01){
					$RateT="< 0.01%";
					}
				else{
					$RateT=$RateTypeTemp."%";
					}
				}
		$SubTotal_Y=$SumType_Y[0][$T]==0?"&nbsp;":($SumType_Y[0][$T]>0?number_format($SumType_Y[0][$T]):"<span class='redN'>(".number_format(-$SumType_Y[0][$T]).")</span>");
		$SubTotal_W=$SumType_W[0][$T]==0?"&nbsp;":($SumType_W[0][$T]>0?number_format($SumType_W[0][$T]):"<span class='redN'>(".number_format(-$SumType_W[0][$T]).")</span>");
		$SubTotal_A=$SumType_A[0][$T]==0?"&nbsp;":($SumType_A[0][$T]>0?number_format($SumType_A[0][$T]):"<span class='redN'>(".number_format(-$SumType_A[0][$T]).")</span>");
		echo"<tr align=\"right\" bgcolor=\"#6C6\" onmouseover=\"this.style.background='#6C6'; \" onmouseout =\"this.style.background='#6C6'; \"><td class=\"A0111\" >小计</td><td class=\"A0101\")>&nbsp;</td>
		<td class=\"A0101\">$SubTotal_Y</td>
		<td class=\"A0101\">$SubTotal_W</td>
		<td class=\"A0101\">$SubTotal_A</td>
		<td class=\"A0101\">&nbsp;</td>
		<td class=\"A0101\">$RateT</td>";
		for($i=1;$i<=$MonthCount;$i++){
			$RateT="&nbsp;";
			if($SumType_A[$i][$T]>0 && $T>0){
				//计算分类所占支出百分比=分类和/总支出
				$RateTypeTemp=sprintf("%.2f",$SumType_A[$i][$T]/$SumOut_A[$i]*100);
				if($RateTypeTemp<0.01){
					$RateT="< 0.01%";
					}
				else{
					$RateT=$RateTypeTemp."%";
					}
				}
			$SubTotal_M=$SumType_A[$i][$T]==0?"&nbsp;":($SumType_A[$i][$T]>0?number_format($SumType_A[$i][$T]):"<span class='redN'>(".number_format($SumType_A[$i][$T]).")</sapn>");
			echo"<td class=\"A0101\">$SubTotal_M</td>";	//月份小计
			echo"<td class=\"A0101\"  style='display:none;width:50px;background:#6C6'> &nbsp;</td>";//月份分类比
			echo"<td class=\"A0101\"  style='display:none;width:50px;background:#6C6'>$RateT</td>";//月份支出比
			}
		echo "</tr><tr id='TrShowT$T' style='display:none;background:#000;'><td colspan='$colspanHide' style=\"height:500px\" valign=\"top\"></td></tr>";
		$T++;//大分类累加
		}while($rateRow = mysql_fetch_array($rateResult));
	}

//总计
$SumY0STR=$SumCol_Y[0]-$PreAmount>0?"<span class=\"greenB\">".number_format($SumCol_Y[0]-$PreAmount)."</span>":"<span class=\"redB\">(".number_format(-($SumCol_Y[0]-$PreAmount)).")</span>";
$SumW0STR=$SumCol_W[0]>0?"<span class=\"greenB\">".number_format($SumCol_W[0])."</span>":"<span class=\"redB\">(".number_format(-$SumCol_W[0]).")</span>";
$SumA0STR=$SumCol_A[0]-$PreAmount>0?"<span class=\"greenB\">".number_format($SumCol_A[0]-$PreAmount)."</span>":"<span class=\"redB\">(".number_format(-($SumCol_A[0]-$PreAmount)).")</span>";
echo"<tr align=\"right\" bgcolor=\"#fff\"><td class=\"A0111\" align=\"right\" style=\"height:35px;\">损益表统计</td><td class=\"A0101\")>&nbsp;</td>
		<td class=\"A0101\">$SumY0STR</td>
		<td class=\"A0101\">$SumW0STR</td>
		<td class=\"A0101\">$SumA0STR</td>
		<td class=\"A0101\">&nbsp;</td>
		<td class=\"A0101\">&nbsp;</td>";
		$SumCol_ALL=0;
for($i=1;$i<=$MonthCount;$i++){
	$SumMonthStr=$SumCol_A[$i]>0?"<span class=\"greenB\">".number_format($SumCol_A[$i])."</span>":"<span class=\"redB\">(".number_format(-$SumCol_A[$i]).")</span>";
	$SumCol_ALL+=$SumCol_A[$i];
	echo"<td class=\"A0101\">$SumMonthStr</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";
//桌面项目
include"../desk/subtask/subtask-120.php";//现金结存
include"../desk/subtask/subtask-121.php";//审核通过未结付
include"../desk/subtask/subtask-122.php";//审核通过未结付
$noJF=$GatheringSUM-$noPayRMB;
//桌面值
$deskJY=$jy-$SumCol_A[1]; 
$deskJY=$deskJY>0?"<span class=\"greenB\">".number_format($deskJY)."</span>":"<span class=\"redB\">(".number_format(-$deskJY).")</span>";
//实际与预估报表
$SumTotal=$SumTotal>0?"<span class=\"greenB\">".number_format($SumTotal)."</span>":"<span class=\"redB\">(".number_format(-$SumTotal).")</span>";
$noJF=$noJF>0?"<span class=\"greenB\">".number_format($noJF)."</span>":"<span class=\"redB\">(".number_format(-$noJF).")</span>";
$jy=$jy>0?"<span class=\"greenB\">".number_format($jy)."</span>":"<span class=\"redB\">(".number_format(-$jy).")</span>";

echo"<tr align=\"right\" bgcolor=\"#FFFFFF\" >
<td class=\"A0111\" align=\"right\" style=\"height:30px\">桌面统计<br>$deskJY</td>
<td class=\"A0101\">&nbsp;</td>
<td class=\"A0101\">$SumTotal</td>
<td class=\"A0101\">$noJF</td>
<td class=\"A0101\">$jy</td>
<td class=\"A0101\">&nbsp;</td>
<td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	echo"<td class=\"A0101\">&nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";
//实际与预估报表
$BGC="#ffcccc";
$BGC2="#ffffff";
$OverC="#6cf";//6C6   /99ff99
$RowH="20px";

//行政－实际
		echo"<tr align=\"right\" bgcolor=\"$BGC\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC'; \"><td class=\"A0011\" align=\"right\" style=\"height:$RowH;background:$BGC\"><span style=\"color:#ff9933\">★</span>行政</td><td class=\"A0101\")>实际</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
		for($i=1;$i<=$MonthCount;$i++){
			echo"<td class=\"A0101\">".number_format($DataCheck1A[$i])."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
			}
		echo"</tr>";
//行政－预估
		echo"<tr align=\"right\" bgcolor=\"$BGC2\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC2'; \"><td class=\"A0111\" align=\"right\" style=\"height:$RowH;background:$BGC\">&nbsp;</td><td class=\"A0101\")>预估</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
		for($i=1;$i<=$MonthCount;$i++){
			//比较
			$TempA=$DataCheck1A[$i];
			$TempB=$DataCheck1B[$i];
			if($TempA>$TempB){//实际大于需求，需求低估
				$TempB="<span class=\"XQDG\">▼".number_format($TempB)."</span>";
				}
			else{
				if($TempA<$TempB){//实际少于需求，需求高估
					$TempB="<span class=\"XQGG\">▲".number_format($TempB)."</span>";
					}
				else{
					$TempB="<strong><em>".number_format($TempB)."</em></strong>";
					}
				}
			echo"<td class=\"A0101\">".$TempB."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
			}
		echo"</tr>";
//生产人工－实际
		echo"<tr align=\"right\" bgcolor=\"$BGC\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC'; \">
		<td class=\"A0011\" align=\"right\" style=\"height:$RowH;background:$BGC\">生产人工</td><td class=\"A0101\")>实际</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
		for($i=1;$i<=$MonthCount;$i++){
			echo"<td class=\"A0101\">".number_format($DataCheck2A[$i])."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
			}
		echo"</tr>";
//生产人工－预估
		echo"<tr align=\"right\" bgcolor=\"$BGC2\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC2'; \">
		<td class=\"A0111\" align=\"right\" style=\"height:$RowH;background:$BGC\">&nbsp;</td><td class=\"A0101\")>预估</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
		for($i=1;$i<=$MonthCount;$i++){
			//比较
			$TempA=$DataCheck2A[$i];
			$TempB=$DataCheck2B[$i];
			if($TempA>$TempB){//实际大于需求，需求低估
				$TempB="<span class=\"XQDG\">▼".number_format($TempB)."</span>";
				}
			else{
				if($TempA<$TempB){//实际少于需求，需求高估
					$TempB="<span class=\"XQGG\">▲".number_format($TempB)."</span>";
					}
				else{
					$TempB="<strong><em>".number_format($TempB)."</em></strong>";
					}
				}
		
			echo"<td class=\"A0101\">".$TempB."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
			}
		echo"</tr>";
/*
echo"<tr align=\"right\" bgcolor=\"$BGC\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC'; \">
<td class=\"A0011\" align=\"right\" style=\"height:$RowH;background:$BGC\">辅料</td><td class=\"A0101\")>实际</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	echo"<td class=\"A0101\">".number_format($DataCheck3A[$i])."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";

echo"<tr align=\"right\" bgcolor=\"$BGC2\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC2'; \">
<td class=\"A0111\" align=\"right\" style=\"height:$RowH;background:$BGC\">&nbsp;</td><td class=\"A0101\")>预估</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	//比较
	$TempA=$DataCheck3A[$i];
	$TempB=$DataCheck3B[$i];
	if($TempA>$TempB){//实际大于需求，需求低估
		$TempB="<span class=\"XQDG\">▼".number_format($TempB)."</span>";
		}
	else{
		if($TempA<$TempB){//实际少于需求，需求高估
			$TempB="<span class=\"XQGG\">▲".number_format($TempB)."</span>";
			}
		else{
			$TempB="<strong><em>".number_format($TempB)."</em></strong>";
			}
		}

	echo"<td class=\"A0101\">".$TempB."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}

echo"</tr>";
*/
echo"<tr align=\"right\" bgcolor=\"$BGC\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC'; \">
<td class=\"A0011\" align=\"right\" style=\"height:$RowH;background:$BGC\">FOB</td><td class=\"A0101\")>实际</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	echo"<td class=\"A0101\">".number_format($DataCheck4A[$i])."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";
echo"<tr align=\"right\" bgcolor=\"$BGC2\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC2'; \">
<td class=\"A0111\" align=\"right\" style=\"height:$RowH;background:$BGC\">&nbsp;</td><td class=\"A0101\")>预估</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	//比较
	$TempA=$DataCheck4A[$i];
	$TempB=$DataCheck4B[$i];
	if($TempA>$TempB){//实际大于需求，需求低估
		$TempB="<span class=\"XQDG\">▼".number_format($TempB)."</span>";
		}
	else{
		if($TempA<$TempB){//实际少于需求，需求高估
			$TempB="<span class=\"XQGG\">▲".number_format($TempB)."</span>";
			}
		else{
			$TempB="<strong><em>".number_format($TempB)."</em></strong>";
			}
		}

	echo"<td class=\"A0101\">".$TempB."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}

echo"</tr>";

//仓储
echo"<tr align=\"right\" bgcolor=\"$BGC\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC'; \">
<td class=\"A0011\" align=\"right\" style=\"height:$RowH;background:$BGC\">仓储摊提</td><td class=\"A0101\")>实际</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	echo"<td class=\"A0101\">&nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";
echo"<tr align=\"right\" bgcolor=\"$BGC2\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC2'; \">
<td class=\"A0111\" align=\"right\" style=\"height:$RowH;background:$BGC\">&nbsp;</td><td class=\"A0101\")>预估</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	$TempB=$DataCheck5B[$i];
	$TempB="<strong><em>".number_format($TempB)."</em></strong>";
	echo"<td class=\"A0101\">".$TempB."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";



//合计
echo"<tr align=\"right\" bgcolor=\"$BGC\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC'; \">
<td class=\"A0011\" align=\"right\" style=\"height:$RowH;background:$BGC\">合计</td><td class=\"A0101\")>实际</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	$DataCheckALLA=$DataCheck1A[$i]+$DataCheck2A[$i]+$DataCheck3A[$i]+$DataCheck4A[$i];
	echo"<td class=\"A0101\">".number_format($DataCheckALLA)."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}
echo"</tr>";
echo"<tr align=\"right\" bgcolor=\"$BGC2\" onmouseover=\"this.style.background='$OverC'; \" onmouseout =\"this.style.background='$BGC2'; \">
<td class=\"A0111\" align=\"right\" style=\"height:$RowH;background:$BGC\">&nbsp;</td><td class=\"A0101\")>预估</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
for($i=1;$i<=$MonthCount;$i++){
	//比较
	$TempA=$DataCheck1A[$i]+$DataCheck2A[$i]+$DataCheck3A[$i]+$DataCheck4A[$i];
	$TempB=$DataCheck1B[$i]+$DataCheck2B[$i]+$DataCheck3B[$i]+$DataCheck4B[$i]+$DataCheck5B[$i];
	if($TempA>$TempB){//实际大于需求，需求低估
		$TempB="<span class=\"XQDG\">▼".number_format($TempB)."</span>";
		}
	else{
		if($TempA<$TempB){//实际少于需求，需求高估
			$TempB="<span class=\"XQGG\">▲".number_format($TempB)."</span>";
			}
		else{
			$TempB="<strong><em>".number_format($TempB)."</em></strong>";
			}
		}

	echo"<td class=\"A0101\">".$TempB."</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td><td class=\"A0101\"  style='display:none;width:50px;background:#6cf'> &nbsp;</td>";
	}

echo"</tr>";

  ?>
  <tr bgcolor="#999999" align="right">
   	<td class="A0111" style="height:25px; "  align="center" rowspan="2">项目</td>
    <td class="A0101"  align="center" rowspan="2">明细</td>
    <td  class="A0101" align="right" rowspan="2">已结付金额</td>
    <td class="A0101" align="right" rowspan="2">未结付金额</td>
    <td class="A0101" align="right" rowspan="2">小计</td>
    <td align="right" class="A0101">分类</td>
    <td align="right" class="A0101">总支出</td>
    <?php 
	$MonthStartCol=$unMonthTitleLastCol+1;		//起始月的列序号
	for($i=0;$i<$MonthCount;$i++){
		$CheckTime=date("Y-m",strtotime("$NowMonth -$i month"));
		echo "<td style=\"CURSOR: pointer;width:80px;background:#6cf\" class=\"A0101\"  rowspan=\"2\" colspan=\"1\" onclick=\"hideOrShowCol($MonthStartCol)\" title='显示或隐藏月份百分比数据列' onmouseover=\"this.style.background='#6C6'; \" onmouseout =\"this.style.background='#6cf'; \">$CheckTime</td>";
		$MonthStartCol++;
		}
	?>
  </tr>
  <tr bgcolor="#999999" ><td  class="A0101" align="center" colspan="2">百分比</td></tr>
</table>

<p style='color:#eeeeee;'><? echo number_format($SumCol_ALL)?><p>
<script language="JavaScript" type="text/JavaScript">
function ShowSheet(TrId,DivId,Month,DataT,ItemId){//隐藏行ID,隐藏行DIV,全部数据还是月份数据,项目ID
 ShowDiv=eval(DivId);
 ShowTr=eval(TrId);
 ShowTr.style.display=(ShowTr.style.display=="none")?"":"none";
 ShowDiv.style.display=(ShowDiv.style.display=="none")?"":"none";
var url="desk_syb_ajax.php?Month="+Month+"&DataT="+DataT+"&ItemId="+ItemId;
 var ajax=InitAjax();
 ajax.open("GET",url,true);
 ajax.onreadystatechange =function(){
 　　if(ajax.readyState==4 && ajax.status ==200 && ajax.responseText!=""){
 　　　 var BackData=ajax.responseText;
   ShowDiv.innerHTML=BackData;
   }
  }
 ajax.send(null); 
 }
 //计算公式：（月份列序号－非月份最后列序号）＊合并列数＋数据行非月份最后列序号
 //参数
 var UnionCol=<?php  echo $UnionCol?>;											//合并的列数
 var StartRow=2;																//标题行数，也就是要隐藏、显示的数据起始行
 var unMonthTitleLastCol=<?php  echo $unMonthTitleLastCol?>;			//标题非月份最后列序号
 var unMonthDataLastCol=<?php  echo $unMonthDataLastCol?>;			//数据行非月份最后列序号
 var rowsLen = DataList.rows.length;		//数据表格的总行数
 //显示或隐藏列
 function hideOrShowCol(MonthCol){//MonthCol 标题行中月份所在列序号
	//隐藏列计算
	var hORsLastCol=(MonthCol-unMonthTitleLastCol)*UnionCol+unMonthDataLastCol;//计算出来的为隐藏的最后一列，如：7、8为月份的隐藏列，则此计算结果为8
	//更改月份列的单元格
	DataList.rows[0].cells[MonthCol].colSpan=(DataList.rows[StartRow].cells[hORsLastCol].style.display=="none")?UnionCol:1;			//更改顶部标题行中，月份单元格的合并列数
　	//底部对应的月份单元格=数据行非月份最后列序号+ 标题行中月份所在列序号-标题非月份最后列序号
	var BottomMonthCol=unMonthDataLastCol+MonthCol-unMonthTitleLastCol;
	DataList.rows[rowsLen-StartRow].cells[BottomMonthCol].colSpan=(DataList.rows[StartRow].cells[hORsLastCol].style.display=="none")?UnionCol:1;		//更改底部标题行中，月份单元格的合并列数
	for (var i=2; i<rowsLen-11; i=i+2){//首尾两行不处理，单行不处理（隐藏行），即需要隔行处理
		for(var j=0;j<UnionCol-1;j++){//同一行中要隐藏的列,列序号递减
			DataList.rows[i].cells[hORsLastCol-j].style.display=(DataList.rows[i].cells[hORsLastCol-j].style.display=="none")?"":"none";
			}
		}
	//总计后的处理
	for (var i=rowsLen-11; i<rowsLen-2; i++){//首尾两行不处理，单行不处理（隐藏行），即需要隔行处理
		for(var j=0;j<UnionCol-1;j++){//同一行中要隐藏的列,列序号递减
			DataList.rows[i].cells[hORsLastCol-j].style.display=(DataList.rows[i].cells[hORsLastCol-j].style.display=="none")?"":"none";
			}
		}
	}
</script>
