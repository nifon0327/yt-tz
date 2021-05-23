<?php
//CG出CEL
//echo "FromCounty:$FromCounty";
//$CurDate=date('y年n月j日');
$Field=explode("|",$FromCounty);  //把括号中的提取出来
$Count=count($Field);
if($Count==2){
	$FSKDate=$Field[0];
	$batchNO=$Field[1];

}
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=1 bordercolor='#720000'>
	<TBODY>
       <TR height='28'>
          <TD  width='168'><span style='font-size:22px; font-weight:bold' >公司全称</span> </TD>
          <TD width='416'><span style='font-size:22px; font-weight:bold' >&nbsp;上海市研砼包装有限公司</span> </TD>
      </TR>		

       <TR height='28'>
          <TD  width='168'><span style='font-size:22px; font-weight:bold' >品名</span> </TD>
          <TD><span style='font-size:22px; font-weight:bold' >&nbsp;$eCode </span> </TD>
      </TR>	

       <TR height='28'>
          <TD  width='168'><span style='font-size:22px; font-weight:bold'>料号</span></TD>
		  <TD><span style='font-size:22px; font-weight:bold' >&nbsp;$Description </span>  </TD>
	  </TR> 
	  
       <TR height='28'>
          <TD  width='168'><span style='font-size:22px; font-weight:bold' >版次</span> </TD>
		  <TD><span style='font-size:22px; font-weight:bold' >&nbsp;$PackingRemark </TD>
	  </TR>
      <TR height='28'>
         <TD  width='168'><span style='font-size:22px; font-weight:bold' >数量</span></TD>
		  <TD><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxPcs </span>  </TD>
	  </TR>   
	  
      <TR height='28'>
         <TD  width='168'><span style='font-size:22px; font-weight:bold'>生产日期</span></TD>
		  <TD><span style='font-size:22px; font-weight:bold' >&nbsp;$FSKDate </span> </TD>
	  </TR>   
 
       <TR height='28'>
          <TD  width='168'><span style='font-size:22px; font-weight:bold'>生产批号</span></TD>
		  <TD><span style='font-size:22px; font-weight:bold' >&nbsp; $batchNO </span>  </TD>
	  </TR>  
	 </TBODY>	  
</TABLE>";
//echo "</div>";
?>