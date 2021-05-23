<?php 
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//结付状态	
	$SearchRows="";
	
	
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.cw18_workhurtsheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 AND S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
		    $cSign = $cSign==""?$cSignValue:$cSign;
			if($cSign==$cSignValue){
				$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
				$SearchRows.=" and  S.cSign ='$cSignValue'";
				}
			else{
				$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
				}
			}while($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.="</select>&nbsp;";
		}
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw18_workhurtsheet S WHERE 1 AND S.Estate='$Estate' group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.="and  DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$SearchRows=$SearchRows==""?"and  DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'":$SearchRows;
		$MonthSelect.="</select>&nbsp;";
	}	
	
	
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	$SearchRows.="and S.Estate=3";
	echo $cSignSelect;
	echo $MonthSelect;
    
	  
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}
//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo"$CencalSstr";

//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$mySql="SELECT 
	S.Id,S.cSign,S.Number,S.Month,S.Amount,S.SSecurityAmout,S.AllAmout,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,P.ComeIn,S.CheckT,S.HurtDate,S.HostpitalInvoice,S.SSecurityInvoice
	 FROM $DataIn.cw18_workhurtsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1  $SearchRows ";	
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
//$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	do{
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"]; //公司需要报销的费用
			$SSecurityAmout =$myRow["SSecurityAmout"];  //社保局支付费用
			$AllAmout =$myRow["AllAmout"];  //总费用
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
            $HurtDate=$myRow["HurtDate"]=="0000-00-00"?"&nbsp;":$myRow["HurtDate"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
            $Remark=$myRow["Remark"];
            $CheckT=$myRow["CheckT"];
            //$CheckTime=$GDnumber[$CheckT];
 			include "../model/subprogram/staffname.php";
 			$cSignFrom=$myRow["cSign"];
		    include"../model/subselect/cSign.php";
 			$Estate="<span class='yellowB'>请款中</span>";

			$Attached=$myRow["Attached"];
			//echo "Attached1:$Attached";
			if($Attached!="" ){
				 //echo "Attached2:$Attached";
				 $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/Hurtfile/",$SinkOrder,$motherSTR);		
				 $Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: hand;color:#FF6633'>View</a>";
				 
			  }
			else $Attached="&nbsp;";
			
			$HostpitalInvoice=$myRow["HostpitalInvoice"];
			if($HostpitalInvoice!="" ){
				 $f1=anmaIn($HostpitalInvoice,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/Hurtfile/",$SinkOrder,$motherSTR);		
				 $HostpitalInvoice="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: hand;color:#FF6633'>View</a>";
			  }
			else $HostpitalInvoice="&nbsp;";
	
			$SSecurityInvoice=$myRow["SSecurityInvoice"];
			if($SSecurityInvoice!="" ){
				 $f1=anmaIn($SSecurityInvoice,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/Hurtfile/",$SinkOrder,$motherSTR);		
				 $SSecurityInvoice="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: hand;color:#FF6633'>View</a>";
			  }
			else $SSecurityInvoice="&nbsp;";

        /*
         $filereportResult=mysql_fetch_array(mysql_query("SELECT Attached FROM $DataPublic.staff_tj WHERE Number=$Number AND Mid=$Id",$link_id));
         $ReportAttached=$filereportResult["Attached"];
         if($ReportAttached!=""){
		      $f2=anmaIn($ReportAttached,$SinkOrder,$motherSTR);
		      $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
               $FileReport="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Rs_staffHurt_tjfile\",\"$Id\")' src='../images/edit.gif' title='上传体检报告' width='13' height='13'><a href=\"openorload.php?d=$d1&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: hand;color:#FF6633'>View</a>";
               }
        else {
               $FileReport="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Rs_staffHurt_tjfile\",\"$Id\")' src='../images/edit.gif' title='上传体检报告' width='13' height='13'>";
                }
				*/
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
		    array(0=>$ComeIn, 	1=>"align='center'"),
			array(0=>$AllAmout,	1=>"align='right'"),
			array(0=>$Remark, 	1=>"align='left'"),
			array(0=>$HurtDate, 	1=>"align='center'"),
			array(0=>$Attached, 	1=>"align='center'"),
			array(0=>$SSecurityInvoice, 	1=>"align='center'"),
			array(0=>$SSecurityAmout,	1=>"align='right'"),			
			array(0=>$HostpitalInvoice, 	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>