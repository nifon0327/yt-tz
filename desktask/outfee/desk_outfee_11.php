<?php   
//员工薪资OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1240;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
		$Th_Col="分类|30|部门|40|职位|40|员工<br>姓名|50|工龄<br>Y(M)|40|底薪|40|加班费|40|工龄<br>津贴|40|岗位<br>津贴|40|
		奖金|40|生活<br>补助|40|住宿<br>补助|40|交通<br>补助|40|夜宵<br>补助|40|个税<br>补助|40|考勤<br>扣款|40|小计|55|借支|40|社保|40|个税|40|其它|40|实付|55|状态|40|备注|40";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='30' align='center'>分类</td>
		<td width='40' align='center'>部门</td>
		<td width='40' align='center'>职位</td>
		<td width='50' align='center'>员工<br>姓名</td>
		<td width='40' align='center'>工龄<br>Y(M)</td>
		<td width='40' align='center'>底薪</td>
		<td width='40' align='center'>加班费</td>
		<td width='40' align='center'>工龄<br>津贴</td>
		<td width='40' align='center'>岗位<br>津贴</td>
		<td width='40' align='center'>奖金</td>
		<td width='40' align='center'>生活<br>补助</td>
		<td width='40' align='center'>住宿<br>补助</td>
		<td width='40' align='center'>交通<br>补助</td>
		<td width='40' align='center'>夜宵<br>补助</td>
		<td width='40' align='center'>个税<br>补助</td>
		<td width='40' align='center'>考勤<br>扣款</td>
		<td width='55' align='center'>小计</td>
		<td width='40' align='center'>借支</td>
		<td width='40' align='center'>社保</td>
		<td width='40' align='center'>个税</td>
		<td width='40' align='center'>其它</td>
		<td width='55' align='center'>实付</td>
		<td width='40' align='center'>状态</td>
		<td width='40' align='center'>备注</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp'";
$chooseMonth=$MonthTemp;
$mySql="SELECT 
S.Id,S.KqSign,S.Number,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,
S.Kqkk,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Remark,S.Locks,M.Name,M.ComeIn,M.Estate AS mEsate,M.Id As PID,B.Name AS Branch,J.Name AS Job
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
        $m=1;
		//初始化数据
		$Dx=0;		$Gljt=0;	$Gwjt=0;	$Jj=0;	$Shbz=0;	$Zsbz=0;	$Jtbz=0; $Jbf=0;	$Yxbz=0;    $taxbz=0;
		$Jz=0;		$Sb=0;		$Kqkk=0;	$RandP;	$Otherkk;		$Total=0;	$Amount=0;
		$Id=$myRow["Id"];
		$KqSign=$myRow["KqSign"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
        $strName=$mainRows["Name"];
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
		$Total=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk;
		$Jz=$myRow["Jz"];				//借支
		$Sb=$myRow["Sb"];				//社保
		$RandP=$myRow["RandP"];			//社保
		
		$Otherkk=$myRow["Otherkk"];		//社保
		$AmountSys=$Total-$Jz-$Sb-$RandP-$Otherkk;		//实付
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
		include "../../public/subprogram/staff_model_gl.php";

		$sumDx+=$Dx;		$sumGljt+=$Gljt;		$sumGwjt+=$Gwjt;		$sumJj+=$Jj;
		$sumShbz+=$Shbz;	$sumZsbz+=$Zsbz;		$sumJtbz+=$Jtbz; $sumYxbz+=$Yxbz;	$Sumtaxbz+=$taxbz;  $sumJtbz+=$Jtbz;    	$sumKqkk+=$Kqkk;	$sumRandP+=$RandP; $sumOtherkk+=$Otherkk;
		$sumTotal+=$Total;	$sumJz+=$Jz;			$sumSb+=$Sb;			$sumAmount+=$Amount;
		$sumJbf+=$Jbf;		
		$Dx=SpaceValue0($Dx);$Gljt=SpaceValue0($Gljt);$Gwjt=SpaceValue0($Gwjt);$Jj=SpaceValue0($Jj);
		$Shbz=SpaceValue0($Shbz);$Zsbz=SpaceValue0($Zsbz);$Jtbz=SpaceValue0($Jtbz);$Yxbz=SpaceValue0($Yxbz);$taxbz=SpaceValue0($taxbz);$Kqkk=SpaceValue0($Kqkk);$RandP=SpaceValue0($RandP);
		$Total=SpaceValue0($Total);
		$Jz=SpaceValue0($Jz);$Sb=SpaceValue0($Sb);$Otherkk=SpaceValue0($Otherkk);$Amount=SpaceValue0($Amount);
		$Jbf=SpaceValue0($Jbf);
		if($KqSign<3){//加入考勤连接以便查询
			$Jbf="<a href='../../public/kq_checkio_reportm.php?DefaultNumber=$Number&defaultMonth=$chooseMonth' target='_blank'>$Jbf</a>";
			}
                if ($Kqkk>0){
                        $Kqkk="<a href='../../public/staffwage_kqkk.php?Number=$Number&chooseMonth=$chooseMonth&Name=$strName' target='_blank'>$Kqkk</a>";
                }
		if(round($AmountSys)!=$Amount){
			$Amount="<div class='redB'>$Amount</div>";
			}

         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='30' align='center'>$KqSignStr</td>
				<td width='40' align='center'>$Branch</td>
				<td width='40' align='center'>$Job</td>
				<td width='50' align='center'>$Name</td>
				<td width='40' align='center'>$Gl</td>
                <td width='40' align='center'> $Dx</td>
				<td width='40' align='center'>$Jbf</td>
				<td width='40' align='center'>$Gljt</td>
				<td width='40' align='center' >$Gwjt</td>
				<td width='40' align='center' >$Jj</td>
				<td width='40' align='center' >$Shbz</td>
				<td width='40' align='center'>$Zsbz</td>
				<td width='40' align='center' >$Jtbz</td>
				<td width='40' align='center' >$Yxbz</td>
				<td width='40' align='center' >$taxbz</td>
				<td width='40' align='center'><div class='redB'>".$Kqkk."</div></td>
				<td width='55' align='center' >$Total</td>
				<td width='40' align='center' ><div class='redB'>".$Jz."</div></td>
				<td width='40' align='center' ><div class='redB'>".$Sb."</div></td>
				<td width='40' align='center'>$RandP</td>
				<td width='40' align='center' >$Otherkk</td>
				<td width='55' align='center' >$Amount</td>
				<td width='40' align='center' >$EstateSign</td>
				<td width='40' align='center' >$Remark</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>