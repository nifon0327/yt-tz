<?php
	
	include "../../basic/parameter.inc";
	include("getStaffNumber.php");
	
	$num = $_POST["number"];
	if(strlen($num) != 5)
	{
		$num = getStaffNumber($num, $DataPublic);
	}
	
	$month = $_POST["month"];
	
	$wagesSql = sprintf("Select S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jbf,S.Yxbz,S.Jz,S.Sb,S.Kqkk,S.RandP,S.Otherkk,S.Amount,S.Gjj,S.Jtbz,S.taxbz From $DataIn.cwxzsheet S Left Join $DataPublic.staffmain M ON M.Number=S.Number Where 1 And S.Number='$num' And S.Month = '$month' Order By S.Month Desc");
	
	$wagesResult = mysql_query($wagesSql);
	$wagesRow = mysql_fetch_assoc($wagesResult);
	
	$dx = $wagesRow["Dx"]; //底薪
	$jbf = $wagesRow["Jbf"]; //加班费
	$gljt = $wagesRow["Gljt"]; //工龄津贴
	$gwjt = $wagesRow["Gwjt"]; //岗位津贴
	$jj = $wagesRow["Jj"]; // 奖金
	$shbz = $wagesRow["Shbz"]; //生活补助
	$zsbz = $wagesRow["Zsbz"]; //住宿补助
	$yxbz = $wagesRow["Yxbz"]; //夜宵补助
	$jtbz = $wagesRow["Jtbz"]; //交通补助
	$taxbz = $wagesRow["taxbz"]; //个税补助
	
	$totle = $dx + $jbf + $gljt + $gwjt + $jj + $shbz + $zsbz + $yxbz + $jtbz + $taxbz; //小计
	
	$kqkk = $wagesRow["Kqkk"]; //考勤扣款
	$sb = $wagesRow["Sb"]; //社保
	$randP = $wagesRow["RandP"]; //个税
	$otherkk = $wagesRow["Otherkk"]; //其他扣款
	$gjj = $wagesRow["Gjj"]; //公积金
	$jz = $wagesRow["Jz"]; //借支
	
	$amount = $wagesRow["Amount"]; //总数
	
	$wage = array("$dx".":底薪", "$jbf".":加班费", "$gljt".":工龄津贴", "$gwjt".":岗位津贴", "$jj".":奖金", "$shbz".":生活补助", "$zsbz".":住宿补助", "$yxbz".":夜宵补助", "$jtbz".":交通补助", "$taxbz".":个税补助", "$totle".":小计", "$kqkk".":考勤扣款", "$sb".":社保", "$randP".":个税", "$otherkk".":其他扣款", "$gjj".":公积金", "$jz".":借支", "$amount".":实发");
	
	echo json_encode($wage);
		
?>