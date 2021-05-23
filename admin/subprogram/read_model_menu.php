<?php   
echo"<DIV id=ie5menu style='border-right: #9bc9df 1px solid; border-top: #9bc9df 1px solid; display: none; z-index: 99999; border-left: #9bc9df 1px solid; border-bottom: #9bc9df 1px solid; position: absolute; background-color: white'>";
if($ChooseFun!="N"){
	$DivStr="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='All_elects(\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",$ColsNumber)' align='right'>全选记录&nbsp;&nbsp;</DIV>
		<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='Instead_elects(\"$theDefaultColor\",\"$theDefaultColor\",\"$theMarkColor\",$ColsNumber)' align='right'>反选记录&nbsp;&nbsp;</DIV>
		<DIV class=hr><HR color=#9bc9df SIZE=1></DIV>";//全选反选项和分隔行
	}
$actionArray=explode(",",$ActioToS);
for($i=0;$i<count($actionArray);$i++){
	$Id=$actionArray[$i];
	$checkPageMenuS=mysql_query("SELECT Name,ToRecord,ToPower,ToPage,ToWin,ToWarn FROM $DataPublic.sys3_pagemenu WHERE Id=$Id AND Estate=1",$link_id);
	if($checkPageMenuR=mysql_fetch_array($checkPageMenuS)){
		$ToRecord=$checkPageMenuR["ToRecord"];
		$ToPower=$checkPageMenuR["ToPower"];
		$tempName=$checkPageMenuR["Name"];
		$ToPage=$checkPageMenuR["ToPage"];
		$ToWin=$checkPageMenuR["ToWin"];
		$ToWarn=$checkPageMenuR["ToWarn"];
			switch($ToPower){
				case 1:
				if($Keys & mREAD){
					$DivStr.="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='ActionTo($Id,$ToRecord,\"$ToPage\",\"$ToWin\",$ToWarn)' align='right'>$tempName&nbsp;&nbsp;</DIV>";}
				break;
				case 2:
				if($Keys & mADD){
					$DivStr.="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='ActionTo($Id,$ToRecord,\"$ToPage\",\"$ToWin\",$ToWarn)' align='right'>$tempName&nbsp;&nbsp;</DIV>";}
				break;
				case 4:
				if($Keys & mUPDATE){
					$DivStr.="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='ActionTo($Id,$ToRecord,\"$ToPage\",\"$ToWin\",$ToWarn)' align='right'>$tempName&nbsp;&nbsp;</DIV>";}
				break;
				case 8:
				if($Keys & mDELETE){
					$DivStr.="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='ActionTo($Id,$ToRecord,\"$ToPage\",\"$ToWin\",$ToWarn)' align='right'>$tempName&nbsp;&nbsp;</DIV>";}
				break;
				case 16:
				if($Keys & mLOCK){
					$DivStr.="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='ActionTo($Id,$ToRecord,\"$ToPage\",\"$ToWin\",$ToWarn)' align='right'>$tempName&nbsp;&nbsp;</DIV>";}
				break;
				case 32:
				if($Keys & mLOCK){
					$DivStr.="<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick='showJpDiv($Id,\"$ToPage\")' align='right'>$tempName&nbsp;&nbsp;</DIV>";}
				break;
				}
		}
	}
echo "<DIV id='ColorSide' style='Z-INDEX: 866; FILTER: Alpha(Opacity=40,FinishOpacity=95,Style=1);left:0px;WIDTH: 20px; POSITION: absolute; BACKGROUND-COLOR: lightblue;'></DIV>";
echo "$DivStr<DIV class=hr><HR color=#9bc9df SIZE=1></DIV>";
echo"<DIV class=menuitems onmouseover=myover(this); onmouseout=myout(this); onclick=window.location.reload(); align='right'>刷新页面$SideDefultH &nbsp;&nbsp;</DIV>";
echo"</DIV>";
echo"<DIV id='TotalStatusBar' style='display:none;'>&nbsp;</div>";
echo"<DIV id=adbottom>
	<SCRIPT src=\"../model/pagebottom.js\" type=text/javascript></script>
</DIV>";
?>