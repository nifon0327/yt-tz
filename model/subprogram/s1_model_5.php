<?php
$SearchSTR = $SearchSTR == "0" ? "" : "<span onClick='SearchToNext(2)' $onClickCSS>查询　</span>&nbsp";
echo "
</td>
<td width='35' class='timeTop' style='background-color: #f2f3f5;'></td>
<td style='background-color: #f2f3f5;margin-right:20px' width='150' id='menuT2' align='center' class='' >
		<table border='0' align='center' cellspacing='0'>
			<tr style='background-color: #f2f3f5;'>
				<td class='readlink'>
					<nobr>					
					$SearchSTR
					<span onClick='s1ReBack()' $onClickCSS>确定　</span>&nbsp
					<span onClick='All_elects(\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",$ColsNumber)' $onClickCSS>全选　</span>&nbsp;
					<span onClick='Instead_elects(\"$theDefaultColor\",\"$theDefaultColor\",\"$theMarkColor\",$ColsNumber)' $onClickCSS>反选　</span>&nbsp;
					<span onClick='CopyToClicp(\"1\",\"1\")' $onClickCSS>复制　</span>&nbsp;
					</nobr>
				</td>
			</tr>
		</table>
	</td>
  </tr>
</table>";
?>