<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
$DataPublic.staffmain
$DataPublic.staffsheet
$DataPublic.branchdata
$DataPublic.jobdata
$DataPublic.nationdata
$DataPublic.rprdata
$DataIn.education
*/
//步骤1
include "../model/modelhead.php";
include "../model/subprogram/read_datain.php";
include "public_appconfig.php";	 

$tableMenuS=500;
$tableWidth=700;
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
$Sql="
	SELECT M.cSign,M.Id,M.Number,M.Name,M.Nickname,M.Grade,M.KqSign,M.Mail,M.ComeIn,M.Introducer,
		S.Sex,S.Nation,S.Rpr,S.Education,S.Married,S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,
		S.Mobile,S.Dh,S.Bank,S.Note,S.HealthPhoto,
		B.Name AS Branch,J.Name AS Job,C.InIp 
		FROM $DataPublic.staffmain M
		LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
		LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
		LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
		LEFT JOIN $DataPublic.companys_group C ON C.cSign=M.cSign
		WHERE M.Id=$Id LIMIT 1
	";
$Result = mysql_query($Sql); 
if($Result){
       $RowNote = mysql_fetch_array($Result);
       $Number=$RowNote["Number"];
	   $cSign=$RowNote["cSign"];
	   $InIp=$RowNote["InIp"];
     }
	$StaffPhotoPath="../download/staffPhoto/";
	$StaffPhotoPath="http://$InIp/download/staffPhoto/";
?>
<title>员工详细资料</title>
<body onkeydown="unUseKey()"   oncontextmenu="event.returnValue=false"   onhelp="return false;">
<form name="form1" enctype="multipart/form-data" action="staff_Save.php" method="post" >
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td <?php  echo $td_bgcolor?> class="A0100" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="35" <?php  echo $td_bgcolor?>  class="A0100"></td>
   <td width="150" id="menuT2" align="center" class="">
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>员工资料</nobr>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
  <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  </table>   
  <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
        <table width="557" border="0" align="center" cellspacing="5">
		 <tr>
		   <td scope="col"><div align="center">员 工 ID</div></td>
	       <td width="163" scope="col"><span class="style1"><?php  echo $RowNote["Number"];?></span></td>
           <td width="281" rowspan="4" scope="col">
		   <?php  if ($RowNote["Photo"]==1) {
		   echo "<img src='$StaffPhotoPath"."p".$Number.".jpg' width='130' height='128'>";}
		   else{echo"<div align='center'>无照片</div>";} ?>
            </td>
		 </tr>
		<tr>
            <td scope="col"><div align="center">姓&nbsp;&nbsp;名</div></td>
            <td scope="col"><span class="style1"><?php  echo $RowNote["Name"];?></span></td>
		</tr>
		<tr>
		  <td scope="col"><div align="center">性&nbsp;&nbsp;别</div></td>
		  <td scope="col"><?php  echo $RowNote["Sex"]==1?"男":"女";?></td>
		  </tr>
				<tr>
            <td scope="col"><div align="center">民&nbsp;&nbsp;族</div></td>
            <td scope="col">
			<?php 
			$Nation= $RowNote["Nation"];
			$Result2 = mysql_query("SELECT Name FROM $DataPublic.nationdata WHERE 1  and Id=$Nation order by Id LIMIT 1",$link_id);
			     if($myRow2 = mysql_fetch_array($Result2)){
				    echo"$myRow2[Name]";
				     }
			?></td>
          </tr>
				<tr>
				  <td scope="col"><div align="center">户口原地</div></td>
		          <td scope="col">
				  <?php 
					$Rpr= $RowNote["Rpr"];
					$Result3 = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE 1  and Id=$Rpr order by Id LIMIT 1",$link_id);
					 if($myRow3 = mysql_fetch_array($Result3)){
						echo"$myRow3[Name]";
						}
					?>
				</td>
		        <td rowspan="5" scope="col">
		   <?php  if ($RowNote["IdcardPhoto"]==1) {
		   echo "<img src='$StaffPhotoPath/c".$Number.".jpg' width='200' height='250'>";}
		   else{echo"<div align='center'>身份证</div>";} ?>
				</td>
		  </tr>
<tr>
            <td><div align="center">教育程度</div></td>
            <td>
			<?php 
			$Education= $RowNote["Education"];
			$Result4 = mysql_query("SELECT Name FROM $DataPublic.education WHERE 1  and Id=$Education order by Id LIMIT 1",$link_id);
			    if($myRow4 = mysql_fetch_array($Result4)){
				    echo"$myRow4[Name]";
				   }
			?>
			</td>
</tr>
          <tr>
            <td><div align="center">婚姻状况</div></td>
            <td><?php 
			 switch($RowNote["Married"]){
			 	case 0:
					echo"已婚";
				break;
				case 1:
					echo"未婚";
				break;
				case 2:
					echo"离异";
				break;
				case 3:
					echo"再婚";
				break;
				}
			 ?></td>
          </tr>          <tr>
            <td width="87"><div align="center">出生日期</div></td>
            <td><span class="style1"><?php  echo $RowNote["Birthday"];?></span></td>
            </tr>
          <tr>
            <td><div align="center">身份证号</div></td>
            <td><span class="style1"><?php  echo $RowNote["Idcard"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">家庭地址</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Address"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">邮政编码</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Postalcode"];?></span></td></tr>
          <tr>
            <td><div align="center">家庭电话</div></td>
            <td scope="col"><span class="style1"><?php  echo $RowNote["Tel"];?></span></td>
			<td rowspan="5" scope="col">
		   <?php  if ($RowNote["HealthPhoto"]==1) {
		   echo "<img src='$StaffPhotoPath/H".$Number.".jpg' width='210' height='128'>";}
		   else{echo"<div align='center'>健康证</div>";} ?>
            </td>
          </tr>
          
          <tr>
            <td><div align="center">部&nbsp;&nbsp;门</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Branch"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">职&nbsp;&nbsp;位</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Job"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">移动电话</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Mobile"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">短&nbsp;&nbsp;号</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Dh"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">电子邮件</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Mail"];?></span></td>
          </tr>
          <tr>
            <td><div align="center">入职时间</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["ComeIn"];?></span></td>
          </tr>
          <tr>
            <td valign="top"><div align="center">备&nbsp;&nbsp;注</div></td>
            <td colspan="2"><span class="style1"><?php  echo $RowNote["Note"];?></span></td>
          </tr>
        </table>
</td></tr></table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td <?php  echo $td_bgcolor?> class="A1000" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class="">
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>员工资料</nobr>					
				</td>
			</tr>
	 </table>
   </td>
   </tr>
</table>
</form>
</body>
</html>
