
<?php   
//电信-zxq 2012-08-01
//传入参数：$ProductId 、$StuffId、$OrderQty 

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$dataArray=explode("|",$args);
$ProductId=$dataArray[0];
$StuffId=$dataArray[1];
$OrderQty=$dataArray[2];

if ($OrderQty>0) $AddtdStr="<td width='60' align='center'>需求数量</td><td width='60' align='center'>完成数量</td>";
                else $AddtdStr="";
echo"<table  cellspacing='1' border='1' align='left' style='margin-left:65px;margin-top:10px' ><tr bgcolor='#CCCCCC'>
		<td width='40'  align='center'>序号</td>
                <td width='60'  align='center'>工序Id</td>
		<td width='200' align='center'>工序名称</td>
                <td width='40'  align='center'>图档</td>
		<td width='60' align='center'>对应关系</td>
                $AddtdStr
		</tr>"; //<td width='60'  align='center'>单价</td>

$ProcessResult=mysql_query("SELECT D.ProcessId,D.ProcessName,A.Relation,D.Price,D.Picture
	     FROM $DataIn.process_bom A  
             LEFT JOIN $DataIn.process_data D ON D.ProcessId=A.ProcessId   
	     WHERE A.ProductId='$ProductId' AND A.StuffId='$StuffId' ORDER BY A.Id",$link_id);
$i=1;

if ($ProcessRow = mysql_fetch_array($ProcessResult)) {
$d=anmaIn("download/process/",$SinkOrder,$motherSTR);
	do{
		$ProcessId=$ProcessRow["ProcessId"];
		$ProcessName=$ProcessRow["ProcessName"];
		$Relation=$ProcessRow["Relation"];
                $Price=$ProcessRow["Price"]==""?"&nbsp;":$ProcessRow["Price"];
                $Picture=$ProcessRow["Picture"];
                $ProcessQty=ceil($OrderQty*$Relation);
                
                include "subprogram/process_Gfile.php";	//图档显示
                
		echo"<tr bgcolor=#EAEAEA>";
		echo"<td align='center'>$i</td>";	 //序号
		echo"<td align='center'>$ProcessId</td>";	 //工序ID
                echo"<td>$ProcessName</td>";	 //工序名称
		echo"<td align='center'>$Gfile</td>";	 //工序图档
               // echo"<td align='center'>$Price</td>";	 //序号
		echo"<td align='center'>$Relation</td>";	 //对应关系
                if ($OrderQty>0){
                   echo"<td align='center'>$ProcessQty</td>";	 //需求数量
		   echo"<td align='center'>&nbsp;</td>";	 //完成数量
                }
		echo"</tr>";
		$i++;
 	   }while ($ProcessRow = mysql_fetch_array($ProcessResult));
	}
else{
	echo"<tr><td height='30' colspan='8' >没有加工工序明细资料,请检查.</td></tr>";
	}
echo"</table>";
?>