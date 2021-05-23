<script>
function ShowOrHideFixed(e,f,Order_Rows,BfId,RowId,GoodsId){
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
		if(BfId!=""){			
			var url="../public/nonbom10_ajax.php?BfId="+BfId+"&RowId="+RowId+"&GoodsId="+GoodsId; 
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
ChangeWtitle("$SubCompany 非bom配件报废明细");
$funFrom="nonbom10";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|40|报废日期|100|分类|100|编码|60|非bom配件名称|350|条码|100|单位|40|报废数量|60|备注|300|状态|40|操作员|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
$sumCols="8";			//求和列,需处理
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	//月份查询
	}
	
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.Qty,A.Remark,A.ReturnReasons,A.Estate,A.Locks,A.Date,A.Operator,
B.GoodsName,B.BarCode,B.Attached,B.Unit,
C.TypeName,
D.wStockQty,D.oStockQty,D.mStockQty 
FROM $DataIn.nonbom10_outsheet A
LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
WHERE 1 $SearchRows ORDER BY A.Date DESC,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$TypeName=$myRow["TypeName"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$Attached=$myRow["Attached"];
		$BarCode=$myRow["BarCode"];
		$Unit=$myRow["Unit"];
		
		$Qty=$myRow["Qty"];
		$QtySum+=$Qty;
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
        include"../model/subprogram/good_Property.php";//非BOM配件属性
		$wStockQty=$myRow["wStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$mStockQty=$myRow["mStockQty"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 1:
				$Estate="<span class='greenB'>已核</span>";
				$LockRemark="记录已经审核，强制锁定操作！";
				$Locks=0;
				break;
			case 2:
				$Estate="<span class='redB'>未核</span>";
				break;
			break;
			case 3:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    $Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
				break;
			}
		$Locks=$myRow["Locks"];
             $CheckCodeRow=mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.nonbom10_bffixed WHERE BfId='$Id'",$link_id));
             $CheckCodeId=$CheckCodeRow["Id"];
             if($CheckCodeId>0){
			         $showPurchaseorder="<img src='../images/showtable.gif' onClick='ShowOrHideFixed(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i,\"$GoodsId\");' name='showtable$i' title='显示或隐藏信息资料.' width='13' height='13' style='CURSOR: pointer'/>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$tableWidth'>&nbsp;</div><br></td></tr></table>";
           }
        else{
               $showPurchaseorder="";$StuffListTB="";
          }
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$GoodsId,1=>"align='center'"),
			array(0=>$GoodsName),
            array(0=>$BarCode,1=>"align='center'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Remark),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
         echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	
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