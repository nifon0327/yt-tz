<?php 
/*$DataIn.电信---yang 20120801
$DataIn.cwxzsheet
$DataPublic.staffmain
$DataPublic.branchdata
$DataPublic.jobdata
二合一已更新
*/

$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$monthResult = mysql_query("SELECT S.Month FROM $DataIn.cwxzsheet S WHERE 1 and S.Estate='$Estate' group by S.Month order by S.Id DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$monthRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and S.Month='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$SearchRows=$SearchRows==""?"and S.Month='$chooseMonth'":$SearchRows;
		$MonthSelect.="</select>&nbsp;";
		}
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	echo $MonthSelect;
	$SearchRows.="and S.Estate=3";
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
//步骤4：
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
//步骤6：需处理数据记录处理
$Sum_DX=0;	$Sum_Gljt=0;	$Sum_Gwjt=0;	$Sum_Gljt=0;	$Sum_Jj=0;		$Sum_Shbz=0;	$Sum_Zsbz=0;$Sum_Jtbz=0;$sumCt=0;
$Sum_Jbf=0;	$Sum_Yxbz=0;	$Sum_taxbz=0;    $Sum_Jz=0;		$Sum_Sb=0;	$Sum_Gjj=0;	$Sum_Kqkk=0;$Sum_dkfl=0;	$Sum_Total=0;	$Sum_Amount=0;

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.KqSign,S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Gjj,S.Ct,
S.Kqkk,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Remark,S.Locks,M.Name,M.ComeIn,M.Estate AS mEsate,M.Id As PID,B.Name AS Branch,J.Name AS Job
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
WHERE 1 $SearchRows
order by M.BranchId,M.JobId Asc,M.Id,M.ComeIn";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//初始化数据
		$Dx=0;		$Gljt=0;	$Gwjt=0;	$Jj=0;	$Shbz=0;	$Zsbz=0;	$Jtbz=0; $Jbf=0;	$Yxbz=0;    $taxbz=0;
		$Jz=0;		$Sb=0;		$Gjj=0;     $Kqkk=0;	$RandP;	$Otherkk;		$Total=0;	$Amount=0;
		$Id=$myRow["Id"];
		$KqSign=$myRow["KqSign"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
        $strName=$mainRows["Name"];
        include "../model/subprogram/staff_qj_day.php";
		$Name=$myRow["mEsate"]==1?$myRow["Name"]:"<div class='yellowB'>".$myRow["Name"]."</div>";
		$ComeIn=$myRow["ComeIn"];
		$Dx=$myRow["Dx"];			//底薪
		$Jbf=$myRow["Jbf"];			//加班费
		$Gljt=$myRow["Gljt"];		//工龄津贴
		$Gwjt=$myRow["Gwjt"];		//岗位津贴
		$Jj=$myRow["Jj"];			//奖金
		$Shbz=$myRow["Shbz"];		//生活补助
		$Zsbz=$myRow["Zsbz"];		//住宿补助
		$Jtbz=$myRow["Jtbz"];		//交通补助
		$Yxbz=$myRow["Yxbz"];		//夜宵补助	
		$taxbz=$myRow["taxbz"];			//个税补助
		$Kqkk=$myRow["Kqkk"];		//考勤扣款
		$dkfl=$myRow["dkfl"];		//抵扣福利  //add by zx 2013-05-29
		$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk-$dkfl;
		$Jz=$myRow["Jz"];				//借支
		$Sb=$myRow["Sb"];				//社保
		$Gjj=$myRow["Gjj"];				//社保
        $Ct=$myRow["Ct"];//餐费扣款
		$RandP=$myRow["RandP"];			//社保
		
		$Otherkk=$myRow["Otherkk"];		//社保
		$AmountSys=$Total-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk;		//实付
		$Amount=$myRow["Amount"];		//数据表值
		$KqSignStr=$KqSign==1?"※":"●";
		$Estate=$myRow["Estate"];//结付标记
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		if(($Keys & mUPDATE) || ($Keys & mLOCK)){
			$Locks=1;
			}
		$LockRemark="";
		switch($Estate){
			case "3"://未结付
				$EstateSign="<div class='redB'>未付</div>";
				break;
			default://未审核
				$EstateSign="<div class='redB'>错误</div>";
				break;
			}
		//工龄计算
		include "subprogram/staff_model_gl.php";

		$sumDx+=$Dx;		$sumGljt+=$Gljt;		$sumGwjt+=$Gwjt;		$sumJj+=$Jj;
		$sumShbz+=$Shbz;	$sumZsbz+=$Zsbz;		$sumJtbz+=$Jtbz; $sumYxbz+=$Yxbz;	$Sumtaxbz+=$taxbz;  $sumJtbz+=$Jtbz;    	$sumKqkk+=$Kqkk;  $sumdkfl+=$dkfl;	$sumRandP+=$RandP; $sumOtherkk+=$Otherkk;  $sumCt+=$Ct;
		$sumTotal+=$Total;	$sumJz+=$Jz;			$sumSb+=$Sb;		$sumGjj+=$Gjj;			$sumAmount+=$Amount;
		$sumJbf+=$Jbf;		
		$Dx=SpaceValue0($Dx);$Gljt=SpaceValue0($Gljt);$Gwjt=SpaceValue0($Gwjt);$Jj=SpaceValue0($Jj);
		$Shbz=SpaceValue0($Shbz);$Zsbz=SpaceValue0($Zsbz);$Jtbz=SpaceValue0($Jtbz);$Yxbz=SpaceValue0($Yxbz);$taxbz=SpaceValue0($taxbz);$Kqkk=SpaceValue0($Kqkk);$dkfl=SpaceValue0($dkfl); $RandP=SpaceValue0($RandP);
		$Total=SpaceValue0($Total);
		$Jz=SpaceValue0($Jz);$Sb=SpaceValue0($Sb);$Gjj=SpaceValue0($Gjj);$Ct=SpaceValue0($Ct);$Otherkk=SpaceValue0($Otherkk);$Amount=SpaceValue0($Amount);
		$Jbf=SpaceValue0($Jbf);
		if($KqSign<3){//加入考勤连接以便查询
			$Jbf="<a href='kq_checkio_reportm.php?DefaultNumber=$Number&defaultMonth=$chooseMonth' target='_blank'>$Jbf</a>";
			}
                if ($Kqkk>0){
                        $Kqkk="<a href='staffwage_kqkk.php?Number=$Number&chooseMonth=$chooseMonth&Name=$strName' target='_blank'>$Kqkk</a>";
                }
		if(round($AmountSys)!=$Amount){
			$Amount="<div class='redB'>$Amount</div>";
			}
		$ValueArray=array(
			array(0=>$KqSignStr, 	1=>"align='center'"),
			array(0=>$Branch, 		1=>"align='center'"),
			array(0=>$Job, 			1=>"align='center'"),
			array(0=>$Name,	 		1=>"align='center' $qjcolor"),
			array(0=>$Gl, 			1=>"align='right'"),
			array(0=>$Dx, 			1=>"align='center'"),
			array(0=>$Jbf, 			1=>"align='center'"),
			array(0=>$Gljt,			1=>"align='center'"),
			array(0=>$Gwjt, 		1=>"align='center'"),
			array(0=>$Jj, 			1=>"align='center'"),
			array(0=>$Shbz, 		1=>"align='center'"),
			array(0=>$Zsbz, 		1=>"align='center'"),
			array(0=>$Jtbz, 		1=>"align='center'"),
			array(0=>$Yxbz, 		1=>"align='center'"),
			array(0=>$taxbz, 		1=>"align='center'"),
			array(0=>"<div class='redB'>".$Kqkk."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$dkfl."</div>", 1=>"align='center'"),
			array(0=>$Total,		1=>"align='center'"),
			array(0=>"<div class='redB'>".$Jz."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$Sb."</div>", 1=>"align='center'"),
			array(0=>"<div class='redB'>".$Gjj."</div>", 1=>"align='center'"),
		        array(0=>"<div class='redB'>".$Ct."</div>", 1=>"align='center'"),
			array(0=>$RandP, 		1=>"align='center'"),
			array(0=>$Otherkk, 		1=>"align='center'"),
			array(0=>$Amount, 		1=>"align='center'"),
			array(0=>$EstateSign, 	1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'")
			);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
$sumDx=SpaceValue0($sumDx);$sumJbf=SpaceValue0($sumJbf);$sumGljt=SpaceValue0($sumGljt);$sumGwjt=SpaceValue0($sumGwjt);$sumJj=SpaceValue0($sumJj);
$sumShbz=SpaceValue0($sumShbz);$sumZsbz=SpaceValue0($sumZsbz);$sumJtbz=SpaceValue0($sumJtbz);$sumYxbz=SpaceValue0($sumYxbz);$Sumtaxbz=SpaceValue0($Sumtaxbz);$sumKqkk=SpaceValue0($sumKqkk);$sumdkfl=SpaceValue0($sumdkfl);$sumCt=SpaceValue0($sumCt);
$sumOtherkk=SpaceValue0($sumOtherkk);
$sumTotal=SpaceValue0($sumTotal);$sumJz=SpaceValue0($sumJz);$sumSb=SpaceValue0($sumSb);
$sumRandP=SpaceValue0($sumRandP);$sumAmount=SpaceValue0($sumAmount);

echo"<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0'style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr height='25'>";
	echo"<td class='A0111' align='center' width='270' bgcolor=$Title_bgcolor>合 计</td>";
	echo"<td class='A0101' align='right' width='40'>$sumDx</td>";
	echo"<td class='A0101' align='right' width='40'>$sumJbf</td>";
	echo"<td class='A0101' align='right' width='40'>$sumGljt</td>";
	echo"<td class='A0101' align='right' width='40'>$sumGwjt</td>";
	echo"<td class='A0101' align='right' width='40'>$sumJj</td>";
	echo"<td class='A0101' align='right' width='40'>$sumShbz</td>";
	echo"<td class='A0101' align='right' width='40'>$sumZsbz</td>";
	echo"<td class='A0101' align='right' width='40'>$sumJtbz</td>";
	echo"<td class='A0101' align='right' width='40'>$sumYxbz</td>";
	echo"<td class='A0101' align='right' width='40'>$Sumtaxbz</td>";
	echo"<td class='A0101' align='right' width='40'>$sumKqkk</td>";
	echo"<td class='A0101' align='right' width='40'>$sumdkfl</td>";
	echo"<td class='A0101' align='center' width='55'>$sumTotal</td>";
	echo"<td class='A0101' align='right' width='40'>$sumJz</td>";
	echo"<td class='A0101' align='right' width='40'>$sumSb</td>";
	echo"<td class='A0101' align='right' width='40'>$sumGjj</td>";
	echo"<td class='A0101' align='right' width='40'>$sumCt</td>";
	echo"<td class='A0101' align='right' width='40'>$sumRandP</td>";
	echo"<td class='A0101' align='right' width='40'>$sumOtherkk</td>";
	echo"<td class='A0101' align='center' width='55'>$sumAmount</td>";
	echo"<td class='A0101' align='center' width='80'  bgcolor=$Title_bgcolor>&nbsp;</td></tr><table>";

echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>