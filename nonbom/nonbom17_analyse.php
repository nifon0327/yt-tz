<?php 
include "../model/modelhead.php";
$CheckSql=mysql_query("SELECT  GoodsId,Estate  FROM $DataIn.nonbom7_code  WHERE  BarCode='$tempBarCode'",$link_id);; 
if($CheckRow = mysql_fetch_array($CheckSql))	{
	   $Estate=$CheckRow["Estate"];
	   $GoodsId=$CheckRow["GoodsId"];
	  $thisDate=date("Y-m-d");
	}

//入库
$UnionSTR="SELECT left(O.Date,10) AS Date,concat('1') AS Sign,M.Name AS People
FROM $DataIn.nonbom7_code O
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.Operator
WHERE O.BarCode='$tempBarCode'  AND O.TypeSign=1 GROUP BY left(O.Date,10)";

//转入

$UnionSTR.="
UNION ALL
SELECT left(O.Date,10) AS Date,concat('2') AS Sign,M.Name AS People
FROM $DataIn.nonbom7_code O
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.Operator
WHERE O.BarCode='$tempBarCode'  AND O.TypeSign=2 GROUP BY left(O.Date,10)";


//报废
$UnionSTR.="
UNION ALL
SELECT left(O.Date,10) AS Date,concat('3') AS Sign,M.Name AS People
FROM $DataIn.nonbom10_bffixed O
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.Operator
WHERE O.BarCode='$tempBarCode'  GROUP BY left(O.Date,10)";

//个人领用记录
$UnionSTR.="
UNION ALL
SELECT left(O.Date,10) AS Date,concat('4') AS Sign,M.Name AS People
FROM $DataIn.nonbom8_outfixed O
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.LyMan
WHERE O.BarCode='$tempBarCode'  GROUP BY left(O.Date,10)";


// 个人报废
$UnionSTR.="
UNION ALL
SELECT left(O.Date,10) AS Date,concat('5') AS Sign,M.Name AS People
FROM $DataIn.nonbom8_bffixed B 
LEFT JOIN $DataIn.nonbom8_bf O  ON O.Id=B.BfId
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.Operator
WHERE B.BarCode='$tempBarCode'  GROUP BY left(O.Date,10)";


// 个人退回
$UnionSTR.="
UNION ALL
SELECT left(O.Date,10) AS Date,concat('6') AS Sign,M.Name AS People
FROM  $DataIn.nonbom8_rebackfixed  R 
LEFT JOIN $DataIn.nonbom8_reback O  ON O.Id=R.BackId
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.Operator
WHERE R.BarCode='$tempBarCode'  GROUP BY left(O.Date,10)";


// 个人转交
$UnionSTR.="
UNION ALL
SELECT left(O.Date,10) AS Date,concat('7') AS Sign,M.Name AS People
FROM  $DataIn.nonbom8_turnfixed  T 
LEFT JOIN $DataIn.nonbom8_turn O  ON O.Id=T.TurnId
LEFT JOIN $DataPublic.staffmain M ON M.Number=O.Operator
WHERE T.BarCode='$tempBarCode'  GROUP BY left(O.Date,10)";

//echo $UnionSTR;
$result = mysql_query($UnionSTR,$link_id);

$DateTemp=array();
$PeopleTemp=array();
$SignTemp=array();
if($myrow = mysql_fetch_array($result)){
	do{
		$People= $myrow["People"];
		$Sign= $myrow["Sign"];
		if($myrow["Date"]==""){
			  $Date="0000-00-00";
			}
		else{
			$Date=substr($myrow["Date"],0,10);
			}
		if($People!=""){
			$DateTemp[]=$Date;
			$PeopleTemp[]=$People;
			$SignTemp[]=$Sign;
			}
		
		}while ($myrow = mysql_fetch_array($result));		
	}
else{
	       echo"没有记录";
	     }
$grade = array("Date"=>$DateTemp,"People"=>$PeopleTemp,"Sign"=>$SignTemp);
$tt=array_multisort($grade["Date"], SORT_STRING, SORT_ASC,$grade["Sign"], SORT_NUMERIC, SORT_ASC,$grade["People"], SORT_NUMERIC, SORT_ASC);
$count=count($DateTemp);
//print_r($DateTemp);
?>

<form name="form1" method="post" action="">
<table  cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <th height="37" colspan="9" scope="col">固定资产数据分析报表</th>
  </tr>
  <tr>
    <td height="25" colspan="5" class="A0100">ID/Code：<?php  echo "$GoodsId-$tempBarCode"; ?>
      <input name="BarCode" type="hidden" id="BarCode" value="<?php  echo $tempBarCode?>">
    </td>
    <td colspan="4" class="A0100" align="right">报表日期：<?php  echo date("Y年m月d日")?></td>
  </tr>
    <tr class=''>
    <td width="40" height="25" class="A0111" align="center">序号</td>
    <td width="80" class="A0101" align="center">日期</td>
	<td width="80" class="A0101" align="center">入库</td>
    <td width="80" class="A0101" align="center">转入</td>
	<td width="80" class="A0101" align="center">报废</td>
    <td width="80" class="A0101" align="center">个人领用</td>
    <td width="80" class="A0101" align="center">个人报废</td>
    <td width="80" class="A0101" align="center">个人退回</td>
    <td width="80" class="A0101" align="center">个人转交</td>
  </tr>
<?php
$NumOfCol=7;
$ColTemp=$NumOfCol;//当前列
$DateTemp="";
$Rowtemp=0;
$LastEstate=$Estate;
for($i=0;$i<$count;$i++){
	$Date=$grade[Date][$i];	
	$People=$grade[People][$i];
	$Sign=$grade[Sign][$i];//有数据的列
	if($DateTemp!=$grade[Date][$i]){//新行,如果日期与参照日期不一致，表示新行开始
		$DateTemp=$grade[Date][$i];//重新设置参照日期
		if($ColTemp!=$NumOfCol){//如果当前列数不是4，表示上一行未结束,先补足上一行
			for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
				echo "<td  class='A0101'>&nbsp</td>";
				}
			echo "</tr>";//结束上一行
			}
		//新行正式开始
		$ColTemp=0;
		$Rowtemp++;
		echo"<tr><td class='A0111' align='center'>$Rowtemp</td><td class='A0101' align='center'>$Date</td>";
	}

	for($ColTemp=$ColTemp+1;$ColTemp<$Sign*1;$ColTemp++){
		echo "<td  class='A0101'>&nbsp</td>";
		}
	echo"<td  class='A0101'><div align='center'>$People</div></td>";


	switch($ColTemp){
		case 1://入库
		   $LastEstate=1;
		   break;
		case 2://转入
		   $LastEstate=1;
		   break;
		case 3://报废
		   $LastEstate=0;
		   break;
	    case 4://个人领用
		   $LastEstate=2;
		   break;
		case 5://个人报废
		   $LastEstate=0;
		   break;
		case 6://个人退回
		   $LastEstate=1;
		   break;
	    case 7: //个人转交
		   $LastEstate=2;
			 break;
	 }
	
}

if($ColTemp!=$NumOfCol){//上一行未结束
	for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
		echo "<td  class='A0101'>&nbsp</td>";
		}
	}

         switch($Estate){
                case 1:
                        $EstateStr="<span class='greenB'>在库</span>";  break;
                case 2:
                        $EstateStr="<span class='blueB'>领用</span>";  break;
                case 0:
                        $EstateStr="<span class='redB'>报废</span>";  break;
                      }

         switch($LastEstate){
                case 1:
                        $LastEstateStr="<span class='greenB'>在库</span>";  break;
                case 2:
                        $LastEstateStr="<span class='blueB'>领用</span>";  break;
                case 0:
                        $LastEstateStr="<span class='redB'>报废</span>";  break;
                      }


?>
    <tr class=''>
    <td width="40" height="25" class="A0111" align="center">序号</td>
    <td width="80" class="A0101" align="center">日期</td>
	<td width="80" class="A0101" align="center">入库</td>
    <td width="80" class="A0101" align="center">转入</td>
	<td width="80" class="A0101" align="center">报废</td>
    <td width="80" class="A0101" align="center">个人领用</td>
    <td width="80" class="A0101" align="center">个人报废</td>
    <td width="80" class="A0101" align="center">个人退回</td>
    <td width="80" class="A0101" align="center">个人转交</td>
  </tr>

  <tr>
    <td height="21" colspan="9" class="A0100">&nbsp;</td>
  </tr>

  <tr class=''>
    <td height="21" colspan="2" class="A0111" align="center">项目</td>
    <td height="21" colspan="2"  class="A0101" align="center">目前的状态</td>
    <td colspan="2"  class="A0101" align="center">分析到的状态</td>
    <td colspan="3"  class="A0101" align="center">操作</td>
    </tr>

  <tr>
    <td height="24" colspan="2" class="A0111 " align="center">状态</td>
    <td colspan="2" align="center" class="A0101"><?php  echo $EstateStr?></td>
    <td colspan="2" align="center" class="A0101"><?php  echo $LastEstateStr?><input  type="hidden" id="LastEstate" name="LastEstate" value="<?php echo $LastEstate?>"></td>
    <td colspan="3" class="A0101">
	<?php 
	if($LastEstate==$Estate){
		echo"正确";
		}
	else{
				echo"<input type='button' name='Submit' value='更正状态' onClick='javascript:ErrorCorrection();'>";
		}
	?>
	</td>
  </tr>
</table>
</form>
</body>
</html>
<script>
function ErrorCorrection(){
	var BarCode=document.form1.BarCode.value;
    var LastEstate=document.form1.LastEstate.value;
		myurl="nonbom17_analyse_ajax.php?BarCode="+BarCode+"&LastEstate="+LastEstate; 
		var ajax=InitAjax(); 
	     ajax.open("GET",myurl,true);
	     ajax.onreadystatechange =function(){
		 if(ajax.readyState==4 && ajax.status ==200){// && ajax.status ==200
			      alert("已更正！");
		        	document.form1.submit();
	            }
		   }
	         ajax.send(null); 
  }
</script>
