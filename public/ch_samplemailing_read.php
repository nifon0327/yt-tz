<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataPublic.freightdata
$DataIn.ch10_mailaddress
$DataIn.trade_object
$DataPublic.staffmain
$DataIn.ch10_samplepicture
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=19;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 样品寄送列表");
$funFrom="ch_samplemailing";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|所属公司|60|寄件日期|70|快递公司|60|客户|70|目的地|120|提单号码|100|发票|40|样品<br>照片|40|寄送<br>进度|40|件数|40|重量<br>(KG)|40|单价|40|金额|50|经手人|50|签收日期|70|状态|40|备注|40";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8,14,36";
$sumCols="11,12,14";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$TempEstateSTR="EstateSTR".strval($Estate); $$TempEstateSTR="selected";	
	$SearchRows.=$Estate==""?"":" AND S.Estate='$Estate'";
	$date_Result = mysql_query("SELECT S.SendDate 
	FROM $DataIn.ch10_samplemail S 
	WHERE 1 $SearchRows GROUP BY DATE_FORMAT(S.SendDate,'%Y-%m') ORDER BY S.SendDate DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["SendDate"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["SendDate"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((S.SendDate>'$StartDate' and S.SendDate<'$EndDate') OR S.SendDate='$StartDate' OR S.SendDate='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//货运公司
	$clientResult = mysql_query("SELECT S.CompanyId,D.Forshort 
	FROM $DataIn.ch10_samplemail S
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	WHERE 1 $SearchRows GROUP BY S.CompanyId ORDER BY S.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and S.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.Mid,S.DataType,S.CompanyId,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator
,P.Name AS HandledBy,C.Forshort AS Client,D.Forshort AS Freight,I.Website,M.Termini,S.cSign 
FROM $DataIn.ch10_samplemail S
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=D.CompanyId
LEFT JOIN $DataIn.ch10_mailaddress M ON M.Id=S.LinkMan
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.HandledBy
WHERE 1 $SearchRows
ORDER BY S.SendDate DESC,S.Id DESC";

//echo "$mySql";

//$myResult = mysql_query($mySql,$link_id);
$myResult = mysql_query($mySql." $PageSTR",$link_id);

if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SendDate=$myRow["SendDate"];
		$Freight=$myRow["Freight"];
		//$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";		
		$Freight=$myRow["Website"]==""?"$Freight":"<a href='http://$myRow[Website]' target='_blank'>$Freight</a>";	
		$Client=$myRow["Client"];
		$Termini=$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		//提单
		$Lading="../download/expressbill/".$ExpressNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			}
		//发票
		$Invoice="<a href='ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
		//寄样图片处理
		$SamplePicture="&nbsp;";
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Id,Picture FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		if($checkPicture["Id"]!=""){	
             $Picture=$checkPicture["Picture"];
			$f2=anmaIn($Picture,$SinkOrder,$motherSTR);
			$SamplePicture="<span onClick='OpenOrLoad(\"$d\",\"$f2\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		//进度图片
		$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
		if($Schedule==1){
			$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
			$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		$Pieces=$myRow["Pieces"];
		$Weight=$myRow["Weight"];
		$Price=$myRow["Price"];
		$Amount=$myRow["Amount"];
		$HandledBy=$myRow["HandledBy"];		
		$ReceiveDate=$myRow["ReceiveDate"]==""?"&nbsp;":$myRow["ReceiveDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myrow[Remark]' width='18' height='18'>";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
				break;
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Locks=$myRow["Locks"];
	    $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		
		$ValueArray=array(
		    array(0=>$cSign,		1=>"align='center'"),
			array(0=>$SendDate,		1=>"align='center'"),
			array(0=>$Freight),
			array(0=>$Client),
			array(0=>$Termini),
			array(0=>$ExpressNO, 	1=>"align='center'"),
			array(0=>$Invoice,		1=>"align='center'"),
			array(0=>$SamplePicture,1=>"align='center'"),
			array(0=>$Schedule,		1=>"align='center'"),
			array(0=>$Pieces, 		1=>"align='center'"),
			array(0=>$Weight,		1=>"align='right'"),
			array(0=>$Price, 		1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='center'"),
			array(0=>$HandledBy, 	1=>"align='center'"),
			array(0=>$ReceiveDate, 	1=>"align='right'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'")					 
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