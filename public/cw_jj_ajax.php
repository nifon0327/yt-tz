<?php 
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$KeyId='Stuff_cwxz';

$TempArray=explode("|",$TempId);
$Number=$TempArray[0];
$ItemName=$TempArray[1];
echo"
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' align='center'>
<tr bgcolor='#EEE' align='center'>
<td width='50' height='20' class='A1111'>序号</td>
<td width='100' class='A1101'>费用名称</td>
<td width='70' class='A1101'>部门</td>
<td width='60' class='A1101'>职位</td>
<td width='60' class='A1101'>员工姓名</td>
<td width='110' class='A1101'>计算月份</td>
<td width='60' class='A1101'>比率参数</td>
<td width='40' class='A1101'>状态</td>
<td width='80' class='A1101'>请款月份</td>
<td width='100' class='A1101'>请款金额</td>
</tr>
";
//读取记录
$checkSql=mysql_query("SELECT A.Number,A.ItemName,B.Name AS Branch,C.Name AS Job,A.Number,D.Name,D.ComeIn,A.Month,A.MonthS,A.MonthE,A.Divisor,A.Rate,A.Amount,A.Estate,A.Locks,A.Date,D.Name AS Operator,E.Idcard 
FROM $DataIn.cw11_jjsheet A 
LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId 
LEFT JOIN $DataPublic.jobdata C ON C.Id=A.JobId 
LEFT JOIN $DataPublic.staffmain D ON D.Number=A.Number
LEFT JOIN $DataPublic.staffsheet E ON E.Number=A.Number 
WHERE 1 AND A.Number='$Number' AND A.ItemName='$ItemName'  ORDER BY A.Month DESC",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Id=$checkRow["Id"];
		$ItemName=$checkRow["ItemName"];
		$Branch=$checkRow["Branch"];
		$Job=$checkRow["Job"];
		$Name=$checkRow["Name"];
		$Number=$checkRow["Number"];
		//include "../admin/subprogram/staff_model_gl.php";
		$MonthS=$checkRow["MonthS"];
		$MonthE=$checkRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Rate=$checkRow["Rate"]*100/100;
		$Estate=$checkRow["Estate"]==0?"<span class=\"greenB\">已付</span>":"<span class=\"redB\">未付</span>";
		$Month=$checkRow["Month"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$ItemName</td>
			<td width='70' class='A0101'>$Branch</td>
			<td width='60' class='A0101' align='center'>$Job</td>
			<td width='60' class='A0101' align='center'>$Name</td>
			<td width='110' class='A0101' align='center'>$MonthSTR</td>
			<td width='60' class='A0101' align='center'>$Rate %</td>
			<td width='40' class='A0101'  align='center'>$Estate</td>
			<td width='80' class='A0101' align='center'>$Month</td>
			<td width='100' class='A0101' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
		
		$SumAmount=number_format(sprintf("%.0f",$SumAmount));
		echo"
		<tr bgcolor='#EEE' align='center'>
		<td height='20' class='A0111' colspan='2'>合计</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0101' align='right'>¥$SumAmount</td>
		</tr>
		";
}
else{
	echo "<tr><td  class='A0111' colspan='10' align='center' height='30'><div style='color:#F00;'>无数据</div></td></tr>";
}
echo "</table>";


/*
//include "../soapServer/stuff_cwxz_read.php";

//echo $Log;
//取得员工薪资数据
if (count($data)>0){
   $ServerIp=$_SERVER[SERVER_ADDR];
   $nameResult=mysql_query("SELECT CShortName FROM $DataPublic.companys_group WHERE IPaddress='$ServerIp' ",$link_id);
   $CShortName=mysql_result($nameResult,0,"CShortName");
   
   
   $Th_Col="序号|35|薪资<br>月份|50|单位|35|底薪|40|加班费|40|工龄<br>津贴|35|岗位<br>津贴|40|奖金|40|生活<br>补助|35|住宿<br>补助|35|交通<br>补助|35|夜宵<br>补助|35|个税<br>补助|40|考勤<br>扣款|40|小计|50|借支|40|社保|35|个税|40|其它|40|实付|50|节日<br>加班费|50|奖金</br>基数|60";
   echo"<table id='$TableId' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>";
   $Field=explode("|",$Th_Col);
   $Count=count($Field);
   //输出表格标题
   for($i=0;$i<$Count;$i=$i+2){
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]'  align='center' height='25px'>$Field[$j]</td>";
   }
  echo"</tr>";
  
  $i=1; $sumAmount=0;$sumHdjbf=0;$sumjjAmount=0;$oldMonth="";$addAmount=0;
  usort($data,'compare');
  
  foreach( $data as $datas){
       while(list($key,$value)=each($datas)){
            if ($key=="Month" || $key=="cSign") $$key=$value;
            else if ($value==0 || $value=="") $$key="&nbsp;"; else $$key=sprintf("%.0f",$value);    
       }
        
       if ($oldMonth=="") {
           $oldMonth=$Month;
           $trbgcolor='#99FF99';
           $sumFlag=1;
           }
       else{
           if ($oldMonth==$Month){
              $trbgcolor='#DDDDDD'; 
              $sumFlag=0;
           }else{
               $trbgcolor='#99FF99';
               $sumFlag=1;
               $oldMonth=$Month;
           }
       }
       
        $sumHdjbf+=$Hdjbf;
        $monthAmount=$Amount+$Sb+$Jz-$taxbz+$Hdjbf;
        
        if ($sumFlag==1) {$sumAmount+=$Amount;$sumjjAmount+=$monthAmount;}
       echo "<tr bgcolor='$trbgcolor'>
             <td align='center'>$i</td>
             <td align='center'>$Month</td>
             <td align='center'>$cSign</td>
             <td align='right'>$Dx</td>
             <td align='right'>$Jbf</td>
             <td align='right'>$Dx</td>
             <td align='right'>$Gljt</td>
             <td align='right'>$Gwjt</td>
             <td align='right'>$Jj</td>
             <td align='right'>$Shbz</td>
             <td align='right'>$Zsbz</td>
             <td align='right'>$Yxbz</td>
             <td align='right'>$taxbz</td>
             <td align='right'>$Kqkk</td>
             <td align='right'>$Total</td>  
             <td align='right'>$Jz</td>
             <td align='right'>$Sb</td>
             <td align='right'>$RandP</td>
             <td align='right'>$Otherkk</td>
             <td align='right'>$Amount</td>
             <td align='right'>$Hdjbf</td>
             <td align='right'>$monthAmount</td>
           </tr>
       ";
       $i++;
  }
  if ($sumHdjbf==0) $sumHdjbf="&nbsp;";
   echo "<tr bgcolor='#99FF99'>
         <td colspan='19' align='right'>合计</td>
         <td align='right'>$sumAmount</td>
         <td align='right' width=''>$sumHdjbf</td>
         <td align='right' width=''>$sumjjAmount</td>
       </tr>";
   echo "</table>"; 
   echo "奖金：<span style='color:#1E3E91'>" . floor(($sumjjAmount/12)*($Rate/100)) . "</span>";
 }
else{
    echo "无数据"; 
}

function compare($x,$y){
if($x['Month'] == $y['Month']){
    if ($x['SortId']<$y['SortId']) return 1; else return 0;
}else    if($x['Month'] < $y['Month']){
        return -1;
    }else{
        return 1;
    }
}
*/
?>

