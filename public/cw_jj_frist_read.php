<?php 
//电信-zxq 2012-08-01
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
include "../model/modelhead.php";
//echo"<link rel='stylesheet' href='../model/mask.css'>";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=14;
$sumCols="10";
$tableMenuS=600;
ChangeWtitle("$SubCompany 奖金预算列表");
$funFrom="cw_jj_frist";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|奖金项目|120|部门|70|职位|60|员工ID|50|员工姓名|60|工龄<br>Y(M)|40|计算月份|110|比率参数|60|总金额|80|已结付比率|65|已结付金额|80|状态|40|请款月份|80|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,4,38";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT ItemName FROM $DataIn.cw11_jjsheet_frist  WHERE 1 GROUP BY ItemName order by ItemName DESC",$link_id);
	
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseItem' id='chooseItem' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["ItemName"];
			if($chooseItem==""){$chooseItem=$dateValue;}
			if($chooseItem==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="AND S.ItemName='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
		//选择员工类别
       $kqSelectSign="kqSelectSign" .$chooseKqSign ;
       $$kqSelectSign=" selected ";
       echo"<select name='chooseKqSign' id='chooseKqSign' onchange='document.form1.submit()'>";
       echo"<option value='' $kqSelectSign>全部</option>";
       echo"<option value='1' $kqSelectSign1>固定薪</option>";
       echo"<option value='2' $kqSelectSign2>非固定薪</option>";
       echo"</select>&nbsp;";
    	 
    	switch($chooseKqSign){
    	   case 1:$SearchRows.="AND P.kqSign>'1'";break; 
    	   case 2:$SearchRows.="AND P.kqSign='1'";break; 
    	}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
 
  echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;结付方式:<select name='jfSign' id='jfSign' name='jfSign'>";
       echo"<option value='1' >按结付比率</option>";
       echo"<option value='2' >按分月结付</option>";
       echo"</select>&nbsp;";
       
 echo "&nbsp;&nbsp;&nbsp;&nbsp;  结付比率:<input id='jfRate' name='jfRate' type='text' value='' style='width:80px;'>";
 echo "&nbsp;&nbsp;&nbsp;&nbsp;  请款月份:<input id='qkMonth' name='qkMonth' type='text' value=''  onfocus=\"WdatePicker({dateFmt:'yyyy-MM'})\" style='width:80px; ' readonly>";
$otherAction="<span onclick='javascript:ckeckForm()' $onClickCSS>预先结付</span>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.ItemName,B.Name AS Branch,W.Name AS Job,S.Number,P.Name,P.ComeIn,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,S.Estate,S.Locks,S.Date,P.Name AS Operator,F.Idcard,P.Estate AS PEstate  
FROM $DataIn.cw11_jjsheet_frist  S 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId 
LEFT JOIN $DataPublic.jobdata W ON W.Id=S.JobId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
LEFT JOIN $DataPublic.staffsheet F ON F.Number=S.Number 
WHERE 1 $SearchRows ORDER BY P.BranchId,P.JobId,P.Number";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemName=$myRow["ItemName"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$PEstate=$myRow["PEstate"];
		$Name=$PEstate==0?"<div class='redB'>$Name</div>":$Name;
		$outDateTitle="";
		if ($PEstate==0){
			$checkResult=mysql_fetch_array(mysql_query("SELECT D.outDate FROM $DataPublic.dimissiondata D WHERE D.Number='$Number'",$link_id));
			$outDate=$checkResult["outDate"];
			$outDateTitle=" title='离职日期:$outDate'";
		}
		$Month=$myRow["Month"];
		$MonthS=$myRow["MonthS"];
		$MonthE=$myRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Divisor=$myRow["Divisor"];
        $Idcard=$myRow["Idcard"];
		$Rate=$myRow["Rate"]*100/100;
		$Amount=$myRow["Amount"];
	   $Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$ComeIn=$myRow["ComeIn"];
		$chooseMonth="";
		include "subprogram/staff_model_gl.php";
		$TotalResult=mysql_query("SELECT SUM(jjAmount) AS Amount,SUM(JfRate) AS  JfRate  FROM $DataIn.cw11_jjsheet    
		                            WHERE ItemName='$ItemName' AND Number='$Number'",$link_id);
		$TotalAmount =mysql_result($TotalResult,0,"Amount");
		$TotalJfRate  =mysql_result($TotalResult,0,"JfRate");
		$TotalAmount=$TotalAmount==""?"&nbsp;":$TotalAmount;
		$TotalJfRate=$TotalJfRate==""?"&nbsp;":$TotalJfRate;
		$LockRemark="";
		if($TotalJfRate==1){
				            $Estate="<div align='center' class='greenB' title='已开始结付'>√</div>";
				            $LockRemark="记录已经开始结付，强制锁定！修改需删除奖金列表中的记录。";
				            $Locks=0;		          
		         }
		else{
			          $Estate="<div align='center' class='redB' title='未处理'>×</div>";
				      $LockRemark="";
		       }
		       $Operator="&nbsp;";
		 switch($ItemName){
			 case "2013年终奖金": $Rate=$Rate/100;break;
			 default: $Rate.="%"; break;
		 }
	
		$ValueArray=array(
			array(0=>$ItemName, 1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$Name, 	1=>"align='center' $outDateTitle"),
			array(0=>$Gl, 			1=>"align='right'"),
			array(0=>$MonthSTR,	1=>"align='center'"),
			array(0=>$Rate, 	1=>"align='center'"),
			array(0=>$Amount, 	1=>"align='center'"),
			array(0=>$TotalJfRate, 	1=>"align='center'"),
			array(0=>$TotalAmount , 	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Month,	 	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
        $TempId="$Number|$ItemName";//日期/层参数/统计分类
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cw_jj\",\"public\");' id='ThisImg_$DivNum$i' title='$TempId' name='ThisImg_$DivNum$i' src='../images/showtable.gif'' width='13' height='13' style='CURSOR: pointer'><input type='hidden' id='totalJfRate$i' name='totalJfRate$i'  value='$TotalJfRate'>";
		$HideTableHTML="
				<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
					<tr bgcolor='#B7B7B7'>
						<td class='A0111' height='30'>
							<br>
								<div id='HideDiv_$DivNum$i' align='right'>&nbsp;</div>
							<br>
						</td>
					</tr>
				</table>";
		include "../model/subprogram/read_model_6.php";
                echo $HideTableHTML;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function ckeckForm(){
       var Message="";
	    var upId="";
        var jfRate=document.getElementById("jfRate").value;
        var funFrom=document.form1.funFrom.value;
		var choosedRow=0;
		var j=1;
		for (var i=0;i<form1.elements.length;i++){
		   //var tempRate=0;
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				if(e.checked){
						if(upId=="")upId=e.value;
						else  upId=upId+"^^"+e.value;
						choosedRow=choosedRow+1;
						//tempRate=document.getElementById("totalJfRate"+j).value;
						//alert(tempRate);
					} 
				}
				j++;
			}
		if(choosedRow==0){
			Message="该操作要求选定记录！";
			}
	   	if(Message!=""){
		       alert(Message);return false;
		       }
		else{
		      var jfSign=document.getElementById("jfSign").value;
		      if (jfSign==1){
	               if(jfRate=="" || jfRate>1){alert("结付比率不能为空 || 结付比率大于1");return false;}
	          }
	          
		     document.form1.action="cw_jj_frist_updated.php?ActionId=89&Id="+upId;	
		      document.form1.submit();
		      }
	}
</script>