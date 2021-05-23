<?php
//步骤1
include "../model/modelhead.php";
include "../model/systemfunction.php";
echo "<link rel='stylesheet' href='../model/style/popupframe.css'>";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=500;

ChangeWtitle("$SubCompany 半成品BOM审核");
$funFrom="semifinishedbom";
$From=$From==""?"m":$From;
$Th_Col="选项|30|序号|30|配件ID|50|半成品配件名称|250|单位|30|图档|30|单价|60|成本价|60|采购期限|60|选项|30|NO.|30|配件ID|50|原材料名称|320|
图档|30|单位|30|对应关系|60|单价|60|采购|50|供应商|90|存放楼层|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,17";
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-可选条件下拉框
if($From!="slist"){
    $SearchRows="  ";
	$result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter 
	         FROM $DataIn.semifinished_bom A
             LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
             LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
             WHERE 1 $SearchRows AND D.bomEstate>0 AND  D.Estate>0 GROUP BY T.TypeId order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	   echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected >$TypeName</option>";
				$SearchRows=" AND D.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'  >$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
		
	}
        
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
echo "<input type='checkbox' onclick='CheckAll()' >全选";
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
$tId=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT A.mStuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,U.Name AS Unit,D.CostPrice,IFNULL(V.ReduceWeeks,0) AS ReduceWeeks 
FROM $DataIn.semifinished_bom A
LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.semifinished_deliverydate V ON V.mStuffId=A.mStuffId 
where 1 $SearchRows AND D.bomEstate>0 AND D.StuffId>0 GROUP BY A.mStuffId order by A.mStuffId DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	$NumberArray=array("零","一","二","三","四","五","六","七","八","九","十");
	do{
		$m=1;
		$mStuffId=$myRow["mStuffId"];                 
		//读取配件数
		$PO_Temp=mysql_query("select count(*) from $DataIn.semifinished_bom where mStuffId=$mStuffId",$link_id);
		$PO_myrow = mysql_fetch_array($PO_Temp);
		$numrows=$PO_myrow[0];
		if($numrows>0){
              // $numrows  = 100 ;
                $numrows=$numrows*2;
                
                $StuffCname=$myRow["StuffCname"];
		        $Price=$myRow["Price"];
		        $gCostPrice=$myRow["CostPrice"];
		        $Unit=$myRow["Unit"]==""?"&nbsp;":$myRow["Unit"];
		        $Picture=$myRow["Picture"];
				$Gfile=$myRow["Gfile"];
				$Gstate=$myRow["Gstate"];
		                
		        include "../model/subprogram/stuffimg_Gfile.php";	//图档显示		
				//检查是否有图片
				include "../model/subprogram/stuffimg_model.php";
				include"../model/subprogram/stuff_Property.php";//配件属性  
		
                $ReduceWeeks=abs($myRow["ReduceWeeks"]);
                $DeliveryDate=$ReduceWeeks==0?'同周':'前' . $NumberArray[$ReduceWeeks] . '周';
               
               
 
				echo"<table width='$tableWidth' id='ListTable$i' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
				<tr bgcolor='$theDefaultColor'
	onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				echo"<td rowspan='$numrows' scope='col' height='21' width='$Field[$m]' class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$mStuffId'>
				</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$j</td>";
		       $m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$mStuffId</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'>$StuffCname</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Unit</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center'>$Gfile</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$Price</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col'  align='center'>$gCostPrice</td>";
				$m=$m+2;
				echo"<td class='A0101' width='$Field[$m]' rowspan='$numrows' scope='col' align='center' >$DeliveryDate</td>";
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
			$k=1;$CostPrice=0;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
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


				    //二级BOM表
				    $showSemiStr="&nbsp;";$showSemiTable="";$colspan=11;
				    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.semifinished_bom A  WHERE  A.mStuffId='$StuffId' LIMIT 1",$link_id);
                    if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
                          $showSemiStr="<img onClick='ShowDropTable(SemiTable$tId,showtable$tId,SemiDiv$tId,\"semifinishedbom_ajax\",\"$StuffId|$k\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏二级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
		                  $showSemiTable="<tr id='SemiTable$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='SemiDiv$tId' width='720'></div></td></tr>"; 
                          $tId++;
                     }
                     /*
                     else{
	                      //加工工序
                        $CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.process_bom A  WHERE A.ProductId='$ProductId' AND A.StuffId='$StuffId' LIMIT 1",$link_id);
                        if($CheckProcessRow=mysql_fetch_array($CheckProcessSql)){
                              $showSemiStr="<img onClick='ShowDropTable(ProcessTable$tId,showtable$tId,ProcessDiv$tId,\"processbom_ajax\",\"$StuffId\",\"admin\");'  src='../images/showtable.gif' 
			title='显示或隐藏加工工序资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
			                   $showSemiTable="<tr id='ProcessTable$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='ProcessDiv$tId' width='720'></div></td></tr>"; 
                              $tId++;
                            }
                     }
                     */

                    if($k>1){echo"<tr>";}
                     echo"<td  class='A0101' align='center' width='$Field[$n]' height='21' bgcolor='$TypeColor'>$showSemiStr</td>";
                     $n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center'  bgcolor='$TypeColor'>$k</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$StuffId</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' bgcolor='$TypeColor'>$StuffCname</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Gfile</td>";
					$n=$n+2;
				    echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Unit</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Relation</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' align='center' bgcolor='$TypeColor'>$Price</td>";
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
$ChooseFun="N";
include "../model/subprogram/read_model_menu.php";
?>
<script>
function CheckAll(){
	var tempcheckid = document.getElementsByName("checkid[]");

	for (var i=0;i<tempcheckid.length;i++){
		if(tempcheckid[i].checked){
            tempcheckid[i].checked = false;   
		}else{
			tempcheckid[i].checked = true;
		}
	}

 
	
}
</script>

