<?php   //   DP与MC不同  DP 为pands_profitNew.php电信---yang 20120801
//利润行政费用百分比
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
$ColsNumber=12;
$tableMenuS=600;
ChangeWtitle("$SubCompany 产品利润查询");
$Th_Col="序号|30|产品ID|45|中文名|200|Product Code|180|检讨|30|背卡<br>条码|30|PE袋<br>条码|30|外箱<br>标签|30|售价|50|序号|60|配件ID|50|配件名称|350|图档|30|历史订单|60|含税价|50|单位|40|对应<br>数量|50|产品<br>成本|50|备品率|60|供应商|100|理论净利|70";//

include "../model/subprogram/read_model_3.php";
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
$j=1;$tId=1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.ProductId,P.cName,P.eCode,P.TestStandard,P.Price,F.Rate
FROM $DataIn.pands A 
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata F ON F.Id=C.Currency
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
	
	//recode change
	$changeRowResult = mysql_query("Select count(*) From $DataIn.pandscharge Where ProductId=$ProductId And oldStuffId != '0' And newStuffId = '0' And Estate = '1' ");
	$changeRow = mysql_fetch_array($changeRowResult);
	
	
	$numrows=$PO_myrow[0]+$changeRow[0];
	
	echo"<table id='ListTable$j' width='$tableWidth' border='0' cellspacing='0'  bgcolor='#FFFFFF'><tr>";
	$m=1;	
	$uWidth = $Field[$m]+1;
	echo"<td class='A0111' width='$uWidth' rowspan='$numrows' scope='col' align='center'>$j</td>";
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
		
		$StuffIdList="";$UniteIdList=""; 
		$cbResultSql = "
		(SELECT D.StuffCname,D.Price,D.Gfile,D.Gstate,D.Picture,D.StuffId,D.TypeId,M.Name,C.Rate,G.Forshort,G.Currency,G.Currency,G.ProviderType,P.Relation,P.Id ,P.Diecut,P.Cutrelation ,U.Name AS UnitName,MT.TypeColor,S.uName  AS bpRateName, MT.Id as MTId,P.bpRate, '' as chargeId
		FROM  $DataIn.pands P
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
		LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
		LEFT JOIN $DataIn.trade_object G ON G.CompanyId=B.CompanyId
		LEFT JOIN $DataIn.currencydata C ON C.Id=G.Currency
		LEFT JOIN $DataIn.standbyrate  S  ON S.Id=P.bpRate
		WHERE P.ProductId='$ProductId')
		Union
		(SELECT D.StuffCname,D.Price,D.Gfile,D.Gstate,D.Picture,D.StuffId,D.TypeId,M.Name,C.Rate,G.Forshort,G.Currency,G.Currency,G.ProviderType,P.Relation,P.Id ,P.Diecut,P.Cutrelation ,U.Name AS UnitName,MT.TypeColor,S.uName  AS bpRateName,MT.Id as MTId,P.bpRate, P.Id as chargeId
		FROM  $DataIn.pandscharge P
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.oldStuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
		LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
		LEFT JOIN $DataIn.trade_object G ON G.CompanyId=B.CompanyId
		LEFT JOIN $DataIn.currencydata C ON C.Id=G.Currency
		LEFT JOIN $DataIn.standbyrate  S  ON S.Id=P.bpRate
		WHERE P.ProductId='$ProductId' 
		And P.oldStuffId != '0' And P.newStuffId = '0'
		And P.Estate = '1' 
		) 
		ORDER BY substring_index('1,0,3,2',MTId,1),Id";
		$cbResult = mysql_query($cbResultSql);
		
		$k=1;
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		if($cbRow=mysql_fetch_array($cbResult)) {//如果设定了产品配件关系
			$BuyRmbSum=0;	//配件全部成本
			$BuyHzSum=0;	//非美元行政成本
			do{	
				$n=$m;
				$StuffId=$cbRow["StuffId"];			//配件ID
				//生成组装图
				$StuffIdList.=$k==1?$StuffId:"|$StuffId";
				$UniteId="";
				$UniteResult=mysql_query("SELECT U.uStuffId FROM $DataIn.pands_unite U   WHERE  U.ProductId='$ProductId' AND U.StuffId='$StuffId'",$link_id);
				while($UniteRow = mysql_fetch_array($UniteResult)){
				       $UniteId.=$UniteId==""?$UniteRow["uStuffId"]:"," . $UniteRow["uStuffId"];
				   }
				$UniteIdList.=$k==1?$UniteId:"|$UniteId";
				$chargeId = $cbRow["chargeId"];
				$StuffCname=$cbRow["StuffCname"];	//配件名称
				$TypeId=$cbRow["TypeId"];			//产品分类
				$Name=$cbRow["Name"];				//采购
				$Forshort=$cbRow["Forshort"]==""?"&nbsp;":$cbRow["Forshort"];		//供应商
				$stuffPrice=$cbRow["Price"];		//价格
				$Currency=$cbRow["Currency"];		//货币ID
				$gRate=$cbRow["Rate"]==""?1:$cbRow["Rate"];				//货币汇率
				$Relation=$cbRow["Relation"];		//对应数量
				$UnitName=$cbRow["UnitName"]==""?"&nbsp;":$cbRow["UnitName"];		//单位
				$bpRateName=$cbRow["bpRateName"] ==""?"&nbsp;":$cbRow["bpRateName"] ;
				$OppositeQTY=explode("/",$Relation);
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
                include"../model/subprogram/stuff_Property.php";//配件属性             

        
    
                
                $stateColor = $theDefaultColor;
                $stateImage = "";
				//配件名称
				//从变更列表中找出变更项
				$discribeCharge = "";
				$chargeItemSql = "Select * From $DataIn.pandscharge Where (newStuffId = '$StuffId' or oldStuffId='$StuffId') and ProductId = '$ProductId' and Estate = '1'";
				if($chargeId != "")
				{
					$chargeItemSql = "Select * From $DataIn.pandscharge Where Id = '$chargeId' and Estate = '1'";
				}
				
				$chargeItemResult = mysql_query($chargeItemSql);
				if(mysql_num_rows($chargeItemResult) > 0)
				{
					$chargeItem = mysql_fetch_assoc($chargeItemResult);
					$newStuffId = $chargeItem["newStuffId"];
					$oldStuffId = $chargeItem["oldStuffId"];
					
					if($newStuffId == $oldStuffId && $newStuffId == $StuffId)
					{
						//有变更
						$oldRelation = $chargeItem["relation"];
						if($oldRelation != $Relation)
						{
							$discribeCharge .= "对应数量从$oldRelation 变更为 $Relation;";
						}
						
						
						$oldBpRate = $chargeItem["bpRate"];
						if($oldBpRate != $cbRow["bpRate"])
						{
							$discribeCharge .= "备品率变更;";
						}
						
						$stateColor = "#acd598";
						$stateImage = "<img src='../images/updateStuff.gif' title='更新' width='13' height='13'>";
					}
					else if($newStuffId == $StuffId && $oldStuffId == '0')
					{
						//有添加
						$stateColor = "#fff100";
						$stateImage = "<img src='../images/addStuff.gif' title='添加' width='13' height='13'>";
					}
					else if($oldStuffId == $StuffId && $newStuffId == '0')
					{
						//有删除
						
						$stateColor = "#a0a0a0";
						$stateImage = "<img src='../images/deleteStuff.gif' title='删除' width='13' height='13'>";
					}
					else if($oldStuffId != $newStuffId &&  $oldStuffId!=0 && $newStuffId != 0)
					{
						$stateColor = "#acd598";
						$discribeCharge = "配件 $oldStuffId 变更为 $newStuffId ;";
						$stateImage = "<img src='../images/updateStuff.gif' title='更新' width='13' height='13'>";
					}
				}
				
				if($discribeCharge != "")
						{
							$StuffCname = "$StuffCname($discribeCharge)";
						}
				
				$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
				
				
				//二级BOM表
			    $showSemiStr="&nbsp;";$showSemiTable="";$colspan=13;
			    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.semifinished_bom A  WHERE  A.mStuffId='$StuffId' LIMIT 1",$link_id);
	            if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
	                  $showSemiStr="<img onClick='ShowDropTable(SemiTable$tId,showtable$tId,SemiDiv$tId,\"semifinishedbom_ajax\",\"$StuffId|$k\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏二级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
	                  $showSemiTable="<tr id='SemiTable$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='SemiDiv$tId' width='720'></div></td></tr>"; 
	                  $tId++;
	                }
                
                
               $showProduct =""; $ProductTable="";
               $CheckProductSql=mysql_query("SELECT A.Id FROM $DataIn.pands A  WHERE  A.StuffId='$StuffId' LIMIT 1",$link_id);
               if($CheckProductRow=mysql_fetch_array($CheckProductSql)){
                                    
                  $showProduct="<img onClick='ShowDropTable(ProductTable$tId,showProductTable$tId,ProductDiv$tId,\"Stuffdata_Gfile_ajax\",\"$StuffId\",\"public\");'  src='../images/showtable.gif'  title='显示或隐藏所用到的产品.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showProductTable$tId'>";
                  $ProductTable="<tr id='ProductTable$tId' style='display:none;background:#83b6b9;'><td colspan='10'><div id='ProductDiv$tId' width='720'></div></td></tr>"; 
                  $tId++;        
                              
                }
                
				if($k>1){echo"<tr bgcolor='$theDefaultColor'>";}
				echo"<td class='A0101' width='$Field[$n]' align='center' $MTCOLOR >$showSemiStr $k $showProduct</td>";
				$n=$n+2;	
				echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$stateColor'> $StuffId $stateImage</td>";
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
				$n=$n+2;	$jlCel=$k==1?"<td class='A0101' width='$Field[$n]' rowspan='$numrows' align='right'>&nbsp;</td>":"";
				
				echo"$jlCel</tr>";//结束首行
                echo $showSemiTable ;
                echo $ProductTable;
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

if ($StuffIdList!=""){
        //echo "../public/bomflow/createbomflow.php?StuffIdList=$StuffIdList&UniteIdList=$UniteIdList";
	    echo"<br><table  width='1495' cellspacing='1' border='1' align='center'><tr bgcolor='#FFFFFF'>";
		echo "<td><img  src='../public/bomflow/createbomflow.php?StuffIdList=$StuffIdList&UniteIdList=$UniteIdList&ProductId=$ProductId'/><td>";
		echo"</tr></table>";
}

$myResult = mysql_query($mySql,$link_id);
pBottom(1,1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);


include "../model/subprogram/read_model_menu.php";
?>