<?php
//2013-10-11 ewen
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$funFrom="aqsc01";
if($Action==0){//读取下级项目
		$checkSql=mysql_query("SELECT A.Id,A.Name,A.Grade,A.Sort,A.Estate,A.Locks,A.Date,A.Operator,IFNULL(B.Name,'无') AS PreType 
																										   FROM $DataPublic.aqsc01 A
																										   LEFT JOIN $DataPublic.aqsc01 B ON B.Id=A.PreItem
																										   WHERE A.PreItem='$tempValue' ORDER BY A.Sort,A.Id",$link_id);
		if($myRow=mysql_fetch_array($checkSql)){
			$ReturnInfo="<table width='100%' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
			$k=1;
			$i=$tempValue*1000+1;
			do{
				$SubTR=$showPurchaseorder="";
				$Id=$myRow["Id"];
				$PreType=$myRow["PreType"];
				$Sort=$myRow["Sort"];
				$Sort="<input type='text' id='sortId' name='sortId' style='width:40px;text-align: right;' value='$Sort' onblur='updateSort(this,$Id,1,\"\")'>";
				$Name=$k."、".$myRow["Name"];
				$Grade=$myRow["Grade"];
				$GradeSTR=$Grade."级分类";
				$Estate=$myRow["Estate"]==1?"<div class='greenB'>有效</div>":"<div class='redB'无效</div>";
				$Date=$myRow["Date"];
				$Operator=$myRow["Operator"];
				include "../model/subprogram/staffname.php";
				$Locks=$myRow["Locks"];
				//检查是否存在下级项目
				$checkSubItemSql=mysql_query("SELECT Id FROM $DataPublic.aqsc01 WHERE PreItem='$Id'",$link_id);
				if($checkSubItemRow=mysql_fetch_array($checkSubItemSql)){
					$showPurchaseorder="<img onClick='Model_ShowOrHide($i,Sub$i,Img_openORclose$i,$Id,0);' name='Img_openORclose$i' src='../images/showtable.gif' title='展开子项目' width='13' height='13' style='CURSOR: pointer'>";
						$SubTR="<tr bgcolor='#B7B7B7' id='Sub$i' style='display:none'>
							<td class='A0100' height='30' valign='top' id='SubDiv$i' colspan='9'></td>
							</tr>";
					}
				else{//如果没有下级项目，则检查是否存在子文件
					$checkSubSql=mysql_query("SELECT Id FROM $DataPublic.aqsc02 WHERE TypeId='$Id'",$link_id);
					if($checkSubRow=mysql_fetch_array($checkSubSql)){
						$showPurchaseorder="<img onClick='Model_ShowOrHide($i,Sub$i,Img_openORclose$i,$Id,1);' name='Img_openORclose$i' src='../images/showtable.gif' title='展开资料' width='13' height='13' style='CURSOR: pointer'>";
						$SubTR="
							<tr bgcolor='#C6E3CB' id='Sub$i' style='display:none'>
							<td class='A0100' height='30' valign='top' colspan='9' id='SubDiv$i' align='right'></td></tr>";
						}
					else{
						//$showPurchaseorder="<img name='Img_openORclose$i' src='../images/spacer.gif' width='13' height='13'>";
						}
					}
				//输出记录
				if($Grade==2){
					$ReturnInfo.="<tr bgcolor='#CCCCCC'>";  //#ccc
					$ReturnInfo.= "<td width='80' align='right' class='A0100' height='20'>$showPurchaseorder</td>";
					$ReturnInfo.= "<td width='20' align='right' class='A0101'>&nbsp;</td>";
					$ReturnInfo.= "<td width='60' class='A0101' align='center'>$GradeSTR</td>";
					$ReturnInfo.= "<td width='200' class='A0101'>$PreType</td>";
					$ReturnInfo.= "<td width='200' class='A0101'>&nbsp;&nbsp;$Name</td>";
					$ReturnInfo.= "<td width='40' align='center' class='A0101'>$Sort</td>";
					$ReturnInfo.= "<td width='50' align='center' class='A0101'>$Estate</td>";
					$ReturnInfo.= "<td width='80' align='center' class='A0101'>$Date</td>";
					$ReturnInfo.= "<td align='center' class='A0100'>$Operator</td>";
					$ReturnInfo.= "</tr>";
					}
				else{
					$ReturnInfo.="<tr bgcolor='#CCFFFF'>";  //#BDE3EE
					$ReturnInfo.= "<td width='89' align='right' class='A0100' height='20'>$showPurchaseorder</td>";
					$ReturnInfo.= "<td width='10' class='A0101'>&nbsp;</td>";
					$ReturnInfo.= "<td width='60' class='A0101' align='right'>$GradeSTR</td>";
					$ReturnInfo.= "<td width='200' class='A0101'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$PreType</td>";
					$ReturnInfo.= "<td width='200' class='A0101'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Name</td>";
					$ReturnInfo.= "<td width='40' align='center' class='A0101'>$Sort</td>";
					$ReturnInfo.= "<td width='50' align='center' class='A0101'>$Estate</td>";
					$ReturnInfo.= "<td width='80' align='center' class='A0101'>$Date</td>";
					$ReturnInfo.= "<td align='center' class='A0100'>$Operator</td>";
					$ReturnInfo.= "</tr>";
					}
				$ReturnInfo.= $SubTR;
				$k++;
				$i++;
				}while($myRow=mysql_fetch_array($checkSql));
				$ReturnInfo.= "</table>";
			}
	}
else{//取文档资料
	$checkSql=mysql_query("SELECT * FROM $DataPublic.aqsc02 WHERE TypeId='$tempValue' ORDER BY Estate DESC,Date DESC",$link_id);
	$ReturnArray = array();
	if($checkRow=mysql_fetch_array($checkSql)){
		$ReturnInfo="<br><table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr align='center' bgcolor='#CCCCCC'><td width='30' height='20' class='A1111'>序号</td><td width='60' class='A1101'>文档类型</td><td width='390' class='A1101'>文档名称</td></tr>";
		$i=1;
		do{
		$Attached=$checkRow["Attached"];
		$Caption=$checkRow["Caption"];
		$FileType=substr($Attached,-3,3);
		if($Attached!=""){
			$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			if($FileType=="mp4"){
				$Caption="<a href=\"../m_videos/videoview.php?d=$d&f=$f&Type=$FileType&Action=6\" target=\"_blank\">$Caption</a>";	
				}
			else{
				$Caption="<a href=\"openorload.php?d=$d&f=$f&Type=$FileType&Action=6\" target=\"download\">$Caption</a>";
				}
			
			}
			$Estate=$checkRow["Estate"]==1?"<sapn class='greenB'>有效</span>":"<sapn class='redB'>无效</span>";
			$Date=$checkRow["Date"];
			$ReturnInfo.= "<tr bgcolor='#FFFFFF'><td align='center' class='A0111' height='20'>$i</td><td align='center' class='A0101'>$FileType</td><td class='A0101'>$Caption</td></tr>";
			$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
		$ReturnInfo.= "</table><br>";
		}
	}
echo $ReturnInfo;
?>
