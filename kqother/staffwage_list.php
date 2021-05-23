<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$Lens=count($newcheckid);
if($Lens>0){
          for($i=0;$i<$Lens;$i++){
	       $Id=$newcheckid[$i];
	       if ($Id!=""){
		          $Ids=$Ids==""?$Id:$Ids.",".$Id;
		          }
	        }
     $checkStr="  AND X.Id IN ($Ids)";
      $MonthStr="";
      }
else{
         $MonthTemp=date("Y-m",strtotime($chooseMonth."-01"));
          $checkStr="";
         $MonthStr="  AND X.Month='$chooseMonth' ";
		 
		 if ($BranchId!=""){
			$BranchStr=" AND X.BranchId=$BranchId "; 
		 }
		 
		 if($WorkAdd!="") {
			$workStr=" AND M.WorkAdd=$WorkAdd ";  
		 }		 
     }
?>
<body>
<?php 
$mySql="SELECT X.Id,X.KqSign,X.BranchId,X.Number,X.Dx,X.Gljt,X.Gwjt,X.Jj,X.Shbz,X.Zsbz,X.Jtbz,X.Jbf,X.Yxbz,X.taxbz,X.Jz,X.Sb,X.Kqkk,X.RandP,X.Ct,
X.Otherkk,X.Amount,X.Remark,M.Name,B.Name AS Branch,B.TypeId,J.Name AS Job,P.Jj AS dJj ,X.Month,X.Gjj,G.GroupName
FROM $DataIn.cwxzsheet X
LEFT JOIN $DataPublic.staffmain M ON M.Number=X.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=X.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=X.JobId
LEFT JOIN $DataPublic.paybase P ON P.Number=X.Number
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
WHERE 1 $MonthStr  $checkStr   ORDER BY X.BranchId,X.JobId,M.ComeIn";
//echo  $mySql;
$myResult = mysql_query($mySql,$link_id);//
if($myRow = mysql_fetch_array($myResult)) {
	$i=1;$TbStartRow=1;
	do{
		if($TbStartRow==1){
			echo "<table cellspacing='0' cellpadding='0' width='715'>";
			}
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];	
		$TypeId=$myRow["TypeId"];
		$Job=$myRow["Job"];
		$Dx=$myRow["Dx"];
		$Jj=$myRow["Jj"];
		$Gljt=$myRow["Gljt"];
		$Gwjt=$myRow["Gwjt"];
		$Shbz=$myRow["Shbz"];
		$Zsbz=$myRow["Zsbz"];
		$Jtbz=$myRow["Jtbz"];
		$Jbf=$myRow["Jbf"];
		$Yxbz=$myRow["Yxbz"];
		$taxbz=$myRow["taxbz"];
		$Kqkk=$myRow["Kqkk"];
		$RandP=$myRow["RandP"];
		$Otherkk=$myRow["Otherkk"];
		$Jz=$myRow["Jz"];
		$Sb=$myRow["Sb"];
        $Gjj=$myRow["Gjj"];
        $Ct=$myRow["Ct"];
		$dJj=$myRow["dJj"];
        $Month=$myRow["Month"];
		$Amount=SpaceValue0($myRow["Amount"]);
		$KqSign=SpaceValue0($myRow["KqSign"]);
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$GroupName=trim($myRow["GroupName"]);
		
		$Total=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Jbf+$Yxbz+$taxbz-$Kqkk-Otherkk;
		$BranchId=$myRow["BranchId"];
		$TempsTR=$TypeId==1?"奖金":"加班费";
		//$TempsTR=$BranchId<5?"奖金":"加班费";
		//$Jbf=$BranchId<5?$Jj:$Jbf+$Jj;
		$Dx=SpaceValue0($Dx);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);
		$Jtbz=SpaceValue0($Jtbz);
		$Jj=SpaceValue0($Jj);
		$Jbf=SpaceValue0($Jbf);
		$Yxbz=SpaceValue0($Yxbz);
		$taxbz=SpaceValue0($taxbz);
		$Kqkk=SpaceValue0($Kqkk);
		$RandP=SpaceValue0($RandP);
		$Otherkk=SpaceValue0($Otherkk);
		$Jz=SpaceValue0($Jz);
		$Sb=SpaceValue0($Sb);
		$Gjj=SpaceValue0($Gjj);
		$Ct=SpaceValue0($Ct);
		$Amount=SpaceValue0($Amount);
		$KqSign=SpaceValue0($KqSign);
?>
<tr valign="bottom">
	<td width="50" align="center" >月份</td>
	<td width="50" height="20" align="center" >姓名</td>
	<td width="80" align="center" >小组</td>
	<td width="45" align="center" >职位</td>
	<td width="35" align="center" >底薪</td>
	<td width="35" align="center" >工龄<br>津贴</td>
	<td width="35" align="center" >岗位<br>津贴</td>
	<td width="35" align="center" >奖金</td>
	<td width="35" align="center">加班费</td>
    <td width="35" align="center" >生活<br>补助</td>
    <td width="35" align="center" >住宿<br>补助</td>
	<td width="35" align="center" >交通<br>补助</td>
    <td width="35" align="center" >夜宵<br>补助</td>
    <td width="35" align="center" >个税<br>补助</td>    
    <td width="35" align="center" >考勤<br>扣款</td>
	<td width="40" align="center" >小计</td>
	<td width="35" align="center" >社保<br>扣款</td>
	<td width="35" align="center" >公积金</td>
	<td width="35" align="center" >借支<br>扣款</td>
	<td width="35" align="center" >个税</td>
	<td width="35" align="center" >其他<br>扣款</td>
    <td width="40" align="center" >实发</td>
  </tr>
<tr>
	<td align="center"><?php  echo $Month?></td>
	<td height="30"  align="center"><?php  echo $Name?></td>
	<td align="center"><?php  echo $GroupName ?></td>
	<td align="center"><?php  echo $Job?></td>
	<td align="center"><?php  echo $Dx?></td>
  	<td align="center"><?php  echo $Gljt?></td>
  	<td align="center"><?php  echo $Gwjt?></td>
  	<td align="center"><?php  echo $Jj?></td>
	<td align="center"><?php  echo $Jbf?></td>
  	<td align="center"><?php  echo $Shbz?></td>
  	<td align="center"><?php  echo $Zsbz?></td>
	<td align="center"><?php  echo $Jtbz?></td>
  	<td align="center"><?php  echo $Yxbz?></td>
   	<td align="center"><?php  echo $taxbz?></td>   
  	<td align="center"><?php  echo $Kqkk?></td>
  	<td align="center"><?php  echo $Total?></td>
	<td align="center"><?php  echo $Sb?></td>
	<td align="center"><?php  echo $Gjj?></td>
    <td align="center"><?php  echo $Jz?></td>
    <td align="center"><?php  echo $RandP?></td>
	<td align="center"><?php  echo $Otherkk?></td>
  <td align="center"><?php  echo $Amount?></td>
</tr>
<tr height="10">
  <td colspan="21" class="GZbottom" align="right">&nbsp;备注:<?php  echo $Remark ?></td>
 </tr>
<tr height="8">
  <td colspan="21"></td>
  </tr>
 <?php 
 $TbStartRow++;
 if($i%12==0){
 	echo"</table><div style='PAGE-BREAK-AFTER: always'></div>";
	//echo"</table></div>";
	$TbStartRow=1;
 	}
	$i++;
	}while ($myRow = mysql_fetch_array($myResult));
}
if($i%14!=0){
 	echo"</table>";
 	}
 ?>

