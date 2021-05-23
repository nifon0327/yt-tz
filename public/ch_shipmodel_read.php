<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 客户出货文档模板");
$funFrom="ch_shipmodel";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|客户|100|模板标题|80|项目名称|80|目的地|160|始发地|160|联系人|80|联系电话|100|Invoice模板|75|标签模板|120|ShipFrom<br>(Pdf)|60|FromFaxNo<br>(Pdf)|60|FromAdress<br>(Pdf)|65|ShipTo<br>(Pdf)|45|FaxNo<br>(Pdf)|45|Adress<br>(Pdf)|45|货运信息|100|PI标识|50|转发标识|50|状态|40|操作员|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort FROM $DataIn.ch8_shipmodel M 
	        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	        WHERE C.Id>0  AND MOD(C.CompanySign,7) = 0  GROUP BY M.CompanyId ORDER BY M.CompanyId ",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT M.Id,M.CompanyId,M.Title,M.Company AS items,M.StartPlace,M.EndPlace,M.Contact,M.TEL,M.SoldFrom,M.FromAddress,M.FromFaxNo,M.SoldTo,M.Address,M.FaxNo,M.Wise,M.PISign,M.OutSign,M.Estate,M.Locks,M.Date,M.Operator,
C.Forshort,I.Company,I.Fax,I.Address AS iAddress,IM.Name AS InvoiceModel,LM.Name AS LabelModel
FROM $DataIn.ch8_shipmodel M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId  AND  I.Type=8
LEFT JOIN $DataIn.sys2_invoicemodel IM ON IM.Id=M.InvoiceModel
LEFT JOIN $DataIn.sys2_labelmodel LM ON LM.Id=M.LabelModel
WHERE 1 $SearchRows  AND MOD(C.CompanySign,7) = 0 
ORDER BY M.Estate DESC,M.CompanyId";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Title=$myRow["Title"];
		$Company=$myRow["items"];
		$EndPlace=trim($myRow["EndPlace"])==""?"&nbsp;":$myRow["EndPlace"];
		$StartPlace=trim($myRow["StartPlace"])==""?"&nbsp;":$myRow["StartPlace"];
		$Contact=$myRow["Contact"];
		$TEL=$myRow["TEL"];

		$InvoiceModel=$myRow["InvoiceModel"];
		$LabelModel=$myRow["LabelModel"];
		
		$SoldFrom=$myRow["SoldFrom"]==""?$myRow["Company"]:$myRow["SoldFrom"];
		$FromAddress=$myRow["FromAddress"]==""?$myRow["iAddress"]:$myRow["FromAddress"];
		$FromFaxNo=$myRow["FromFaxNo"]==""?$myRow["Fax"]:$myRow["FromFaxNo"];
		
		$SoldFrom="<img src='../images/remark.gif' title='$SoldFrom' width='18' height='18'>";
		$FromAddress="<img src='../images/remark.gif' title='$FromAddress' width='18' height='18'>";
		$FromFaxNo="<img src='../images/remark.gif' title='$FromFaxNo' width='18' height='18'>";		
		
		
		$SoldTo=$myRow["SoldTo"]==""?$myRow["Company"]:$myRow["SoldTo"];
		$Address=$myRow["Address"]==""?$myRow["iAddress"]:$myRow["Address"];
		$FaxNo=$myRow["FaxNo"]==""?$myRow["Fax"]:$myRow["FaxNo"];
	
		$SoldTo="<img src='../images/remark.gif' title='$SoldTo' width='18' height='18'>";
		$Address="<img src='../images/remark.gif' title='$Address' width='18' height='18'>";
		$FaxNo="<img src='../images/remark.gif' title='$FaxNo' width='18' height='18'>";
		
		$Date=$myRow["Date"];
		$Job=$myRow["Name"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$PISign=$myRow["PISign"]==1?"<div class='greenB'>默认PI</div>":"&nbsp;";
		$OutSign=$myRow["OutSign"]>0?"<div class='redB'>转发!!</div>":"&nbsp;";
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Forshort."<$Id>"),
			array(0=>$Title),
			array(0=>$Company),
			array(0=>$EndPlace),
			array(0=>$StartPlace),
			array(0=>$Contact),
			array(0=>$TEL),
			array(0=>$InvoiceModel,
					 1=>"align='center'"),
			array(0=>$LabelModel,					
					 1=>"align='center'"),
			array(0=>$SoldFrom,
					 1=>"align='center'"),	
			array(0=>$FromFaxNo,
					 1=>"align='center'"),
			array(0=>$FromAddress,					
					 1=>"align='center'"),			
			array(0=>$SoldTo,
					 1=>"align='center'"),
			array(0=>$FaxNo,
					 1=>"align='center'"),
			array(0=>$Address,					
					 1=>"align='center'"),
			array(0=>$Wise,
					 1=>"align='left'"),						
			array(0=>$PISign,
					 1=>"align='center'"),		
			array(0=>$OutSign,
					 1=>"align='center'"),				
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
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
