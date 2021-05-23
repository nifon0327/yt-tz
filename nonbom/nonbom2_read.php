<?php 
//非BOM配件子分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$tableMenuS=600;
ChangeWtitle("$SubCompany 非BOM配件子分类");
$funFrom="nonbom2";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|会计科目|100|主分类|80|分类名称|100|说 明|300|命名规则|200|默认购买供应商|150|采购|60|申购终审|60|送货地点|60|款项是否<BR>收回|60|可用|40|操作员|60";//公司标识|60|
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	//采购选择
	$checkResult = mysql_query("SELECT A.BuyerId,B.Name FROM $DataPublic.nonbom2_subtype A 
LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId GROUP BY A.BuyerId ORDER BY B.Name",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部采购</option>";
		do{			
			$BuyerIdTemp=$checkRow["BuyerId"];
			$BuyerIdName=$checkRow["Name"];
			if($BuyerId==$BuyerIdTemp){
				echo"<option value='$BuyerIdTemp' selected>$BuyerIdName</option>";
				$SearchRows.=" AND A.BuyerId='$BuyerIdTemp'";
				}
			else{
				echo"<option value='$BuyerIdTemp'>$BuyerIdName</option>";					
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	//主分类选择
	$checkResult = mysql_query("SELECT  A.mainType,B.Name FROM $DataPublic.nonbom2_subtype A 
LEFT JOIN $DataPublic.nonbom1_maintype B  ON B.Id=A.mainType 
WHERE 1 $SearchRows GROUP BY A.mainType ORDER BY A.mainType,B.Name",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)){
		echo "<select name='mainType' id='mainType' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部主分类</option>";
		do{
			$mainTypeTemp=$checkRow["mainType"];
			$NameTemp=$checkRow["Name"];
			if($mainType==$mainTypeTemp){
				echo"<option value='$mainTypeTemp' selected>$NameTemp </option>";
				$SearchRows.=" AND A.mainType='$mainTypeTemp'";
				}
			else{
				echo"<option value='$mainTypeTemp'>$NameTemp</option>";
				}
			}while ($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.cSign,A.TypeName,A.Remark,A.NameRule,A.Estate,A.Locks,A.Operator,B.Name AS mainType,C.Name AS Buyer,D.Name AS Checker,K.Name AS SendFloor,A.GetSign,F.ListName AS FirstName 
FROM $DataPublic.nonbom2_subtype A 
LEFT JOIN $DataPublic.nonbom1_maintype B ON B.Id=A.mainType
LEFT JOIN $DataPublic.staffmain C ON C.Number=A.BuyerId
LEFT JOIN $DataPublic.staffmain D ON D.Number=A.CheckerId
LEFT JOIN $DataPublic.nonbom0_ck K  ON K.Id=A.SendFloor
LEFT JOIN $DataPublic.acfirsttype F ON F.FirstId=A.FirstId 
WHERE 1 $SearchRows ORDER BY A.Estate DESC,B.Id,A.TypeName";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$SharingShow="Y";//显示共享
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Buyer=$myRow["Buyer"];
		$Checker=$myRow["Checker"];
		$Name=$myRow["TypeName"];
		$mainType=$myRow["mainType"];        
		$SendFloor=$myRow["SendFloor"]==""?"&nbsp;":$myRow["SendFloor"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$NameRule=$myRow["NameRule"]==""?"&nbsp;":$myRow["NameRule"];
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		$FirstName=$myRow["FirstName"]==""?"&nbsp;":$myRow["FirstName"];
		
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";

        $CompanyStr="";
          $CheckCompanyResult=mysql_query("SELECT  T.Forshort,T.CompanyId  FROM $DataPublic.nonbom3_link  L  
      LEFT JOIN $DataPublic.nonbom3_retailermain  T  ON T.CompanyId=L.CompanyId  WHERE L.TypeId=$Id ",$link_id);
        while($CheckCompanyRow=mysql_fetch_array($CheckCompanyResult)){
              $Forshort=$CheckCompanyRow["Forshort"];
              $CompanyId=$CheckCompanyRow["CompanyId"];
              if($CompanyStr=="")$CompanyStr=$CompanyId."----".$Forshort;
              else $CompanyStr=$CompanyStr."|".$CompanyId."----".$Forshort;
              }
             $CompanyStr="<DIV STYLE='width:150px;overflow: hidden; text-overflow:ellipsis' title='$CompanyStr'><NOBR>$CompanyStr</NOBR></DIV>";

    $GetSign=$myRow["GetSign"];
     $GetSign=$GetSign==1?"<SPAN class='redB'>YES</SPAN>":"<SPAN class='blueB'>NO</SPAN>";
		$ValueArray=array(
           // array(0=>$cSign,	1=>"align='center'"),
			array(0=>$FirstName),
			array(0=>$mainType),
			array(0=>$Name),
			array(0=>$Remark),
			array(0=>$NameRule),
			array(0=>$CompanyStr),
			array(0=>$Buyer,	1=>"align='center'"),
			array(0=>$Checker,	1=>"align='center'"),
			array(0=>$SendFloor,	1=>"align='center'"),
			array(0=>$GetSign,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
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