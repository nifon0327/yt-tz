<?php 
//include "../basic/chksession.php" ;
//电信-jospeh
include "../basic/parameter.inc";
function anmaOut($RuleStr,$EncryptStr,$Type){
	$SinkOrder="xacdefghijklmbnopqrstuvwyz";
	$RuleLen = strlen($RuleStr);					//渗透码长度，隔1取1
	for($i=1;$i<$RuleLen;$i++){				
		$inChar=substr($RuleStr,$i,1);				//取出渗透码字符
		$inNum=strpos($SinkOrder,$inChar);			//将 渗透码字母 转为数字
		$oldStr.=substr($EncryptStr,$inNum,1);		//从加密码中读取原文字符
		$i++;
		}
	return $oldStr;
	}
/*	
$fArray=explode("|",$c);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Company=anmaOut($RuleStr1,$EncryptStr1,"f");
*/
$dArray=explode("|",$d);//ID
$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$Id=anmaOut($RuleStr2,$EncryptStr2,"d");

//echo "Id: $Id <br>";
include "../model/subprogram/Getstaffname.php";
$SearchRows="AND D.Id=$Id";
$mySql="SELECT D.Id,D.CpName,D.Qty,D.price,B.Name AS Branch,M.Name as Buyer,D.Model,D.SSNumber,D.BuyDate,D.ServiceLife,D.MTCycle,D.Estate,D.Retiredate,D.Warranty,D.Attached,T.Name AS TypeName,D.Remark,D.Date,D.Operator,D.Locks,S.Id AS CId,D.CompanyId,S.Forshort,D.Operator 
FROM $DataPublic.fixed_assetsdata D 
LEFT JOIN $DataPublic.staffmain M ON M.Number=D.Buyer
LEFT JOIN $DataPublic.branchdata B ON B.Id=D.BranchId
LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId
LEFT JOIN $DataPublic.oa1_fixedmaintype O ON O.Id=T.MainTypeID
LEFT JOIN $DataPublic.dealerdata S ON S.CompanyId=D.CompanyId
WHERE 1 $SearchRows ORDER BY D.CpName DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);

//echo "$mySql";
if($myRow = mysql_fetch_array($myResult)){
	do{
		
		$Id=$myRow["Id"];
		//获取使用人员,维修人员表
		$User="&nbsp;";
		$UserDate="";
		$User_Date=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$DataPublic,$link_id);
		if($User_Date!=""){
			$Temp_User=explode('|',$User_Date);	
			$User=$Temp_User[0];
			$UserDate=$Temp_User[1];
			//$User="<div  title='$UserDate'>$User</div>";
		}

		
		//$User=GetSName_Date($Id,'fixed_userdata','1',$DataIn,$link_id);
		$maintainer="&nbsp;";
		$maintainerDate="";
		$maintainer_Date=GetSName_Date($Id,'fixed_userdata','2',$DataIn,$DataPublic,$link_id);
		if($maintainer_Date!=""){
			$Temp_maintainer=explode('|',$maintainer_Date);	
			$maintainer=$Temp_maintainer[0];
			$maintainerDate=$Temp_maintainer[1];	
			//$maintainer="<div  title='$maintainerDate'>$maintainer</div>";
		}

		
		//$maintainer=GetSName_Date($Id,'fixed_userdata','2',$DataIn,$link_id);
		
		$CpName=$myRow["CpName"];
		$TypeName=$myRow["TypeName"];
		$Qty=$myRow["Qty"];
		$price=$myRow["price"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		//$Idc=anmaIn($CompanyData,$SinkOrder,$motherSTR);
		$CId=$myRow["CId"];
		//$Ids=anmaIn($CId,$SinkOrder,$motherSTR);
		//echo "$CompanyId <br>";
		if ($CompanyId==-1)
		{
			$CResult = mysql_query("SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id ",$link_id);
			//echo "SELECT Company FROM $DataPublic.company_assets WHERE Mid=$Id <br>";
			if($CRow = mysql_fetch_array($CResult)){
				$Company=$CRow["Company"];
				$Forshort="<a href='companyinfo_assets.php?Mid=$Id' target='_blank'>$Company</a>";
				}
		}
		else{
			$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
		}
		$Branch=$myRow["Branch"];
		$Buyer=$myRow["Buyer"];
		$Model=$myRow["Model"];
		
		$SSNumber=$myRow["SSNumber"]==""?"&nbsp;":$myRow["SSNumber"];
		$BuyDate=$myRow["BuyDate"];
		$tempBuyDate=$BuyDate;
		$ServiceLife=$myRow["ServiceLife"];   //使用年限
		$MTCycle=$myRow["MTCycle"];   //维修周期
		$Warranty=$myRow["Warranty"];    //保修年限
		$Attached=$myRow["Attached"];
		
		$Date=$myRow["Date"];
		//$Remark=$myRow["Remark"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//计算保修期是否已过
		$BDate=date("Y",strtotime($BuyDate))+$Warranty."-".date("m",strtotime($BuyDate))."-".date("d",strtotime($BuyDate));
		$BuyDate=$BDate>=date("Y-m-d")?"<span class='greenB'>$BuyDate</span>":"<span class='redB'>$BuyDate</span>";
		
		$Estate=$myRow["Estate"];
		if($Estate==1){ //在使用需要维护
			$NexMTDate=$maintainerDate==""?$tempBuyDate:$maintainerDate; //如果有最新维修期，否则用采购日期，则加上维修周期
			//echo "NexMTDate:$NexMTDate";
			$NexMTDate=date("Y-m-d",strtotime($NexMTDate."+ $MTCycle day")); 
			//echo "NexMTDate:$NexMTDate :MTCycle:$MTCycle";
			if($NexMTDate<date("Y-m-d")){ //如果超过时间了，则显示红色
				$NexMTDate="<div class='redB' >$NexMTDate</div>";
			}
			else {
				$NexMTDate="<div  >$NexMTDate</div>";
			}
		}	
	
		switch($Estate){
			case 0:
				$Estate="<span style='color:#F00' >报废</span>";
				break;
			case 1:
				$Estate="<span style='color:#0F0' >使用中</span>";
				break;
			case 3://配件名称审核中
				$Estate="<span style='color:#FF0' >闲置</span>";
				break;
			}
		$Retiredate=$myRow["Retiredate"];
	
		
		
		
	}while ($myRow = mysql_fetch_array($myResult));
}


/*
$CompanyRow = mysql_fetch_array(mysql_query("SELECT * FROM $Company WHERE Id ='$Id' LIMIT 1",$link_id));
$Operator=$CompanyRow["Operator"];
$CompanyId=$CompanyRow["CompanyId"];
$CompanyInfo=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.companyinfo WHERE CompanyId ='$CompanyId' LIMIT 1",$link_id));
$pRow = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Operator' Limit 1",$link_id));
$Operator=$pRow["Name"];
*/
?>
<html>
<head>
<?php 
include "../model/characterset.php";
?>
<link rel="stylesheet" href="../images/BullentidCss.css">
<title>固定资产详细资料</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
}
.style1 {color: #0033FF}
-->
</style></head>
<body>
<span style="color:#FF0"></span>
<?php 
//$linkman_result = mysql_query("SELECT * FROM $DataIn.linkmandata  where CompanyId=$CompanyId order by  Defaults",$link_id);
$BoxCode0='8'.str_pad($Id,11,"0",STR_PAD_LEFT);  //条码不够位前边补0
$BoxCode0=GetCode($BoxCode0,13,"0",1);
//echo "BoxCode0:$BoxCode0";
//$BoxCode0="1234567890123";
/*
echo "
<iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='ean_13code.php?Code=$BoxCode0&lw=1&hi=25'>
";
*/
?>
<table width="522" height="534" border="0" align="center" cellspacing="0">
  <tr>
    <td width="16" height="68" background="../images/M_viewaddress_r1_c1.gif">&nbsp;</td>
    <td background="../images/m_viewfixedassets_r1_c2.gif"></td>
    <td width="16" background="../images/M_viewaddress_r1_c3.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="443" background="../images/M_viewaddress_r2_c1.gif"></td>
    <td width="484" valign="top" background="../images/addresslist_back.gif">
		<div align="left">
		资产名称：<?php  echo $Model?> &nbsp; &nbsp; <?php  echo "
<iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='../admin/ean_13code.php?Code=$BoxCode0&lw=1&hi=25'>
</iframe>" ?><br>
        资产编号：<?php  echo $CpName?><br>
        购买日期：<?php  echo $BuyDate?><br>
        购&nbsp;买&nbsp;人：<?php  echo $Buyer?><br>
		使用状态：<?php  echo $Estate?><br>
        
        领&nbsp;用&nbsp;人：<?php  echo $User?><br>
        领用日期：<?php  echo $UserDate?><br>
        维&nbsp;护&nbsp;人：<?php  echo $maintainer?><br>
        维护日期：<?php  echo $maintainerDate?><br>
        
        <br>
        <?php 
		/*
		$i=1;
		if ($linkman_myrow = mysql_fetch_array($linkman_result)) {
			do{
				$Defaults=$linkman_myrow["Defaults"]=="0"?"(默认)":"";
				
				$Name=$linkman_myrow["Name"]==""?"&nbsp":$linkman_myrow["Name"];
				$Nickname=$linkman_myrow["Nickname"]==""?$Name:$linkman_myrow["Nickname"];
				$Mobile=$linkman_myrow["Mobile"]==""?"&nbsp":$linkman_myrow["Mobile"];
				$MSN=$linkman_myrow["MSN"]==""?"&nbsp":$linkman_myrow["MSN"];
				$SKYPE=$linkman_myrow["SKYPE"]==""?"&nbsp":$linkman_myrow["SKYPE"];
				$Email=$linkman_myrow["Email"]==""?"&nbsp":$linkman_myrow["Email"];
				$Headship=$linkman_myrow["Headship"]==""?"&nbsp":$linkman_myrow["Headship"];
				$Nickname=$linkman_myrow["Email"]==""?$Nickname:"<a href='mailto:$linkman_myrow[Email]'>$Nickname</a>";
				echo "联系人$i"."：$Name$Defaults<br>";
				echo"职 &nbsp;&nbsp;&nbsp;务：$Headship<br>
				昵&nbsp;&nbsp;&nbsp;&nbsp;称：$Nickname<br>
				移动电话：$Mobile<br>				
				邮件地址：$Email<br>
				&nbsp;&nbsp;&nbsp;SKYPE：$SKYPE<br>&nbsp;
				&nbsp;&nbsp;&nbsp;MSN：$MSN<br><br>";
				$i++;
				}while ($linkman_myrow = mysql_fetch_array($linkman_result));
			} */
			
        ?>
		
    </div></td>
    <td background="../images/M_viewaddress_r2_c3.gif"></td>
  </tr>
  <tr>
    <td height="12" background="../images/M_viewaddress_r3_c1.gif"></td>
    <td background="../images/M_viewaddress_r3_c2.gif"></td>
    <td background="../images/M_viewaddress_r3_c3.gif"></td>
  </tr>
</table>
</body>
</html>
