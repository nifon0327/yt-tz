<?php   
include "../model/modelhead.php";
include "../public/kqcode/kq_function.php";

echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
ChangeWtitle($SubCompany."车间生产统计");
//取得计算工价的时薪$OneHourSalaryt
include "../model/subprogram/onehoursalary.php";
?>
<style type="text/css">
<!--
td {height:31px;vertical-align: middle;}
.groupclass{
	color: #999999;	
	}
-->
</style>
<script>
function ViewMonths(f,TempTD){//只有年才展开
	var e = document.getElementById(TempTD);
	var len=TempTD.length;
	var TempSTR=len==4?"年":"";
	if(e.style.display=="none"){
		f.innerHTML="<div class=rmbB>"+TempTD+TempSTR+"<span class='yellowB'> <- </span></div>";
		var url="desk_cjtj_count_2.php?ChooseTime="+TempTD;
		var ajax=InitAjax(); 
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
			if(ajax.readyState==4){// && ajax.status ==200
				e.innerHTML=ajax.responseText;
				}
			}
			ajax.send(null);
		}
	else{
		e.innerHTML="&nbsp;";
		f.innerHTML="<div class=rmbB>"+TempTD+TempSTR+"<span class='yellowB'> -> </span></div>";
		}
	e.style.display=(e.style.display=="none")?"":"none";
	}
</script>
<body>
<?php   
$LinkTo=$From==""?2:"";
//$LinkToSTR=$From==""?"点击这里显示最近5个月的月统计":"点击这里显示年统计";
?>
<table cellspacing="0">
		<tr><td align="center" style="height:15px"><?php    echo $TempTitle?>生产统计</td></tr>
		<tr><td align="right" style="height:15px"><table cellspacing='0' border='0' cellpadding='0' width="100%">
				<tr><td align="left" style="height:15px">
					<a href="?From=<?php    echo $LinkTo?>"><?php    echo $LinkToSTR?></a></td><td align="right" style="height:15px"><?php    echo $S_Company?>
				</td></tr>
			</table></td></tr>
	<tr><td>
		<table valign="top" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
			<tr>
			<td><?php    include "desk_cjtj_count3_1.php";?></td><!-- 说明表格 -->
			<?php   
			//显示年或月份明细统计
			switch($From){
			//显示年统计
			case 2:
				$sYear=2010;
				$eYear=date("Y");
				for($CheckTheDay=$eYear;$CheckTheDay>=$sYear;$CheckTheDay--){
					echo"<td width='240px' valign='top'>";
					include "desk_cjtj_count_data.php";
					echo"</td>";//<!-- 年数据单元格 -->
					echo"<td id='$CheckTheDay' style='display:none;'>&nbsp;</td>";//<!-- 隐藏的月数据单元格 -->
					}
				break;
			default://显示月份统计
				////////////////////////////////
				$NowMonth=date("Y-m-01");
				$MonthCount=3;
				//初始化统计数组
				$m=0;
				$groupSql=mysql_query("SELECT G.GroupId FROM `$DataIn`.`staffgroup` G WHERE 1 AND G.TypeId>0 AND G.Estate=1 ORDER BY G.SortId DESC",$link_id);
				if($groupRow=mysql_fetch_array($groupSql)){
					do {
						 $GroupId=$groupRow["GroupId"];
						 $GroupArray[$m]=$GroupId;
						 $sumAmount[$GroupId]["xq"]=0;
			             $sumAmount[$GroupId]["yg"]=0;
			             $sumAmount[$GroupId]["sj"]=0;
						 $m++;
					}while($groupRow=mysql_fetch_array($groupSql));
				 }
			      $sumAmount["other"]["yg"]=0;
			      $sumAmount["other"]["sj"]=0;
				  $sumFlag=0;
				for($count_i=0;$count_i<$MonthCount;$count_i++){
					$CheckTheDay=date("Y-m",strtotime("$NowMonth -$count_i month"));//echo $M."<br>";
					//$CheckTheDay="2011-06";
				  if ($CheckTheDay>=date("2011-06")){
					echo"<td width='1px' valign='top'>";
					$bgColor=$count_i%2==0?"bgcolor='#D9F0DD'":"";
					echo "<td valign='top' $bgColor width='1px'>";
					include"desk_cjtj_count_data.php";
					echo"</td>";
					$sumFlag++;
					}
				}
				////////////////////////////////
				if ($sumFlag>0){//显示合计
				  //$count_i++;
				  $bgColor=$count_i%2==0?"bgcolor='#D9F0DD'":"";
				  echo "<td valign='top' $bgColor width='1px'>";
				  echo "<table width='200' border='0' cellpadding='0' cellspacing='0'>
                     <tr class=''>
                          <td colspan='3' align='center' class='A1101'style='height:20px'><div class=rmbB>合  计</div></td></tr>
                       <tr class=''>
                          <td width='60' align='center' class='A0101' style='height:20px'>需求支出</td>
                          <td width='80' align='center' class='A0101' style='height:20px'>预估支出</td>
                          <td width='60' align='center' class='A0101' style='height:20px'>实际支出</td>
                       </tr>";
				  $xqAmountSUM=0;$ygAmountSUM=0;$sjAmountSUM=0;$n=1;
		          while (list($Keys,$gId) = each($GroupArray))
                  {
					 $xqAmount=$sumAmount[$gId]['xq'];
					 $xqAmountSUM+=$xqAmount;
	                 $ygAmount=$sumAmount[$gId]['yg'];
					 $ygAmountSUM+=$ygAmount;
	                 $sjAmount=$sumAmount[$gId]['sj'];
					 $sjAmountSUM+=$sjAmount;
					 
				   $bgcolor=$n%2==0?"bgcolor='#cccccc'":"";
				   resultsAnalysis($xqAmount,$ygAmount,$sjAmount,$bgcolor);
				   $n++;
				  }
                           /*
				     $ygAmount=$sumAmount["other"]["yg"];
					 $ygAmountSUM+=$ygAmount;
	                 $sjAmount=$sumAmount["other"]["sj"];
					 $sjAmountSUM+=$sjAmount;
					 $ygAmount=$ygAmount==0?"&nbsp;":number_format($ygAmount);
			         $sjAmount=$sjAmount==0?"&nbsp;":number_format($sjAmount);
					 $bgcolor=$n%2==0?"bgcolor='#cccccc'":"";
				     echo"<tr $bgcolor align='right'>
		  	           <td class='A0101'>&nbsp;</td>
			           <td class='A0101'>$ygAmount</td>
			           <td class='A0101'>$sjAmount</td></tr>";
					
					 $ygAmount=$sumAmount["temp"]["yg"];
					 $ygAmountSUM+=$ygAmount;
	                 $sjAmount=$sumAmount["temp"]["sj"];
					 $sjAmountSUM+=$sjAmount;
					 $ygAmount=$ygAmount==0?"&nbsp;":number_format($ygAmount);
			         $sjAmount=$sjAmount==0?"&nbsp;":number_format($sjAmount);
					 $n++;
					 $bgcolor=$n%2==0?"bgcolor='#cccccc'":"";
				     echo"<tr $bgcolor align='right'>
		  	           <td class='A0101'>&nbsp;</td>
			           <td class='A0101'>$ygAmount</td>
			           <td class='A0101'>$sjAmount</td></tr>";
			*/		   
	                 $sjAmount=$sumAmount["gs"]["sj"];
					 $sjAmountSUM+=$sjAmount;
			         $sjAmount=$sjAmount==0?"&nbsp;":number_format($sjAmount);
					 $n++;
					 $bgcolor=$n%2==0?"bgcolor='#cccccc'":"";
				     echo"<tr $bgcolor align='right'>
		  	           <td class='A0101'>&nbsp;</td>
			           <td class='A0101'>&nbsp;</td>
			           <td class='A0101'>$sjAmount</td></tr>";
					
					resultsAnalysis($xqAmountSUM,$ygAmountSUM,$sjAmountSUM,$bgcolor);  
				    echo "</table>";
				    echo"</td>";
				}
				break;
			}
			
  function resultsAnalysis($xqAmount,$ygAmount,$sjAmount,$bgcolor){ 	  //需求预估百分比分析
			$TrendsImg="<div>&nbsp;</div>";
			$ygValue="<div>&nbsp;</div>";
			$sjValue="<div>&nbsp;</div>";
			if($xqAmount>0){//需求值为0时重置
				$ygValue=round(($xqAmount-$ygAmount)/$ygAmount*100);
				//值区域判断
				if($xqAmount-$ygAmount>0){//高估
					$TrendsImg="<div class='redB'>▲</div>";
					$ygValue="<div class='redB'>$ygValue%</div>";
					}
				else{////低估
					$TrendsImg="<div class='greenB'>▼</div>";
					$ygValue=$ygValue*(-1);
					$ygValue="<div class='greenB'>$ygValue%</div>";
					}
				if($sjAmount>0){
					$sjValue=round(($xqAmount-$sjAmount)/$sjAmount*100);
					if($xqAmount-$sjAmount>0){//高估
						$TrendsImg="<div class='redB'>▲</div>";
						$sjValue="<div class='redB'>$sjValue%</div>";
						}
					else{////低估
						$TrendsImg="<div class='greenB'>▼</div>";
						$sjValue=$sjValue*(-1);
						$sjValue="<div class='greenB'>$sjValue%</div>";
						}
					}
				}
			   $xqAmount=$xqAmount==0?"&nbsp;":number_format($xqAmount);
			   $ygAmount=$ygAmount==0?"&nbsp;":number_format($ygAmount);
			   $sjAmount=$sjAmount==0?"&nbsp;":number_format($sjAmount);
			  echo"<tr $bgcolor align='right'>
		  	           <td class='A0101'>$xqAmount $TrendsImg</td>
			           <td class='A0101'>$ygAmount $ygValue</td>
			           <td class='A0101'>$sjAmount $sjValue</td></tr>";
  }
			?>
			</tr>
		</table>
	</td></tr>
</table>
</body>
</html>
