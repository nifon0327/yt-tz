<script>
function ShowOrHideFixed(e,f,Order_Rows,PdId,RowId){
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
		if(PdId!=""){
			var url="../nonbom/nonbom24_ajax.php?PdId="+PdId+"&RowId="+RowId;
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

function bfQty(Id,TotalQty,Qty){
			var url="../nonbom/nonbom24_bf.php?Id="+Id+"&TotalQty="+TotalQty+"&Qty="+Qty;
		　	var ajax=InitAjax();
		　	 ajax.open("GET",url,true);
		    	 ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
				    	var BackData=ajax.responseText;
                          document.form1.submit();
					}
				}
			ajax.send(null);
}
</script>
<?php
include "../model/modelhead.php";
$ColsNumber=10;
ChangeWtitle("$SubCompany 非bom配件个人领用盘点记录");
$funFrom="nonbom24";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|30|分类|80|编码|50|非bom配件名称|350|单位|40|盘点总数|80|丢弃报废数量|80|备注|250|盘点人|60|盘点时间|70|报废处理|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$sumCols="6,7";			//求和列,需处理
$nowWebPage=$funFrom."_read";
  $ActioToS="1,4";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	}
if($Single==1){
	$SearchRows=" AND  A.PdNumber='$Login_P_Number'";
   }
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr<input id='Single' name='Signle' type='hidden' value='$Single'>";

include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.TotalQty,A.Qty,B.GoodsName,B.Attached,B.Unit,C.TypeName,A.Date,A.Estate,A.Operator,A.Remark,M.Name
FROM $DataIn.nonbom8_pd A
LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.PdNumber
WHERE 1 $SearchRows   ORDER BY A.Date ASC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeName=$myRow["TypeName"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"];
		$Unit=$myRow["Unit"];
       $Name=$myRow["Name"];
		$TotalQty=del0($myRow["TotalQty"]);
		$Qty=del0($myRow["Qty"]);
		$QtySum+=$Qty;
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
	   include"../model/subprogram/good_Property.php";//非BOM配件属性
		//配件分析
		$GoodsIdStr="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
        $Date=substr($myRow["Date"], 0, 10);
        $Estate=$myRow["Estate"];
      //   $EstateStr=$Estate==2?"<span class='redB'>未处理</span>":"<span class='greenB'>已处理</span>";
        if($Estate==2 && $Qty>0){
                     $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
                     $UpdateClick="onclick='bfQty($Id,$TotalQty,$Qty)'";
            }
      else {
               $UpdateIMG="<span class='greenB'>已处理</span>";$UpdateClick="";
              }
        $CheckCodeRow=mysql_fetch_array(mysql_query("SELECT  Id FROM $DataIn.nonbom8_pdfixed WHERE PdId='$Id'",$link_id));
        $CheckCodeId=$CheckCodeRow["Id"];
         if($CheckCodeId!=""){
			 $showPurchaseorder="<img src='../images/showtable.gif' onClick='ShowOrHideFixed(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' title='显示或隐藏信息资料.' width='13' height='13' style='CURSOR: pointer'/>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$tableWidth'>&nbsp;</div><br></td></tr></table>";
              }
        else{
               $showPurchaseorder="";$StuffListTB="";
              }

		$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$GoodsIdStr,1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$TotalQty,1=>"align='center'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
		//	array(0=>$EstateStr,1=>"align='center'"),
			array(0=>$UpdateIMG,1=>"align='center' $UpdateClick")
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
include '../admin/cancel_project_all.php';
//cancel_project_all(40);
?>
