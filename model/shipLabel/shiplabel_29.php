<?php

$chDate = date("Y/m/d");
$productNameArray = explode("|", $Description);
$productName = $productNameArray[0];
$productColor = $productNameArray[1];

echo "<div style='page-break-after:always;'>";
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;订单编号 : $OrderPO </span> </TD>
      </TR>	
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;品名 : $productName </span> </TD>
      </TR>
      <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;存货编码 : $eCode</span></TD>
      </TR>   
 
      <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;颜色 : $productColor</span></TD>
      </TR>   
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;数量 : $BoxPcs</span></TD>
      </TR>  
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;出货日期 :  $chDate</span></TD>
      </TR> 
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;供应商 : 研砼包装(HY)</span></TD>
      </TR> 	  
	 </TBODY>	  
</TABLE>";
echo "</div>";
?>