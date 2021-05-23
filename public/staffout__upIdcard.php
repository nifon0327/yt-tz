<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rkmain
$DataSharing.providerdata
$DataSharing.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.dimissiondata";
ChangeWtitle("$SubCompany 更改身份证号");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upIdcard";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.WorkAdd,M.Number,M.IdNum,M.Name,M.Nickname,M.Grade,M.KqSign,M.BranchId,M.GroupId,
M.JobId,M.Mail,M.AppleID,M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.AttendanceFloor,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,M.FormalSign,S.Sex,S.BloodGroup,S.Nation,S.Rpr,S.Education,S.Married,
S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,S.Mobile,S.Dh,S.Bank,S.Note,S.InFile,S.HealthPhoto,S.vocationHPhoto,M.cSign,I.Name as StaffName
FROM $DataPublic.dimissiondata D
LEFT JOIN  $DataPublic.staffmain M  ON M.Number=D.Number
LEFT JOIN  $DataPublic.staffsheet S ON S.Number=M.Number
LEFT JOIN  $DataPublic.staffmain I ON I.Number=M.Introducer
WHERE D.Id='$Mid' LIMIT 1",$link_id);
if($mainRow = mysql_fetch_array($MainResult)) {
	$Type=$mainRow["Type"];
	$TempType="TypeSTR".strval($Type);
	$$TempType="selected";
	$WorkAdd=$mainRow["WorkAdd"];
	$floorAdd = $mainRow["AttendanceFloor"];
	$Number=$mainRow["Number"];
	$IdNum=$mainRow["IdNum"];
	
	$StaffName=$mainRow["StaffName"];
	
	$Introducer=$mainRow["Introducer"];
	$Name=$mainRow["Name"];
	$Nickname=$mainRow["Nickname"];
	$Sex=$mainRow["Sex"];
	$Nation=$mainRow["Nation"];
	$Education=$mainRow["Education"];
	$Married=$mainRow["Married"];
	$Rpr=$mainRow["Rpr"];
	$Birthday=$mainRow["Birthday"];
	$Tel=$mainRow["Tel"];
	$Postalcode=$mainRow["Postalcode"];
	$Address=$mainRow["Address"];
	$Idcard=$mainRow["Idcard"];
	$IdcardPhoto=$mainRow["IdcardPhoto"];
	$HealthPhoto=$mainRow["HealthPhoto"];
	$vocationHPhoto=$mainRow["vocationHPhoto"];
	$Photo=$mainRow["Photo"];
	$Mobile=$mainRow["Mobile"];
	$Dh=$mainRow["Dh"];
	$ExtNo=$mainRow["ExtNo"];
	$ComeIn=$mainRow["ComeIn"];
	
	$ContractSDate=$mainRow["ContractSDate"];
	$ContractEDate=$mainRow["ContractEDate"];
	
	$Mail=$mainRow["Mail"];
	$AppleID=$mainRow["AppleID"];
	
	$Bank=$mainRow["Bank"];
	$Note=$mainRow["Note"];
	$InFile=$mainRow["InFile"];
	$GroupId=$mainRow["GroupId"];
	$BloodGroup=$mainRow["BloodGroup"];
	$FormalSign=$mainRow["FormalSign"];
	if($FormalSign==1){$selected1="selected";$selected2="";}
	else {$selected2="selected";$selected1="";}
	$cSign=$mainRow["cSign"];
	if(strlen($Idcard)>0){
		$lenIdcard=strlen($Idcard);
		$NewIdcard=substr($Idcard,0,$lenIdcard-1).'Y';
	}
}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,-99,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,Number,$Number";




//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" scope="col">姓名：</td>
            <td scope="col"><?php    echo $Name?></td>
		</tr>
		<tr>
            <td align="right" scope="col">员工ID：</td>
            <td scope="col"><?php    echo $Number?></td>
		</tr>
                
		<tr>
            <td align="right" scope="col">身份证号码：</td>
            <td scope="col"><?php    echo $Idcard?></td>
		</tr>
		<tr>
            <td align="right" scope="col">更改身份证号码：</td>
            <td scope="col"><input name="NewIdcard" type="text" id="NewIdcard" value="<?php  echo $NewIdcard?>" style="width:380px;" maxlength="18"></td>
		</tr>        
 
          
</table>
</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>