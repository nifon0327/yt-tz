<?php 
		//$Sum_KGTime=$Sum_GTime-$Sum_WorkTime-$Sum_SJTime-$Sum_BJTime-$Sum_BXTime-$Sum_WXJTime-$Sum_QQTime-$Sum_WXTime;
		echo"<tr height='25' bgcolor='#FFCC99'><td colspan='4' class='A0111'> 合    计</td>";
		echo"<td class='A0101' align='center'><input name='Dhours' type='hidden' id='Dhours' value='$Sum_GTime'>$Sum_GTime</td>";//应到总工时
		echo"<td class='A0101' align='center'><input name='Whours' type='hidden' id='Whours' value='$Sum_WorkTime'>$Sum_WorkTime</td>";//实到总工时
		echo"<td class='A0101' align='center'><input name='Ghours' type='hidden' id='Ghours' value='$Sum_GJTime'>$Sum_GJTime</td>";//加点总工时
		echo"<td class='A0101' align='center'><input name='Xhours' type='hidden' id='Xhours' value='$Sum_XJTime'>$Sum_XJTime</td>";//加班总工时
		echo"<td class='A0101' align='center'><input name='Fhours' type='hidden' id='Fhours' value='$Sum_FJTime'>$Sum_FJTime</td>";//法定假日加班总工时
		echo"<td class='A0101' align='center'><input name='InLates' type='hidden' id='InLates' value='$Sum_InLates'>$Sum_InLates</td>";//迟到总次数
		echo"<td class='A0101' align='center'><input name='OutEarlys' type='hidden' id='OutEarlys' value='$Sum_OutEarlys'>$Sum_OutEarlys</td>";//早退总次数
		echo"<td class='A0101' align='center'><input name='SJhours' type='hidden' id='SJhours' value='$Sum_SJTime'>$Sum_SJTime</td>";//事假总工时
		echo"<td class='A0101' align='center'><input name='BJhours' type='hidden' id='BJhours' value='$Sum_BJTime'>$Sum_BJTime</td>";//病假总工时
		echo"<td class='A0101' align='center'><input name='BXhours' type='hidden' id='BXhours' value='$Sum_BXTime'>$Sum_BXTime</td>";//补休总工时
		echo"<td class='A0101' align='center'><input name='WXJhours' type='hidden' id='WXJhours' value='$Sum_WXJTime'>$Sum_WXJTime</td>";//无薪假总工时
		echo"<td class='A0101' align='center'><input name='LJhours' type='hidden' id='LJhours' value='$Sum_LJTime'>$Sum_LJTime</td>";//年假总工时
		echo"<td class='A0101' align='center'><input name='QQhours' type='hidden' id='QQhours' value='$Sum_QQTime'>$Sum_QQTime</td>";//缺勤总工时
		echo"<td class='A0101' align='center'><input name='WXhours' type='hidden' id='WXhours' value='$Sum_WXTime'>$Sum_WXTime</td>";
		echo"<td class='A0101' align='center'><input name='KGhours' type='hidden' id='KGhours' value='$Sum_KGTime'>$Sum_KGTime</td>";//旷工总工时
		echo"<td class='A0101' align='center'><input name='BKhours' type='hidden' id='BKhours' value='$Sum_BKTime'>$Sum_BKTime</td>";//因迟到早退等原因被扣总工时
		echo"<td class='A0101' align='center'><input name='YBs' type='hidden' id='YBs' value='$Sum_YBs'>$Sum_YBs</td></tr>";//夜班次数
		//加班费
$kq_Result = mysql_query("SELECT * FROM cwxzsheet  WHERE Month='$defaultMonth' and Number=$DefaultNumber",$link_id);
if($kq_Row=mysql_fetch_array($kq_Result)){
	$DX=$kq_Row["DX"];
	$Shbz=$kq_Row["Shbz"];
	$Zsbz=$kq_Row["Zsbz"];
	$Gwjt=$kq_Row["Gwjt"];
	$Zgjt=$kq_Row["Zgjt"];
	}
	$oneHours=sprintf("%.2f",$DX/174);
	$jbf_GJTime=intval($Sum_GJTime*$oneHours*1.5);
	$jbf_XJTime=intval($Sum_XJTime*$oneHours*2);
	$jbf_FJTime=intval($Sum_FJTime*$oneHours*3);
	$jbf_InLates=$Sum_InLates*5;
	$jbf_OutEarlys=$Sum_OutEarlys*5;
	//事假分类，如果事假超32小时，需扣补助
	if($Sum_SJTime>32){
		if($Sum_WorkTime==0){
			$jbf_SJTime=intval($Sum_SJTime*(sprintf("%.2f",$DX/$Sum_GTime)+($Shbz+$Zsbz+$Gwjt+$Zgjt)/$Sum_GTime));
			}
		else{
			$jbf_SJTime=intval($Sum_SJTime*($oneHours+($Shbz+$Zsbz+$Gwjt+$Zgjt)/174));
			}
		}
	else{
		$jbf_SJTime=intval($Sum_SJTime*$oneHours);
		}
	$jbf_BJTime=intval($Sum_BJTime*$oneHours*0.4);
	$jbf_BXTime=intval($Sum_BXTime*$oneHours*2);
	$jbf_WXJTime=intval($Sum_WXJTime*$oneHours);
	$jbf_QQTime=intval($Sum_QQTime*$oneHours);
	//$jbf_WXTime=intval($Sum_WXTime*$oneHours);
	$jbf_KGTime=intval($Sum_KGTime*($oneHours+($Shbz+$Zsbz+$Gwjt+$Zgjt)/174));
	$jbf_BKTime=intval($Sum_BKTime*$oneHours);
	$jbf_YBs=$Sum_YBs*5;
	$JBF=$jbf_GJTime+$jbf_XJTime+$jbf_FJTime-$jbf_SJTime-$jbf_BJTime-$jbf_BXTime-$jbf_WXJTime-$jbf_QQTime-$jbf_KGTime-$jbf_BKTime;
	echo"<tr height='25' bgcolor='#FFCC99'><td colspan='4' class='A0111'> $Sum_FJtime 加班费(当前时薪:$oneHours)：$JBF</td>";
	echo"<td class='A0101' align='center'>&nbsp;</td>";//应到总工时
	echo"<td class='A0101' align='center'>&nbsp;</td>";//实到总工时
	echo"<td class='A0101' align='center'>$jbf_GJTime</td>";//加点总工时
	echo"<td class='A0101' align='center'>$jbf_XJTime</td>";//加班总工时
	echo"<td class='A0101' align='center'>$jbf_FJTime</td>";//法定假日加班总工时
	echo"<td class='A0101' align='center'>&nbsp;</td>";//迟到总次数
	echo"<td class='A0101' align='center'>&nbsp;</td>";//早退总次数
	echo"<td class='A0101' align='center'>$jbf_SJTime</td>";//事假总工时
	echo"<td class='A0101' align='center'>$jbf_BJTime</td>";//病假总工时
	echo"<td class='A0101' align='center'>$jbf_BXTime</td>";//补休
	echo"<td class='A0101' align='center'>$jbf_WXJTime</td>";//无薪假
	echo"<td class='A0101' align='center'>&nbsp;</td>";//年薪假
	echo"<td class='A0101' align='center'>$jbf_QQTime</td>";
	echo"<td class='A0101' align='center'>&nbsp;</td>";
	echo"<td class='A0101' align='center'>$jbf_KGTime</td>";//旷工总工时
	echo"<td class='A0101' align='center'>$jbf_BKTime</td>";//因迟到早退等原因被扣总工时
	echo"<td class='A0101' align='center'>&nbsp;</td></tr>";//夜班次数
	//判断考勤数据是否锁定：Month Number
	$ActionSTR="<input type='submit' name='Submit' value='保存统计结果' onclick='javascript:toSavekq()'>";
	$kqdata_Result = mysql_query("SELECT * FROM kqdata  WHERE Month='$defaultMonth' and Number=$DefaultNumber and Locks=0",$link_id);
	if($kqdata_row=mysql_fetch_array($kqdata_Result)){
		$ActionSTR="锁定";
		}
	echo"<tr height='25' bgcolor='#FFCC99'><td colspan='15' class='A0111'>(注:G为工作日加点工时,X为休息日加班工时,F为法定假日加班工时)</td>
		<td colspan='6' class='A0101' align='center'>$ActionSTR</td></tr>";
?>