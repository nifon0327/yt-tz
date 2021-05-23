<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS=500;
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 预付订金列表");
$funFrom="nonbom11";
$Th_Col="选项|40|序号|40|供应商|80|采购单号|60|预付说明|400|凭证|60|货币|40|预付金额|60|记录状态|60|抵付状态|60|请款人|50|请款日期|75";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//$ActioToS="1,2,3,14,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付
$ActioToS="";
$TempEstateSTR="EstateSTR".strval($Estate); 
$$TempEstateSTR="selected";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){	
	$SearchRows=$Estate==""?"":"AND A.Estate=$Estate";
	$monthResult = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m') AS Month FROM $DataIn.nonbom11_djsheet GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$thisMonth=$monthRow["Month"];
			if($chooseMonth==$thisMonth){
				echo"<option value='$thisMonth' selected>$thisMonth</option>";
				$SearchRows.=" AND DATE_FORMAT(A.Date,'%Y-%m')='$thisMonth'";
				}
			else{
				echo"<option value='$thisMonth'>$thisMonth</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	//结付状态
	$EstateResult = mysql_query("SELECT A.Estate FROM $DataIn.nonbom11_djsheet A WHERE 1 $SearchRows GROUP BY A.Estate ORDER BY A.Estate DESC",$link_id);
	if($EstateRow = mysql_fetch_array($EstateResult)) {
		echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>";
		echo"<option value='' $EstateSTR>全  部</option>";
		do{
			$Estate=$EstateRow["Estate"];
			switch($Estate){
				case "0":
					echo"<option value='0' $EstateSTR0>已结付</option>";
				break;
				case "1":
					echo"<option value='1' $EstateSTR1>未处理</option>";
				break;
				case "2":
					echo"<option value='2' $EstateSTR2>请款中</option>";
				break;
				case "3":
					echo"<option value='3' $EstateSTR3>请款通过</option>";
				break;
				case "4":
					echo"<option value='4' $EstateSTR4>退回</option>";
				break;
				}
			}while($EstateRow = mysql_fetch_array($EstateResult));
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
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Mid,A.Did,A.CompanyId,A.PurchaseID,A.Amount,A.Remark,A.ReturnReasons,A.Date,A.Estate,A.Locks,A.Operator,A.ContractFile,
		B.Id AS cgMid,B.Date AS cgDate,
		C.Forshort,D.Symbol
 	FROM $DataIn.nonbom11_djsheet A 
	LEFT JOIN $DataIn.nonbom6_cgmain B ON B.PurchaseID=A.PurchaseID 
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
      $Dir=anmaIn("download/nonbomht/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		//加密
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		$PurchaseID=$myRow["PurchaseID"];
		$cgDate=$myRow["cgDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Amount=$myRow["Amount"];
		$Symbol=$myRow["Symbol"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
		$cgMid=$myRow["cgMid"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
		switch($Estate){
				case "1":
					$Estate="<div align='center' class='redB'>未处理</div>";
					$LockRemark="";
					break;
				case "2":
					$Estate="<div align='center' class='yellowB'>请款中</div>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "3":
					$Estate="<div align='center' class='yellowB'>待结付</div>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "4":
					$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    	$Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
					$LockRemark="";
					break;
					break;
				case "0":
					$Estate="<div align='center' class='greenB'>已结付</div>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}
		$Did=$myRow["Did"];
		if($Did==0){
			$Did="<span class='redB'>未抵付</span>";
			}
		else{
			//连接至抵付的结付单？？
			$Did="<a href='nonbom6_cwview.php?d=$Did' target='_blank'><span class='greenB'>已抵付-$Did</span></a>";
			$LockRemark="记录已经抵付，锁定操作！";
			
			}
		//财务强制锁:非未处理皆锁定
		//传PDF
		$ContractFile=$myRow["ContractFile"];
		if($ContractFile==1){
			$ContractFile="C".$Id.".jpg";
			//$Dir=anmaIn("download/nonbomht/",$SinkOrder,$motherSTR);
			$ContractFile=anmaIn($ContractFile,$SinkOrder,$motherSTR);
			//$InvoiceNUM="<span onClick='OpenOrLoad(\"$Dir\",\"$InvoiceFile\",7)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNUM</span>";
			$ContractNum="<a href=\"../admin/openorload.php?d=$Dir&f=$ContractFile&Type=&Action=6\" target=\"download\">View</a>&nbsp;&nbsp;&nbsp;
			 <A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' title='重新上传' width='12' height='12'>
							</A>
			";
			}
		 else{
		             // $Getdate="&nbsp;";
                        $ContractNum="<A onfocus=this.blur();  onclick='ActionToUpFile($Id)' style='CURSOR: pointer;color:#FF6633'> 
							<img src='../images/upFile.gif' style='background:#F00' title='上传凭证' width='15' height='15'>
							</A>";
             }
			 
		 //$ContractNum="&nbsp;";	 
			 
		$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$PurchaseID,1=>"align='center'"),				
			array(0=>$Remark,3=>"..."),
			array(0=>$ContractNum,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Amount,1=> "align='right'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Did,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'")
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

<script language="JavaScript" type="text/JavaScript">
function ActionToUpFile(upId){
	var funFrom=document.form1.funFrom.value;
        document.form1.action=funFrom+"_upfile.php?ActionId=84&Id="+upId;
        document.form1.target="_self";
        document.form1.submit();		
        document.form1.target="_self";
        document.form1.action="";
}

</script>