<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?
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
$ColsNumber=16;
$tableMenuS=1000;
ChangeWtitle("$SubCompany 采购交期统计");
$funFrom="desk_deliverydate";
//$From=$From==""?"deliverydate":$From;
$Th_Col="选项|30|序号|40|供应商|80|配件ID|50|配件名称|280|图档|30|单价|50|单位|40|采购数量|60|未收数量|60|采购日期|70|预定交期|70|天数|40|需求流水号|90|历史订单|50|采购员|40|备注|200";

$sumCols="8,9";
		
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 200;
//$ActioToS="1";
$nowWebPage=$funFrom."_unuse";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
  	$TempDiffDateSTR="DiffDateStr".strval($DiffDate); 
	$$TempDiffDateSTR="selected";
	echo"<select name='DiffDate' id='DiffDate' onchange='ResetPage(this.name)'>";
	echo"<option value='' $DiffDateStr>全部</option>
	    <option value='1' $DiffDateStr1>20天以上</option>
		<option value='2' $DiffDateStr2>10～20天</option>
		<option value='3' $DiffDateStr3>0～10天</option>
	</select>&nbsp;";
	
	$curDate=date("Y-m-d");
	switch($DiffDate){
	     case 1:
	          $SearchRows="   AND  DATEDIFF('$curDate',S.DeliveryDate)>20 "; 
	        break;
		  case 2:
		     $SearchRows=" AND  DATEDIFF('$curDate',S.DeliveryDate)>10 AND  DATEDIFF('$curDate',S.DeliveryDate)<=20  "; 
		   break;
		  case 3:
		     $SearchRows=" AND  DATEDIFF('$curDate',S.DeliveryDate)<=10 "; 
			break;
		 default:
		    $SearchRows=""; 
    }    
    
    if ($_SESSION["Login_GroupId"]==401 && $_SESSION["Login_P_Number"]!="10007" && $_SESSION["Login_P_Number"]!="10341")
	{
	   $SearchRows.=" AND S.BuyerId='" .$_SESSION["Login_P_Number"] . "' ";
	}

     $SearchRows.=" AND S.DeliveryDate<'$curDate' ";
    //供应商
	$providerSql= mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
	   FROM $DataIn.cg1_stocksheet S
	   LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id 
	  LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	  WHERE 1  AND S.rkSign>0 AND S.Mid>0  $SearchRows 
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId) 	GROUP BY M.CompanyId",$link_id);
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

  echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页  </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
}	
  echo "<input name='AcceptText' type='hidden' id='AcceptText' value='$upFlag'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$NowYear=date("Y");
$NowMonth=date("m");
$Nowtoday=date("Y-m-d");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT M.Id,M.Date,DATEDIFF('$Nowtoday',S.DeliveryDate) AS Days,S.StockId,S.POrderId,S.StuffId,S.Price,U.Name AS UnitName,(S.AddQty+S.FactualQty) AS cgQty,D.StuffCname,D.Gfile,D.Picture,P.Forshort,A.Name AS Operator,S.DeliveryDate    
        FROM $DataIn.cg1_stocksheet S
        LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id 
        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	    LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
        LEFT JOIN $DataPublic.staffmain A ON A.Number=B.BuyerId 
    	LEFT JOIN  $DataPublic.stuffunit U ON U.Id=D.Unit
	   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
        WHERE 1  AND S.rkSign>0 AND S.Mid>0  AND D.Estate=1 $SearchRows 
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId) 
         ORDER BY Days DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Mid=$myRow["Id"];
		$Date=$myRow["Date"];
		$POrderId=$myRow["POrderId"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$cgQty=$myRow["cgQty"];
		$Forshort=$myRow["Forshort"];
		$DeliveryDate=$myRow["DeliveryDate"];
		$Operator=$myRow["Operator"];
		$StockId=$myRow["StockId"];
		$Picture=$myRow["Picture"];
		$Days=$myRow["Days"];
		
		$checkRemark=mysql_fetch_array(mysql_query("SELECT Remark FROM $DataIn.cg_remark WHERE StockId='$StockId' order by Id DESC LIMIT 1",$link_id));
		$Remark=$checkRemark["Remark"]==""?"<img src='../images/edit.gif' title='添加备注信息' width='16' height='16'>":"<img src='../images/edit.gif' title='更新备注信息' width='16' height='16'>&nbsp;" . $checkRemark["Remark"];
		
		include "../model/subprogram/stuffimg_model.php";
		$Gfile=$myRow["Gfile"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		/*
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>查看</a>";
		*/
		  //历史订单
         $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		//收货数量计算
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$unQty=$cgQty-$rkQty;

 //按天数加颜色背景
       $BackImg="";
       if ($Days>20){
			$BackImg="background='../images/daysred.gif'";
		}
		else{
			if ($Days>10){
			        $BackImg="background='../images/daysyellow.gif'";
		    }
		}
		$ValueArray=array(
			array(0=>$Forshort, 		1=>"align='center' height='30'"),
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			array(0=>$UnitName,		1=>"align='center'"),
			array(0=>$cgQty,		1=>"align='right'"),
			array(0=>$unQty,		1=>"align='right'"),
			array(0=>$Date, 		1=>"align='center' "),
			array(0=>$DeliveryDate, 		1=>"align='center' "),
			array(0=>$Days ."天",		1=>"align='center' $BackImg"),
			array(0=>$StockId, 		1=>"align='center'"),
			array(0=>$OrderQtyInfo, 	1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'"),
			array(0=>$Remark,		1=>"onclick='updateJq($i,16,$StockId)' style='CURSOR: pointer'")
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

<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script> 
<script>
function addRemark(e,StockId)
{
  var remark=prompt("请输入备注信息:",""); 
    if (remark!=null &&  remark!="") {
        var url="desk_deliverydate_ajax.php?StockId="+StockId+"&Remark="+encodeURI(remark)+"&ActionId=AddRemark"; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
			     e.innerHTML="<img src='../images/edit.gif' width='16' height='16'>&nbsp;"+remark;
				}
			 else{
			    alert ("添加备注失败！"); 
			  }
			}
		 }
	   ajax.send(null); 
       }
}

function updateJq(TableId,RowId,runningNum){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;//表格名称
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='18' class='TM0000' readonly>的备注信息:<input name='newRemark' type='text' id='newRemark' size='50' class='INPUT0100'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+RowId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {
		   theDiv.filters.revealTrans.apply();//防止错误
		   theDiv.filters.revealTrans.play(); //播放
		 }
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) {
		theDiv.filters.revealTrans.apply();
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	}

function aiaxUpdate(RowId){
	var tempTableId="ListTable"+document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var remark=document.form1.newRemark.value;
	
	myurl="desk_deliverydate_ajax.php?StockId="+temprunningNum+"&Remark="+encodeURI(remark)+"&ActionId=AddRemark"; 
	var ajax=InitAjax(); 
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
	　		 	if(ajax.readyState==4){// && ajax.status ==200
				  //更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
               if(ajax.responseText=="Y"){//更新成功
			        eval(tempTableId).rows[0].cells[RowId].innerHTML="<img src='../images/edit.gif' width='16' height='16'>&nbsp;"+remark;
				}
			 else{
			    alert ("添加备注失败！"); 
			  }
			CloseDiv();
		}
	}
	ajax.send(null); 
	}
</script>