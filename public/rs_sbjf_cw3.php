<?php 
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//结付状态	
	$SearchRows=" AND S.Estate='3' ";
	
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.sbpaysheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 $SearchRows  GROUP BY S.cSign ",$link_id);
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
		
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.sbpaysheet S 
	WHERE 1 $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
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
	$TypeResult=mysql_query("SELECT S.TypeId FROM $DataIn.sbpaysheet S WHERE 1 $SearchRows  GROUP BY S.TypeId",$link_id);
    if($TypeRow=mysql_fetch_array($TypeResult)){
    echo"<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
    do{
           $thisTypeId =$TypeRow["TypeId"];
           switch($thisTypeId){
                     case 1: $TypeName="社保";break;
                     case 2: $TypeName="公积金";break;
                     case 3: $TypeName="意外险";break;
                     case 4: $TypeName="商业险";break;
                  }
             $TypeId=$TypeId==""?$thisTypeId:$TypeId;
             if($TypeId==$thisTypeId){
                 echo"<option value='$thisTypeId' selected>$TypeName</option>";
                 $SearchRows.=" AND S.TypeId='$thisTypeId'";
              }
              else{
                   echo"<option value='$thisTypeId' >$TypeName</option>";
               }
           }while($TypeRow=mysql_fetch_array($TypeResult));
           echo "</select>";
       }
              
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
$mySql = "SELECT S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,S.Date,S.Operator,S.Estate,S.TypeId,P.Name,J.Picture,S.cSign
	 FROM $DataIn.sbpaysheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
	LEFT JOIN $DataPublic.rs_sbjf_picture  J ON J.Mid=S.Id 
	WHERE 1 $SearchRows 
	ORDER BY S.Month DESC,S.BranchId,S.JobId,P.Number";
	//echo $mySql;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];			
		$Name=$myRow["Name"];
		$Month =$myRow["Month"];
		$mAmount =$myRow["mAmount"];
		$cAmount =$myRow["cAmount"];
		$Amount=sprintf("%.2f",$mAmount +$cAmount);
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
            switch($myRow["TypeId"]){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
                    case 4: $TypeName="商业险";break;
            }
		//有更新权限则解锁
		if($Keys & mUPDATE){$Locks=1;}
		else{$Locks=0;}
		$Estate="<div align='center' class='yellowB'>未结付</div>";
		
        $Picture=$myRow["Picture"];
        //echo "Picture:$Picture";
        if($Picture!=""){
		    $Dir=anmaIn("download/sbjf_List/",$SinkOrder,$motherSTR);
			$Bill=anmaIn($Picture,$SinkOrder,$motherSTR);
			$Month="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>$Month</span>";
            }
            		
		$ValueArray=array(
		    array(0=>$cSign,		1=>"align='center'"),
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Month,	1=>"align='center'"),
			array(0=>$mAmount,	1=>"align='center'"),
			array(0=>$cAmount,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
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