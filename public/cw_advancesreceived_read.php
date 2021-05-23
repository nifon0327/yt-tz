<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw6_advancesreceived
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
$sumCols="4,7";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 预收货款列表");
$funFrom="cw_advancesreceived";
$Th_Col="选项|40|序号|40|客户|80|预收说明|400|预收金额|60|货币|40|结付银行|100|转RMB|80|凭证|40|抵付<br>状态|40|收款日期|75|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付
$TempEstateSTR="EstateSTR".strval($Estate); 
$$TempEstateSTR="selected";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){	
	$SearchRows=$Estate==""?"":"and S.Estate=$Estate";
	$monthResult = mysql_query("SELECT S.PayDate FROM $DataIn.cw6_advancesreceived S group by DATE_FORMAT(S.PayDate,'%Y-%m') order by S.PayDate DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
			$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(S.PayDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.Mid,S.CompanyId,S.Amount,S.Remark,S.PayDate,S.Locks,S.Operator,P.Forshort,C.Symbol,C.Rate,B.Title,S.Attached
FROM $DataIn.cw6_advancesreceived S 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=S.BankId
WHERE 1 $SearchRows order by S.PayDate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d1=anmaIn("download/cwadvance/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$BankName=$myRow["Title"];
		$Rate=$myRow["Rate"];
		$RmbAmount=sprintf("%.2f",$Amount*$Rate);
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$PayDate=$myRow["PayDate"];
		$Locks=$myRow["Locks"];		
		$Mid=$myRow["Mid"];			
		if($Mid==0){
			$Estate="<div align='center' class='redB' title='未抵付'>×</div>";
			$LockRemark="";
			}
		else{
			$Estate="<div align='center' class='greenB' title='已抵付'>√</div>";
			$LockRemark="记录已经抵付，强制锁定！";
			$Locks=0;
			}
        $Attached=$myRow["Attached"];	
        if($Attached!=""){
	        $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
		    $PictureView="<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">view</a>";  
          }
        else $PictureView="&nbsp;";
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Forshort,		1=>"align='center'"),
			array(0=>$Remark,		3=>"..."),
			array(0=>$Amount,		1=>"align='center'"),
			array(0=>$Symbol,		1=>"align='center'"),
			array(0=>$BankName),
			array(0=>$RmbAmount,	1=>"align='center'"), 
			array(0=>$PictureView,		1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$PayDate,			1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
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