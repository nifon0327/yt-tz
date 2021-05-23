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
	margin-top:20px;
	margin-left:200px;
}
-->
</style>
<p>下单、出货金额数量分析</p>
 <table  width="100" border="0" align="left" cellspacing="0" style="margin-left:20px;">
 <tr  style='background:#ddd;'><td colspan="2" class="A1111" align="center" height="30">月份 \ 年</td></tr>
  <?php for ($i=0;$i<13;$i++){ 
      if ($i<12){
	      $monthStr=($i+1) ."月";
      }
      else{
	     //$monthStr=$i==12?"平均":"合计"; 
	     $monthStr="合计"; 
      }
  ?>
        
           <tr height="35">
                <td rowspan="2" class="A0111" width="50" align="center"><b><?php echo $monthStr; ?></b></td>
			    <td width="50" height="20" align="center" class="A0101" scope="col">下单</td>
           </tr>
			    <tr height="35">
		        <td width="50" align="center" style='background:#ddd;' class="A0101" scope="col">出货</td>
			    </tr>
 <?php }?>  
 </table>

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
		$NumY=$EndY-$StartY;
		$K=0;
		$AmountOutArray=array();
		$AmountInArray=array();
		$QtyOutArray=array();
		$QtyInArray=array();
		
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
	   
	 for($i=$EndY;$i>=$StartY;$i--){
	          echo "<table  width='100' border='0' align='left' cellspacing='0'>
 <tr style='background:#ddd;'><td  class='A1101' align='center' height='30' colspan='2'>$i 年</td><tr>";
			   $SumIn=0;$SumOut=0;$SumQtyOut=0;$SumQtyIn=0;$AvgIn=0;$AvgOut=0;
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
	              $imgVal=$imgVal==""?"":"<img src='../images/$imgVal' class='img' height='$imgH'/>";
	              
	              $AmountOut=$AmountOutArray[$Month]==0?"&nbsp;":"¥" . number_format($AmountOutArray[$Month]);
	              $AmountIn=$AmountInArray[$Month]==0?"&nbsp;":"¥" . number_format($AmountInArray[$Month]);
	          	  //$AmountOut=zerotospace(number_format($AmountOutArray[$Month]));
	             // $AmountIn=zerotospace(number_format($AmountInArray[$Month]));
	              $QtyOut=zerotospace(number_format($QtyOutArray[$Month])); 
	              $QtyIn=zerotospace(number_format($QtyInArray[$Month]));     
	               
	               echo "<tr height='35'><td class='A0100'>&nbsp;</td><td scope='col' class='A0101' align='right'><div>$AmountIn </div><div  class='colorB'>$QtyIn</div></td></tr>";
	               echo "<tr style='background:#ddd;' height='35'><td width='35' class='A0100' align='right' valign='top'>$imgVal</td><td  scope='col' class='A0101' align='right'><div  class='div2'>$AmountOut</div><div   class='colorB'>$QtyOut</div></td></tr>";
	         }//for($j=1;$j<13;$j++)
	         /*
	        if($i==date("Y")){//最后交易年为当前年
					$Avgsum=date("m");
			}
			else{
				    $Avgsum=12;
			}
			//平均数

			$AvgAmountOut=zerotospace(number_format($SumOut/$Avgsum));
			$AvgAmountIn=zerotospace(number_format($SumIn/$Avgsum));
			$AvgQtyOut=zerotospace(number_format($SumQtyOut/$Avgsum));
			$AvgQtyIn=zerotospace(number_format($SumQtyIn/$Avgsum));
			
			echo "<tr height='35'><td class='A0100'>&nbsp;</td><td scope='col' class='A0101' align='right'><div>¥$AvgAmountIn</div><div  class='greenB'>$AvgQtyOut</div></td></tr>";
	               echo "<tr style='background:#ddd;' height='35'><td class='A0100'>&nbsp;</td><td scope='col' class='A0101' align='right'><div>¥$AvgAmountOut</div><div  class='greenB'>$AvgQtyIn</div></td><tr>";
			*/
			$SumOut=number_format($SumOut);$SumIn=number_format($SumIn);
			$SumQtyOut=number_format($SumQtyOut);$SumQtyIn=number_format($SumQtyIn);
			
			echo "<tr height='35'><td class='A0100'>&nbsp;</td><td scope='col' class='A0101' align='right'><div>¥$SumIn</div><div  class='greenB'>$SumQtyIn</div></td></tr>";
	               echo "<tr  height='35' style='background:#ddd;'><td class='A0100'>&nbsp;</td><td  scope='col' class='A0101' align='right'><div>¥$SumOut</div><div  class='greenB'>$SumQtyOut</div></td></tr>";
	       echo"</table>";  
	   }
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
