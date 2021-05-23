<?php 
//echo "BoxCode:".$BoxCode;
    $cNoSign=$OrderPO=="140431(36101)"?1:0;
    
	$OrderPOArray1=explode("(",$OrderPO);  //只抓括号内的
	$Count=count($OrderPOArray1);
	if($Count>1) {
		$OrderPOArray2=explode(")",$OrderPOArray1[1]);
		$OrderPO=$OrderPOArray2[0];
	}
	
$Fontheight=" style='font-size:22px; font-weight:bold;height:48px;line-height:48px;'";

echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0 >
	<TBODY>
       <TR height=''  valign='middle'  >
          <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;BIGBEN CONNECTED</span> </TD>
      </TR>		

       <TR height='' >
          <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;PO NO: $OrderPO </span> </TD>
	  </TR>	

       <TR height='' >
          <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;$eCode </span> </TD>
	  </TR>
      <TR height='' >
         <TD class='td_line' ><span $Fontheight>&nbsp;&nbsp;&nbsp;&nbsp;QTY/CTN: $BoxPcs &nbsp;</span></TD>
	  </TR>   
	  ";
	if ($cNoSign==1){
		  echo "<TR height=''>
         <TD class='td_line' ><span $Fontheight >&nbsp;</span></TD>
	      </TR>" ;
	}
	else{
        echo "<TR height=''>
         <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;C/NO:  $PreWord$i &nbsp;OF&nbsp; $PreWord$BoxTotal </span></TD>
	  </TR>" ;   
  }
      echo  "<TR  >
          <TD class='td_line' ><span $Fontheight>&nbsp;&nbsp;&nbsp;&nbsp;MADE IN CHINA</span> </TD>
	  </TR>  

	 </TBODY>	  
</TABLE>";
//echo "</div>";
?>