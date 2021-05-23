<?php 
//电信-EWEN
include "../basic/parameter.inc";
include "../model/modelfunction.php";

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../DatePicker/WdatePicker.js'></script></head>";


include "../model/subprogram/sys_parameters.php";

$searchper=" AND $TAsName.$TField like '$search%'";
$Estate=$Estate!=1?"":" AND $TAsName.Estate=1 ";
if($Company!=""){
    if($Company=="CEL")$Company=" and CompanyId IN (1004,1059,1072) ";
}


$i=0;
/*
$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	WHERE 1 AND P.Estate=1  $CompanySTR $search group by P.eCode ORDER BY P.eCode ";   //AND P.cName LIKE '%MUVIT%'
*/
$mySql= "SELECT $TAsName.$TField FROM $Ttable $TAsName
	WHERE 1 $Estate $searchper  $Company group by $TAsName.$TField ORDER BY $TAsName.$TField ";   //AND P.cName LIKE '%MUVIT%'
if ($ProPer==1) //表示有前导百分号
{
	
	$persearch=" AND $TAsName.$TField like '%$search%'";	
	$mySql= "SELECT $TAsName.$TField FROM $DataIn.$Ttable $TAsName
		WHERE 1 $Estate $persearch group by $TAsName.$TField ORDER BY $TAsName.$TField  ";   //AND P.cName LIKE '%MUVIT%'
	
}
echo "$mySql";
$DIV_SHOW_ITEMS=30; //如果显示的小于20条则不显示Y轴条
$DIV_SHOW_HEIGHT="400px"; //显示可视区DIV的大小
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	echo "^";

	do{

			$eCode=$myRow["$TField"]==""?"&nbsp;":$myRow["$TField"];
			//$eCode=strtr("$eCode"," ",'&nbsp;');
			$eCode=str_replace(" ","&nbsp;",$eCode);  
           //var result = document.createElement("div");
            // 设置结果div的显示样式
			//$No=$i-1;
			echo "<div id='search_div$i' name='search_div$i' style='cursor:pointer;' onmousedown='selectResult($i);' onmouseover='highlightResult($i);' onmouseout='unhighlightResult($i)' >";
            // 设置为未选中
            //_unhighlightResult(result);
           
            // 设置鼠标移进、移出等事件响应函数
            //result.onmousedown = selectResult;
            //result.onmouseover = highlightResult;
            //result.onmouseout = unhighlightResult;

            //echo "<span  class='result1'  style='text-align:left;font-weight:bold' >$eCode</span> ";
             echo "<span  id='search_span$i' name='search_span$i'  class='result1'  style='text-align:left;font-weight:bold'  >$eCode</span> ";
            
            // 结果的文本是一个span
            //var result1 = document.createElement("span");
            // 设置文本span的显示样式
            //result1.className = "result1";
            //result1.style.textAlign = "left";
            //result1.style.fontWeight = "bold";
            //result1.innerHTML = resultArray[i];
         
            // 将span添加为结果div的子节点
            //result.appendChild(result1);
            echo "</div>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
		$i=$i+1;  //记录总数
		echo "^$DIV_SHOW_ITEMS^$DIV_SHOW_HEIGHT^";

	}
else{
	noRowInfo($tableWidth);
  	}
echo"</form>";
?>

