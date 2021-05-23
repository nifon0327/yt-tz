<script>
function ShowOrHideFixed(e,f,Order_Rows,OutId,RowId){
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
		if(OutId!=""){			
			var url="../nonbom/nonbom8_ajax.php?OutId="+OutId+"&RowId="+RowId; 
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
<?php 
//EWEN 2013-02-27 OK
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
ChangeWtitle("$SubCompany 非bom配件个人领用明细");
$funFrom="nonbom8_single";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|30|申领人|60|申领日期|90|使用地点|50|编码|50|非bom配件名称|350|申领数量|60|单位|40|申领备注|300|发放日期|90|发放人|60|状态|60|确认|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$sumCols="10";			//求和列,需处理
$nowWebPage=$funFrom."_read";
  $ActioToS="1,2,3,4";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全部</option>
	<option value='2' $EstateSTR2>待审核</option>
	<option value='3' $EstateSTR3>审核退回</option>
	<option value='1' $EstateSTR1>已审核</option>
	<option value='0' $EstateSTR0>已发放</option>
	</select>&nbsp;";
	if($Estate!=""){
		$SearchRows=" AND A.Estate='$Estate'";
		$ActioToS.=$Estate==0?",156":"";
		}
	//月份查询
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.WorkAdd,A.Qty,A.Remark,A.ReturnReasons,A.OutDate,A.Estate,A.Locks,A.Date,A.Operator,A.Confirm,
B.GoodsName,B.BarCode,B.Attached,B.Unit,C.TypeName,D.wStockQty,D.oStockQty,D.mStockQty,E.Name AS WorkAdd,F.Name AS OutOperator,G.Name AS GetName
FROM $DataIn.nonbom8_outsheet A
LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom0_ck  E ON E.Id=A.WorkAdd AND E.TypeId IN (0,2)
LEFT JOIN $DataPublic.staffmain F ON F.Number=A.OutOperator
LEFT JOIN $DataPublic.staffmain G ON G.Number=A.GetNumber
WHERE 1 $SearchRows  AND A.GetNumber='$Login_P_Number' ORDER BY A.Date DESC,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$OutOperator="<span class='redB'>未发放</span>";
		//$OutDate="<span class='redB'>未发放</span>";
       $OutDate=$myRow["OutDate"]=="0000-00-00 00:00:00"?"&nbsp;":$myRow["OutDate"];
		$TypeName=$myRow["TypeName"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"];
		$Unit=$myRow["Unit"];
		
		$Qty=del0($myRow["Qty"]);
		$QtySum+=$Qty;
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		        include"../model/subprogram/good_Property.php";//非BOM配件属性	
		$wStockQty=del0($myRow["wStockQty"]);
		$oStockQty=del0($myRow["oStockQty"]);
		$mStockQty=del0($myRow["mStockQty"]);
		$LockRemark="";
		switch($myRow["Estate"]){
			case 1:
				$EstateStr="<span class='yellowB'>已审核</span>";
				$Locks=0;
				break;
			case 2:
				$EstateStr="<span class='redB'>待审核</span>";
				break;
			break;
			case 3:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    $EstateStr="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
				break;
			case 0:
				$EstateStr="<span class='greenB'>已发放</span>";
				$OutOperator=$myRow["OutOperator"];
				$OutDate=$myRow["OutDate"];
				break;
			}
        $GetName=$myRow["GetName"];
		$wStockQty=$wStockQty<$Qty?"<span class='redB'>".$wStockQty."</span>":"<span class='greenB'>".$wStockQty."</span>";
		$oStockQty=$oStockQty<$Qty?"<span class='redB'>".$oStockQty."</span>":"<span class='greenB'>".$oStockQty."</span>";
		$Locks=$myRow["Locks"];
		$WorkAdd=$myRow["WorkAdd"];
		//配件分析
		$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
             $CheckCodeRow=mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.nonbom8_outfixed WHERE OutId='$Id'",$link_id));
             $CheckCodeId=$CheckCodeRow["Id"];
             if($CheckCodeId>0){
			         $showPurchaseorder="<img src='../images/showtable.gif' onClick='ShowOrHideFixed(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' title='显示或隐藏信息资料.' width='13' height='13' style='CURSOR: pointer'/>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$tableWidth'>&nbsp;</div><br></td></tr></table>";
           }
        else{
           $showPurchaseorder="";$StuffListTB="";
          }
        $Confirm=$myRow["Confirm"];
       if($myRow["Estate"]==0){
                $ConfirmStr=$Confirm==1?"<span class='redB'>未确认</span>":"<span class='greenB'>已确认</span>";
           }
       else{
              $ConfirmStr="";
             }
		$ValueArray=array(
			array(0=>$GetName,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$WorkAdd,1=>"align='center'"),
			array(0=>$GoodsId,1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$OutDate,1=>"align='center'"),
			array(0=>$OutOperator,1=>"align='center'"),
			array(0=>$EstateStr,1=>"align='center'"),
			array(0=>$ConfirmStr,1=>"align='center'")
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