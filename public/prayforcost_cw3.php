<?php 
/*电信---yang 20120801
$DataIn.cwdyfsheet
二合一已更新
*/
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

if($From!="slist"){
	//结付状态	
	$SearchRows="";
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 AND S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
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
		
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cwdyfsheet S WHERE 1 AND S.Estate='$Estate' group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
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
	echo $cSignSelect;
	echo $MonthSelect;
	
	$SearchRows.="and S.Estate=3";
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.ItemId,K.Name as KName,S.Date,S.Amount,C.Name as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator,S.cSign
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	WHERE 1 $SearchRows order by S.Date DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$KName=$myRow["KName"];
		$Description=$myRow["Description"]==""?"&nbsp":$myRow["Description"];
		$Amount=$myRow["Amount"];
		$CName=$myRow["CName"];
		$ModelDetail=$myRow["ModelDetail"]==""?"&nbsp":$myRow["ModelDetail"];		
		$Remark=$myRow["Remark"]==""?"&nbsp":"<span  style='CURSOR: pointer;color:#FF6633' Title='$myRow[Remark]'>查看</span>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];
		//有更新权限则解锁
		if($Keys & mUPDATE){
			$Locks=1;
			}
		else{
			$Locks=0;
			}		
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$ItemId,1=>"align='center'"),
			array(0=>$KName,3=>"..."),
			array(0=>$Date,1=>"align='center'"),			
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$CName,1=>"align='center'"),
			array(0=>$Description,3=>"..."),
			array(0=>$Bill,1=>"align='center'"),			
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Provider,3=>"..."),			
			array(0=>$Remark,1=>"align='center'")			
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