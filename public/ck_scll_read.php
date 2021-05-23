<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=9;
$tableMenuS=500;
ChangeWtitle("$SubCompany 订单领料有误记录列表");
$funFrom="ck_scll";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|40|订单流水号|100|需求单流水号|100|配件ID|50|配件名称|320|单位|40|需领料数|60|领料|60|分析|60|操作|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
//$Page_Size = 500;							//每页默认记录数量2,3,4,
$ActioToS="1";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
    $client_Result = mysql_query("SELECT M.CompanyId,T.Forshort
    FROM  $DataIn.yw1_ordersheet Y 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
    LEFT JOIN $DataIn.trade_object  T ON T.CompanyId = M.CompanyId
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId  = Y.POrderId 
    WHERE 1 $SearchRows AND Y.Estate =0  AND G.blSign = 1 GROUP BY M.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($client_Result)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
		do{			
			$CompanyIdValue=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			$CompanyId=$CompanyId==""?$CompanyIdValue:$CompanyId;
			if($CompanyId==$CompanyIdValue){
				echo"<option value='$ActionIdValue' selected>$Forshort</option>";
				$SearchRows.=" AND M.CompanyId='$CompanyIdValue'";
				}
			else{
				echo"<option value='$CompanyIdValue'>$Forshort</option>";					
				}
			}while($clientRow = mysql_fetch_array($client_Result));
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
$mySql="SELECT Y.POrderId,G.StockId,D.StuffId,D.Picture,D.StuffCname,G.OrderQty,U.Name AS UnitName
FROM  $DataIn.yw1_ordersheet Y 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId  = Y.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id = D.Unit
WHERE 1 $SearchRows AND Y.Estate =0  AND G.blSign = 1   ";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
			
        $m=1;
        $POrderId=$myRow["POrderId"];
	
		$StuffId=$myRow["StuffId"];
		$StockId=$myRow["StockId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		$OrderQty=$myRow["OrderQty"];
		$UnitName=$myRow["UnitName"];
		
        include"../model/subprogram/stuff_Property.php";//配件属性
        $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    //检查是否有图片
	    include "../model/subprogram/stuffimg_model.php";
	    $checkllQtyResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) 
				AS llQty FROM $DataIn.ck5_llsheet WHERE  StockId='$StockId' AND StuffId ='$StuffId'",$link_id));
	    $llQty=$checkllQtyResult["llQty"];
		
		$diffQty = $OrderQty -$llQty;
		
		$diffQty = sprintf("%.2f", $diffQty);
		
		
		$UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
       
       

	    if($diffQty>0){
		    $diffStr = "<span class='blueB'>少领 $diffQty</span>";
		    
	    }
	    if($diffQty<0){
		    $diffStr = "<span class='redB'>多领 $diffQty</span>";
	    }
	    

	    if($diffQty!=0 && $llQty>0){
	            $ValueArray=array(
				    array(0=>$POrderId,1=>"align='center'"),
				    array(0=>$StockId,1=>"align='center'"),
					array(0=>$StuffId,1=>"align='center'"),
					array(0=>$StuffCname),
					array(0=>$UnitName,1=>"align='center'"),
					array(0=>$OrderQty,1=>"align='right'"),
					array(0=>$llQty,1=>"align='right'"),
					array(0=>$diffStr,1=>"align='center'"),
					array(0=>$UpdateIMG,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateLLQty($sPOrderId,$StockId,$diffQty,$POrderId,$StuffId,this)' style='CURSOR: pointer'")
					);
				include "../model/subprogram/read_model_6.php";	
			}	
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",1);
$RecordToTal= $i-1;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script language="javascript" type="text/javascript">
function updateLLQty(sPOrderId,StockId,diffQty,POrderId,StuffId,ee){  


    var url="ck_scll_ajax.php?sPOrderId="+sPOrderId+"&StockId="+StockId+"&POrderId="+POrderId+"&StuffId="+StuffId+"&diffQty="+diffQty;
    var ajax=InitAjax();
    ajax.open("GET",url,true);
    ajax.onreadystatechange =function(){
       if(ajax.readyState==4){
              var returnText=ajax.responseText;
              returnText=returnText.replace(/(^\s*)|(\s*$)/g,"");
                    if(returnText=="Y"){
                         //更新该单元格底色和内容
                         ee.innerHTML="&nbsp;";
                         ee.style.backgroundColor="#339900"; 
                         ee.onclick="";
                     }else{
                        alert("更新失败！数据更新出现错误。"+returnText); 
                    }
            }
      }
    ajax.send(null);
}

</script>