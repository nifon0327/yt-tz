<?php 
		$i=$j+1;
		echo"<tr onmousedown='setPointer(this,$i,\"click\",\"$bgcolor\",\"$MouseOver_bgcolor\",\"#FFCC99\");' 
				onmouseover='setPointer(this,$i,\"over\",\"$bgcolor\",\"$MouseOver_bgcolor\",\"#FFCC99\");' 
				onmouseout='setPointer(this,$i,\"out\",\"$bgcolor\",\"$MouseOver_bgcolor\",\"#FFCC99\");'
				bgcolor='$bgcolor'>";
		$AI=SpaceValue($AI);
		$AO=SpaceValue($AO);
		$today_GTime=SpaceValue($today_GTime);
		$today_WorkTime=SpaceValue($today_WorkTime);
		$today_GJTime=SpaceValue($today_GJTime);
		$today_XJTime=SpaceValue($today_XJTime);
		$today_FJTime=SpaceValue($today_FJTime);
		$today_InLates=SpaceValue($today_InLates);
		$today_OutEarlys=SpaceValue($today_OutEarlys);
		$today_SJTime=SpaceValue($today_SJTime);
		$today_BJTime=SpaceValue($today_BJTime);
		$today_BXTime=SpaceValue($today_BXTime);
		$today_WXJTime=SpaceValue($today_WXJTime);
		$today_LJTime=SpaceValue($today_LJTime);
		$today_KGTime=SpaceValue($today_KGTime);
		$today_BKTime=SpaceValue($today_KGTime*2);
		$today_YBs=SpaceValue($today_YBs);
		$today_WXTime=SpaceValue($today_WXTime);	
		$today_QQTime=SpaceValue($today_QQTime);
		$Sum_BKTime=$Sum_BKTime+$today_BKTime;
	    echo"<td class='A0111' align='center'>$i</td>";//日期
		echo"<td class='A0101' align='center'>$today_WeekDay</td>";//星期
		echo"<td class='A0101' align='center'><span $AIcolor>$AI</span></td>";//时间段1的上班签到时间
		echo"<td class='A0101' align='center'><span $AOcolor>$AO</span></td>";//时间段1的上班签退时间
		echo"<td class='A0101' align='center'>$today_GTime</td>";//当天应到工时
		echo"<td class='A0101' align='center'>$today_WorkTime</td>";//当天实到工时
		echo"<td class='A0101' align='center'>$today_GJTime</td>";//当天加点工时
		echo"<td class='A0101' align='center'>$today_XJTime</td>";//当天加班工时（休息日）
		echo"<td class='A0101' align='center'>$today_FJTime</td>";//当天加班工时（法定假日）
		echo"<td class='A0101' align='center'>$today_InLates</td>";//当天迟到次数
		echo"<td class='A0101' align='center'>$today_OutEarlys</td>";//当天早退次数
		echo"<td class='A0101' align='center'>$today_SJTime</td>";//当天事假工时
		echo"<td class='A0101' align='center'>$today_BJTime</td>";//当天病假工时
		echo"<td class='A0101' align='center'>$today_BXTime</td>";//当天补休工时
		echo"<td class='A0101' align='center'>$today_WXJTime</td>";//当天无薪假工时
		echo"<td class='A0101' align='center'>$today_LJTime</td>";//当天无薪假工时
		echo"<td class='A0101' align='center'>$today_QQTime</td>";//缺勤工时
		echo"<td class='A0101' align='center'>$today_WXTime</td>";//无薪假日
		echo"<td class='A0101' align='center'>$today_KGTime</td>";//当天旷工工时
		echo"<td class='A0101' align='center'>$today_BKTime</td>";//当天被扣工时
		echo"<td class='A0101' align='center'>$today_YBs $test1</td>";//夜班
		echo"</tr>";

?>