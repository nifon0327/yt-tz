<?php   
//电信-zxq 2012-08-01
//代码-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//当天小组数据登记:第一个登录的人
$Today=date("Y-m-d");
$checkYsql=mysql_query("SELECT Id FROM $DataIn.sc1_memberset WHERE Date='$Today'",$link_id);
if(!$checkYrow=mysql_fetch_array($checkYsql)){
	$ChartinRecode="INSERT INTO $DataIn.sc1_memberset SELECT NULL,GroupId,Number,KqSign,'$Today','0','$Login_P_Number','1','0','10002',NOW(),'10002',NOW()  FROM $DataPublic.staffmain WHERE BranchId>5 AND Estate=1 AND cSign='$Login_cSign'";//车间人员：品检、仓库、车间员工均需要登记 ewen 2014-01-07
	$inAction=@mysql_query($ChartinRecode);
	}
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php   
//CSS模板 
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
echo"<link rel='stylesheet' href='../model/css/sharing.css'>";
echo"<link rel='stylesheet' href='../model/css/topwin.css'>";
?>
<title></title>
</head>
<body onkeydown="unUseKey()"   oncontextmenu="event.returnValue=false"   onhelp="return false;">
<table width="100%" border="1" cellpadding="0" cellspacing="1" class="ModelLineColorSet" rules="rows" frame="above">
  <tr>
    <td height="4" bordercolor="#FFFFFF" class="" scope="col"></td>
  </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" >
  <tr >
   <!-- <td rowspan="2" class="A1000" style="width:250px">&nbsp;&nbsp;<a href="../desktask/desk_formula_fob.php" target='_blank'>Fob计算器</a>&nbsp;&nbsp;<a href="../desktask/desk_formula_price.php"  target='_blank'>售价计算器</a>&nbsp;&nbsp;<a href="../desktask/desk_formula_cg2.php" target="_blank">采购计算器</a></td><td class="A1000">
	</td>-->
<td rowspan="2" class="A0000" style="width:250px">&nbsp;&nbsp;<a href="../desktask/desk_formula_fob.php" target='_blank'>Fob计算器</a>&nbsp;&nbsp;<a href="../desktask/desk_formula_cg2.php" target="_blank">采购计算器</a></td><td class="A1000">
	</td>
	<?php   
	echo"<td width='70' bgcolor='#FFFFFF' class='A1110' scope='col' align='center'><a href='../desk/calendar.php' target='_blank'>行事历</a></td>";
	echo"<td width='70' bgcolor='#FFFFFF' class='A1110' scope='col' align='center'><a href='../public/loginlog_read.php' target='_blank'>登录记录</a></td>";
        echo"<td width='80' bgcolor='#FFFFFF' class='A1110' scope='col' align='center'><a href='cam.php?f=48' target='_blank'>48</a></td>";
        echo"<td width='80' bgcolor='#FFFFFF' class='A1110' scope='col' align='center'><a href='cam.php?f=47' target='_blank'>47</a></td>";

	$rMenuResult = mysql_query("SELECT U.ModuleId,F.ModuleName FROM $DataIn.upopedom U LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=U.ModuleId WHERE 1 and U.Action>0 and F.TypeId=3 and U.UserId=$Login_Id and F.Estate=1 ORDER BY F.OrderId",$link_id);
	$i=7;
	if($rMenuRow = mysql_fetch_array($rMenuResult)){
		do{
			$ModuleId=$rMenuRow["ModuleId"];//加密
			$Mid=anmaIn($ModuleId,$SinkOrder,$motherSTR);
			$ModuleName=$rMenuRow["ModuleName"];
			echo"<td width=70 bgcolor=#FFFFFF class=A1110 scope=col align=center>
					<a href='mcright.php?Mid=$Mid' target='rightFrame'>$ModuleName</a>
				</td>";
			$i++;
			}while ($rMenuRow = mysql_fetch_array($rMenuResult));
		}
	?>
	<!--<td width='70' bgcolor='#FFFFFF' class='A1110'  scope="col" align="center"><a href='../study/study_read.php' target='_blank'>教 程</a></td>-->
    <td width="70" bgcolor="#FFFFFF" class="A1110"  scope="col" align="center"><a href="../public/oprationlog_read.php?From=mcmain" target="mainFrame">操作日志</a></td>
    <!--<td width="70" bgcolor="#FFFFFF" class="A1111"  scope="col" align="center"><a href="#">系统帮助</a></td>-->
    <td width="158" rowspan="2" scope="col" bgcolor="#E7E7E7" align="center" class="A1000"><?php    echo $SubCompany?>系统</td>
  </tr>
  <tr >
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
  </tr>
  
</table>
</body>
</html>