<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 下单、出货金额数量分析");
?>
<style type="text/css">
<!--
body{
	background: #fff;
}
.colorB{color:#FF6B00};

.BgColorSet{
	background: #bbb;
}
p{
	font-size: 15px;
   text-align: center;
   font-weight: bold;
}
-->
</style>
<p>下单、出货金额数量分析</p>
<?php 
		$MonthArray=array();
		//起始交易年 与终止交易年
		$MonthRow=mysql_fetch_array(mysql_query("
			SELECT  MAX(EndY) AS EndY,MIN(StartY) AS StartY FROM ( 
				SELECT MAX(DATE_FORMAT(M.Date,'%Y')) AS EndY,MIN(DATE_FORMAT(M.Date,'%Y')) AS StartY FROM $DataIn.ck1_rkmain M WHERE M.CompanyId='$myCompanyId'
				UNION ALL 
				SELECT MAX(DATE_FORMAT(M.Date,'%Y')) AS EndY,MIN(DATE_FORMAT(M.Date,'%Y')) AS StartY FROM $DataIn.cg1_stockmain M WHERE M.CompanyId='$myCompanyId'
			)A
			",$link_id));
		$StartY=$MonthRow["StartY"];
		$EndY=$MonthRow["EndY"];
		$StartY=$StartY==""?date("Y"):$StartY;
        $EndY=$EndY==""?date("Y"):$EndY;
        
		$NumY=$EndY-$StartY;
		$K=0;
		$AmountOutArray=array();
		$AmountInArray=array();
		$QtyOutArray=array();
		$QtyInArray=array();
?>
<?php		
		
		for($i=$EndY;$i>=$StartY;$i--){
		/*
			$K++;
						$SumIn=0;$SumOut=0;$AvgIn=0;$AvgOut=0;$SumQtyOut=0;$SumQtyIn=0;
			$S_J=0;
			$E_J=0;
		*/
			for($j=1;$j<13;$j++){
				$Month=$i."-".$j;
				$AmountOutArray[$Month]=0;
		        $AmountInArray[$Month]=0;
		        $QtyOutArray[$Month]=0;
		        $QtyInArray[$Month]=0;
		           //出货总数    
				$checkSql=mysql_query("
				SELECT SUM(AmountOut) AS AmountOut,SUM(AmountIn) AS AmountIn,SUM(QtyOut) AS  QtyOut,SUM(QtyIn) AS  QtyIn FROM (
					SELECT SUM(S.Qty*G.Price) AS AmountOut,'0' AS AmountIn,SUM(S.Qty) AS QtyOut,'0' AS QtyIn FROM $DataIn.ck1_rksheet S 
					LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=S.Mid
					LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
					WHERE M.CompanyId='$myCompanyId' AND DATE_FORMAT(M.Date,'%Y-%c')='$Month'
				UNION ALL
					SELECT '0' AS AmountOut,SUM((S.FactualQty+S.AddQty)*S.Price) AS AmountIn,'0' AS QtyOut,SUM(S.FactualQty+S.AddQty) AS QtyIn
					FROM $DataIn.cg1_stocksheet S 
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					WHERE M.CompanyId='$myCompanyId' AND DATE_FORMAT(M.Date,'%Y-%c')='$Month'
				)A
				",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
				      $AmountInArray[$Month]=$checkRow["AmountIn"];
				       $QtyInArray[$Month]=$checkRow["QtyIn"];
		              $AmountOutArray[$Month]=$checkRow["AmountOut"];
		              $QtyOutArray[$Month]=$checkRow["QtyOut"];
				}
	      }
	  }
	
	 echo "<table  border='0' align='left' cellspacing='0' ><tr style='background:#ddd;'>";
	 echo "<td colspan='2' class='A1111' align='center' height='30'>年\月份 </td>";
	 for($j=1;$j<13;$j++){
	      echo "<td  width='70' class='A1101' align='center' height='30' colspan='2'>$j 月</td>";
	 }
	      echo "<td  width='70' class='A1101' align='center' height='30' colspan='2'>合计</td></tr>"; 
	      
	 for($i=$EndY;$i>=$StartY;$i--){
			   $SumIn=0;$SumOut=0;$SumQtyOut=0;$SumQtyIn=0;$AvgIn=0;$AvgOut=0;
			   echo "<tr height='35'>";
			   echo " <td rowspan='2' class='A0111' width='45' align='center'><b>" . $i . "年</b></td>
			    <td width='30' align='center' class='A0001' scope='col'>下单</td>";
			   $chList_STR="<tr  height='35' style='background:#ddd;'>
			   <td width='30' align='center' style='background:#ddd;' class='A0101' scope='col'>出货</td>";
	          for($j=1;$j<13;$j++){
	                 $Month=$i."-".$j;
	                $SumOut+=$AmountOutArray[$Month];
	                $SumIn+=$AmountInArray[$Month];
	                $SumQtyOut+=$QtyOutArray[$Month];
	                $SumQtyIn+= $QtyInArray[$Month];
	                
	                $imgVal="";
	                //增长率
	                if ($i>$StartY){
		                  $Y=$i-1;
		                  $mTemp=$Y."-".$j;
		                  $growthRate=$AmountOutArray[$mTemp]==0?0:($AmountOutArray[$Month]-$AmountOutArray[$mTemp])/$AmountOutArray[$mTemp];
		                  $growthRate=$growthRate*100;
		                  $imgH=12;
		                  if ($growthRate>0){
			                  //增长
			                  if ($growthRate>100){
				                           $imgVal="arrows_red_3.png";
				                           $imgH=14;
			                  }
			                  else{
			                          if ($growthRate>30){
				                            $imgVal="arrows_red_2.png";
			                          }else{
				                            $imgVal="arrows_red_1.png";
			                          }
			                 }
		                  }
		                 else{
			                 if ($growthRate<0){
			                         $growthRate=abs($growthRate); 
			                        if ($growthRate>100){
				                                 $imgVal="arrows_green_3.png";
				                                 $imgH=14;
			                         }
			                       else{
			                           if ($growthRate>30){
				                                 $imgVal="arrows_green_2.png";
			                          }else{
				                                 $imgVal="arrows_green_1.png";
			                          }
			                    }
			               }
		              }
	              }//增长率
	              $imgVal=$imgVal==""?"&nbsp;":"<img src='../images/$imgVal' class='img' height='$imgH'/>";
	              if ($AmountOutArray[$Month]==0) $imgVal="&nbsp;";
	              $AmountOut=$AmountOutArray[$Month]==0?"&nbsp;":"¥" . number_format($AmountOutArray[$Month]);
	              $AmountIn=$AmountInArray[$Month]==0?"&nbsp;":"¥" . number_format($AmountInArray[$Month]);
	              $QtyOut=zerotospace(number_format($QtyOutArray[$Month])); 
	              $QtyIn=zerotospace(number_format($QtyInArray[$Month]));     
	               
	               echo "<td class='A0000' width='20'>&nbsp;</td><td scope='col' class='A0001' align='right'><div>$AmountIn </div><div  class='colorB'>$QtyIn</div></td>";
	                $chList_STR.="<td class='A0100' align='right' valign='top' width='20'>$imgVal</td><td  scope='col' class='A0101' align='right'><div  class='div2'>$AmountOut</div><div   class='colorB'>$QtyOut</div></td>";
	       }         
			$SumOut=number_format($SumOut);$SumIn=number_format($SumIn);
			$SumQtyOut=number_format($SumQtyOut);$SumQtyIn=number_format($SumQtyIn);
			
			echo "<td class='A0000'>&nbsp;</td><td scope='col' class='A0001' align='right'><div>¥$SumIn</div><div  class='greenB'>$SumQtyIn</div></td>";
	             $chList_STR.="<td class='A0100'>&nbsp;</td><td  scope='col' class='A0101' align='right'><div>¥$SumOut</div><div  class='greenB'>$SumQtyOut</div></td>";
	       echo "</tr>";
	       echo $chList_STR . "</tr>";
}
	   echo "</table>";
?>
<div style='position: relative;float:left;width:100%;margin-left:20px;height:50px;'>
<br>
<span><img src='../images/arrows_red_1.png' class='img' height='14'/> 增长0～30%</span>&nbsp;&nbsp;
<span><img src='../images/arrows_red_2.png' class='img' height='14'/> 增长30～100%</span>&nbsp;&nbsp;
<span><img src='../images/arrows_red_3.png' class='img' height='16'/> 增长>100%</span>&nbsp;&nbsp;&nbsp;&nbsp;
<span><img src='../images/arrows_green_1.png' class='img' height='14'/> 衰减0～30%</span>&nbsp;&nbsp;
<span><img src='../images/arrows_green_2.png' class='img' height='14'/> 衰减30～100%</span>&nbsp;&nbsp;
<span><img src='../images/arrows_green_3.png' class='img' height='16'/> 衰减>100%</span>
<br>
</div>
