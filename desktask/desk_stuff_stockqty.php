<?php   
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
$ColsNumber=22;
$tableMenuS=500;
ChangeWtitle("$SubCompany 已下单使用库存的配件");
$Th_Col="选项|50|序号|30|配件ID|45|配件名称|280|图档|30|QC图|40|认证|40|送货</br>楼层|40|历史<br>资料|40|单位|45|订单<br>数量|45|使用<br>库存|45|需购<br>数量|45|增购<br>数量|45|实购<br>数量|45|可用<br>库存|45|最低<br>库存|45";
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	//非查询：供应商
	$providerSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 AND T.mainType<2 and S.Mid=0 and S.StockQty>0 and ( S.addqty>0 OR  S.FactualQty>0) GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit();'>";
		echo "<option value='' selected>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			//$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				    echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				    $SearchRows.=" and S.CompanyId='$thisCompanyId'";
				   }
			else{
				   echo"<option value='$thisCompanyId'>$Forshort</option>";
				 }
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.StuffId,A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.SendFloor,A.TypeId,U.Name AS UnitName
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId  
WHERE 1 $SearchRows AND T.mainType<2  and S.Mid=0 and  S.StockQty>0 and ( S.addqty>0 OR  S.FactualQty>0) GROUP BY A.StuffId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$tempStuffId="";
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$TypeId=$myRow["TypeId"];
        $QCImage="";		//配件QC检验标准图
        include "../model/subprogram/stuffimg_qcfile.php";
        $QCImage=$QCImage==""?"&nbsp;":$QCImage;
		$Gfile=$myRow["Gfile"];
		$tempGfile=$Gfile;  
		$Gstate=$myRow["Gstate"];
		$Picture=$myRow["Picture"];
		include "../model/subprogram/stuffreach_file.php";	//REACH 法规图
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
        $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];        	
		$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
		$oStockQty=$checkKC["oStockQty"];
		$mStockQty=$checkKC["mStockQty"]==0?"&nbsp;":$checkKC["mStockQty"];
	    $oStockQty=zerotospace($oStockQty);
		$checkNum=mysql_query("SELECT S.Price,D.StuffCname,M.Date FROM $DataIn.cg1_stocksheet S
	                          LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	                          LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
	                          WHERE S.StuffId=$StuffId and S.Mid!=0",$link_id);
		if($checkRow=mysql_fetch_array($checkNum)){
		          $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>"; 
		        }
		else{
		         $OrderQtyInfo="&nbsp;";
               }
		if ($mStockQty>0){
			      $mStockColor="title='最低库存:$mStockQty'";
			      $oStockQty="<span style='color:#FF9900;font-weight:bold;'>$oStockQty</span>";
			    }
		else{
			     $mStockColor="";	
			    }
        //该配件正在采购的数量
        $stockResult=mysql_fetch_array(mysql_query("SELECT  
                SUM(S.OrderQty) AS OrderQty,SUM(S.StockQty) AS StockQty,SUM(S.AddQty) AS AddQty, SUM(S.FactualQty) AS FactualQty 
                FROM $DataIn.cg1_stocksheet S  WHERE S.StuffId='$StuffId' AND S.Mid=0 AND S.StockQty>0 and ( S.addqty>0 OR  S.FactualQty>0)",$link_id));
         $OrderQty=$stockResult["OrderQty"];
         $StockQty=$stockResult["StockQty"];
         $AddQty=$stockResult["AddQty"];
	     $FactualQty =$stockResult["FactualQty"];
	     $Qty=$AddQty+$FactualQty;


		$URL="cg1_stock_ajax.php";
         $theParam="StuffId=$StuffId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		 $ValueArray=array(
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile,		1=>"align='center'"),
			array(0=>$QCImage, 	1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$OrderQtyInfo, 1=>"align='center'"),
			array(0=>$UnitName,	 	1=>"align='center'"),
			array(0=>$OrderQty,		1=>"align='right'"),
			array(0=>$StockQty,		1=>"align='right'"),
			array(0=>$FactualQty, 	1=>"align='right'"),
			array(0=>$AddQty, 		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
            array(0=>$oStockQty,	1=>"align='right' $mStockColor"),
			array(0=>$mStockQty, 	1=>"align='right'")
			);
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
