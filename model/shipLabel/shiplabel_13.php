<?php 

//标准标签模板
			//echo $StrL."-".$AutoDiv;
			//TESCO
			if($LabelModel==3){$numberTemp=$PackingRemark*$BoxPcs;$SPR=" SPR($numberTemp PCS)";}
			//$StartPlace=trim($StartPlace)==""?"Ash Cloud Co.,Ltd.Shenzhen":$StartPlace;
			$StartPlace="Ash Cloud Co.,Ltd.Shenzhen";
	        //$EndPlace=trim($EndPlace)==""?"&nbsp;":$EndPlace;
			//echo "$BoxCode";
			$eLen=strlen(trim($BoxCode));
			if($eLen==4){
				$ArryeCode=explode("(",$eCode);
				$eCode=$ArryeCode[0];
				$Count=count($ArryeCode);
				if ($Count>1){
					$BARCODE=$ArryeCode[1];
					$BARCODE=substr($BARCODE,0,13);
					
				}	
				$DIGITS=$BoxCode;
				
			}
			else
			{
				
				$ArryeCode=explode("(",$eCode);
				$eCode=$ArryeCode[0];
				$Count=count($ArryeCode);
				if ($Count>1){
					$BARCODE=$ArryeCode[1];
					$BARCODE=substr($BARCODE,0,13);
					$DIGITS=substr($BARCODE,8,4);
				}

			}
								 
echo"
<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
    
     <TR height=''> 
          <TD> <div style='font-size:24px; font-weight:bold'><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CUSTOMER: AVENIR TELECOM</span>  </div>  </TD>
     </TR>
     <TR height=''> 
          <TD><div style='font-size:24px; font-weight:bold'><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PO NO.: $OrderPO</span>  </div> </TD>
     </TR>
     <TR height=''> 
          <TD><div style='font-size:24px; font-weight:bold' ><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REF. NO.: $eCode</span>  </div> </TD>
     </TR>
     <TR height=''> 
          <TD><div style='font-size:24px; font-weight:bold'><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BARCODE: $BARCODE </span>   </div> </TD>
     </TR>
     <TR height=''> 
          <TD><div style='font-size:24px; font-weight:bold'><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4 DIGITS: $DIGITS </span>  </div> </TD>
     </TR>
     <TR height=''> 
          <TD><div style='font-size:24px; font-weight:bold'><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; QTY: $BoxPcs </span> </div> </TD>
     </TR>
     <TR height=''> 
          <TD><div style='font-size:24px; font-weight:bold'><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SUPPLIER: $StartPlace</span><div>   </TD>
     </TR>

         

  </TABLE>

";
?>