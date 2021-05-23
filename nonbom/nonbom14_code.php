<?php 
include "../model/modelhead.php";
$tableMenuS=500;
ChangeWtitle("$SubCompany 非bom配件(固)入库条码设定");
$funFrom="nonbom14";
$From=$From==""?"code":$From;
$sumCols="6,7";			//求和列,需处理
$MergeRows=5;
$Th_Col="序号|30|配件编号|50|非bom配件名称|350|采购总数|50|入库单号|100|采购|50|供应商|120|入库数量|50|设置|40|序号|30|条码|100|资产编号|150|图片|40|入库地点|60|状态|30|领用人|60";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
$ActioToS="1";
//步骤3：
$nowWebPage=$funFrom."_code";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql= "SELECT   	A.GoodsId,	C.GoodsName,C.BarCode,C.Attached,C.Unit
FROM $DataIn.nonbom7_insheet A
LEFT JOIN $DataPublic.nonbom4_goodsdata C ON C.GoodsId=A.GoodsId 
WHERE  1 $SearchRows AND A.GoodsId IN (SELECT GoodsId FROM $DataPublic.nonbom4_goodsproperty WHERE Property=7) GROUP BY  A.GoodsId";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	$DirRK=anmaIn("download/nonbom_rk/",$SinkOrder,$motherSTR);
	do{
           $m=1;
	     	$GoodsId=$myRow["GoodsId"];		
			$GoodsName=$myRow["GoodsName"];
			$BarCode=$myRow["BarCode"]==""?"&nbsp;":$mainRows["BarCode"];
			$Attached=$myRow["Attached"];
			 if($Attached==1){
				$Attached=$GoodsId.".jpg";
				$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
				$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
				}
			$Unit=$myRow["Unit"];
           include"../model/subprogram/good_Property.php";//非BOM配件属性
		$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
		//配件分析
		$GoodsIdStr="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
		  $PO_Temp=mysql_fetch_array(mysql_query("select SUM(Qty) AS rkQty from $DataIn.nonbom7_insheet where GoodsId=$GoodsId ",$link_id));
		   $rkQty = $PO_Temp["rkQty"];
         $cgResult=mysql_fetch_array(mysql_query("select SUM(Qty) AS cgQty from $DataIn.nonbom6_cgsheet where GoodsId=$GoodsId ",$link_id));
        $cgQty=$cgResult["cgQty"];
	    $tableStr="<table  id='ListTable$j' width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		$tableStr.="<td rowspan='$rkQty' scope='col' height='$rowHeight' width='$Field[$m]' class='A0111' align='center'>$j</td>";
		$m=$m+2;
		$tableStr.="<td class='A0101' width='$Field[$m]' rowspan='$rkQty' scope='col' align='center'>$GoodsIdStr</td>";
		$m=$m+2;
		$tableStr.="<td class='A0101' width='$Field[$m]' rowspan='$rkQty' scope='col' >$GoodsName</td>";
		$m=$m+2;
		$tableStr.="<td class='A0101' width='$Field[$m]' rowspan='$rkQty' scope='col' align='center'>$cgQty</td>";
		$m=$m+2;
		if($rkQty>0){
			$OrderResult = mysql_query("SELECT A.Id,A.Mid,A.GoodsId,A.Qty,A.cgId,A.Locks,B.Bill,B.BillNumber,B.CompanyId,B.BuyerId,B.Remark,B.Date AS rkDate,B.Locks AS mainLocks,B.Operator,E.Forshort,F.Name AS Buyer
                        FROM $DataIn.nonbom7_insheet A
                         LEFT JOIN $DataIn.nonbom7_inmain B ON B.Id=A.Mid 
                         LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=B.CompanyId 
                         LEFT JOIN $DataPublic.staffmain F ON F.Number=B.BuyerId 
						 WHERE  A.GoodsId='$GoodsId' ORDER BY B.Date",$link_id);
			$k=1;
			if($OrderRow=mysql_fetch_array($OrderResult)) {//如果设定了产品配件关系
				do{	
					$n=$m;
	           		$rkId=$OrderRow["Id"];
	           		$Qty=del0($OrderRow["Qty"]);
	           		$Bill=$OrderRow["Bill"];
	           		$Mid=$OrderRow["Mid"];
	           		$BillNumber=$OrderRow["BillNumber"];
		           			if($Bill==1){
			           			$Bill=$Mid.".jpg";
				           		$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			           			$BillNumber="<span onClick='OpenOrLoad(\"$DirRK\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$BillNumber</span>";
			           			}
	           				else{
			           			$BillNumber=$BillNumber;
			           			}
				$BuyerId=$OrderRow["BuyerId"];
				$Buyer=$OrderRow["Buyer"];
				$Forshort=$OrderRow["Forshort"];
				$Remark=$OrderRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$OrderRow[Remark]' width='18' height='18'>";
					$CompanyId=$OrderRow["CompanyId"];
					//加密
					$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
					$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
	                $Stocknumrows=$Qty;
                    if($rkQty==$cgQty)$Qty="<span class='greenB'>$Qty</span>";
                    else  $Qty="<span class='redB'>$Qty</span>";
                     $passId=$rkId."|".$GoodsId;
		 $OnclickStr="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom14_upmain\",\"$passId\")' src='../images/edit.gif' title='更新配件条码' width='13' height='13'>";
					//配件名称
					if ($k==1) echo $tableStr;
					echo"<td class='A0101' width='$Field[$n]' rowspan='$Stocknumrows' height='$rowHeight' align='center'>$BillNumber</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' rowspan='$Stocknumrows' align='center'>$Buyer</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' rowspan='$Stocknumrows' align='left'>$Forshort</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' rowspan='$Stocknumrows' align='center' >$Qty</td>";
					$n=$n+2;
					echo"<td class='A0101' width='$Field[$n]' rowspan='$Stocknumrows' align='center' >$OnclickStr</td>";
					$n=$n+2;
                    $CheckResult=mysql_query("SELECT  C.BarCode,C.GoodsNum,C.Picture,C.Estate  ,K.Name AS rkName
                     FROM  $DataIn.nonbom7_code  C   
                     LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
                    WHERE  C.rkId=$rkId AND C.GoodsId=$GoodsId",$link_id);
                      $BarCodeArray=array();$GoodsNumArray=array();$PictureArray=array();$EstateArray=array();
                      $Code=0;
                     while($CheckRow=mysql_fetch_array($CheckResult)){
                           $BarCodeArray[$Code]=$CheckRow["BarCode"];
                           $GoodsNumArray[$Code]=$CheckRow["GoodsNum"];
                           $PictureArray[$Code]=$CheckRow["Picture"];
                           $EstateArray[$Code]=$CheckRow["Estate"];
                           $rkNameArray[$Code]=$CheckRow["rkName"];
                          $Code++;
                          }           
					  $r=1;
					 for($tempk=0;$tempk<$Stocknumrows;$tempk++){ 
						    $h=$n;
                            if($Code>0){
		                         $Dir=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
                                      $BarCode=$BarCodeArray[$tempk];
                                      $GoodsNum=$GoodsNumArray[$tempk];
                                      $Picture=$PictureArray[$tempk];
                                      $Estate=$EstateArray[$tempk];
                                      $rkName=$rkNameArray[$tempk];
                                      $PictureStr="&nbsp;";
                                      if($Picture!="") {
                                         $Picture=anmaIn($Picture,$SinkOrder,$motherSTR);
                                          $PictureStr="<span onClick='OpenOrLoad(\"$Dir\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
                                          }
                                      $Estate=$Estate==1?"<span class='greenB'>√</span>":"<span class='redB'>X</span>";
                                    }
                            else{
                                    $BarCode="&nbsp;";$GoodsNum="&nbsp;";$Picture="&nbsp;";$Estate="&nbsp;";$PictureStr="&nbsp;";$rkName="&nbsp;";
                                     }
						    if($r>1){echo"<tr>";}
					        echo"<td class='A0101' width='$Field[$h]' align='center' height='25px'>$r</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' align='center'>$BarCode</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' align='center' >$GoodsNum</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' align='center' >$PictureStr</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]' align='center' >$rkName</td>";
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]'  align='center' >$Estate</td>";                     
					        $h=$h+2;
					        echo"<td class='A0101' width='$Field[$h]'  align='center' >$LyMan</td>";                     
					        $h=$h+2;
					    $r++;$t++;$s++;
						echo"</tr>";
					  }
				   $k++;$i++;
				} while ($OrderRow = mysql_fetch_array($OrderResult));
			   if ($k>1) {echo "</table>";	echo $HideTableHTML;$j++;}
			}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
		}
         }while ($myRow = mysql_fetch_array($myResult));
	}
else{
	    noRowInfo($tableWidth);
	}
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
