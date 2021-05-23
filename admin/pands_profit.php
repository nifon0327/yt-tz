<?php     //   DP与MC不同  DP 为pands_profitNew.php电信---yang 20120801
//利润行政费用百分比
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
$ColsNumber=12;
$tableMenuS=600;
ChangeWtitle("$SubCompany 产品利润查询");
$Th_Col="序号|30|产品ID|45|中文名|200|Product Code|180|检讨|30|背卡<br>条码|30|PE袋<br>条码|30|外箱<br>标签|30|售价|50|&nbsp;|20|NO.|25|配件ID|50|配件名称|200|图档|30|历史订单|60|单价|50|单位|40|对应<br>数量|50|产品<br>成本|50|备品率|60|供应商|100|理论净利|70";//
include "../model/subprogram/read_model_3.php";
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
$j=1;$tId=1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.ProductId,P.cName,P.eCode,P.TestStandard,P.Price,F.Rate
FROM $DataIn.pands A 
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata F ON F.Id=C.Currency
WHERE 1 AND A.ProductId='$Cid' GROUP BY ProductId ORDER BY A.Id";

$myResult= mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$ProductId=$myRow["ProductId"];
	$cName=$myRow["cName"];
	$productPrice=$myRow["Price"];
	$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
	
	$TestStandard=$myRow["TestStandard"];
	include "../model/subprogram/product_teststandard.php";
	$productRate=$myRow["Rate"];//汇率
	$saleRMB=sprintf("%.2f",$productPrice*$productRate);//售价RMB
	//读取配件数
	$PO_Temp=mysql_query("SELECT count(*) FROM $DataIn.pands WHERE ProductId=$ProductId",$link_id);
	$PO_myrow = mysql_fetch_array($PO_Temp);
	$numrows=$PO_myrow[0];
	echo"<table id='ListTable$j' width='$tableWidth' border='0' cellspacing='0'  bgcolor='#FFFFFF'><tr>";
	$m=1;	echo"<td class='A0111' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$ProductId</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$TestStandard</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$eCode</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$CaseReport</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$CodeFile</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$LableFile</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$BoxFile</td>";
	$m=$m+2;echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='right'>$productPrice</td>";
	$m=$m+2;
	if($numrows>0){
		//从配件表和配件关系表中提取配件数据	  
		$cbResult = mysql_query("
		SELECT D.StuffCname,D.Price,D.Gfile,D.Gstate,D.Picture,D.StuffId,D.TypeId,M.Name,IFNULL(C.Rate,1) AS Rate,G.Forshort,G.Currency,G.Currency,G.ProviderType,P.Relation,P.Id ,P.Diecut,P.Cutrelation ,U.Name AS UnitName,MT.TypeColor,S.uName  AS bpRateName
		FROM  $DataIn.pands P
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
		LEFT JOIN $DataIn.trade_object G ON G.CompanyId=B.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=G.Currency
		LEFT JOIN $DataPublic.standbyrate  S  ON S.Id=P.bpRate
		WHERE P.ProductId='$ProductId' 
		ORDER BY substring_index('1,0,3,2',MT.Id,1),P.Id",$link_id);
		$k=1;
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		if($cbRow=mysql_fetch_array($cbResult)) {//如果设定了产品配件关系
			$BuyRmbSum=0;	//配件全部成本
			$BuyHzSum=0;	//非美元行政成本
			do{	
				$n=$m;
				$StuffId=$cbRow["StuffId"];			//配件ID
				$StuffCname=$cbRow["StuffCname"];	//配件名称
				$TypeId=$cbRow["TypeId"];			//产品分类
				$Name=$cbRow["Name"];				//采购
				$Forshort=$cbRow["Forshort"];		//供应商
				$stuffPrice=$cbRow["Price"];		//价格
				$Currency=$cbRow["Currency"];		//货币ID
				$gRate=$cbRow["Rate"];				//货币汇率
				$Relation=$cbRow["Relation"];		//对应数量
				$UnitName=$cbRow["UnitName"]==""?"&nbsp;":$cbRow["UnitName"];		//单位
				 $bpRateName=$cbRow["bpRateName"] ==""?"&nbsp;":$cbRow["bpRateName"] ;
				$OppositeQTY=explode("/",$Relation);
				$Diecut=$cbRow["Diecut"]==""?"&nbsp;":$cbRow["Diecut"];
	            $Cutrelation=$cbRow["Cutrelation"]==0?"&nbsp;":$cbRow["Cutrelation"];
				$thisRMB=$OppositeQTY[1]!=""?sprintf("%.4f",$gRate*$stuffPrice*$OppositeQTY[0]/$OppositeQTY[1]):sprintf("%.4f",$gRate*$stuffPrice*$OppositeQTY[0]);	//此配件的成本
				$BuyRmbSum+=$thisRMB;					//成本累加
				$BuyHzSum=$Currency==1?($BuyHzSum+$thisRMB):$BuyHzSum;				//自购成本累加
				$thisRMB=number_format($thisRMB,3);
				$TypeColor=$cbRow["TypeColor"];
				if($Currency==2){//美金标红色
					$stuffPrice="<span class='redB'>".$stuffPrice."</span>";
					$Relation="<span class='redB'>".$Relation."</span>";
					$thisRMB="<span class='redB'>".$thisRMB."</span>";
					$Forshort="<span class='redB'>".$Forshort."</span>";
					}
				if($bpRateName!="")$bpRateName="<a href='standbyrate_read.php'   target='_blank'>$bpRateName</a>";
				$theDefaultColor=$TypeColor;
				$ProviderType=$cbRow["ProviderType"];
						switch($ProviderType){
						   case 2:$TypeColor="style='color:#FF00FF'";break;
						 }
				$Picture=$cbRow["Picture"];			//配件照片参数
				$Gfile=$cbRow["Gfile"];				//配件图档参数
				$Gstate=$cbRow["Gstate"];  			//图档状态
				include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
                                
                                   //加工工序
                        $CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.process_bom A  WHERE A.ProductId='$ProductId' AND A.StuffId='$StuffId' LIMIT 1",$link_id);
                        if($CheckProcessRow=mysql_fetch_array($CheckProcessSql)){
                              $showProcess="<img onClick='ShowDropTable(ProcessTable$tId,ProcessDiv$tId,\"processbom_ajax\",\"$ProductId|$StuffId|0\");'  src='../images/showtable.gif' 
			title='显示或隐藏加工工序资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor'>";
			      $ProcessTable="<tr id='ProcessTable$tId' style='display:none;background:#83b6b9;'><td colspan='12'><div id='ProcessDiv$tId' width='720'></div></td></tr>"; 
                              $tId++;
                            }
                        else{
                            $showProcess="&nbsp;";
                            $ProcessTable="";
                        }
                        
				//配件名称
				$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId' target='_blank'>查看</a>";
				
				if($k>1){echo"<tr bgcolor='$theDefaultColor'>";}
				
                 echo"<td  class='A0100' align='center' width='$Field[$n]' $MTCOLOR>$showProcess</td>";              
				$n=$n+2;    echo"<td class='A0101' width='$Field[$n]' align='center'>$k</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='center'>$StuffId</td>";
				include "../model/subprogram/stuffimg_model.php";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]'>$StuffCname</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]'>$Gfile</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='center'>$OrderQtyInfo</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='right' $TypeColor>$stuffPrice</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='center'$TypeColor>$UnitName</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='center' $TypeColor>$Relation</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='right' $TypeColor>$thisRMB</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' align='center'>$bpRateName</td>";
				$n=$n+2;	echo"<td class='A0101' width='$Field[$n]' $TypeColor>$Forshort</td>";
				//$n=$n+2;	$jlCel=$k==1?"<td class='A0101' width='' rowspan='$numrows' align='right'>&nbsp;</td>":"";//首行最后一列
				$n=$n+2;	$jlCel=$k==1?"<td class='A0101' width='$Field[$n]' rowspan='$numrows' align='right'>&nbsp;</td>":"";//首行最后一列
				echo"$jlCel";
				echo "</tr>";//结束首行
                                echo $ProcessTable;
				$k++;
				} while ($cbRow = mysql_fetch_array($cbResult));
			$profitRMB=sprintf("%.2f",$saleRMB-$BuyRmbSum-$BuyHzSum*$HzRate);	//理论净利
			$BuyRmbSum=sprintf("%.2f",$BuyRmbSum);								//配件总成本
			$BuyHzSum=sprintf("%.2f",$BuyHzSum);								//配件非美元总成本
			$BuyRmbSuma=sprintf("%.2f",$BuyRmbSum+$BuyHzSum*$HzRate);			//配件总成本+行政费
			$productRate=sprintf("%.2f",$productRate);							//产品汇率
			$profitRMBPC=sprintf("%.0f",($profitRMB*100/$saleRMB));				//净利百分比
			$profitRMB=$profitRMBPC>15?"<span class='greenB'>$profitRMB($profitRMBPC%)</sapn>":($profitRMBPC>7?"<span class='yellowB'>$profitRMB($profitRMBPC%)</sapn>":($profitRMB<0?"<span class='purpleB'>$profitRMB($profitRMBPC%)</sapn>":"<span class='redB'>$profitRMB($profitRMBPC%)</sapn>"));
			echo"<tr><td colspan='2' align='center' height='30' class='A0111' bgcolor='#9BCFE3'>$profitRMBPC RMB利润计算</td><td colspan='7' align='right' class='A0101'>$productPrice*$productRate=$saleRMB</td><td colspan='10' align='right' class='A0101'>$BuyRmbSum($BuyRmbSuma)</td><td colspan='3' align='right' class='A0101'>$profitRMB</td></tr>";
			echo "</table>";					
			//写理论净利至首行最后一列
			echo"<script>ListTable$j.rows[0].cells[21].innerHTML=\"$profitRMB\"</script>";
			}//if($cbRow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
		}//结束存在配件表
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
pBottom(1,1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>