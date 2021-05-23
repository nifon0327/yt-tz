<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=600;
ChangeWtitle("$SubCompany 提货单列表");
$funFrom="ch_shipout";
$From=$From==""?"add":$From;
$sumCols="7,8";			//求和列,需处理
$Th_Col="选项|60|序号|40|提货单号|120|提货日期|80|提货单|80|标签|80|Forwarder公司|100|提货数量|60|提货金额|60|备注|220|操作人|70|发货状态|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,26,28,34,36,114";
$nowWebPage=$funFrom."_read";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";	
    $clientResult = mysql_query("
	SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_deliverymain M
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId WHERE 1  GROUP BY M.CompanyId 
	",$link_id);
   if($clientRow = mysql_fetch_array($clientResult)) {
	echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo"<option value='' selected>全部客户</option>";
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
	
	$date_Result = mysql_query("SELECT M.DeliveryDate FROM $DataIn.ch1_deliverymain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.DeliveryDate,'%Y-%m') ORDER BY M.DeliveryDate DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["DeliveryDate"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
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
$mySql="SELECT M.Id, M.DeliveryNumber,M.Remark,M.DeliveryDate,M.Operator ,F.Forshort ,M.Estate
        FROM $DataIn.ch1_deliverymain M
        LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=M.ForwaderId 
        WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d1=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$DeliveryNumber=$myRow["DeliveryNumber"];
		$Forshort=$myRow["Forshort"]; 
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$DeliveryDate=$myRow["DeliveryDate"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $Bill="&nbsp;"; 
		$filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
        if(file_exists($filename)){
			$f1=anmaIn($DeliveryNumber,$SinkOrder,$motherSTR);
			if (strlen($DeliveryNumber)>25){
				$dfname=urldecode("$DeliveryNumber.pdf");
				$Bill="<a href=\"openorload.php?d=$d1&dfname=$dfname&Type=LongFile\" target=\"download\">查看</a>";
			}
			else {
				$Bill="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">查看</a>";
			}
		}
		
		$DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty,SUM(DeliveryQty*Price) AS DeliveryAmount  FROM $DataIn.ch1_deliverysheet WHERE Mid='$Id'",$link_id);

		$DeliveryQty =mysql_result($DeliveryResult,0,"DeliveryQty");
		$DeliveryAmount =mysql_result($DeliveryResult,0,"DeliveryAmount"); 
		$DeliveryAmount =sprintf("%.2f",$DeliveryAmount);
		//检查是否有装箱
		$Packing="<div class='redB'>未装箱</div>";
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch1_deliverypacklist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$Packing="<a href='ch_shipoutlist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			} 
        $Estate=$myRow["Estate"];$EstateStr="";$EstateIMG="";
        switch($Estate){
            case 1:
                $EstateIMG="<span class='greenB'>√</span>";$EstateStr="";
                 break;
            case 2:
                   $EstateStr="onclick='ConfirmOut($Id,this)'";
                    $EstateIMG="<img src='../images/register.png' width='30' height='30'>";
                 break;
               }
		$showPurchaseorder="<img onClick='ShowInVoice(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$DeliveryNumber,1=>"align='center'"),
			array(0=>$DeliveryDate,1=>"align='center'"),
			array(0=>$Bill,1=>"align='center'"),
			array(0=>$Packing,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$DeliveryQty,1=>"align='center'"),
			array(0=>$DeliveryAmount,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$EstateIMG,1=>"align='center'$EstateStr")
			);
		//echo $Id;
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="javascript">

function ShowInVoice(e,f,Order_Rows,Id,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		
		if(Id!=""){			
			var url="../admin/ch_shipout_ajax.php?Id="+Id+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					}
				}
			ajax.send(null); 
			}
		}
}
function ShowOrderHidden(e,f,Cut_Rows,RowId,Id,Mid){  

	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Cut_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Cut_Rows.myProperty=false;
		if(Mid!=""){			
			var url="../admin/ch_shipout_order_ajax.php?&Mid="+Mid;
			//alert(url);
		　	var show=eval("HideDiv"+RowId+Id);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}

function ConfirmOut(Mid,e){
   var  Message="确认发货?"
if(confirm(Message)){
			var url="../admin/ch_shipout_confirm.php?Mid="+Mid; 
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			     ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
                          if(ajax.responseText=="Y"){
                                  document.form1.submit();
                               }
					}
				}
			ajax.send(null); 
        }
}

</script>