<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=500;
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 采购单扣款列表");
$funFrom="cw_cgkk";
$Th_Col="选项|60|序号|30|扣款单号|80|日期|80|供应商|80|总金额|80|扣款原因|300|凭证|40|状态|60|操作人|60";
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="15,17";		
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	
	$SearchRows=" AND M.Estate=1";
	//供应商
	$ProviderResult=mysql_query("SELECT P.CompanyId ,P.Letter,P.Forshort
	                FROM $DataIn.cw15_gyskkmain M
	                LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId
					WHERE 1 $SearchRows
					GROUP BY P.CompanyId ORDER BY P.Letter",$link_id);
	 if($ProviderRow=mysql_fetch_array($ProviderResult)){
	    echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
		echo "<option vaule='' selected>全部</option>";
		 do{
		    $ThisCompanyId=$ProviderRow["CompanyId"];
			$Letter=$ProviderRow["Letter"];
			$Forshort=$ProviderRow["Forshort"];
			if($ThisCompanyId==$CompanyId){
			   echo"<option value='$ThisCompanyId' selected>$Letter"."_"."$Forshort</option>";
			   $SearchRows.=" AND M.CompanyId='$ThisCompanyId'";
			    }
			else{
			    echo"<option value='$ThisCompanyId'>$Letter"."_"."$Forshort</option>";
			    }
		 
		   }while($ProviderRow=mysql_fetch_array($ProviderResult));
	     }
		echo"</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id,M.BillNumber,M.CompanyId,M.Date,M.TotalAmount,M.BillFile,M.Remark,M.Operator,P.Forshort,M.Estate,M.Locks,M.Picture
        FROM $DataIn.cw15_gyskkmain M
        LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId
		WHERE 1  $SearchRows AND exists (SELECT  S.Mid FROM $DataIn.cw15_gyskksheet S WHERE S.Mid=M.Id)";
	//	echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$dc=anmaIn("download/cgkkbill/",$SinkOrder,$motherSTR);
	do{
	    $m=1;
	    $Id=$myRow["Id"];
		$BillNumber=$myRow["BillNumber"];
		$CompanyId=$myRow["CompanyId"];
		$Date=$myRow["Date"];
		$TotalAmount=$myRow["TotalAmount"];
		$FileName=$BillNumber.".pdf";
		$fc=anmaIn($FileName,$SinkOrder,$motherSTR);
		$BillFile=$myRow["BillFile"];
		if($BillFile==1){
		 $BillNumber="<a href=\"../admin/openorload.php?d=$dc&f=$fc&Type=&Action=6\"target=\"download\">$BillNumber</a>";  
		     }

        $PictureView=$myRow["Picture"];
		if($PictureView==1){
		    $PictureFileName=$myRow["BillNumber"].".jpg";
	        $fd=anmaIn($PictureFileName,$SinkOrder,$motherSTR);
		    $PictureView="<a href=\"../admin/openorload.php?d=$dc&f=$fd&Type=&Action=6\" target=\"download\">view</a>";  
		     }
       else $PictureView="";
		$Remark=$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Forshort=$myRow["Forshort"];
		$Estate="<div class=redB>未审核</div>";
		$Locks=$myRow["Locks"];
		$showPurchaseorder="<img onClick='Showcgkk(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$BillNumber,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$TotalAmount,	1=>"align='right'"),
			array(0=>$Remark,			3=>"..."),
			array(0=>$PictureView,1=>"align='center'"),
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
<script language="javascript">
function Showcgkk(e,f,Order_Rows,Id,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(Id!=""){			
			var url="../admin/cw_cgkk_ajax.php?Id="+Id+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
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


</script>