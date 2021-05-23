<?php 

//标准标签模板
			//echo $StrL."-".$AutoDiv;
			//big ben
			$OrderPOArray1=explode("(",$OrderPO);  //只抓括号内的
			$Count=count($OrderPOArray1);
			if($Count>1) {
				$OrderPOArray2=explode(")",$OrderPOArray1[1]);
			    $OrderPO=$OrderPOArray2[0];
			}
			echo"
<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
    <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td   style=' height:21px;border-bottom:2px solid #000; text-align:left;  ' ><span style='font-weight:bold; font-size:11pt;'>BBI FRANCE </span></td>
      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>  
  
     <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  '  ><span style='font-weight:bold; font-size:11pt;'>$eCode </span></td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>  
 
     <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
	 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  ' ><span style='font-weight:bold; font-size:11pt;'>C/NO:$PreWord$i &nbsp; OF &nbsp; $PreWord$BoxTotal </span></td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>  
  
     <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  '  ><span style='font-weight:bold; font-size:11pt;'>PO NO.$OrderPO  </span></td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr> 
 
    <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  ' ><span style='font-weight:bold; font-size:11pt;'>SIZE:$BoxSpec </span></td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>  
  
     <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  '  ><span style='font-weight:bold; font-size:11pt;'>NW:$NG &nbsp; &nbsp; KGS  </span> </td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>  
 
     <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  ' ><span style='font-weight:bold; font-size:11pt;'>GW: $WG &nbsp;&nbsp; KGS  </span></td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>  
  
     <tr>
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td style=' height:21px;border-bottom:2px solid #000; text-align:left;  '  ><span style='font-weight:bold; font-size:11pt;'>PCB:$BoxPcs </span></td>

      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr> 
  
     <tr height='85' >
     <TD   class='td_Verleft'  width='14'>&nbsp;</TD> 
     <td  style=' height:21px;border-bottom:2px solid #000; text-align:left;  '  > <div calss='div_boxTable'>$BoxCodeTable</div></td>
     
      <TD  class='td_Verright' width='14'>&nbsp;</TD> 
    </tr>    
               
  </TABLE>
";
?>