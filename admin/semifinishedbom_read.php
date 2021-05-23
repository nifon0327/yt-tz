<?php
include "../model/modelhead.php";
include "../model/systemfunction.php";
echo "<link rel='stylesheet' href='../model/style/popupframe.css'>";//弹出窗口css
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 半成品BOM");
$funFrom="semifinishedbom";
$From=$From==""?"read":$From;
$Th_Col="选项|30|序号|30|配件ID|50|半成品配件名称|320|单位|30|图档|30|含税价|60|成本价|60|采购期限|60|选项|30|NO.|30|配件ID|50|原材料名称|320|历史<br>订单|40|图档|30|单位|30|对应关系|60|含税价|60|成本价|60|采购|50|供应商|90|存放楼层|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
$ActioToS="1,2,3,4,13";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
    //项目
    $result = mysql_query("SELECT T.TradeId,T.TradeNo,O.Forshort,O.CompanyId
	         FROM $DataIn.trade_info T 
	         INNER JOIN $DataIn.trade_object O ON O.Id = T.TradeId",$link_id);
    if($myrow = mysql_fetch_array($result)){
        echo"<select name='TradeId' id='TradeId' onchange='ResetPage(this.name)'><option value='all' selected>--全部项目--</option>";
        do{
            $theTradeId=$myrow["TradeId"];
            $TradeNo = $myrow["TradeNo"];
            $Forshort = $myrow["Forshort"];
            $CompanyId = $myrow["CompanyId"];
            $TradeId = $TradeId == "" ? $CompanyId : $TradeId;
            if ($TradeId==$CompanyId){
                echo "<option value='$CompanyId' selected >$Forshort</option>";
                $TradeName="B-".$TradeNo;
                $SearchRows .=" AND P.CompanyId = $TradeId ";

            }
            else{
                echo "<option value='$CompanyId'  >$Forshort</option>";
            }
        }while ($myrow = mysql_fetch_array($result));
        echo "</select>&nbsp;";
    }

    //楼栋层
    $BFresult = mysql_query("SELECT T.BuildingNo,T.FloorNo
FROM $DataIn.trade_drawing T
INNER JOIN $DataIn.trade_object P ON P.Id = T.TradeId
where 1 $SearchRows GROUP BY T.BuildingNo,T.FloorNo order by T.BuildingNo,T.FloorNo+0",$link_id);
    if($BFrow = mysql_fetch_array($BFresult)){
        echo"<select name='buildFloor' id='buildFloor' onchange='ResetPage(this.name)'><option value='all' selected>--全部栋层--</option>";
        do{
            $thebuild = $BFrow["BuildingNo"];
            $theFloor = $BFrow["FloorNo"];
            $thebuildFloor = $thebuild.'-'.$theFloor;
            $buildFloor = $buildFloor == "" ? $thebuildFloor : $buildFloor;
            if ($buildFloor==$thebuildFloor){
                echo "<option value='$thebuildFloor' selected >$thebuild 栋 $theFloor 层</option>";
                $TradeName .= '-'.$buildFloor.'-';
            }
            else{
                echo "<option value='$thebuildFloor'  >$thebuild 栋 $theFloor 层</option>";
            }
        }while ($BFrow = mysql_fetch_array($BFresult));
        echo "</select>&nbsp;";
    }

 //类型
    $typeResult = mysql_query("SELECT T.TypeId,T.TypeName
	FROM $DataIn.producttype T 
	LEFT JOIN $DataIn.productdata P ON P.TypeId=T.TypeId 
	LEFT JOIN $DataIn.pands S ON S.ProductId=P.ProductId 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	WHERE 1  $SearchRows GROUP BY T.TypeId",$link_id);

    if($typeRow = mysql_fetch_array($typeResult)){
        echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'><option value='all' selected>--全部类型--</option>";
        do{
            $theTypeId = $typeRow["TypeId"];
            $theTypeName = $typeRow["TypeName"];
            $TypeId = $TypeId == "" ? $theTypeId : $TypeId;
            if ($TypeId==$theTypeId){
                echo "<option value='$theTypeId' selected >$theTypeName</option>";
                $SearchRows .=" AND P.TypeId='$TypeId' ";
            }
            else{
                echo "<option value='$theTypeId'  >$theTypeName</option>";
            }
        }while ($typeRow = mysql_fetch_array($typeResult));
        echo "</select>&nbsp;";
    }



	$result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter 
	         FROM $DataIn.semifinished_bom A
             LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
             LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
             WHERE D.Estate>0 GROUP BY T.TypeId order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	  echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected >$TypeName</option>";
				$SearchRows .=" AND D.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'  >$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
		
	}
  echo "<input type='text' name='component' id='component' value='$component'><span class='ButtonH_25' onclick='var component = document.getElementById(\"component\").value; document.form1.submit();'>查询</span>";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
echo"<div id='PopupDiv' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$tId=1;$upCount=0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
if ($component != '') {
  $sqlAdd = " AND D.StuffCname LIKE '%$component%' ";
}

$SearchRows .=" AND D.StuffCname LIKE '%$TradeName%' ";

$mySql= "SELECT A.mStuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,U.Name AS Unit,D.CostPrice,
C.Price AS NewPrice,C.CostPrice AS NewCostPrice,IFNULL(V.ReduceWeeks,0) AS ReduceWeeks 
FROM $DataIn.semifinished_bom A
LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
LEFT JOIN $DataIn.stuffcostprice C ON C.StuffId=A.mStuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.semifinished_deliverydate V ON V.mStuffId=A.mStuffId 
LEFT JOIN $DataIn.pands S ON S.StuffId = D.StuffId
LEFT JOIN $DataIn.productdata P ON S.ProductId = P.ProductId 
where 1 $SearchRows AND D.StuffId>0 $sqlAdd GROUP BY A.mStuffId order by A.mStuffId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	$NumberArray=array("零","一","二","三","四","五","六","七","八","九","十");
	do{
		$m=1;
		$StuffId=$myRow["mStuffId"];                 
		//读取配件数
		$PO_Temp=mysql_query("select count(*) from $DataIn.semifinished_bom where mStuffId=$StuffId",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0];
		if($numrows>0){
              // $numrows  = 100 ;
                $numrows=$numrows*2;
                
                $StuffCname=$myRow["StuffCname"];
		        $mPrice=$myRow["Price"];
		        $mCostPrice=$myRow["CostPrice"];
		        $NewPrice =$myRow["NewPrice"];
		        $NewCostPrice = $myRow["NewCostPrice"];
		        
		        $priceClass = $mPrice!=$NewPrice?'redB':'greenB';
		        $costpriceClass = $mCostPrice!=$NewCostPrice?'redB':'greenB';
		        $Unit=$myRow["Unit"]==""?"&nbsp;":$myRow["Unit"];
		        $Picture=$myRow["Picture"];
				$Gfile=$myRow["Gfile"];
				$Gstate=$myRow["Gstate"];
		                
		        include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
				//检查是否有图片
				include "../model/subprogram/stuffimg_model.php";
				include"../model/subprogram/stuff_Property.php";//配件属性 
				$mStuffCname = $StuffCname; 
				$mStuffId = $StuffId;
		
                $ReduceWeeks=abs($myRow["ReduceWeeks"]);
                $DeliveryDate=$ReduceWeeks==0?'同周':'前' . $NumberArray[$ReduceWeeks] . '周';
                
				echo"<table class='ListTableUd' width='$tableWidth' id='ListTable$i' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$mStuffId'>
				</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		       $m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mStuffId</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$mStuffCname</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Unit</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Gfile</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$mPrice<br><span class='$priceClass'>$NewPrice </span></td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$mCostPrice<br><span class='$costpriceClass'> $NewCostPrice</span></td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'  onclick='ShowPopupDiv($i,8,$mStuffId,1)' onmousedown='window.event.cancelBubble=true;' style='CURSOR: pointer'>$DeliveryDate</td>";
				$m=$m+2;
			//从配件表和配件关系表中提取配件数据	

			$StuffResult = mysql_query("SELECT A.Id,A.Relation,A.StuffId,D.StuffCname,D.Price,D.CostPrice,D.Picture,D.Gfile,D.Gstate,
			    D.Gremark,D.TypeId,D.SendFloor,U.Name AS Unit,C.Rate,P.Forshort,ST.mainType,MT.TypeColor    
				FROM  $DataIn.semifinished_bom A  
                LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId 
                LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
		        LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		        LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType 
		        LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
		        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
				LEFT JOIN $DataIn.currencydata C ON C.Id=P.Currency 
                WHERE A.mStuffId='$mStuffId' ORDER BY MT.SortId,A.Id",$link_id);// MT.SortId,
			$k=1;$CostPrice=0; $taxPrice=0;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$TypeId=$StuffMyrow["TypeId"];
					$mainType=$StuffMyrow["mainType"];
					$TypeColor=$StuffMyrow["TypeColor"];
					
					
					if($mainType ==2 && $TypeId ==9175){ //半成品的制造费用按照加工类配件的价格按比率来计算 
						
						$checkStuffRow = mysql_fetch_array(mysql_query("SELECT D.Price,D.CostPrice,D.TypeId 
						FROM $DataIn.semifinished_bom A 
						LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId  
						LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
						WHERE A.mStuffId = '$mStuffId' AND T.mainType = 3 ",$link_id));
						$scTypeId = $checkStuffRow["TypeId"];
						if($scTypeId==9141){ //开料
							$SystemValue = getSystemParam("703",$DataIn,$link_id);
						}else{
							$SystemValue = getSystemParam("702",$DataIn,$link_id);
						}
						
						$scPrice = $checkStuffRow["Price"];
						$scCostPrice = $checkStuffRow["CostPrice"];
						$Price = sprintf("%.4f", $scPrice*$SystemValue);
						$thisCostPrice = sprintf("%.4f", $scCostPrice*$SystemValue);
						
					}else{
						$Price=$StuffMyrow["Price"];
					    $thisCostPrice=$StuffMyrow["CostPrice"];
					}
					
                    $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];

					$Relation=$StuffMyrow["Relation"];
					$RelArray=explode("/", $Relation);
					$mRelation=count($RelArray)==2?$RelArray[0]/$RelArray[1]:$RelArray[0];
					$Rate=$StuffMyrow["Rate"]==""?1:$StuffMyrow["Rate"];
					
					$taxPrice+=round($Price*$mRelation*$Rate,4);
					
					$CostPrice+=round($thisCostPrice*$mRelation*$Rate,4);
					
					
					$bps = mysql_query("SELECT M.Name,P.Forshort 
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.providerdata P ON P.CompanyId=B.CompanyId
					WHERE B.StuffId='$StuffId'",$link_id);
					if($bpsMyrow=mysql_fetch_array($bps)){
						$Name=$bpsMyrow["Name"];
						$Forshort=$bpsMyrow["Forshort"];
						}
                   $Name=$Name==""?"&nbsp;":$Name;
                   $Forshort=$StuffMyrow["Forshort"]==""?"&nbsp;":$StuffMyrow["Forshort"];


					$Picture=$StuffMyrow["Picture"];
					$Gfile=$StuffMyrow["Gfile"];
					$Gstate=$StuffMyrow["Gstate"]; 
					$Gremark=$StuffMyrow["Gremark"];

					include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
					//检查是否有图片
					include "../model/subprogram/stuffimg_model.php";
					
					$SendFloor=$StuffMyrow["SendFloor"];
					include "../model/subprogram/stuff_GetFloor.php";
					$SendFloor=$SendFloor=="" || $SendFloor==0?"&nbsp;":$SendFloor;
					
					include"../model/subprogram/stuff_Property.php";//配件属性

                    $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
				    //二级BOM表
				    $showSemiStr="&nbsp;";$showSemiTable="";$colspan=13;
				    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.semifinished_bom A  WHERE  A.mStuffId='$StuffId' LIMIT 1",$link_id);
                    if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
                          $showSemiStr="<img onClick='ShowDropTable(SemiTable$tId,showtable$tId,SemiDiv$tId,\"semifinishedbom_ajax\",\"$StuffId|$k\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏二级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
		                  $showSemiTable="<tr id='SemiTable$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='SemiDiv$tId' width='720'></div></td></tr>"; 
                          $tId++;
                     }
                     else{
	                      //加工工序
                        $CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.process_bom A  WHERE  A.StuffId='$StuffId' LIMIT 1",$link_id);
                        if($CheckProcessRow=mysql_fetch_array($CheckProcessSql)){
                              $showSemiStr="<img onClick='ShowDropTable(ProcessTable$tId,showtable$tId,ProcessDiv$tId,\"processbom_ajax\",\"$StuffId\",\"admin\");'  src='../images/showtable.gif' 
			title='显示或隐藏加工工序资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
			                   $showSemiTable="<tr id='ProcessTable$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='ProcessDiv$tId' width='720'></div></td></tr>"; 
                              $tId++;
                            }
                     }

                    if($k>1){echo"<tr>";}
                     echo"<td  class='A0101' align='center' width='$Field[$n]' height='21' bgcolor='$TypeColor'>$showSemiStr</td>";
                     $n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'  bgcolor='$TypeColor'>$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$StuffId</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' bgcolor='$TypeColor'>$StuffCname</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$OrderQtyInfo</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Gfile</td>";
					$n=$n+2;
				    echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Unit</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Relation</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Price</td>";
					$n=$n+2;
	                echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$thisCostPrice</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Name</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Forshort</td>";
					$n=$n+2;
					echo"<td class='A0101' width='' align='center' bgcolor='$TypeColor'>$SendFloor</td>";
					echo"</tr>";
					echo $showSemiTable;
					$k++;$i++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
				}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
				echo "</table>";
				
				//添加配件成本计算
				if (round($NewCostPrice,4)!=round($CostPrice,4) || round($NewPrice,4)!=round($mPrice,4)){
				      $upSql = "UPDATE  $DataIn.stuffdata  D 
				                     LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
					                 SET D.Price = '$NewPrice',D.CostPrice='$NewCostPrice' 
				  	                 WHERE D.StuffId='$mStuffId' AND T.mainType = '".$APP_CONFIG['SEMI_MAINTYPE']."' ";
			  	            // echo $upSql;
			                 $upResult = mysql_query($upSql);
				}
				/*
				$mCostPrice=round($mCostPrice,4);
				$CostPrice=round($CostPrice,4);
				$mPrice=round($mPrice,4);
				$taxPrice=round($taxPrice,4);
			 if ($mCostPrice!=$CostPrice ||  $mPrice !=$taxPrice ){
				$upSql = "UPDATE  $DataIn.stuffdata  D 
			    LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
				SET D.Price = '$taxPrice',D.CostPrice='$CostPrice' 
				WHERE D.StuffId='$mStuffId' AND T.mainType = '".$APP_CONFIG['SEMI_MAINTYPE']."' ";
				//echo $upSql;
			     $upResult = mysql_query($upSql);
			     if($upResult && mysql_affected_rows()>0){
			         $upCount++;
				     echo "配件 ($mStuffId) 的含税价格($mPrice) 已更新为:$taxPrice,成本价格($mCostPrice)已更新为:$CostPrice;";
			     }
			  }
			  */
			  
			}//结束存在配件表
		$j++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$j-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
// $ChooseFun="N";
$ColsNumber = 9;//
include "../model/subprogram/read_model_menu.php";
?>

<script  src='../model/IE_FOX_MASK.js' type='text/javascript'></script>
<script language='JavaScript' type='text/JavaScript'>

//增加td点击选择table的功能 add by ckt 2018-01-04
jQuery('.ListTableUd').each(function(){
	var theMerge = <?php echo $ColsNumber?>;
	var that = this;
	jQuery(this).find('td:lt('+theMerge+')').click(function(){
		var host = jQuery(this).parent().find('td:first input')[0];
		var tds = jQuery(that).find('td:lt('+theMerge+')');
		if(host.checked){
			host.checked = false;
			tds.removeAttr('bgcolor');
		}else{
			host.checked = true;
			tds.attr('bgcolor', '#FFCC99');
		}	
	})		
})


//var upCount=<?php //echo $upCount;?>//;
//if (upCount>0){
//	alert('已更新半成品价格数量:'+upCount);
//}

function ShowPopupDiv(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("PopupDiv");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	
	if(theDiv.style.visibility=="hidden"){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//采购交货期
				InfoSTR="半成品配件ID:<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='8' class='TM0000' readonly/>采购交货期限<select id='ReduceWeeks' name='ReduceWeeks' style='width:150px;'><option value='' 'selected'>请选择</option>";
				<?PHP 
				    $echoInfo="<option value='0'>同周</option>";
				    $NumberArray=array("零","一","二","三","四","五","六","七","八","九","十");
				    for($m=1;$m<$APP_CONFIG['REDUCE_WEEKS'];$m++){ 
					   $echoInfo.="<option value='-$m'>前".$NumberArray[$m]."周</option>"; 
				    }
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				break;
		}
		//if(InfoSTR.length>0){
			buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		//}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
	}
	
}        
function CloseDiv(){
	var theDiv=document.getElementById("PopupDiv");	
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value*1;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	//alert(ObjId);
	switch(ObjId){
		case 1:		//更新交货期:
		    var obj=document.form1.ReduceWeeks;
			var ReduceWeeks=obj.value;
			myurl="semifinishedbom_updated.php?mStuffId="+temprunningNum+"&ReduceWeeks="+ReduceWeeks+"&ActionId=DeliveryDate";
			var ajax=InitAjax(); 
			ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
			   if(ajax.readyState==4){// && ajax.status ==200
					  eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+obj.options[obj.selectedIndex].text+"</NOBR></DIV>";
					  CloseDiv();
				}							
		    }
		    ajax.send(null);
		break;
	}
}

function findComponent(){
    console.log(111);

}
</script>
