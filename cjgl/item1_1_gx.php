<style type="text/css">
.divmain{ width:320px;background:#FFFFFF; }
.divmain ul{margin:0;padding:0;list-style:none;height:25px;}
.divmain ul li{ border-top:1px solid #ccc;float:left;vertical-align: middle;}
.liwidth{ width: 80px;line-height:12px;height:25px;text-align:center;font-weight: bold;color:#555555;}
</style>
<?php
//更新OK
$Th_Col="[ + ]|30|ID|30|期限|50|工单流水号|90|半成品名称|280|单位|40|加工工序|340|工单数量|55|最低产量|55|已生产|55|登记|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$nowInfo="当前:皮套登记";
//步骤4：需处理-条件选项
$SearchRows=" AND SC.WorkShopId='$fromWorkshop' AND SC.ActionId='$fromActionId' AND SC.scFrom>0 AND SC.Estate>0";//生产分类里的ID


if (strlen($tempStuffCname)>0){
	$SearchRows.=" AND A.StuffCname LIKE '%$tempStuffCname%' ";
	$searchList1="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'/>";
    }
else{
	$searchList1="<input type='text' name='tempStuffCname' id='tempStuffCname' value='' width='20'/> &nbsp;<input class='ButtonH_25' type='button'  id='okQuery' value='查询' onclick='ResetPage(1,1)'/>";
 }

$checkScSign=3;//可生产标识

$processList = "";
$processResult= mysql_query("SELECT PD.ProcessId,PD.ProcessName
FROM  $DataIn.yw1_scsheet  SC 
INNER JOIN $DataIn.cg1_processsheet B  ON B.StockId = SC.StockId
INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = B.StockId
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
INNER JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
INNER JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
WHERE 1 $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY PD.ProcessId ",$link_id);
	if ($processRow = mysql_fetch_array($processResult)){
		$processList.="<select name='ProcessId' id='ProcessId' onchange='ResetPage(1,1)'>";
		do{
			$theProcessId=$processRow["ProcessId"];
			$ProcessName=$processRow["ProcessName"];
			$ProcessId=$ProcessId==""?$theProcessId:$ProcessId;
			if($ProcessId==$theProcessId){
				$processList.="<option value='$theProcessId' selected>$ProcessName</option>";
                $SearchRows.=" AND PD.ProcessId='$theProcessId'";
				}
			else{
				$processList.="<option value='$theProcessId'>$ProcessName</option>";
				}
		  }while($processRow = mysql_fetch_array($processResult));
		 $TypeList.="</select>&nbsp;";
	}
//步骤5：
	echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='6' height='40px' class=''>$processList</td><td colspan='5' align='right' class=''>$searchList1<input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
			$Class_Temp=$i==0?"A1111":"A1101";
			$j=$i;
			$k=$j+1;
			echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
	 }
	echo"</tr>";
	///////////////////////////////////////////////////////////////
$DefaultBgColor=$theDefaultColor;
$j=2;$i=1;
$mySql="SELECT A.StuffId,A.StuffCname,A.Picture,A.TypeId ,B.POrderId, B.StockId, PD.ProcessId,PD.ProcessName,U.Name AS UnitName,SC.Qty AS OrderQty,SC.sPOrderId,SC.mStockId,G.Mid,G.DeliveryDate,G.DeliveryWeek
FROM  $DataIn.yw1_scsheet  SC 
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = SC.mStockId
INNER JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
INNER JOIN $DataIn.stuffunit U ON U.Id=A.Unit
INNER JOIN $DataIn.cg1_processsheet B  ON B.StockId = SC.StockId
INNER JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
LEFT JOIN $DataIn.ck5_llsheet L ON L.sPOrderId = SC.sPOrderId AND L.Estate>=0 AND L.Qty>0
WHERE 1  $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign AND L.Estate>=0  GROUP BY SC.sPOrderId ORDER BY L.Estate ASC, G.DeliveryWeek ASC ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		$TypeId=$myRow["TypeId"];
        $UnitName=$myRow["UnitName"];
		$thisProcessId=$myRow["ProcessId"];
        $thisProcessName=$myRow["ProcessName"];
        $OrderQty  =  $myRow["OrderQty"];
		$StockId=$myRow["StockId"];
        $POrderId=$myRow["POrderId"];
        $sPOrderId=$myRow["sPOrderId"];
        $mStockId = $myRow["mStockId"];
        $DeliveryDate=$myRow["DeliveryDate"];
        $DeliveryWeek=$myRow["DeliveryWeek"];
	    include "../model/subprogram/deliveryweek_toweek.php";

        include "../model/subprogram/GetMaxGxQty.php";
		include "processcount.php"; //工序计算
	    //已完成的工序数量
	    $CheckthisScQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE   C.StockId='$StockId' AND C.ProcessId='$thisProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
	    $thisScQty=$CheckthisScQty["gxQty"]==""?0:$CheckthisScQty["gxQty"];

	     //已生产数字显示方式
   	     switch($thisScQty){
	 	    case 0:$thisScQty="&nbsp;";break;
		    default://生产数量非0
				if($thisScQty>=$thisLowMinQty && $thisScQty<=$MaxQty ){//生产完成
					 $thisScQty="<div class='greenB'>$thisScQty</div>";
				}
				else{
					if($thisScQty<$thisLowMinQty){//未完成
					    $thisScQty="<div class='yellowB'>$thisScQty</div>";
					}
					else{//多完成
					    $thisScQty="<div class='redB'>$thisScQty</div>";
					}
		          }
			  break;
		  }

         $SumCount=count($ProcessIdArray); $x=1;
         for($k=0;$k<$SumCount;$k++){
             if($thisProcessId==$ProcessIdArray[$k]) {
               $SumGXQty=$SumGXQtyArray[$k];
               break;
            }
           $x++;
        }
        $LastPos=$x==$SumCount?1:0;
	    $UpdateIMG = ""; $UpdateClick ="";
	    if($SubAction==31  ){
	    	 $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
             $UpdateClick="onclick='RegisterGxQty($sPOrderId,$StockId,$SumGXQty,$LastPos,$thisProcessId,$MaxQty)'";
			}
		else{//无权限
			if($SubAction==1){
				$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
				}

			}

	    $cgMid = $myRow["Mid"];

	    if($cgMid==0){
		    include "item1_1_auto_cg.php";
	    }
		//动态读取配件资料
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";

			echo"<tr $bgColor>
				 <td class='A0111' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,\"$POrderId\",\"$sPOrderId\",\"showScOrder\");' >$showPurchaseorder</td>";
			echo"<td class='A0101' align='center' $OrderSignColor>$i</td>";
			echo"<td class='A0101' align='center' >$DeliveryWeek</td>";
			echo"<td class='A0101' align='center'>$sPOrderId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='center' >$UnitName</td>";
			echo"<td class='A0101' >$ProcessStr</td>";
			echo"<td class='A0101' align='right'>$OrderQty</td>";
			echo"<td class='A0101' align='right'>$thisLowMinQty</td>";
			echo"<td class='A0101' align='right'>$thisScQty</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			echo $ListRow;
			$j=$j+2;$i++;
			}while ($myRow = mysql_fetch_array($myResult));
		}
else{
	echo"<tr><td colspan='12' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";

?>
<script language='javascript'>
function RegisterGxQty(sPOrderId,StockId,gxQty,LastPos,thisProcessId,MaxQty){
	var divShadow=document.getElementById('divShadow');
	divShadow.innerHTML="";
	var url="item1_1_gxdj.php?StockId="+StockId+"&sPOrderId="+sPOrderId+"&gxQty="+gxQty+"&LastPos="+LastPos+"&thisProcessId="+thisProcessId+"&MaxQty="+MaxQty;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
   if(!-[1,]){  //判断是否为IE
    divShadow.style.left =document.documentElement.scrollLeft +(document.documentElement.clientWidth-800)/2+"px";
	divShadow.style.top =document.documentElement.scrollTop +(document.documentElement.clientHeight -330)/2+"px";
   }
   else{
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
	divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
   }
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth+"px";
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
}

function ToSaveGxDj(e,sPOrderId,StockId,thisProcessId,LastPos){
	var Qty=document.getElementById("Qty").value;
	var CheckSTR=fucCheckNUM(Qty,"");
	if(CheckSTR==0){
		document.getElementById("InfoBack").innerHTML="不是规范的数字！";
		document.form1.Qty.value="";
		return false;
		}
	else{
		//检查数字是否合法
		var MaxValue=document.getElementById("UnPQty").value;
		thisValue=Number(Qty);
		MaxValue=Number(MaxValue);
		if((thisValue>MaxValue) || thisValue==0){
			document.getElementById("InfoBack").innerHTML="<div style='color:#FF0000;font-size:18px' align='center'>超出范围！</div>";
			document.getElementById("Qty").value="";
			return false;
			}
		else{
			document.getElementById("InfoBack").innerHTML="&nbsp;";
			}
       e.disabled="disabled";
		var url="item1_1_gxdj_ajax.php?sPOrderId="+sPOrderId+"&StockId="+StockId+"&thisProcessId="+thisProcessId+"&Qty="+Qty+"&LastPos="+LastPos;
	　	var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				var BackData=ajax.responseText;
				if(BackData=="Y"){
					document.form1.submit();
					}
				}
			}
		ajax.send(null);
		e.disabled="";
		}
}
</script>