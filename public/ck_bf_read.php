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
<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 报废记录");
$funFrom="ck_bf";
$nowWebPage=$funFrom."_read";
$sumCols="5,6,7,10";			//求和列,需处理
$Th_Col="选项|40|序号|40|报废日期|70|配件|45|配件名称|250|历史<br>订单|40|在库|60|可用库存|60|报废数量|60|单价|50|单位|40|小计|60|库位|80|单据|50|报废原因|200|处理结果|200|分类|80|状态|40|操作|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="1,2,3,4,7,8,173";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
$SearchRows.= " AND F.OutSign=1";
if($From!="slist"){
	$date_Result = mysql_query("SELECT F.Date FROM $DataIn.ck8_bfsheet  F 
	WHERE 1  $SearchRows GROUP BY DATE_FORMAT(F.Date,'%Y-%m') ORDER BY F.Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo  "<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND  DATE_FORMAT(F.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo  "<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
    //配件分类
    	$result = mysql_query("SELECT D.TypeId,T.Letter,T.TypeName 
    	FROM $DataIn.ck8_bfsheet F
        LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
        LEFT JOIN  $DataIn.stufftype  T ON T.TypeId=D.TypeId 
        WHERE 1  $SearchRows Group by D.TypeId order by T.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>配件类型</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND D.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}

		//操作员
	  $Operator_result = mysql_query("SELECT F.Operator,M.Name 
      FROM $DataIn.ck8_bfsheet F
      LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
      LEFT JOIN $DataIn.staffmain  M ON M.Number=F.Operator  
      WHERE 1  $SearchRows Group by F.Operator order by F.Operator",$link_id);
	  if($myrow = mysql_fetch_array($Operator_result)){
	  echo"<select name='Number' id='Number' onchange='ResetPage(this.name)'><option value='' selected>操作员</option>";
	  $NameRule="";
		do{
			$theId=$myrow["Operator"];
			$theName=$myrow["Name"];
			if ($Number==$theId){
				echo "<option value='$theId' selected>$theName</option>";
				$SearchRows.=" AND F.Operator='$theId' ";
				}
			else{
				echo "<option value='$theId'>$theName</option>";
				}
			}while ($myrow = mysql_fetch_array($Operator_result));
			echo "</select>&nbsp;";
		}
		
	//分类

	$Type_Result = mysql_query("SELECT F.Type,C.TypeName,C.TypeColor FROM $DataIn.ck8_bfsheet F
							   LEFT JOIN  $DataIn.ck8_bftype  C ON C.Id=F.Type 
							   WHERE 1  $SearchRows GROUP BY F.Type ORDER BY F.Type DESC",$link_id);
	if ($TypeRow = mysql_fetch_array($Type_Result)) {
		echo"<select name='chooseType' id='chooseType' onchange='ResetPage(this.name)'>";
		echo "<option value='' selected>报废分类</option>";
		do{
			$TypeValue=$TypeRow["Type"];
			$TypeName=$TypeRow["TypeName"];
			$TypeColor=$TypeRow["TypeColor"];
			if($chooseType==$TypeValue){
				echo  "<option value='$TypeValue'  selected>$TypeName</option>";
				$SearchRows.="AND F.Type='$TypeValue'";
				}
			else{
				echo  "<option value='$TypeValue' >$TypeName</option>";					
				}
			}while($TypeRow = mysql_fetch_array($Type_Result));
		echo"</select>&nbsp;";
		}

	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
SELECT F.Id,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,F.Estate,F.Locks,F.Operator,D.StuffCname,K.tStockQty,K.oStockQty,D.Price,D.Price*F.Qty AS Amount,D.Picture,U.Name AS UnitName,C.TypeName,C.TypeColor ,F.Bill,F.DealResult,L.Identifier AS LocationName
FROM $DataIn.ck8_bfsheet F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
LEFT JOIN $DataIn.ck8_bftype  C ON C.id=F.Type 
LEFT JOIN $DataIn.ck_location L ON L.Id = F.LocationId
WHERE 1 $SearchRows ORDER BY F.Id DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$SumQty=0;
$SumAmount=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.4f",$myRow["Amount"]);
		$SumQty+=$Qty;
		$SumAmount+=$Amount;
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];	
		$LocationName=$myRow["LocationName"]==""?"&nbsp;":$myRow["LocationName"];	
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1": $Estate = "<div class='redB'>未核</div>"; break;
			case "0": $Estate = "<div class='greenB'>已核</div>"; break;
			case "2": $Estate = "<div class='blueB'>退回</div>"; break;
		}
		$Operator=$myRow["Operator"];
		$Picture=$myRow["Picture"];
		include "../model/subprogram/staffname.php";
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性
		$Locks=$myRow["Locks"];
		$Type=$myRow["Type"];
		$TypeName=$myRow["TypeName"];
		$TypeColor =$myRow["TypeColor"];
		$TypeName="<span style=\"color:$TypeColor \">$TypeName</span>";
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
        $DealResult=$myRow["DealResult"]==""?"&nbsp;":$myRow["DealResult"];

        $Bill=$myRow["Bill"];
		$Dir=anmaIn("download/ckbf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="B".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
			

		if($myRow["Estate"] >0){
	        if($Qty <= $tStockQty && $Qty<=$oStockQty ){
		        $Qty = "<span class='greenB'>$Qty</span>";
	        }else{
		        $Qty = "<span class='redB'>$Qty</span>";
	        }
	     }else{
	        $Qty = "<span class='greenB'>$Qty</span>";
	     }
		$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$OrderQtyInfo,		1=>"align='center'"),
			array(0=>$tStockQty,	1=>"align='right'"),
			array(0=>$oStockQty, 	1=>"align='right'"),
			array(0=>$Qty,			1=>"align='right'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$UnitName,		1=>"align='center'"),		
			array(0=>$Amount,		1=>"align='right'"),
			array(0=>$LocationName,	1=>"align='center'"),
			array(0=>$Bill,		1=>"align='center'"),
			array(0=>$Remark,		3=>"..."),
			array(0=>$DealResult,2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,15,$StuffId,1,$Id)' style='CURSOR: pointer'"),
			array(0=>$TypeName,	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Operator, 	1=>"align='center'")
				);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
  
	   $m=1;
		$ValueArray=array(
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"<div >".$SumQty."</div>", 1=>"align='right'"),
			array(0=>"<div >"."&nbsp;"."</div>", 1=>"align='right'"),
			array(0=>"&nbsp;"	),
			array(0=>"<div >".$SumAmount."</div>", 1=>"align='right'"),
			array(0=>"&nbsp;"	),
			array(0=>"<div >"."&nbsp;"."</div>", 1=>"align='right'"),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
				);
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";			
		
		
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

function updateJq(TableId,RowId,runningNum,toObj,sId){//行即表格序号;列，流水号，更新源
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;

		var InfoSTR="配件ID为:<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='8' class='TM0000' readonly>的最终处理结果:<textarea name='DealResult' id='DealResult' cols='50' rows='5' ></textarea>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更 新' onclick='aiaxUpdate("+sId+")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取 消' onclick='CloseDiv()'>";
		
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
}

function aiaxUpdate(sId){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;

	var tempDealResult0=document.form1.DealResult.value;
	var tempDealResult1=encodeURIComponent(tempDealResult0);
	var myurl="ck_bf_updated.php?StuffId="+temprunningNum+"&DealResult="+tempDealResult1+"&ActionId=167"+"&Id="+sId;
	//alert (myurl); 
	var ajax=InitAjax(); 
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
	  if(ajax.readyState==4){// && ajax.status ==200
	    if(tempDealResult0.length>0)eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML=tempDealResult0;
		else eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempDealResult0+"</NOBR></DIV>";
		CloseDiv();
	  }
	}
	ajax.send(null); 			
}
</script>