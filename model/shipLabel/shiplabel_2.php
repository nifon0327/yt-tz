<?php 
//ECHO专用标签模板
                $StrL=strlen($eCode);
		if($StrL<18){
				$E0101="E0101";
				}
			else{
				$E0101="E0101_X";
			}
		echo"
	<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=305 cellSpacing=0 cellPadding=0 width=590 border=0>
			  <TBODY>
				  <TR>
					<TD class=Dtablline align='center' valign='top'><TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=100% cellSpacing=0 cellPadding=0 width=588 border=0>
					  <TBODY>
						<TR bgColor=#ffffff>
						  <TD height='22' colSpan=2 class=E0100>&nbsp;Shipper:&nbsp;&nbsp;$StartPlace</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' colSpan=2 class=E0100>&nbsp;Consignee: &nbsp;$EndPlace</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' colSpan=2 class=E0100>&nbsp;Address:&nbsp; Les planes ,2-4- Poligono Fontsanta 08970 Sant Joan Despi-Barcelona</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD width='230' height='22' class=E0101>&nbsp;Attention:&nbsp; Ana Pinar</TD>
						  <TD width='348' class=E0101>&nbsp;Número de caja:&nbsp;&nbsp;&nbsp;$PreWord$i&nbsp; / &nbsp;$PreWord$BoxTotal </TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Pedido Número:&nbsp;&nbsp; PO#$OrderPO</TD>
						  <TD class='$E0101'>&nbsp;Referencia:&nbsp;&nbsp;$eCode</TD>
						</TR>
						<TR vAlign=top bgColor=#ffffff>
						  <TD class=E0100 style='WORD-BREAK: break-all' colSpan=2 height=42>&nbsp;Descripción:&nbsp;&nbsp;$Description</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Cantidad :&nbsp; $BoxPcs $PackingUnit</TD>
						  <TD align=middle class=A0000> <span class='Font_Fill' style='font-size:9px'>$cName</span></TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Medidas:&nbsp;$BoxSpec</TD>
                          <TD class=A0000 vAlign=center align=center rowSpan=5 > $BoxCodeTable</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Peso bruto:&nbsp; $WG Kilos</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Peso neto: &nbsp;$NG Kilos</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0101>&nbsp;Date: &nbsp;$Udate</TD>
						</TR>
						<TR bgColor=#ffffff>
						  <TD height='22' class=E0001>&nbsp;Cod.barras:</TD>
						  
						</TR>
					  </TBODY>
					</TABLE></TD>
				  </TR>
			  </TBODY>
			</TABLE>";
?>