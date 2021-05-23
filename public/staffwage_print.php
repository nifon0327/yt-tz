<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 薪资列表");
$Th_Col="序号|30|分类|30|部门|40|职位|40|员工<br>姓名|50|工龄<br>Y(M)|40|底薪|40|加班费|40|工龄<br>津贴|40|岗位<br>津贴|40|
奖金|40|生活<br>补助|40|住宿<br>补助|40|交通<br>补助|40|夜宵<br>补助|40|个税<br>补助|40|考勤<br>扣款|40|小计|55";//|假日<br>加班|40
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
//步骤6：需处理数据记录处理
$sum_DX=0;	$sum_Gljt=0;	$sum_Gwjt=0;	$sum_Gljt=0;	$sum_Jj=0;		$sum_Shbz=0;	$sum_Zsbz=0;$sum_Jtbz=0;
$sum_Jbf=0;	$sum_Yxbz=0;	$sum_taxbz=0;	$sum_Jz=0;		$sum_Sb=0;		$sum_Kqkk=0;	$sumOtherkk=0;	$sum_Total=0;	$sum_Amount=0;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
$mySql="SELECT 
S.Id,S.KqSign,S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Kqkk,S.RandP,S.Otherkk,S.Amount,
M.Name,M.ComeIn,M.Estate,B.Name AS Branch,J.Name AS Job
FROM $DataIn.cwxzsheet S
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
WHERE 1 and S.Month='$chooseMonth'
order by S.BranchId,S.JobId,M.ComeIn";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//初始化数据
		$Dx=0;		$Gljt=0;	$Gwjt=0;	$Jj=0;	$Shbz=0;	$Zsbz=0;	$Jtbz=0;$Jbf=0;	$Yxbz=0; $taxbz=0; $Holidayjb=0;
		$Jz=0;		$Sb=0;		$Kqkk=0;	$RandP=0;	$Otherkk=0; 	$Total=0;	$Amount=0;
		$Id=$myRow["Id"];
		$KqSign=$myRow["KqSign"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		$Name=$myRow["mEsate"]==1?$myRow["Name"]:"<div class='yellowB'>".$myRow["Name"]."</div>";
		$ComeIn=$myRow["ComeIn"];
		$Dx=$myRow["Dx"];			//底薪
		$Jbf=$myRow["Jbf"];			//加班费
		$Gljt=$myRow["Gljt"];		//工龄津贴
		$Gwjt=$myRow["Gwjt"];		//岗位津贴
		$Jj=$myRow["Jj"];		//奖金
		$Shbz=$myRow["Shbz"];		//生活补助
		$Zsbz=$myRow["Zsbz"];		//住宿补助
		$Jtbz=$myRow["Jtbz"];		//交通补助
		$Yxbz=$myRow["Yxbz"];		//夜宵补助
		$taxbz=$myRow["taxbz"];		//个税补助
		$Kqkk=$myRow["Kqkk"];		//考勤扣款		
		$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk;
		$Jz=$myRow["Jz"];			//借支
		$Sb=$myRow["Sb"];			//社保
		$RandP=$myRow["RandP"];		//奖惩
		$Otherkk=$myRow["Otherkk"];		//其它扣款
		$Amount=$Total-$Jz-$Sb+$RandP-$Otherkk;		//实付
		$KqSignStr=$KqSign==1?"※":"●";
		$Estate=$myRow["Estate"];//结付标记
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$LockRemark="";
		switch($Estate){
			case "3"://未结付
				$EstateSign="<div class='yellowB'>已核</div>";
				$LockRemark="记录已经审核,强制锁定!修改需财务退回.";
				break;
			case "0"://已结付
				$EstateSign="<div class='greenB'>已付</div>";
				$LockRemark="记录已经结付,强制锁定!修改需取消结付.";
				break;
			default://未审核
				$EstateSign="<div class='redB'>未核</div>";
				break;
			}
		//工龄计算
		include "subprogram/staff_model_gl.php";
		$sumD=$sumD>0?$sumD:"-";
		$sumM=$sumM>0?$sumM:"-";
		$sumY=$sumY>0?"<span class='redB'>".$sumY."</span>":"";
		$Gl=$sumY."(".$sumM.")";

		//假日加班费			
		$checkResult = mysql_query("SELECT Amount FROM $DataIn.hdjbsheet WHERE Number=$Number and Month='$chooseMonth'",$link_id);
		if($checkRow = mysql_fetch_array($checkResult)){
			$Holidayjb=sprintf("%.0f",$checkRow["Amount"]);
			$sumHolidayjb+=$Holidayjb;
			}
		$sumDx+=$Dx;		$sumGljt+=$Gljt;		$sumGwjt+=$Gwjt;		$sumJj+=$Jj;
		$sumShbz+=$Shbz;	$sumZsbz+=$Zsbz;$sumJtbz+=$Jtbz;		$sumYxbz+=$Yxbz;	 $sumtaxbz+=$taxbz; 	$sumKqkk+=$Kqkk;	$sumRandP+=$RandP;$sumOtherkk+=$sumOtherkk;
		$sumTotal+=$Total;	$sumJz+=$Jz;			$sumSb+=$Sb;			$sumAmount+=$Amount;
		$sumJbf+=$Jbf;		
		$Dx=SpaceValue0($Dx);$Gljt=SpaceValue0($Gljt);$Gwjt=SpaceValue0($Gwjt);$Jj=SpaceValue0($Jj);
		$Shbz=SpaceValue0($Shbz);$Zsbz=SpaceValue0($Zsbz);$Jtbz=SpaceValue0($Jtbz);$Yxbz=SpaceValue0($Yxbz);$taxbz=SpaceValue0($taxbz);$Kqkk=SpaceValue0($Kqkk);$Jbf=SpaceValue0($Jbf);		
		$Total=SpaceValue0($Total);
		$Jz=SpaceValue0($Jz);$Sb=SpaceValue0($Sb);$RandP=SpaceValue0($RandP);$Otherkk=SpaceValue0($Otherkk);$Amount=SpaceValue0($Amount);
		$Holidayjb=SpaceValue0($Holidayjb);
		if($KqSign<3){//加入考勤连接以便查询
			$Jbf="<a href='kq_checkio_reportm.php?CountType=1&Number=$Number&chooseMonth=$chooseMonth' target='_blank'>$Jbf</a>";
			}
		echo"<tr>";
		echo"<td class='A0111' width='$Field[$m]' align='center'>$i</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$KqSignStr</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Branch</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Job</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Name</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Gl</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Dx</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Jbf</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Gljt</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Gwjt</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Jj</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Shbz</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Zsbz</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Jtbz</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Yxbz</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$taxbz</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Kqkk</td>";$m+=2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Total</td>";$m+=2;
		//echo"<td class='A0101' width='$Field[$m]' align='center'>$Holidayjb</td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	//echo"</table>";
	}
else{
	noRowInfo($tableWidth);
  	}
$sumDx=SpaceValue0($sumDx);$sumJbf=SpaceValue0($sumJbf);$sumGljt=SpaceValue0($sumGljt);$sumGwjt=SpaceValue0($sumGwjt);$sumJj=SpaceValue0($sumJj);
$sumShbz=SpaceValue0($sumShbz);$sumZsbz=SpaceValue0($sumZsbz);$sumJtbz=SpaceValue0($sumJtbz);$sumYxbz=SpaceValue0($sumYxbz);$sumtaxbz=SpaceValue0($sumtaxbz);$sumKqkk=SpaceValue0($sumKqkk);
$sumRandP=SpaceValue0($sumRandP);
$sumTotal=SpaceValue0($sumTotal);$sumHolidayjb=SpaceValue0($sumHolidayjb);$sumJz=SpaceValue0($sumJz);$sumSb=SpaceValue0($sumSb);
$sumOtherkk=SpaceValue0($sumOtherkk);$sumAmount=SpaceValue0($sumAmount);
//echo"<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0'style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr height='25'>";

echo "<tr height='25'>";
	echo"<td class='A0111' align='center' colspan='6' bgcolor=$Title_bgcolor>合 计</td>";
	echo"<td class='A0101' align='center'>$sumDx</td>";
	echo"<td class='A0101' align='center' >$sumJbf</td>";
	echo"<td class='A0101' align='center' >$sumGljt</td>";
	echo"<td class='A0101' align='center' >$sumGwjt</td>";
	echo"<td class='A0101' align='center' >$sumJj</td>";
	echo"<td class='A0101' align='center' >$sumShbz</td>";
	echo"<td class='A0101' align='center' >$sumZsbz</td>";
	echo"<td class='A0101' align='center' >$sumJtbz</td>";
	echo"<td class='A0101' align='center' >$sumYxbz</td>";
	echo"<td class='A0101' align='center' >$sumtaxbz</td>";	
	echo"<td class='A0101' align='center' >$sumKqkk</td>";
	echo"<td class='A0101' align='center' >$sumTotal</td>";
	//echo"<td class='A0101' align='center' >$sumHolidayjb</td>";	
	echo"</tr>";
	echo "</table>";
?>