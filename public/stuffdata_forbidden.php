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
//步骤2：需处理
$Keys=31;
$ColsNumber=23;
$tableMenuS=1000;
$sumCols="18,19";			//求和列,需处理
ChangeWtitle("$SubCompany 未下单配件禁用列表");
$funFrom="stuffdata";
$From=$From==""?"forbidden":$From;
$Th_Col="选项|55|序号|40|配件Id|50|配件名称|280|图档|30|图档日期|70|历史<br>订单|40|QC图|40|认证|40|品检</br>方式|40|状态|30|参考价|60|单位|40|配件类型|60|默认供应商|100|采购|50|规格|120|备注|30|在库|60|可用库存|60|送货</br>楼层|40|更新日期|60|最后下单|60|最后出货|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,107,133";//,151
$nowWebPage=$funFrom."_forbidden";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT A.SendFloor,M.Remark
       FROM $DataIn.stuffdata A 
    LEFT JOIN $DataIn.base_mposition  M ON M.Id=A.SendFloor  
      LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId 
     WHERE 1  AND A.Estate>0 AND G.mainType<2 GROUP BY A.SendFloor ",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='SendFloor' id='SendFloor' onchange='ResetPage(this.name)'><option value='' selected>--送货楼层--</option>";
		do{
			$theSendFloor=$myrow["SendFloor"];
			$theRemark=$myrow["Remark"];
			if ($SendFloor==$theSendFloor){
				echo "<option value='$theSendFloor' selected>$theRemark</option>";
				$SearchRows=" AND A.SendFloor='$theSendFloor' ";
				}
			else{
				echo "<option value='$theSendFloor'>$theRemark</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}


    $LastM=$LastM==""?0:$LastM;
  	$TempProfitSTR="LastMStr".strval($LastM); 
	$$TempProfitSTR="selected";
	        echo"<select name='LastM' id='LastM' onchange='ResetPage(this.name)'>";
		   echo"<option value='0' style= 'color:#f60;' $LastMStr0>全部</option>
                  <option value='1' style= 'color:#f60;' $LastMStr1>3个月以上</option>
				  <option value='4' style= 'color:#f00;' $LastMStr4>无产品关联</option>
	             </select>&nbsp;";// <option value='2' style= 'color:#f00;' $LastMStr2>1年以上</option>
	switch($LastM){
		case 0:
			$ShipMonthStr="";$ShipStr="";$ShipFieldStr="";
	     	break;
		case 1:
			$ShipMonthStr=" AND E.Months>2  AND E.Months IS NOT NULL";//AND E.Months<=12
			$ShipMonthStr.=$StockEstate==0?" AND (K.tStockQty>0 OR K.oStockQty>0) ":"";
		     break;
		case 2:
			$ShipMonthStr=" AND E.Months>12 AND E.Months IS NOT NULL";
		      break;
		case 3:
			  $ShipMonthStr=" AND E.Months<5 AND E.Months IS NOT NULL";
		      break;
		case 4:
			 $ShipMonthStr="";$ShipStr="";$ShipFieldStr="";
		      break;	
			  
		  }
        if($LastM>0){
                $ShipFieldStr=",E.Months,E.LastMonth";
                /*
                $ShipStr="LEFT JOIN (
			           SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,G.StuffId 
			           FROM $DataIn.ch1_shipmain M 
		               LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
		               LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                      WHERE 1 GROUP BY G.StuffId ORDER BY M.Date DESC
					) E ON E.StuffId=A.StuffId";
				*/
				//MAX(IF(S.ywOrderDTime='0000-00-00 00:00:00',M.Date,S.ywOrderDTime))
				$ShipStr="LEFT JOIN (
			           SELECT DATE_FORMAT(MAX(IFNULL(YM.OrderDate,M.Date)),'%Y-%m') AS LastMonth,(TIMESTAMPDIFF(MONTH,MAX(IFNULL(YM.OrderDate,M.Date)),now())-IFNULL(O.Overtime,0)) AS Months,S.StuffId  
			           FROM  $DataIn.cg1_stocksheet S 
			           LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
			           LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			           LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber  
		               LEFT JOIN $DataIn.stuffovertime O ON O.StuffId=S.StuffId 
                      WHERE 1 GROUP BY S.StuffId ORDER BY Months DESC 
					) E ON E.StuffId=A.StuffId";
               }
      $StockEstate=$StockEstate==""?0:$StockEstate;
      $StockStr="Stock".$StockEstate;
      $$StockStr="selected";
	        echo"<select name='StockEstate' id='StockEstate' onchange='ResetPage(this.name)'>";
		   echo"<option value='0' $Stock0>全部</option>
                      <option value='1' $Stock1>在库大于0</option>
		             <option value='2'  $Stock4>在库等于0</option>
	                </select>&nbsp;";
             switch($StockEstate){
                    case 0:
                         $SearchRows.=" ";
                          break;
                    case 1:
                         $SearchRows.="  AND K.tStockQty>0";
                          break;
                    case 2:
                         $SearchRows.=" AND K.tStockQty=0";
                          break;
                   }
   $result = mysql_query("SELECT A.TypeId,G.TypeName,G.Letter FROM $DataIn.stuffdata A 
    LEFT JOIN $DataIn.stufftype  G ON G.TypeId=A.TypeId  
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId
     WHERE G.Estate=1 and A.Estate=1 $SearchRows GROUP BY  G.TypeId  order by G.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND A.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}

	}

  echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
  $searchtable="stuffdata|A|StuffCname|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
  include "../model/subprogram/QuickSearch.php";
//}	
  echo "<input name='AcceptText' type='hidden' id='AcceptText' value='$upFlag'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
if($NameRule!=""){
  echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF'><tr ><td height='15' class='A0011' width='$tableWidth' >
       <span style='color:red'>命名规则:</span>$NameRule
	   </td></tr></table>";
  }
$NowYear=date("Y");
$NowMonth=date("m");
$Nowtoday=date("Y-m-d");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
A.Id,A.StuffId,A.StuffCname,A.StuffEname,A.TypeId,A.Gfile,A.Gstate,A.Picture,A.Gremark,A.Estate,A.Price,A.SendFloor,P.Forshort,
M.Name,A.Spec,A.Remark,A.Weight,A.Date,A.GfileDate,A.Operator,A.Locks,A.CheckSign,G.TypeName,U.Name AS UnitName,K.tStockQty,K.oStockQty,O.Overtime $ShipFieldStr
FROM $DataIn.stuffdata A 
LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId 
LEFT JOIN  $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId
LEFT JOIN $DataIn.stuffovertime O ON O.StuffId=A.StuffId  
$ShipStr 
WHERE 1  and A.Estate=1  AND G.mainType<2 $SearchRows  $ShipMonthStr  
ORDER BY K.tStockQty DESC,K.oStockQty DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$cs=0;
		switch($LastM){
			case 4:
				$sListResult = mysql_query("SELECT count(*) as cs  FROM $DataIn.pands  where StuffId='$StuffId' ",$link_id);
				$cs=mysql_result($sListResult,0,"cs");
				//echo "cs:$cs";
				if ($cs>0) {
					continue;
				}
			break;
		}
		if($LastM==4 && $cs>0) {
			continue;
		}
		
        if ($LastM==0) { //
          /*
			$LastMSQL=mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,G.StuffId 
			           FROM $DataIn.ch1_shipmain M 
		               LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
		               LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                       WHERE 1 AND G.StuffId='$StuffId' GROUP BY G.StuffId ORDER BY M.Date DESC",$link_id);
                       
              $LastMSQL=mysql_query("SELECT DATE_FORMAT(MAX(M.OrderDate),'%Y-%m') AS LastMonth,G.StuffId 
			           FROM $DataIn.yw1_ordermain M 
		               LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber  
		               LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                       WHERE 1 AND G.StuffId='$StuffId' GROUP BY G.StuffId ORDER BY M.OrderDate DESC",$link_id);
                       */
                  $LastMSQL=mysql_query("SELECT DATE_FORMAT(MAX(IFNULL(YM.OrderDate,M.Date)),'%Y-%m') AS LastMonth,S.StuffId 
			           FROM  $DataIn.cg1_stocksheet S
			           LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
			           LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			            LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber  
                       WHERE  S.StuffId='$StuffId' GROUP BY S.StuffId ORDER BY LastMonth DESC",$link_id);
			if($LastRow = mysql_fetch_array($LastMSQL)){
				$LastMonth=$LastRow["LastMonth"];
			}
			else {
				$LastMonth="&nbsp;";
			}
          
			
		}
		else {
			$LastMonth=$myRow["LastMonth"];
		}
		$Overtime=$myRow["Overtime"];
		$LastMonth=$Overtime>0?"<span style='color:#0000FF' title='延长使用期限：$Overtime 个月'>$LastMonth</span>":$LastMonth;
		
		$LastChMSQL=mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,G.StuffId 
			           FROM $DataIn.ch1_shipmain M 
		               LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
		               LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                       WHERE 1 AND G.StuffId='$StuffId' GROUP BY G.StuffId ORDER BY M.Date DESC",$link_id);
		if($LastChRow = mysql_fetch_array($LastChMSQL)){
				$LastChMonth=$LastChRow["LastMonth"];
			}
			else {
				$LastChMonth="&nbsp;";
			}
			
		$StuffCname=$myRow["StuffCname"];
		$TypeName=$myRow["TypeName"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Price=$myRow["Price"];
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];		
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Picture=$myRow["Picture"];
                $TypeId=$myRow["TypeId"];
                //配件QC检验标准图
               $QCImage="";
               include "../model/subprogram/stuffimg_qcfile.php";
               $QCImage=$QCImage==""?"&nbsp;":$QCImage;
               
       $CheckSign=$myRow["CheckSign"]==1?"<div style='color:#E00;' >全检</div>":"抽检";
		include "../model/subprogram/stuffreach_file.php";
		
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
				$Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				break;
			}
		
		$Date=substr($myRow["Date"],0,10);
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Forshort=$myRow["Forshort"];
		$Buyer=$myRow["Name"];

		$tStockQty=$myRow["tStockQty"]==0?"&nbsp;":$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"]==0?"&nbsp;":$myRow["oStockQty"];
        
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		$URL="Stuffdata_Gfile_ajax.php";
                $theParam="StuffId=$StuffId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$GfileDate, 	1=>"align='center'"),
            array(0=>$OrderQtyInfo, 1=>"align='center'"),
            array(0=>$QCImage, 	    1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
            array(0=>$CheckSign, 	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			array(0=>$UnitName,		1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$Forshort),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$tStockQty, 	1=>"align='center'"),
			array(0=>$oStockQty, 	1=>"align='center'"),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
           array(0=>$LastMonth, 		1=>"align='center'"),
           array(0=>$LastChMonth, 		1=>"align='center'")
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

<script language="JavaScript" type="text/JavaScript">
<!--
function checkChange(obj){
	var e=document.getElementById("checkAccept");
    if (e.checked){
	  //document.getElementById("AcceptText").value="";
	  document.location.replace("../Admin/stuffdata_read.php");
	}
}
</script>