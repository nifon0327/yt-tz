<?php 
//电信-zxq 2012-08-01
/*
$DataIn.hzqksheet
$DataPublic.adminitype
$DataPublic.currencydata
二合一已更新
*/
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
   $SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	//echo $MonthSelect;
	$SearchRows.="and S.Estate=3";
	
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.cw11_jjsheet S 
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
	  echo $cSignSelect;
	  
	  
    $date_Result = mysql_query("SELECT S.ItemName FROM $DataIn.cw11_jjsheet S 
                          LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
                          WHERE 1 $SearchRows GROUP BY S.ItemName order by S.ItemName DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='ItemName' id='ItemName' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["ItemName"];
			if($ItemName==""){
				$ItemName=$dateValue;}
			if($ItemName==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND S.ItemName='$dateValue' ";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		
		$TimeResult=mysql_query("SELECT S.JfTime FROM $DataIn.cw11_jjsheet S
                LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
                WHERE  1 $SearchRows GROUP  BY S.JfTime  ",$link_id);
		if($TimeRow=mysql_fetch_array($TimeResult)){
			echo"<select name='JfTime' id='JfTime' onchange='document.form1.submit()'>";
		   do{
		          $thisJfTime=$TimeRow["JfTime"];
		          $timeValue="第". $thisJfTime."次结付";
		          if($JfTime=="")$JfTime= $thisJfTime;
		          if($JfTime==$thisJfTime){
				       echo"<option value='$thisJfTime' selected>$timeValue</option>";
				          $SearchRows.="AND S.JfTime='$thisJfTime'";
			     	     }
			    else{
				      echo"<option value='$thisJfTime'>$timeValue</option>";					
				      }
			     }while ($TimeRow=mysql_fetch_array($TimeResult));
			     echo"</select>&nbsp;";
		      }  
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
    	   case 1:$SearchRows.=" AND P.kqSign>'1'";break; 
    	   case 2:$SearchRows.=" AND P.kqSign='1'";break; 
    	}
}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	 } 

//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.ItemName,B.Name AS Branch,W.Name AS Job,S.Number,P.Name,P.Estate AS mEsate,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,S.Estate,S.Locks,S.Date,P.ComeIn,D.Reason,
D.outDate,S.JfRate,S.JfTime,S.RandP,S.jjAmount,S.cSign  
FROM $DataIn.cw11_jjsheet S 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId 
LEFT JOIN $DataPublic.jobdata W ON W.Id=S.JobId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
LEFT JOIN $DataPublic.dimissiondata D ON D.Number=P.Number
WHERE 1 $SearchRows  ORDER BY S.BranchId,S.JobId,P.Number";
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
		//$Name=$myRow["Name"];
		$Name=$myRow["mEsate"]==1?$myRow["Name"]:"<div class='yellowB'>".$myRow["Name"]."</div>";		
		$Month=$myRow["Month"];
		$MonthS=$myRow["MonthS"];
		$MonthE=$myRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Divisor=$myRow["Divisor"];
		$Rate=$myRow["Rate"]*100/100;
		$Amount=$myRow["Amount"];
		$ComeIn=$myRow["ComeIn"];
		$JfRate=$myRow["JfRate"];
		$JfTime=$myRow["JfTime"];
		
		$RandP = $myRow["RandP"];
		$jjAmount=$myRow["jjAmount"];
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";

		// 取得员工离职资料
		
		$outDate=$myRow["outDate"]==""?"&nbsp;":$myRow["outDate"];
		$Reason=$myRow["Reason"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Reason]' width='18' height='18'>";
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Locks=1;
		$Date=$myRow["Date"];
		$Operator=($myRow["Operator"]=="")?"&nbsp;":$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//////////////////////////////////////////////
		//工龄计算
		$Gl_STR="&nbsp;";
		include "subprogram/staff_model_gl.php";
		$totalResult=mysql_query("SELECT Amount   FROM $DataIn.cw11_jjsheet_frist    
		                            WHERE ItemName='$ItemName' AND Number='$Number'",$link_id);
		$totalAmount =mysql_result($totalResult,0,"Amount");
		$ItemName=$ItemName."--".$JfTime;
        //////////////////////////////////////////////
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$ItemName, 1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$ComeIn, 	1=>"align='center'"),
			array(0=>$Gl_STR, 	1=>"align='center'"),
			array(0=>$MonthSTR,	1=>"align='center'"),
			array(0=>$outDate,	1=>"align='center'"),
			array(0=>$Reason,	1=>"align='center'"),
			array(0=>$Rate."%", 1=>"align='center'"),
			array(0=>$totalAmount, 	1=>"align='center'"),
			array(0=>$JfRate, 	1=>"align='center'"),
			array(0=>$jjAmount, 	1=>"align='center'"),
			array(0=>$RandP, 	1=>"align='center'"),
			array(0=>$Amount, 	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Month,	 	1=>"align='center'"),
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