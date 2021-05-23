<?php   
//**************************************************************
//11月已确认的函数开始*
//**************************************************************

//3		

//2		窗口标题
function WinTitle($Title){
	echo "<SCRIPT type=text/javascript>top.document.title=\"$Title\";</script>";
 	}

//1		表格表头
function Table_Head($Th_Col,$Sign){
	$Field=explode("|",$Th_Col);
	$Count=count($Field);
	echo"<tr height='25' bgcolor=$Title_bgcolor>";
	for ($i=0;$i<$Count;$i=$i+2){
		if($Sign==1){
			$Class_Temp=$i==0?"A1111":"A1101";}
		else{
			$Class_Temp=$i==0?"A0111":"A0101";}
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' Class='$Class_Temp'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
	}

//**************************************************************
//11月已确认的函数结束*
//**************************************************************
function Table_th_line($Th_Col,$OrderKey,$OrderImg,$Row,$Row1Over_bgcolor,$Row1_bgcolor){
	
	$Field=explode("|",$Th_Col);
	$Count=count($Field);
	echo"<tr height='25' bgcolor=$Row1_bgcolor >";
	for ($i=0;$i<$Count;$i=$i+3){
		if($Row==1){
		$Class_Temp=$i==0?"A1111":"A1101";}
		else{
		$Class_Temp=$i==0?"A0111":"A0101";}
		$j=$i+1;
		$k=$j+1;
		if ($Field[$k]==0)
		$Field[$k]="";
		if ($Field[$i]==""){
			echo"<td width='$Field[$k]'  height='8' scope='col'  style='CURSOR: pointer' Class='$Class_Temp'><div align='center'>选项</div></td>";}
		else{
			if($OrderImg!=""){
				if ($Field[$i]==$OrderKey){
					echo"<td width='$Field[$k]' scope='col' bgcolor=\"$Row1Over_bgcolor\" <div align='center'>$Field[$j]$OrderImg</div></td>";
					}
				else{
					echo"<td width='$Field[$k]' scope='col' onMouseOver='this.className=\"read_tdfilter\";this.bgColor=\"$Row1Over_bgcolor\"' onMouseOut='this.className=\"$Class_Temp\";this.bgColor=\"$Row1_bgcolor\"' onClick='GoOrderKey(\"$Field[$i]\")' style='CURSOR: pointer' class='$Class_Temp'><div align='center'>$Field[$j]</div></td>";
					}
				}
			else{
				echo"<td width='$Field[$k]' scope='col' Class='$Class_Temp'><div align='center'>$Field[$j]</div></td>";
				}
				
			}
		}
	echo"</tr>";
	}
//表尾函数
function Tabletail($i,$Page,$Page_count,$Form,$TypeSTR){
echo"
		</td>
   		<td background='../images/maintable_r2_c7.gif'></td>
  	</tr>
  	<tr>
   		<td><img name='maintable_r3_c1' src='../images/maintable_r3_c1.gif' width='7' height='26' border='0'/></td>
   		<td background='../images/maintable_r3_c2.gif' ></td>
   		<td><img name='maintable_r3_c3' src='../images/maintable_r3_c3.gif' width='35' height='26' border='0'/></td>
   		<td background='../images/maintable_r1_c4.gif'>
			<table>
				<tr>
    				<td align='center' class='readlink'><nobr>"; 
					if ($Page_count!=""){
						// 翻页链接
						$Page_string = '';
						if( $Page == 1 ){
						   $Page_string .= '首页 | 上一页 | ';
						}
						else{
						   $Page_string .= '<a href=?Page=1'.$TypeSTR.'>首页</a>&nbsp;|&nbsp;<a href=?Page='.($Page-1).$TypeSTR.'>上一页</a>&nbsp;|&nbsp;';
						}
						if( ($Page == $Page_count) || ($Page_count == 0) ){
						   $Page_string .= '下一页 | 尾页';
						}
						else{
						   $Page_string .= '<a href=?Page='.($Page+1).$TypeSTR.'>下一页</a>&nbsp;|&nbsp;<a href=?Page='.$Page_count.$TypeSTR.'>尾页</a>';
						}						
						echo "第 $Page 页,共 $i 条记录&nbsp;&nbsp;".$Page_string;					
					}
					else{
					echo "第1页,本页记录共 $i 条&nbsp;&nbsp;&nbsp;";
					}
					echo"</nobr></td>
				</tr>
			</table>   
		</td>
   		<td><img name='maintable_r3_c5' src='../images/maintable_r3_c5.gif' width='34' height='26' border='0'/></td>
   		<td background='../images/maintable_r3_c6.gif'>&nbsp;</td>
   		<td><img name='maintable_r3_c7' src='../images/maintable_r3_c7.gif' width='7' height='26' border='0'/></td>
	</tr>
</table>";
if ($Form=="Yes"){
echo"</form>";}
echo"</body>
</html>
";
}
?>