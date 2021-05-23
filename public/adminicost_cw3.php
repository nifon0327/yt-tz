<?php 
//电信-EWEN
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
	
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 and S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
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
	
    $monthResult = mysql_query("SELECT S.Date FROM $DataIn.hzqksheet S 
	WHERE 1 and S.Estate='$Estate' $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and  DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$SearchRows=$SearchRows==""?" and  DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'":$SearchRows;
		$MonthSelect.="</select>&nbsp;";
		}
	  echo $cSignSelect;
	  echo $MonthSelect;
      $SearchRows.="and S.Estate=3";
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 and S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
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
	}
	
//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.Date,S.Estate,S.Locks,S.Operator,S.cSign,
T.Name AS Type,C.Symbol AS Currency,E.Name AS AuditStaff,S.OtherId,O.Estate AS OtherEstate,O.getmoneyNO,S.BillNumber 
FROM $DataIn.hzqksheet S 
LEFT JOIN $DataIn.adminitype T ON S.TypeId=T.TypeId
LEFT JOIN $DataIn.currencydata C ON C.Id=S.Currency
LEFT JOIN $DataIn.staffmain E ON E.Number=S.Auditor 
LEFT JOIN $DataIn.cw4_otherinsheet O ON O.Id=S.OtherId
WHERE 1 $SearchRows order by S.Date";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$AuditStaff=$myRow["AuditStaff"]==""?"&nbsp;":$myRow["AuditStaff"];
		$Type=$myRow["Type"];		
		$Bill=$myRow["Bill"];
		$BillNumber=$myRow["BillNumber"];
		
		$Dir=anmaIn("download/cwadminicost/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="H".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		//有更新权限则解锁
		if($Keys & mUPDATE){
			$Locks=1;
			}
		else{
			$Locks=0;
			}		
        $OtherId=$myRow["OtherId"];	
        $OtherEstate=$myRow["OtherEstate"];	
        $getmoneyNO=$myRow["getmoneyNO"];	
        if($OtherId!=0){
                if($OtherEstate==0)$FontColor="style='color:#FF6633'"; 
                else if($OtherEstate==3) $FontColor="style='color:#0000CC'";          
	            $d1=anmaIn("download/otherin/",$SinkOrder,$motherSTR);		
		        $f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
		        $getmoneyNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\" $FontColor>$getmoneyNO</a>";
                $OtherEstate=$getmoneyNO;
          }
          else{
	          $OtherEstate="&nbsp;";
          }

		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Content,3=>"..."),
			array(0=>$Type,	3=>"..."),
			array(0=>$Bill,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$OtherEstate,1=>"align='center'"),
			array(0=>$AuditStaff,1=>"align='center'"),
			array(0=>$BillNumber,1=>"align='center'")
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