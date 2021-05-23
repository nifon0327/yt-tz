<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1
include "../model/modelhead.php";
echo "<SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=600;
$sumCols="5";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 供应商税款待审核列表");
$funFrom="cw_gyssk";
$Th_Col="选项|60|序号|40|请款日期|75|货款月份|60|供应商|80|货币|40|税款金额|60|加税率|50|说明|300|发票号|80|状态|40|请款人|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
		 // BOM、非BOM分类
		 $TempEstateSTR="GysTypeSTR".strval($GysType); 
         $$TempEstateSTR="selected";

		echo"<select name='GysType' id='GysType' onchange='document.form1.submit()'>";
		echo"<option value='' $GysTypeSTR>全  部</option>";
		echo"<option value='1' $GysTypeSTR1>BOM</option>";
		echo"<option value='2' $GysTypeSTR2>非BOM</option>";
		echo"</select>&nbsp;";
		
		switch($GysType){
		   case 1: $SearchRows.=" and S.Remark NOT LIKE '%非BOM%'";break;
		   case 2: $SearchRows.=" and S.Remark  LIKE '%非BOM%'";break;
		   default:break;
		}
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Mid,S.Forshort,S.PayMonth,S.InvoiceNUM,S.InvoiceFile,S.Amount,S.Rate,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol
 	FROM $DataIn.cw2_gyssksheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows AND S.Estate=2 ORDER BY S.Date DESC,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$PayMonth=$myRow["PayMonth"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Rate=$myRow["Rate"];
		if($Rate!=0)
		   {
		   	 $Rate*=100;
		     $Rate=$Rate."%";
		     }
		else{
             $Rate="&nbsp";
		     }
		     
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$InvoiceNUM=$myRow["InvoiceNUM"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
		switch($Estate){
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				break;
			default:
				$Estate="<div align='center' class='redB' title='状态错误'>×</div>";
				$LockRemark="状态错误";
				$Locks=0;
				break;
			}
		if($InvoiceFile==1){
			$InvoiceFile="S".$Id;
			$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			$InvoiceNUM="<span onClick='OpenOrLoad(\"$Dir\",\"$InvoiceFile\",7)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNUM</span>";
			}
		$URL="nonbom6_relation_ajax.php";
		//$URL="test.php";
        $theParam="Id=$Id";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"nonbom\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏关联非BOM采购单.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
						
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$PayMonth,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),				
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Rate,1=>"align='center'"),
			array(0=>$Remark,3=>"..."),
			array(0=>$InvoiceNUM,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
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