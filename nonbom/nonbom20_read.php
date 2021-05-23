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
			var url="../nonbom/nonbom20_ajax.php?GoodsId="+OutId+"&RowId="+RowId; 
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
include "../model/modelhead.php";
$ColsNumber=10;
ChangeWtitle("$SubCompany 非bom配件个人领用汇总");
$funFrom="nonbom20";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|30|编码|50|非bom配件名称|350|单位|40|盘点期限|80|申领总数|70|已领总数|70|转入数量|70|转出数量|70|退回仓库|70|报废数量|70|剩余数量|70";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$sumCols="6,7,8,9,10,11,12";			//求和列,需处理
$nowWebPage=$funFrom."_read";
  $ActioToS="1,157,158,159,160";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";

	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0); //个人领用的和别人转过来的
$mySql="SELECT  GoodsId,SUM(Qty) AS Qty,GoodsName,Attached,Unit,TypeName,pdFrequency 
 FROM (
       SELECT A.GoodsId,A.Qty,B.GoodsName,B.Attached,B.Unit,C.TypeName,X.Frequency AS pdFrequency
       FROM $DataIn.nonbom8_outsheet A
       LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
       LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
       LEFT JOIN $DataPublic.nonbom6_nx X  ON X.Id=B.pdDate
       WHERE 1 $SearchRows  AND A.GetNumber='$Login_P_Number'
       UNION   ALL 
       SELECT   A.GoodsId,A.Qty,B.GoodsName,B.Attached,B.Unit,C.TypeName,X.Frequency AS pdFrequency
       FROM $DataIn.nonbom8_turn  A  
       LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
       LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
       LEFT JOIN $DataPublic.nonbom6_nx X  ON X.Id=B.pdDate
       WHERE 1 $SearchRows  AND A.InNumber='$Login_P_Number'  AND A.Estate=1
   ) D WHERE 1 GROUP BY  D.GoodsId";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$TypeName=$myRow["TypeName"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"];
		$Unit=$myRow["Unit"];
		$Qty=del0($myRow["Qty"]);
		$QtySum+=$Qty;
        $pdFrequency=$myRow["pdFrequency"]==""?"&nbsp;":$myRow["pdFrequency"];
		$lyResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS lyQty  FROM $DataIn.nonbom8_outsheet  WHERE  GoodsId=$GoodsId AND GetNumber='$Login_P_Number' AND Estate=0",$link_id)); //申领发放的
       $lyQty=$lyResult["lyQty"];
		$backResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS backQty  FROM $DataIn.nonbom8_reback  WHERE  GoodsId=$GoodsId AND BackNumber='$Login_P_Number'",$link_id));//个人退回仓库的
       $backQty=$backResult["backQty"];

		$bfResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS bfQty  FROM $DataIn.nonbom8_bf  WHERE  GoodsId=$GoodsId AND bfNumber='$Login_P_Number'",$link_id));//个人报废
       $bfQty=$bfResult["bfQty"];


		$InResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS InQty  FROM $DataIn.nonbom8_turn  WHERE  GoodsId=$GoodsId 
AND InNumber='$Login_P_Number' AND Estate=1",$link_id)); //转入进来的需审核确认
		$turnInQty=$InResult["InQty"];

		$OutResult=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(Qty),0) AS OutQty  FROM $DataIn.nonbom8_turn  WHERE  GoodsId=$GoodsId 
AND OutNumber='$Login_P_Number'",$link_id)); //转出去的
		$turnOutQty=$OutResult["OutQty"];


          $lastQty=$lyQty+$turnInQty-$backQty-$bfQty-$turnOutQty;  //领用数量+转入数量-转出数量-退仓数量-报废数量
          $backQty=$backQty==0?"&nbsp;":"<a style='font-weight:bold;' href='nonbom21_showajax.php?GoodsId=$GoodsId&backQty=$backQty' target='_blank'>$backQty</a>";
          $bfQty=$bfQty==0?"&nbsp;":"<a style='font-weight:bold;' href='nonbom22_showajax.php?GoodsId=$GoodsId&bfQty=$bfQty' target='_blank'>$bfQty</a>";
          $turnInQty=$turnInQty==0?"&nbsp;":"<a style='font-weight:bold;' href='nonbom23_showajax.php?GoodsId=$GoodsId&turnSign=1' target='_blank'>$turnInQty</a>";
          $turnOutQty=$turnOutQty==0?"&nbsp;":"<a style='font-weight:bold;' href='nonbom23_showajax.php?GoodsId=$GoodsId&turnSign=2' target='_blank'>$turnOutQty</a>";
         $lyQty=$lyQty==$Qty?"<span class='greenB'>$lyQty</span>":"<span class='redB'>$lyQty</span>";      
          $lastQty=$lastQty>0?"<span class='greenB'>$lastQty</span>":$lastQty;
         

		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		  include"../model/subprogram/good_Property.php";//非BOM配件属性	
		//配件分析
		$GoodsIdStr="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $showPurchaseorder="<img src='../images/showtable.gif' onClick='ShowOrHideFixed(StuffList$i,showtable$i,StuffList$i,\"$GoodsId\",$i);' name='showtable$i' title='显示或隐藏信息资料.' width='13' height='13' style='CURSOR: pointer'/>";
			$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$tableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$GoodsIdStr,1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$pdFrequency,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$lyQty,1=>"align='right'"),
			array(0=>$turnInQty,1=>"align='right'"),
			array(0=>$turnOutQty,1=>"align='right'"),
			array(0=>$backQty,1=>"align='right'"),
			array(0=>$bfQty,1=>"align='right'"),
			array(0=>$lastQty,1=>"align='right'")
			);
		$checkidValue=$GoodsId;
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