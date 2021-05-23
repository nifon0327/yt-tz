<?php 
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";

$Login_help="kq_lj_view";
$FunctionFrom="view";
//session_register("Login_help"); 
$_SESSION["Login_help"] = $Login_help;
$DefaultY=date("Y");
$ColsNumber=9;
$Th_Col="序号|40|薪酬类型|80|部门|60|职位|60|员工姓名|80|入职日期|100|总天数/年|80|有效天数|100|$DefaultY 年假|80";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
$tableMenuS=600;
if($Type==""){
	$Type0="selected";
	}
else{
	if($Type=="1"){
		$Type1="selected";
		}
	else{
		$Type2="selected";
		}
	}
?>
<body >
<form name="form1" method="post" action="">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td <?php  echo $td_bgcolor?> class="A0100" id="menuT1" width="<?php  echo $tableMenuS?>">
		<select name="Type" id="Type" onChange="document.form1.submit()">
      <option <?php  echo $Type0?>>全部</option>
      <option value="1" <?php  echo $Type1?>>A类员工</option>
      <option value="2" <?php  echo $Type2?>>B类员工</option>
    </select></td>
   <td width="150" id="menuT2" align="center" class="A1100" <?php  echo $Fun_bgcolor?>>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
				<?php  
					//权限设定
					echo"<nobr><a href='Annualleave_print.php?Type=$Type' target='_blank' $onClickCSS>列印</a></nobr>";
			   ?>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
  </table>
	<?php 
	$Result = mysql_query("SELECT 
	M.Number,
	M.Name,
	M.BranchType,
	M.JobId,
	M.ComeIn,
	B.JbSign
	FROM $DataPublic.paybase B LEFT JOIN $DataPublic.staffmain M ON B.Number=M.Number  
	WHERE 1 AND M.cSign='$Login_cSign' AND M.Dimission='0000-00-00' order by 
	M.BranchType,M.JobId,M.ComeIn,M.Number",$link_id);
	
	List_Title($Th_Col,"1");
	$i=1;
	if($myRow = mysql_fetch_array($Result)){
		do{
			$JbSign=$myRow["JbSign"];
			$Number=$myRow["Number"];
			$Name=$myRow["Name"];
			$ComeIn=$myRow["ComeIn"];			
			$JobId=$myRow["JobId"];
			$Action=1;
			switch($Type){
				case "1":
					if($JobId>12){
						if($BranchType==2){
						$Action=0;
						}
						}
				break;
				case "2":
					if($JobId<13  || $BranchType==1){
						$Action=0;
						}
				}
			if($Action==1){
				$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Number='$JobId' LIMIT 1",$link_id));
				$Job=$J_Result["Name"];
	
				$BranchType=$myRow["BranchType"];	
				/*
				$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Number='$BranchType' LIMIT 1",$link_id));
				*/
				$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata 
														    where 1 AND (cSign=$Login_cSign OR cSign=0 ) and TypeId='$BranchType' LIMIT 1",$link_id));

				$Branch=$B_Result["Name"];
					
				//入职当年
				$ComeInY=substr($ComeIn,0,4);
				//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
				$ValueY=$DefaultY-$ComeInY;
				
				$DefaultLastM=$DefaultY."-12-01";
				$ThisEndDay=$DefaultY."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
				$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
				
				//计算休假工时
				if($JobId<13){//固定薪
					if($ValueY>1){	//年份间隔在2以上的
						$inDays=$CountDays;

						$AnnualLeave=7*8;
						}
					else{
						if($ValueY==1){
							$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
							$AnnualLeave=intval((7*8*$inDays)/$CountDays);
							}
						else{
							$AnnualLeave=0;
							$inDays=0;
							}
						}					
					}
				else{			//非固定薪
					if($ValueY>1){//1年以上
						$inDays=$CountDays;
						$AnnualLeave=5*8;
						}
					else{//不满1年,计算天数:
						if($ValueY==1){
							$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
							$AnnualLeave=intval((5*8*$inDays)/$CountDays);
							}
						else{
							$AnnualLeave=0;
							$inDays=0;
							}
						}
					}
					
				echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$JbSign=$JbSign==0?"固定薪":"非固定薪";
				$AnnualLeave1=intval($AnnualLeave/8);
				$AnnualLeave2=($AnnualLeave%8);
				$AnnualLeave1=$AnnualLeave1==0?"&nbsp;":$AnnualLeave1."天";
				$AnnualLeave2=$AnnualLeave2==0?$AnnualLeave1:$AnnualLeave1.$AnnualLeave2."小时";
				$inDays=$inDays==0?"&nbsp;":$inDays;
				echo"<td class='A0111' width='$Field[1]' align='center'>$i</td>";
				echo"<td height='20' width='$Field[3]' class='A0101' align='center'>$JbSign</td>";
				echo"<td class='A0101' width='$Field[5]' align='center'>$Branch</td>";
				echo"<td class='A0101' width='$Field[7]' align='center'>$Job</td>";
				echo"<td class='A0101' width='$Field[9]' align='center'>$Name</td>";
				echo"<td class='A0101' width='$Field[11]' align='center'>$ComeIn</td>";
				echo"<td class='A0101' width='$Field[13]' align='center'>$CountDays</td>";
				echo"<td class='A0101' width='$Field[15]' align='center'>$inDays</td>";
				echo"<td class='A0101' width='$Field[17]' align='center'>$AnnualLeave2</td>";
				echo"</tr></table>";
				$i++;
			}
		}while ($myRow = mysql_fetch_array($Result));
	}
echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
List_Title($Th_Col,"0");
Page_Bottom($i-1,$i-1,$Page,$Page_count,$timer,$TypeSTR,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany 年假查询");
?>