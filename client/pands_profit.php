<?php   
//电信-zxq 2012-08-01
/*
$DataPublic.currencydata
$DataIn.pands
$DataIn.productdata
$DataIn.trade_object
$DataIn.bps
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
二合一已更新
*/
//步骤1
//毛利行政费用百分比
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 产品毛利查询");
$Th_Col="序号|30|ID|45|中文名|200|Product Code|180|Price|50|NO.|25|配件ID|45|配件名称|200|单价|50|对应<br>数量|50|产品<br>成本|50|Profit|60";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
//解密
$fArray=explode("|",$Cid);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$ProductId=anmaOut($RuleStr1,$EncryptStr1,"f");
$SearchRows=" and A.ProductId='$ProductId'";
echo"汇率：";
$currency_Result = mysql_query("SELECT * FROM $DataPublic.currencydata WHERE Symbol!='RMB' and Estate=1 order by Id",$link_id);
if($currency__Myrow = mysql_fetch_array($currency_Result)){
	do{
		$Name=$currency__Myrow["Name"];
		$Symbol=$currency__Myrow["Symbol"];
		$Rate=$currency__Myrow["Rate"];	
		if($Symbol=="HKD"){$HKD=$Rate;}
		echo $Name."$Rate&nbsp;&nbsp;";
		}while($currency__Myrow = mysql_fetch_array($currency_Result));
	}

//步骤5：
include "../admin/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.ProductId,P.cName,P.eCode,P.TestStandard,P.Price,P.CompanyId 
FROM $DataIn.pands A
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
where 1 $SearchRows GROUP BY ProductId ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$m=1;
		//$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$productPrice=$myRow["Price"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			include "subprogram/teststandard_y.php";
			}
		else{
			if($TestStandard==2){
				$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
				}
			else{
				$TestStandard=$cName;
				}
			}
		$CompanyId=$myRow["CompanyId"];
		if($CompanyId==1044){
			$psValue=0.95;
			}
		else{
			$psValue=1;
			}

		$currency_Temp = mysql_query("SELECT U.Rate,U.Symbol 
		FROM $DataPublic.currencydata U,$DataIn.trade_object  C 
		WHERE U.Id=C.Currency and C.CompanyId=$CompanyId",$link_id);
		if($RowTemp = mysql_fetch_array($currency_Temp)){
			$productRate=$RowTemp["Rate"];//汇率
			$Symbol=$RowTemp["Symbol"];//货币符号
			}		
		//售价RMB
		$saleRMB=sprintf("%.4f",$productPrice*$productRate);
//产品处理完毕
		//读取配件数
		$PO_Temp=mysql_query("SELECT count(*) FROM $DataIn.pands WHERE ProductId=$ProductId",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0];
		echo"<table id='ListTable$j' width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		echo"<td class='A0111' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$ProductId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$TestStandard</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$eCode</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='right'>$productPrice</td>";
		$m=$m+2;
		if($numrows>0){
			//从配件表和配件关系表中提取配件数据	  
			$StuffResult = mysql_query("SELECT D.StuffCname,D.Price,D.Picture,D.StuffId,P.Relation,P.Id 
				FROM  $DataIn.pands P,$DataIn.stuffdata D WHERE P.ProductId='$ProductId' AND D.StuffId=P.StuffId 
				ORDER BY P.Id",$link_id);
			$k=1;
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$Price=$StuffMyrow["Price"];
					$Relation=$StuffMyrow["Relation"];
					$OppositeQTY=explode("/",$Relation);
					$bps = mysql_query("SELECT M.Name,P.Forshort,C.Rate
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
					LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
					WHERE B.StuffId='$StuffId'",$link_id);
					if($SSMMyrow=mysql_fetch_array($bps)){
						$Name=$SSMMyrow["Name"];
						$Forshort=$SSMMyrow["Forshort"];
						$gRate=$SSMMyrow["Rate"];//汇率
						}
					//配件名称
					if($k>1){echo"<tr>";}
					echo"<td class='A0101' width='$Field[$n]' align='center'>$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$StuffId</td>";
					$n=$n+2;
					include "../model/subprogram/stuffimg_model.php";
					echo"<td class='A0101' width='$Field[$n]'>$StuffCname</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='right'>$Price</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'>$Relation</td>";					
					
					if ($OppositeQTY[1]!=""){//非整数对应关系
						$thisRMB=sprintf("%.4f",$gRate*$Price*$OppositeQTY[0]/$OppositeQTY[1]);
						}
					else{//整数对应关系
						$thisRMB=sprintf("%.4f",$gRate*$Price*$OppositeQTY[0]);
						}
					$buyRMB=$buyRMB+$thisRMB;
					$n=$n+2;
						echo"<td class='A0101' width='$Field[$n]' align='right'>$thisRMB</td>";
						if($k==1){
							$n=$n+2;
							echo"<td class='A0101' width='$Field[$n]' rowspan='$numrows' align='right'>&nbsp;</td></tr>";
							}
						else{
							echo"</tr>";
							}					
					$k++;
					$i++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
					
					$profitRMB=sprintf("%.4f",$saleRMB*$psValue-$buyRMB*$HzRate);
					$profitRMB=$profitRMB<=0.3?"<span class='redB'>$profitRMB</sapn>":$profitRMB=$profitRMB<=0.7?"<span class='yellowB'>$profitRMB</sapn>":"<span class='greenB'>$profitRMB</sapn>";
					echo"<tr><td colspan='2' align='center' height='30' class='A0111' bgcolor='#9BCFE3'>RMB毛利计算</td><td colspan='3' align='right' class='A0101'>$productPrice*$productRate=$saleRMB</td><td colspan='6' align='right' class='A0101'>$buyRMB</td><td align='right' class='A0101'>$profitRMB</td></tr>";
					echo "</table>";					
					echo"<script>ListTable$j.rows[0].cells[11].innerHTML=\"$profitRMB\"</script>";
				}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			}//结束存在配件表
		$j++;
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//include "../model/subprogram/read_model_menu.php";
?>
