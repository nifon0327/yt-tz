<?php   
//电信-zxq 2012-08-01
/*
$DataIn.sys_clientfunpower
$DataIn.sys_clientfunmodule
$DataIn.usertable
分开已更新			
*/
session_start();
//if(!(session_is_registered("myCompanyId"))){
if (!$_SESSION["myCompanyId"] ){
	$url="index".$Login_cSign.".php";
	echo "<SCRIPT LANGUAGE=JavaScript>alert('SORRY，SING IN AGAIN!');"; 
	echo "parent.location.href='../$url'"; 
	echo "</script>";
	exit(); 
	}
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php   
include "../model/characterset.php";
include "../model/modelfunction.php";
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
echo"<link rel='stylesheet' href='../model/css/sharing.css'>";
echo"<link rel='stylesheet' href='../model/css/topwin.css'>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
?>
<title></title>
<style type="text/css">
* {margin:0px;padding:0px;}
html,body { height:100%;}
#shadow {
	position:absolute;
	width:100%;
	height:100%;
	background-color:#FFFFFF;
	z-index:11;
	filter: Alpha(Opacity=70);
	display:none;
	overflow:hidden;
	}
</style>
</head>
<body>
<table width="102%" height="23" border="0" cellspacing="0">
  <tr>
    <td width="48%" align="right" scope="col"></td>
    <td colspan="2" align="right" scope="col">
			<table border="1" cellpadding="0" cellspacing="1" class="ModelLineColorSet" frame="above" rules="rows">
			  <tr>
				<td height="6" colspan="2" bordercolor="#FFFFFF" class=""></td>
			  </tr>
			  <tr >        
				   <td width="112" height="23" bgcolor="#FFFFFF"  class="A1111"  scope="col" align="center"><a href="../exit.php" target="_parent">EXIT</a></td>
				   <td width="15">&nbsp;<?php    include "../basic/parameter.inc";?></td>
			  </tr>
			</table>
		</td>
  	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
  	<tr>
    <td colspan="3">
		<table border="0" cellpadding="0" cellspacing="0" class="ModelLineColorSet" rules="rows" frame="below">
			<tr>
			<?php   
			
			//客户名称
			$CheckClient=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId='$myCompanyId' LIMIT 1",$link_id));
			$Forshort=$CheckClient["Forshort"];
			$rMenuResult = mysql_query("SELECT P.ModuleId,F.ModuleName,F.AutoName
			FROM $DataIn.sys_clientfunpower P
			LEFT JOIN $DataIn.sys_clientfunmodule F ON F.ModuleId=P.ModuleId 
			LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
			WHERE 1 AND P.UserId='$Login_Id' AND F.Estate=1 ORDER BY Oby,P.ModuleId",$link_id);
			if ($rMenuRow = mysql_fetch_array($rMenuResult)){
				$i=1;
				do{
					$E_Forshort="";
					$AutoName=$rMenuRow["AutoName"];
					$ModuleId=$rMenuRow["ModuleId"];//加密
					$Mid=anmaIn($ModuleId,$SinkOrder,$motherSTR);
					$ModuleName=$rMenuRow["ModuleName"];
					if($AutoName!=0){
						if($ModuleId=="100004"){//联络名单
							include "../admin/subprogram/mycompany_info.php";
							if($AutoName==1){$ModuleName=$E_Forshort." ".$ModuleName;}
							else{$ModuleName=$ModuleName." ".$E_Forshort;}
							}
						else{//其它项目加客户名称
							if($AutoName==1){$ModuleName=$Forshort." ".$ModuleName;}
							else{$ModuleName=$ModuleName." ".$Forshort;}
							}
						}
					echo"<td bgcolor=#FFFFFF class='A0001' scope='col' align='center' height='23'>
						<a href='mainFrame.php?Mid=$Mid' target='mainFrame'>&nbsp;&nbsp;$ModuleName&nbsp;&nbsp;</a>
						</td>";
					$i++;
					}while ($rMenuRow = mysql_fetch_array($rMenuResult));
				}
				?>
			</tr>
		</table>
	</td>
  </tr>
  <tr>
   <td colspan="3" class="A1100" bgcolor="#FFFFFF">
		<table width="100%" border="0" cellpadding="0" cellspacing="0"   frame="above" rules="rows">
	  		<tr> <td height="4" bordercolor="#FFFFFF" class="" scope="col"></td></tr>
   		</table>
	</td>
  </tr>
</table>
</body>
</html>