<?php
//27 体检费用						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw17_tjmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='100' class='A1101'>体检类型</td>
<td width='60' class='A1101'>员工姓名</td>
<td width='80' class='A1101'>部门</td>
<td width='80' class='A1101'>职位</td>
<td width='80' class='A1101'>入职日期</td>
<td width='60' class='A1101'>金额</td>
<td width='40' class='A1101'>凭证</td>
<td width='60' class='A1101'>合格与否</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/sbjf/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
	S.CheckT,S.HG,
	S.Id,S.Mid,S.Number,S.Month,S.Amount,S.Date,S.Estate,S.Locks,P.Name,S.Remark,BD.Name AS BranchName,J.Name AS JobName,S.Attached ,S.tjType,P.ComeIn
	FROM $DataIn.cw17_tjsheet S
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
    LEFT JOIN $DataPublic.branchdata BD ON BD.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE S.Mid='$Mid' ORDER BY S.Month DESC,P.Number
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
		$j=1;
		do{
			//结付明细数据
			 $BranchName=$checkSheetRow["BranchName"];
        	$JobName=$checkSheetRow["JobName"];
			$PayRemark=$checkSheetRow["PayRemark"]==""?"-":"<img src='../images/remark.gif' title='$checkSheetRow[PayRemark]' width='16' height='16'>";
        	$ComeIn=$checkSheetRow["ComeIn"];
        	$tjType=$checkSheetRow["tjType"];
        	$CheckT=$checkSheetRow["CheckT"];
        	$CheckTime=$GDnumber[$CheckT];
            switch($tjType){
                case "1":  $tjType="岗前体检".$CheckTime;  break;
                case "2":  $tjType="岗中体检".$CheckTime;  break;
                case "3":  $tjType="离职体检".$CheckTime;  break;
				case "4":  $tjType="健康体检".$CheckTime;  break;				
                }
        	$Attached=$checkSheetRow["Attached"];
        	if($Attached!="" && $Attached!=0){
		    	$f1=anmaIn($Attached,$SinkOrder,$motherSTR);
		     	$d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
             	$Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
          		}
           	$HG=$checkSheetRow["HG"];
            switch($HG){
                 case 1: 
                      $HG="<span class='greenB'>合格</span>";
                       break;
                 case 0:
                      $HG="<span class='redB'>不合格</span>";
                       break;
            	}
			$Name=$checkSheetRow["Name"];
			$Month=$checkSheetRow["Month"];
			$Amount=$checkSheetRow["Amount"];
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$tjType</td>";			
			echo"<td class='A0101' align='center'>$Name</td>";		
			echo"<td class='A0101' align='center'>$BranchName</td>";			
			echo"<td class='A0101' align='center'>$JobName</td>";						
			echo"<td class='A0101' align='center'>$ComeIn</td>";						
			echo"<td class='A0101' align='center'>$Amount</td>";			
		    echo"<td class='A0101' align='center'>$Attached</td>";			
			echo"<td class='A0101' align='center'>$HG</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>