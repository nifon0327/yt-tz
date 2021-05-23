<?php 
/*电信---yang 20120801
$DataPublic.my3_express
$DataPublic.my3_exadd
$DataPublic.freightdata
$DataPublic.staffmain
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//读取记录是否已经寄出
$checkSql="SELECT E.Id,E.SendDate,E.BillNumber,E.CompanyId,E.ShipType,E.PayType,E.Contents,E.Pieces,E.Length,E.Width,E.Height,E.cWeight,E.dWeight,E.Amount,E.CFSAmount,E.Remark,E.Estate,E.Date,
A.Name AS Receiver,A.Company AS Company,E.PayerNo,A.Address,A.Zip,A.Country,A.Tel,A.Mobile,F.Forshort,M.Name AS HandledBy,M1.Nickname,M1.Name AS Shipper
FROM $DataPublic.my3_express E
LEFT JOIN $DataPublic.my3_exadd A ON A.Id=E.Receiver 
LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=E.CompanyId
LEFT JOIN $DataPublic.staffmain M ON M.Number=E.HandledBy
LEFT JOIN $DataPublic.staffmain M1 ON M1.Number=E.Shipper
WHERE 1 AND E.Id='$Id' order by A.ID DESC LIMIT 1";
$myResult = mysql_query($checkSql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Estate=$myRow["Estate"];
	if($Estate==1){	//未寄出
		
		}
	else{			//已寄出
		$BillNumber=$myRow["BillNumber"];
		$ShipType=$myRow["ShipType"];
		$Nickname=$myRow["Nickname"];
		$Shipper=$myRow["Shipper"];
		$HandledBy=$myRow["HandledBy"];
		$SendDate=$myRow["SendDate"];
		$Year=date("y",strtotime($SendDate));
		$Month=date("m",strtotime($SendDate));
		$Day=date("d",strtotime($SendDate));
		$CompanyId=$myRow["CompanyId"];
		$Contents=$myRow["Contents"];//物品说明
		$Pieces=$myRow["Pieces"];//件数
		$dWeight =$myRow["dWeight"];//实际重量
		$cWeight =$myRow["cWeight"]==0?"":$myRow["cWeight"];//体积重量
		//尺寸
		$Length =$myRow["Length"]==0?"":$myRow["Length"];
		$Width =$myRow["Width"]==0?"":$myRow["Width"];
		$Height =$myRow["Height"]==0?"":$myRow["Height"];
		//备注
		$Remark=$myRow["Remark"];
		//收件资料
		$Receiver=$myRow["Receiver"];
		$Company=$myRow["Company"];
		$Country=$myRow["Country"];
		$Zip=$myRow["Zip"];
		$Address=$myRow["Address"];
		$Tel=$myRow["Tel"];
		$Mobile=$myRow["Mobile"];
		$PayerNo=$myRow["PayerNo"];
		$Tel=$Tel==""?($Mobile==""?"&nbsp;":$Mobile):$Tel;
		//到付0/寄付1
		$PayType=$myRow["PayType"];
		//现金还是月结
		//运费
		$Amount=$myRow["Amount"]==0?"":$myRow["Amount"];
		$CFSAmount=$myRow["CFSAmount"]==0?"":$myRow["CFSAmount"];
		include "../model/subprogram/mycompany_info.php";
		include "expressmodel/exmodel".$CompanyId.".php";
		}
	}
?>