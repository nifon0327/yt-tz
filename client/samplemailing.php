<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplepicture
$DataIn.ch10_samplemail S
$DataIn.ch10_mailaddress
$DataPublic.freightdata
$DataIn.trade_object
二合一已更新/图片未验证
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=550;
ChangeWtitle("$SubCompany Sample Mailling List");
$ChooseOut="N";
$Th_Col="Item|50|Date|80|Express Company|110|Destination|100|ExpressNo|100|Invoice|60|Picture|60|Pieces|60|Weight|60|Unit|70|Amount|80";
$Pagination=$Pagination==""?0:$Pagination;
$ActioToS="";
$sumCols="10";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
echo"<input name='sumAmout' type='hidden' id='sumAmount'>";
//步骤4：需处理-条件选项
if ($myCompanyId==1004 || $myCompanyId==1059 || $myCompanyId==1072){  //CEL-A OR CEL-B
	 $SearchRows="and (A.CompanyId='1004' OR A.CompanyId='1059' OR A.CompanyId='1072') ";
	}
else{
	if ($myCompanyId==1081 || $myCompanyId==1002 || $myCompanyId==1080 || $myCompanyId==1065 ) {
		$SearchRows="and A.CompanyId in ('1081','1002','1080','1065')";
	}
	else {
	 $SearchRows="and A.CompanyId='$myCompanyId'";
	}
 }

if($From!="slist"){
	//月份
        /*

		 */
	//$SearchRows=" AND A.CompanyId='$myCompanyId'";
	$date_Result = mysql_query("SELECT S.SendDate 
	FROM $DataIn.ch10_samplemail S 
	LEFT JOIN $DataIn.ch10_mailaddress A ON A.Id=S.LinkMan
	WHERE 1 $SearchRows GROUP BY DATE_FORMAT(S.SendDate,'%Y-%m') ORDER BY S.SendDate DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit();'>";
		do{			
			$dateValue=date("M-Y",strtotime($dateRow["SendDate"]));
			$StartDate=date("Y-m-01",strtotime($dateRow["SendDate"]));
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
	}
//步骤5：
//include "../admin/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
S.Id,S.Mid,S.DataType,S.CompanyId,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,
C.Forshort AS Client,D.Forshort AS Freight,A.Termini 
FROM $DataIn.ch10_samplemail S
LEFT JOIN $DataIn.ch10_mailaddress A ON A.Id=S.LinkMan
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
WHERE 1 $SearchRows 
ORDER BY S.SendDate DESC,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/samplemail",$SinkOrder,$motherSTR);
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SendDate=date("d-M-y",strtotime($myRow["SendDate"]));
		$Freight=$myRow["Freight"];
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
		$Invoice="<a href='../public/ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
		//照片
		$SamplePicture="&nbsp;";
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		if($checkPicture["Id"]!=""){			
			$f2=anmaIn($Id,$SinkOrder,$motherSTR);
			$t=anmaIn("ch10_samplepicture",$SinkOrder,$motherSTR);//数据表
			$SamplePicture="<span onClick='OpenPhotos(\"$d\",\"$f2\",\"$t\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		//进度
		$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
		if($Schedule==1){
			$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
			$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		$Pieces=$myRow["Pieces"];
		$Weight=$myRow["Weight"];
		$Price=$myRow["Price"];
		$Amount=$myRow["Amount"];
		$ReceiveDate=$myRow["ReceiveDate"]==""?"&nbsp;":$myRow["ReceiveDate"];
		$ValueArray=array(
			array(0=>$SendDate,		1=>"align='center'"),
			array(0=>$Freight),
			array(0=>$Termini),
			array(0=>$ExpressNO,	1=>"align='center'"),
			array(0=>$Invoice, 		1=>"align='center'"),
			array(0=>$SamplePicture,1=>"align='center'"),
			array(0=>$Pieces, 		1=>"align='center'"),
			array(0=>$Weight,	 	1=>"align='right'"),
			array(0=>$Price, 		1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,1);
  	}
//步骤7：
echo '</div>';
?>