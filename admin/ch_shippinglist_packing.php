<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
$funFrom="ch_shippinglist";
$fromWebPage=$fromWebPage==""?$funFrom."_read":$fromWebPage;
$nowWebPage =$funFrom."_packing";	
$toWebPage  =$funFrom."_updated";	
ChangeWtitle("$SubCompany 装箱设置");
$_SESSION["nowWebPage"]=$nowWebPage; 
$tableMenuS=500;
$tableWidth=850;
?>
<form name="form1" enctype="multipart/form-data" action="" method="post" >
<input name="Id" type="hidden" id="Id" value="<?php    echo $Id?>">
<input name="fromWebPage" type="hidden" id="fromWebPage" value="<?php    echo $fromWebPage?>">
<?php    
//注意：前面只可设置两个表单元素，因为表格中的元素从3开始计算
$ClientResult = mysql_query("SELECT CompanyId,Sign FROM $DataIn.ch1_shipmain WHERE Id=$Id LIMIT 1",$link_id);
if($ClientRows = mysql_fetch_array($ClientResult)) {
	$CompanyId=$ClientRows["CompanyId"];
	$ShipSign=$ClientRows["Sign"];
	}
if($ShipSign==-1){
	$SaveSTR="NO";
	include "../model/subprogram/add_model_t.php";
	echo"<table border='0' width='$tableWidth' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'><tr><td class='A0011'>
			<table width='800' border='0' align='center' cellspacing='0'>
				<tr>
					<td height='30' align='center' class='A1111'><div class='redB'>此出货单为扣款资料,无需装箱设置</div></td>
				</tr>
			</table>
		</td></tr></table>";
	include "../model/subprogram/add_model_b.php";
	}
else{
$StuffTypeSTR="and T.TypeId='9040'";
?>
<input name='PackingList' type='hidden' id='PackingList'>
<table border="0" width="1090" cellpadding="0" cellspacing="0" height="100%">
	<tr><td valign="top">
		<table width='1110' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr>
              <td  valign="top" bgcolor="#FFFFFF" class="A1010" align="left">
              <div style="width:1010;overflow-x:hidden;overflow-y:no"> 
				<table width='1009' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
                 <tr>       
				<td width='40' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101" height="20">选项</td>
				<td width='40' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">序号</td>
				<td width='80' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">PO</td>
				<td width="210"  bgcolor='<?php  echo $Title_bgcolor?>' align='center' class="A0101">产品名称</td>
                <td width="200"  bgcolor='<?php  echo $Title_bgcolor?>' align='center' class="A0101">备注</td>
				<td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">出货数量</td>
				<td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">未装箱数</td>
				<td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">数量/箱</td>
				<td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">毛重</td>				
				<td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">箱数</td>
				<td width='130' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0100">外箱尺寸</td>
				<td width="">&nbsp;</td>
                </tr>
                </table>
               </div></td>
                <td width="100" class="A0010">&nbsp;

                </td>               
			</tr>
			<tr>
				<!--<td colspan='10' valign="top" bgcolor="#FFFFFF" class="A0111" align="left">  -->
                <td  valign="top" bgcolor="#FFFFFF" class="A0110" align="left"> 
					<div style="width:1010;height:300;overflow-x:hidden;overflow-y:scroll"> 
						<table width='1010' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
						<?php   	//记录输出
						$oIdArray=array();
						$rowArray=array();
						$POrderResult = mysql_query("
						SELECT C.SplitId,C.POrderId,C.Type,C.ProductId,S.OrderPO,C.Qty,S.dcRemark,P.cName,P.eCode,P.Weight  
						FROM $DataIn.ch1_shipsheet C 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId 
						LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
						WHERE C.Mid='$Id' and C.Type='1' 
						UNION ALL 
						SELECT C.SplitId,C.POrderId,C.Type,C.ProductId,'' AS OrderPO,S.Qty,'' AS dcRemark,
						S.SampName AS cName,S.Description AS eCode,0 AS Weight  
						FROM $DataIn.ch1_shipsheet C 
						LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId WHERE C.Mid='$Id' AND C.Type='2' AND S.Type='1'",$link_id);
						$i=1;
						if ($POrderRows = mysql_fetch_array($POrderResult)) {
						do{
								$ProductId=$POrderRows["ProductId"];
								$SplitId=$POrderRows["SplitId"];
								$OrderPO=$POrderRows["OrderPO"]==""?"&nbsp;":$POrderRows["OrderPO"];
								$POrderId=$POrderRows["POrderId"];
								$rowArray[$i]=$POrderId;
								$cName=$POrderRows["cName"];
								$dcRemark=$POrderRows["dcRemark"]==""?"&nbsp;":$POrderRows["dcRemark"];
								$Qty=$POrderRows["Qty"];
								$eCode=$POrderRows["eCode"];
								$Weight=$POrderRows["Weight"];
								$CompanyId=$POrderRows["CompanyId"];
								$Type=$POrderRows["Type"];							
								$Spec="";//外箱规格
								$Relation="";
								$BoxResult = mysql_query("SELECT D.Spec,D.Weight,P.Relation FROM $DataIn.pands P 
								LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
								LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
								WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 $StuffTypeSTR",$link_id);
								if($BoxRows = mysql_fetch_array($BoxResult)){
									$SpecArray=explode("CM",strtoupper($BoxRows["Spec"]));//以CM为界拆分
									$Spec=$SpecArray[0]."CM";
									$Relation=$BoxRows["Relation"];
									$BoxWeight=$BoxRows["Weight"];
									
									}
								if($Relation ==""){
									$BoxRows1 = mysql_fetch_array(mysql_query("SELECT D.BoxPcs FROM $DataIn.pands P 
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
												WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 ",$link_id));
								    $Relation=$BoxRows1["BoxPcs"]==0?"":$BoxRows1["BoxPcs"];
								}
								//需检查是否已经装箱，计算未装箱的数量
								$Readonly="";$Disabled=""; $SplitStr="";
								if($SplitId>0){
									$SplitStr = " AND SplitId ='$SplitId'";
								}
								$checkPacking=mysql_query("SELECT SUM(BoxQty*BoxPcs) AS inboxQty FROM $DataIn.ch2_packinglist 
								WHERE Mid='$Id' AND  POrderId='$POrderId' $SplitStr",$link_id);
								if($PackingRow=mysql_fetch_array($checkPacking)){
									$inboxQty=$PackingRow["inboxQty"];
									if($inboxQty==$Qty){
										$Disabled="disabled";
										}
									$unBoxQty=$Qty-$inboxQty;
									
								//计算填入初始化装箱数据
								$tmpShow="";
								$rSql = mysql_query("SELECT Relation FROM  $DataIn.sc1_newrelation WHERE POrderId='$POrderId' order by ID DESC Limit 1 ",$link_id);
								if ($rRows = mysql_fetch_array($rSql)){
									$Relation="1/".$rRows["Relation"];
									$tmpShow="(**生产指定**)";
									}	
							   if ($Relation!=""){
							       $RelationArray=explode("/",$Relation);
							       $Relation=$RelationArray[1]==""?$RelationArray[0]:$RelationArray[1];
								   $BoxPcs=intval($unBoxQty/$Relation);
								   if ($BoxPcs>0){
									    //计算毛重(KG)=产品重量(g)+箱重(g)
 									   if ($Weight>0 && $BoxWeight>0){
 									  
									     $productId=$ProductId;
									     include "../model/subprogram/weightCalculate.php";
										 $realWeight=number_format(($Relation*$Weight+$extraWeight)/1000,2);
									   }
									   else{
										 $realWeight=0;
									   }
								    }
									else{
									    $realWeight=0;
									  }//if ($BoxPcs>0)
								   }//if ($Relation!="")
								   $BoxPcs=$BoxPcs==0?"":$BoxPcs;
								   $realWeight=$realWeight==0?"":$realWeight;
							}  
                                $cName=str_replace('\'','',$cName);
								$eCode=str_replace('\'','',$eCode);
								echo"<tr>";
								echo"<td width='40' class='A0101' align='center'><input name='BillId$i' type='checkbox' value='$OrderPO|$POrderId|$cName|$eCode|$SplitId' $Disabled></td>";
								echo"<td width='40' class='A0101' align='center'>$i</td>";
								echo"<td width='80' class='A0101'>$OrderPO</td>";
								echo"<td width='210' class='A0101'>$cName</td>";
								echo"<td width='200' class='A0101'><span class='redB'>$dcRemark</span></td>";
								echo"<td width='60' class='A0101' align='center'>$Qty</td>";
								echo"<td width='60' class='A0101' align='center'><input type='text' name='Residual$i' size='6' value='$unBoxQty' class='I0000C' readonly $Disabled></td>";
								echo"<td width='60' class='A0101' align='center'><input type='text' name='PCS$i' size='6' class='I0000C' value='$Relation' onChange='getNewBoxNum($i*6-2);' $Disabled></td>";
								echo"<td width='60' class='A0101' align='center'><input type='text' name='WG$i' size='6' class='I0000C' value='$realWeight' $Disabled></td>";
								echo"<td width='60' class='A0101' align='center'><input type='text' name='QTY$i' size='6' class='I0000C' value='$BoxPcs' $Disabled></td>";
								echo"<td width='130' class='A0100' align='Center'><input type='text' name='BoxSpec$i' size='15' class='I0000L'  value='$Spec' $Disabled> $tmpShow</td>";
								echo"<td width=''>&nbsp;</td>";
								echo"</tr>";
								$i++;
								}while ($POrderRows = mysql_fetch_array($POrderResult));
							}
						?>
						</table>
					</div>
				</td>
				<td width="100" class="A0010">&nbsp;
				<input type="button" name="Submit" value="单箱" onclick="Addbox()"><p><<--出货单列表</p><p>&nbsp;
				<input type="button" name="Submit" value="并箱" onclick="uniteBOX()">
				</td>
			</tr>
		</table>
	</td></tr>
	<tr height="80%"><td valign="top">
	<br>
		<table width='1110' height="90%" border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr>
            
            <td  valign="top" bgcolor="#FFFFFF" class="A1010" align="left">
                <div style="width:1010;overflow-x:hidden;overflow-y:no">					
                <table width='1009' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                   <tr>
                    <td width='80' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101" height="20">操作</td>
                    <td width='40' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">箱数</td>
                    <td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">箱号</td>
                    <td width='80' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">PO</td>
                    <td width="220"  bgcolor='<?php  echo $Title_bgcolor?>' align='center' class="A0101">产品名称</td>
                    <td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">数量/箱</td>
                    <td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">总数量</td>				
                    <td width='60' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0101">毛重</td>				
                    <td width='' bgcolor='<?php    echo $Title_bgcolor?>' align='center' class="A0100">外箱尺寸</td>
                    
                   </tr>
                </table>
               </div>
              </td>
                <td width="100" class="A0010">&nbsp;

                </td>                
			</tr>
			<tr>
                <!--<td colspan='9' height="100%" valign="top" bgcolor="#FFFFFF" class="A0111" align="left">    -->   
				<td  height="100%" valign="top" bgcolor="#FFFFFF" class="A0110" align="left">
					<div style="width:1010;height:100%;overflow-x:hidden;overflow-y:scroll">					
					<table width='1010' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='boxTB'>
					<?php   
					$OrderNum=count($rowArray);
					//装箱内容
					$plResult = mysql_query("SELECT L.SplitId,L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec FROM $DataIn.ch2_packinglist L WHERE L.Mid='$Id' ORDER BY L.Id ",$link_id);
					if ($plRows = mysql_fetch_array($plResult)){
						$j=1;
						do{
							$BoxRow=$plRows["BoxRow"];
							$BoxPcs=$plRows["BoxPcs"];
							$BoxQty=$plRows["BoxQty"];
							$POrderId=$plRows["POrderId"];
							$BoxSpec=$plRows["BoxSpec"];
							$FullQty=$plRows["FullQty"];
							$WG=$plRows["WG"];
							$SplitId=$plRows["SplitId"];
			
							$checkType=mysql_fetch_array(mysql_query("SELECT Type FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId' LIMIT 1",$link_id));
							$Type=$checkType["Type"];
							switch($Type){
								case 1:	//产品
									$pSql = mysql_query("SELECT 
									S.OrderPO,P.cName,P.eCode,P.Description 
									FROM $DataIn.yw1_ordersheet S 
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
									WHERE S.POrderId='$POrderId' LIMIT 1",$link_id);
									if ($pRows = mysql_fetch_array($pSql)){
										$OrderPO=$pRows["OrderPO"];
										$cName=$pRows["cName"];
										$eCode=$pRows["eCode"];
										$Description=$pRows["Description"];										
										}
									break;
								case 2:	//样品
									$sSql = mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId='$POrderId'",$link_id);
									if ($sRows = mysql_fetch_array($sSql)){
										$OrderPO="&nbsp;";
										$cName=$sRows["SampName"];
										$eCode="";
										$Description=$sRows["Description"];
										}		
									break;
								}
                                                       
							$BoxRowSTR=$BoxRow>1?"rowspan=$BoxRow":"";//检查是否合并行
							if($BoxRow==0){//并箱非首行
							     
								echo"<tr>
									<td class='A0101' align='center' width='80'  height='20'>$j $OrderPO</td>			
									<td class='A0101' width='220' >$cName</td>
									<td class='A0101' align='center' width=''>$BoxPcs</td>
									</tr>";
								
								//取相应的行号
								for($n=1;$n<=$OrderNum;$n++){
									if($rowArray[$n]==$POrderId){
										$theOrderNumRow=$n*6-3;
										}
									}
								//重新写入行集
								$k=$j-1;
								echo"<script>
								boxTB.rows[$k].cells[0].data=$POrderId;
								boxTB.rows[$mainOrderNumRow].cells[0].data=boxTB.rows[$mainOrderNumRow].cells[0].data+'|'+$theOrderNumRow;</script>";
								}
							else{
								$Sideline=1;
								$WgSUM=$WgSUM+$WG*$BoxQty;//毛重总计
								$NG=$WG;//净重			
								$NgSUM=$NgSUM+$NG*$BoxQty;//净重总计			
								$SUMQty=$SUMQty+$FullQty;//装箱总数合计
								
								$Small=$BoxSUM+1;//起始箱号
								$Most=$BoxSUM+$BoxQty;//终止箱号
								$BoxSUM=$Most;
								if($Most!=$Small){
									$Most=$Small."-".$Most;}
									
								if($BoxQty>1){
									$Operation="<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>&nbsp;<a href='#' onclick='ReduceOne(this.parentNode.parentNode.rowIndex)' title='减去一箱'>-</a>";
									}
								else{
									$Operation="<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>&nbsp;&nbsp;";
									}
								echo"<tr>
									<td class='A0101' width='80' align='center' $BoxRowSTR height='20'>$Operation</td>
									<td class='A0101' width='40' align='center' $BoxRowSTR >$BoxQty</td>
									<td class='A0101' width='60' align='center' $BoxRowSTR >$Most</td>
									<td class='A0101' width='80' align='center'>$OrderPO</td>
									<td class='A0101' width='220'>$cName</td>
									<td class='A0101' width='60' align='center'>$BoxPcs</td>
									<td class='A0101' width='60' align='center' $BoxRowSTR>$FullQty</td>
									<td class='A0101' width='60' align=right $BoxRowSTR>$WG</td>
									<td class='A0101' width='' align='center' $BoxRowSTR>$BoxSpec</td>
									</tr>"; 
								//读取行号								
								for($n=1;$n<=$OrderNum;$n++){
									if($rowArray[$n]==$POrderId){
										$theOrderNumRow=$n*6-3;
										}
									}
								//用javascript 写入数据
								$k=$j-1;
								$mainOrderNumRow=$k;
								echo"<script>
								boxTB.rows[$k].cells[0].data=$theOrderNumRow;
								boxTB.rows[$k].cells[1].data=$BoxQty;
								boxTB.rows[$k].cells[2].data=$BoxRow;
								boxTB.rows[$k].cells[3].data=$POrderId;
								boxTB.rows[$k].cells[4].data=$SplitId;
								boxTB.rows[$k].cells[5].data=$BoxPcs;
								</script>";								
								}
							$j++;
						}while ($plRows = mysql_fetch_array($plResult));
					}
					
					?>
					</table>
					</div>
				</td>
				<td width="100" class="A0010">&nbsp;
				<?php   
				echo"<input type='button' name='Submit' value='保存' onclick='ShipDataSave()'>
				<p><<-装箱明细</p>&nbsp;
				<input type='button' name='Submit' value='刷新' onclick='window.location.reload();'>
				<p>
				&nbsp;&nbsp;<input type='button' name='Submit' value='返回' onclick='javascript:ReOpen(\"$fromWebPage\");'>";
				?>
				</td>
			</tr>
		</table>
	</td></tr>
	</table>
	<input type="hidden" id="BoxSUM" name="BoxSUM" value="<?php    echo $BoxSUM?>">
	<input name="chooseDate" type="hidden" id="chooseDate" value="<?php    echo $chooseDate?>">
	<input name="CompanyId" type="hidden" id="CompanyId" value="<?php    echo $CompanyId?>">
    </form>
</body>
</html>
<?php   
}
?>
<script language="javascript">
function ShipDataSave(){
	//检查是否已全部装箱
	var SaveSign=0;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if ((e.type=="checkbox")&&(e.disabled==false)){
			SaveSign=1;break;
			}
		}
	if(SaveSign==1){
		var message=confirm("装箱未完成,确定要保存此装箱设置吗?");
		if (message==true){
			SaveSign=0;
			var toPackingList="N";//不生成packingList
			}
		}
	if(SaveSign==0){
		var PackingList="";
		for(var m=0; m<boxTB.rows.length; m++){
			if(boxTB.rows[m].cells[2].data>1){//并行处理
				var k=boxTB.rows[m].cells[2].data;//并行数					
				for(var n=0;n<k;n++){
					if(n==0){					//首行
						//并行数量|订单ID|装箱数量|箱数|总数量|毛重|外箱尺寸
						
						PackingList=PackingList+","+
						boxTB.rows[m].cells[2].data+"^^"+
						boxTB.rows[m].cells[3].data+"^^"+
						boxTB.rows[m].cells[5].innerHTML+"^^"+
						boxTB.rows[m].cells[1].data+"^^"+
						boxTB.rows[m].cells[6].innerHTML+"^^"+
						boxTB.rows[m].cells[7].innerHTML+"^^"+
						boxTB.rows[m].cells[8].innerHTML+"^^"+
						boxTB.rows[m].cells[4].data;
						}
					else{						//其它行
						m++;
						PackingList=PackingList+","+"^^"+boxTB.rows[m].cells[0].data+"^^"+boxTB.rows[m].cells[2].innerHTML+"^^^^^^^^"+"^^"+boxTB.rows[m].cells[1].data;
						}
					}//end for(var n=0;n<k;n++)
				}
			else{
				//非并行处理	
			    PackingList=PackingList+","+boxTB.rows[m].cells[2].data+"^^"+boxTB.rows[m].cells[3].data+"^^"+boxTB.rows[m].cells[5].innerHTML+"^^"+boxTB.rows[m].cells[1].data+"^^"+boxTB.rows[m].cells[6].innerHTML+"^^"+boxTB.rows[m].cells[7].innerHTML+"^^"+boxTB.rows[m].cells[8].innerHTML+"^^"+boxTB.rows[m].cells[4].data;
				}
			}
		document.form1.PackingList.value=PackingList;
		document.form1.action="ch_shippinglist_packingsave.php?toPackingList="+toPackingList;
		document.form1.submit();
		}
	else{
		return false;
		}
	}

function ShowSequence(){//箱号重整,注意并行处理
    //alert("ShowSequence");
	//return false;
	var DefaultNUM=1;//起始箱号
	for(i=0;i<boxTB.rows.length;i++){
		var RowS=boxTB.rows[i].cells[2].data;//当前行的并行数
		var BoxS=boxTB.rows[i].cells[1].data;//当前行的装箱数
		//alert (RowS);
		if(BoxS==1){
			//boxTB.rows[i].cells[2].innerText=DefaultNUM;
			boxTB.rows[i].cells[2].innerHTML=DefaultNUM;
			DefaultNUM=DefaultNUM*1+1;
			}
		else{
			var EboxNUM=DefaultNUM*1+BoxS*1-1;
			//boxTB.rows[i].cells[2].innerText=DefaultNUM+"-"+EboxNUM;
			boxTB.rows[i].cells[2].innerHTML=DefaultNUM+"-"+EboxNUM;
			DefaultNUM=EboxNUM*1+1;
			}
		i=i+RowS-1;
		}//end for(i=0;i<boxTB.rows.length;i++)
	}   
	
//减少箱数
function ReduceOne(rowIndex){
	var row=boxTB.rows[rowIndex].cells[0].data;//元素顺序
	var boxNUM=Number(boxTB.rows[rowIndex].cells[1].data);//箱数
	var sORd=boxTB.rows[rowIndex].cells[2].data;//单箱还是并箱
	var POrderID=boxTB.rows[rowIndex].cells[3].data;//产品ID
	//var boxPCS=boxTB.rows[rowIndex].cells[5].innerText;//并行首行装箱数量
	var boxPCS=boxTB.rows[rowIndex].cells[5].innerHTML;//并行首行装箱数量
	//var boxWeight=boxTB.rows[rowIndex].cells[7].innerText;//装箱毛重
	var boxWeight=boxTB.rows[rowIndex].cells[7].innerHTML;//装箱毛重
	//如果只有一箱,则作删除处理
	if(boxNUM==1){
		alert("只有一箱,作删除处理!"+rowIndex)
		deleteRow(rowIndex);
		}
	else{
		boxTB.rows[rowIndex].cells[1].data=boxNUM-1;//箱数减少1
		//boxTB.rows[rowIndex].cells[1].innerText=boxTB.rows[rowIndex].cells[1].data;//箱数重新显示
		boxTB.rows[rowIndex].cells[1].innerHTML=boxTB.rows[rowIndex].cells[1].data;//箱数重新显示
		document.form1.BoxSUM.value=Number(document.form1.BoxSUM.value)-1;//总箱数减1
		ShowSequence();		
		/*
		//箱号重整
		var sNUM=0;//起始编号
		var mNUM=0;//最后编号
		for(var m=0; m<boxTB.rows.length; m++){
			var tempNUM=boxTB.rows[m].cells[1].data;//该记录的箱数
			sNUM=mNUM+1;mNUM=mNUM+tempNUM*1;
			if(tempNUM==1){//箱数为1
				boxTB.rows[m].cells[2].innerText=sNUM;}//箱号为sNUM
			else{
				boxTB.rows[m].cells[2].innerText=sNUM+'-'+mNUM;}//如果是多箱，则显示起始箱号和最好后编号
			if(boxTB.rows[m].cells[2].data>1){//并箱行
				m=m+boxTB.rows[m].cells[2].data*1-1;
				}
			}*/
		if(sORd==1){//单箱减少
			//boxTB.rows[rowIndex].cells[6].innerText=boxTB.rows[rowIndex].cells[6].innerText*1-boxTB.rows[rowIndex].cells[5].innerText*1;//总数量
			boxTB.rows[rowIndex].cells[6].innerHTML=boxTB.rows[rowIndex].cells[6].innerHTML*1-boxTB.rows[rowIndex].cells[5].innerHTML*1;//总数量
			//boxTB.rows[rowIndex].cells[6].innerText=boxTB.rows[rowIndex].cells[6].innerText;//重数量重新显示
			boxTB.rows[rowIndex].cells[6].innerHTML=boxTB.rows[rowIndex].cells[6].innerHTML;//重数量重新显示
			}
		else{//并箱减少
			for(var i=1;i<sORd*1;i++){	//其它行装箱数量
				var k=i+rowIndex*1;
				boxPCS=boxPCS*1+boxTB.rows[k].cells[2].data*1;
				}
			//总数量=更新后的箱数*并箱的装箱数量和	或用 原装箱总数-boxPCS；
			//boxTB.rows[rowIndex].cells[6].innerText=boxTB.rows[rowIndex].cells[1].data*boxPCS;
			boxTB.rows[rowIndex].cells[6].innerHTML=boxTB.rows[rowIndex].cells[1].data*boxPCS;
			}
		
		//将减少的数量回加
		//解封
		//单箱或并箱
		if (sORd==1){
			row=row+"|";}		
		var ROWS=row.split("|");
		for(var i=0;i<sORd*1;i++){
			//ROWS[i];//行号
			var k=i+rowIndex*1;
			if(i==0){
				//boxPCS=boxTB.rows[k].cells[5].innerText*1;}//并行首行的产品装箱总数
				boxPCS=boxTB.rows[k].cells[5].innerHTML*1;}//并行首行的产品装箱总数
			else{
				//boxPCS=boxTB.rows[k].cells[2].innerText*1;}//其它行的产品装箱总数
				boxPCS=boxTB.rows[k].cells[2].innerHTML*1;}//其它行的产品装箱总数
				
			for(var j=ROWS[i];j<ROWS[i]*1+6;j++){
				if (form1.elements[j].disabled){			//可编辑设定
					form1.elements[j].disabled=false;
					}
				if (j==(ROWS[i]*1+1)){
					form1.elements[j].value=boxPCS*1+1*form1.elements[j].value;
					getNewBoxNum(j);
					 if (sORd==1 && form1.elements[j+2].value==""){
						 form1.elements[j+2].value=boxWeight;
					  }
					}
				}
			}
			
			
		}
	}
//删除表格行rowIndex行号，0开始
function deleteRow(rowIndex) {
	var row=boxTB.rows[rowIndex].cells[0].data;//原行号
	var boxNUM=Number(boxTB.rows[rowIndex].cells[1].data);//箱数
	var sORd=Number(boxTB.rows[rowIndex].cells[2].data);//单箱还是并箱
	var POrderID=boxTB.rows[rowIndex].cells[3].data;//产品ID
	//var boxPCS=Number(boxTB.rows[rowIndex].cells[5].innerText);//装箱数量
	var boxPCS=Number(boxTB.rows[rowIndex].cells[5].innerHTML);//装箱数量
	//var boxWeight=boxTB.rows[rowIndex].cells[7].innerText;//装箱毛重
	var boxWeight=boxTB.rows[rowIndex].cells[7].innerHTML;//装箱毛重
	var BoxSUM=document.form1.BoxSUM.value;//原总箱数	
	document.form1.BoxSUM.value=BoxSUM-boxNUM;//新的总箱数
	//解封及原数据处理
		if (sORd==1){
			row=row+"|";}		
		var ROWS=row.split("|");
		for(var i=0;i<sORd*1;i++){
			//ROWS[i];//行号
			var k=i+rowIndex*1;
			if(i==0){
				//boxPCS=boxTB.rows[k].cells[5].innerText*1*boxNUM;}//并行首行的产品装箱总数
				boxPCS=boxTB.rows[k].cells[5].innerHTML*1*boxNUM;}//并行首行的产品装箱总数
			else{
				//boxPCS=boxTB.rows[k].cells[2].innerText*1*boxNUM;}//其它行的产品装箱总数
				boxPCS=boxTB.rows[k].cells[2].innerHTML*1*boxNUM;}//其它行的产品装箱总数
				
			for(var j=ROWS[i];j<ROWS[i]*1+6;j++){
				if (form1.elements[j].disabled){	//可编辑设定
					form1.elements[j].disabled=false;
					}
				if (j==(ROWS[i]*1+1)){
					form1.elements[j].value=boxPCS*1+1*form1.elements[j].value;
					 getNewBoxNum(j);
					 if (sORd==1 && form1.elements[j+2].value==""){
						 form1.elements[j+2].value=boxWeight;
					 }
					}
				}
			}
	//最后做删除行处理
	if(sORd==1){
		boxTB.deleteRow(rowIndex); }
	else{
		for(i=0;i<sORd;i++){
			boxTB.deleteRow(rowIndex); 
			}
		}
	ShowSequence();//箱号重整
	} 

//行下移:有三种计算方法：A、并行的首行先下移	B、并行的尾行先下移		C、当前行与目标行互换，逆向上移达到同样效果(使用)
function downMove(tt){   
	//var nowRow=tt.parentElement.rowIndex; //当前行
	//alert("downMove1")
	var nowRow;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		nowRow=tt.parentNode.rowIndex;
	}
	else{
		nowRow=tt.parentElement.rowIndex;
	}	
	var sORd=boxTB.rows[nowRow].cells[2].data;//当前行的并行数
	var endRow=nowRow+sORd;//目标行
	//alert("downMove3")
	if(boxTB.rows[endRow]!=null){//如果目标行存在，则进行下移操作，否则不操作
		//alert("downMove4")
		var eORd=boxTB.rows[endRow].cells[2].data;//目标行的并行数
		//sORd:起始行的并行数	eORd目标行的并行数
		for(var k=0;k<eORd;k++){
			var startRow=endRow+k;
			for(var i=0;i<sORd;i++){
				//目标行是单箱还是并箱
				var preRow=startRow-1;//目标行为前一行
				if(preRow>=0){
					//boxTB.rows[startRow].swapNode(boxTB.rows[preRow]); 
                    //alert ("S:"+boxTB.rows[startRow].rowIndex);
					//alert ("P:"+boxTB.rows[preRow].rowIndex);
					swapNode(boxTB.rows[startRow],boxTB.rows[preRow]);
					
					startRow=startRow-1;
					}//end if(preRow>=0)
				}//end for($i=0;$i<2;$i++)
			}//end for(var k=0;k<sORd;k++)
			ShowSequence();
		}//end if(boxTB.rows[endRow]!=null)
	}  

function GetRealRow(Thistable,startRow,setnum){ //获得真实行号
    var  SearchRowS=startRow;
	var tmpRowS=Thistable.rows[startRow].cells[setnum].data;  
	if (tmpRowS==null || tmpRowS=="undefined"){  //说明是并行，
		//alert("preRow:"+preRow);	
		for (var tempS=startRow-1;tempS>=0;tempS--){
			SearchRowS=Thistable.rows[tempS].rowIndex; 
			//alert(SearchRowS);
			tmpRowS=Thistable.rows[SearchRowS].cells[setnum].data; 
			if(tmpRowS==null || tmpRowS=="undefined"){
				//
			}
			else{   //表明并箱的第一行，则进行交换
			  //alert(SearchRowS);
			  
			  
			  break; //跳出
			}
		}
	}
 // alert(SearchRowS);
  return SearchRowS;
}

//行上移
function upMove(tt){
	var nowRow;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode

		nowRow=tt.parentNode.rowIndex;
	}
	else{
		nowRow=tt.parentElement.rowIndex;
	}
	//alert("nowRow:"+nowRow);
	var tmpstarrow=GetRealRow(boxTB,nowRow,2);
	//alert("tmpstarrow:"+tmpstarrow);
	if (tmpstarrow>0) {  //首行则无效
		var tempprerow=GetRealRow(boxTB,tmpstarrow-1,2);
		//alert("tempprerow:"+tempprerow);
		downMove(boxTB.rows[tempprerow].cells[2]);
	}
	
   /*
	if(nowRow>0){
		//当前行 单箱还是并箱
		var sORd=boxTB.rows[nowRow].cells[2].data;//箱号列：保存并行数	
		//计算目标行是单箱还是并箱：方法是用循环递减，直到行出现并箱数的记录
		var eORd=1;
		
		for(var j=nowRow-1;j>0;j--){
			if(boxTB.rows[j].cells[1].data==""){//如果数值符合要求，则退出
				eORd++;
				}
			else{
				break;
				}
			}
		//sORd:起始行的并行数	eORd目标行的并行数
		 
		for(var k=0;k<sORd;k++){
			var startRow=nowRow+k;
			for(var i=0;i<eORd;i++){
				//目标行是单箱还是并箱
				var preRow=startRow-1;//目标行为前一行
				//alert("Here:"+preRow);
				if(preRow>=0){
					//boxTB.rows[startRow].swapNode(boxTB.rows[preRow]); 
					var tmpRowS=boxTB.rows[preRow].cells[2].data;  
					
					if (tmpRowS==null || tmpRowS=="undefined"){  //说明是并行，
						//alert("preRow:"+preRow);	
					    for (var tempS=preRow-1;tempS>=0;tempS--){
							SearchRowS=boxTB.rows[tempS].rowIndex; 
							tmpRowS=boxTB.rows[SearchRowS].cells[2].data; 
							if(tmpRowS==null || tmpRowS=="undefined"){
								//
							}
							else{   //表明并箱的第一行，则进行交换
							  //alert(SearchRowS);
							  downMove(boxTB.rows[SearchRowS].cells[2]);
							  //swapNode(boxTB.rows[SearchRowS],boxTB.rows[startRow]);
							  break; //跳出
							}
						}

						//swapNode(boxTB.rows[preRow].parentNode[0],boxTB.rows[startRow]);	
					}
					else {

						//////////////////////////////////////////////////
						swapNode(boxTB.rows[startRow],boxTB.rows[preRow]);
					}
					startRow=startRow-1;
					}//end if(preRow>=0)
				}//end for($i=0;$i<2;$i++)
			}//end for(var k=0;k<sORd;k++)
			
			ShowSequence();
		}//end if(nowRow>0)  */
		
	}

function uniteBOX(){//并箱
	//初始化
	var ValueStr=new Array();
	var pcsES=new Array();
	var ROWS="";//多选项序号集合
	var j=0;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		var temp=i+2;
		var g=form1.elements[temp];//要检查数字是否合法		
		if ((e.type=="checkbox")&&(e.checked==true)){
			//存数据: 多选项项序号|PO|订单流水号号|中文名|英文名|PCS数量		
			ValueStr[j]=i+'|'+e.value+'|'+g.value; 
			pcsES[j]=g.value;
			//检查是否输入数量或单箱数量超出未装箱数量
			
			var Residualtemp=form1.elements[i+1].value-g.value;//未装箱数量
			if(g.value=="" || Residualtemp<0){
				alert("没有输入装箱数量或超出许可值!");
				return;
				}
			if(j==0){
				ROWS=i;
				}
			else{
				ROWS=ROWS+"|"+i;
				}
			 j++;
			}//end if ((e.type=="checkbox")&&(e.checked==true))
		}//end for (var i=0;i<form1.elements.length;i++)
	var returnValue =window.showModalDialog("ch_shippinglist_tountiebox.php",window,"dialogWidth=400px;dialogHeight=300px");
	if (returnValue){
		//var BoxRowSTR="rowspan="+j; //并行数据;
		var reValueStr=returnValue.split("|");
		var wgValue=reValueStr[0];//毛重
		var boxsValue=reValueStr[1];//并箱箱数
		var specValue=reValueStr[2];//外箱尺寸
		var box1QTY=0;
		if(checkNUM(wgValue)==1){//检查数字,合法则新建行			
			//开始新增装箱记录
			var getTb= document.getElementById("boxTB");
			var tmpRow=getTb.rows.length;
			var newTr1 = getTb.insertRow(-1); //并箱的首行  // zx 2011-05-31 insertRow(-1);  必须有-1 否则Safari和firfox不兼容
			var fristindex=boxTB.rows.length-1;//并箱首行索引
			//第一列:并行的操作列
			var newTd1 = newTr1.insertCell(-1); 
				newTd1.rowSpan=j;//j为合并的产品数
				newTd1.data=ROWS;//并箱行数集合
			//第二列:并行的箱数列
			var newTd2 = newTr1.insertCell(-1);
				newTd2.rowSpan=j;
				newTd2.data=boxsValue;//并箱总箱数
			//第三列:并行的箱号列
			var newTd3 = newTr1.insertCell(-1);	
				newTd3.rowSpan=j;
			//第四列:并行的PO列
			var newTd4up = newTr1.insertCell(-1);	
			//第五列:产品名称
			var newTd5up = newTr1.insertCell(-1);
			//第六列:单箱数量
			var newTd6up = newTr1.insertCell(-1); 
				newTd6up.align="center";	
			//第七列:并行的装箱列总数
			var newTd7 = newTr1.insertCell(-1); 
				newTd7.rowSpan=j;
				newTd7.align="center";	
			//第八列:并行的毛重列
			var newTd8 = newTr1.insertCell(-1); 
				newTd8.rowSpan=j;
				newTd8.align="center";	
			//第九列:并行的外箱列
			var newTd9 = newTr1.insertCell(-1); 
				newTd9.rowSpan=j;
				newTd9.align="center";
			
			//开始处理****并箱的其它行
			var DboxSUM=0;
			
			for(var i=1;i<j;i++){
			     
				var Split_ValueStr=ValueStr[i].split("|")
				var otherRow = getTb.insertRow(-1); 				
				var Cel3 = otherRow.insertCell(-1);		//定义PO列
					Cel3.innerHTML=Split_ValueStr[1];	//PO
					Cel3.data=Split_ValueStr[2];		//订单流水号
					Cel3.className ="A0101";
					Cel3.height="20";
					Cel3.width="80";
					
				var Cel4 = otherRow.insertCell(-1);		//定义产品名称列
					Cel4.innerHTML=Split_ValueStr[3];	//产品名称
					
					Cel4.data=Split_ValueStr[5];	//SplitId
					Cel4.className ="A0101";
					Cel4.width="220";
					
				var Cel5 = otherRow.insertCell(-1);//定义单箱数量列
					Cel5.innerHTML=Split_ValueStr[6];//
					Cel5.data=Split_ValueStr[6];//
					Cel5.className ="A0101";
					Cel5.align="center";
					
					DboxSUM=DboxSUM+Cel5.data*1;
				}
				
			//结束处理****并箱的其它行
			
			//首行数据设定
			//定义列宽
		
		
			newTd1.width=80;
			newTd2.width=40;
			newTd3.width=60;
			newTd4up.width=80;
			newTd5up.width=220;
			newTd6up.width=60;
			newTd7.width=60;
			newTd8.width=60;
			newTd9.width="";  		
			if(boxsValue>1){
				newTd1.innerHTML="<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>&nbsp;<a href='#' onclick='ReduceOne(this.parentNode.parentNode.rowIndex)' title='减去一箱'>-</a>";
				}
			else{
				newTd1.innerHTML="<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>&nbsp;";
				}
			
			var row1_ValueStr=ValueStr[0].split("|");//并箱首行数据
			var sNUM=form1.BoxSUM.value*1+1;//起始编号=总箱数+1;
			var mNUM=form1.BoxSUM.value*1+boxsValue*1;//最后编号=总箱数+并箱箱数
			newTd1.className ="A0101";
			newTd1.align="center";
			//首行二列:箱数列
			//newTd2.innerText=boxsValue;//箱数
			newTd2.innerHTML=boxsValue;//箱数
			newTd2.data=boxsValue;//箱数
			newTd2.className ="A0101";
			newTd2.align="center";
			//首行三列:箱号列
			if(boxsValue==1){//并箱的箱数为1
				//newTd3.innerText=sNUM;	//起始箱号=结束箱号
				newTd3.innerHTML=sNUM;
				}
			else{//并箱的箱数大于1
				//newTd3.innerText=sNUM+"-"+mNUM;//起始箱号-结束箱号
				newTd3.innerHTML=sNUM;
				}
			newTd3.data=j;//并箱的产品数
			newTd3.className ="A0101";
			newTd3.align="center";
			//首行四列:PO
			//newTd4up.innerText=row1_ValueStr[1];
			newTd4up.innerHTML=row1_ValueStr[1];
			newTd4up.data=row1_ValueStr[2];
			newTd4up.className ="A0101";
			newTd4up.height="20";
			
			//首行五列:中文名称
			newTd5up.innerHTML=row1_ValueStr[3];	//产品名称
			newTd5up.data=row1_ValueStr[5];
			newTd5up.className ="A0101";
			
			//首行六列:单箱数量
			//newTd6up.innerText=row1_ValueStr[5];
			newTd6up.innerHTML=row1_ValueStr[6];
			newTd6up.data=row1_ValueStr[6];
			newTd6up.className ="A0101";
			
			//首行七列:装箱总数
			newTd7.data=boxsValue*(row1_ValueStr[6]*1+DboxSUM*1);
			newTd7.innerHTML=newTd7.data;
			newTd7.className ="A0101";
			
			//并箱首行第八列:毛重
			newTd8.data=wgValue;
			newTd8.innerHTML=wgValue;
			newTd8.className ="A0101";

			//并箱首行第九列:外箱尺寸
			newTd9.data=specValue;
			newTd9.innerHTML=specValue;
			newTd9.className ="A0101";
			//返回处理数据源
			for(var i=0;i<j;i++){
				var temp_ValueStr=ValueStr[i].split("|");
				var x=temp_ValueStr[0]*1;
				//清空
				form1.elements[x].checked=false;	
				//计算未装箱数量
				form1.elements[x+1].value=form1.elements[x+1].value-temp_ValueStr[6]*boxsValue;
				if (form1.elements[x+1].value==0){//禁止			
				 for(var k=x;k<x+6;k++){
					form1.elements[k].disabled=true;
					}
				}
				else{
				    getNewBoxNum(x+1);
				}
			}
			form1.BoxSUM.value=form1.BoxSUM.value*1+boxsValue*1;	
			}
		else{
			alert("毛重价格不对！重新操作!");return
			}
		}
	else{
		alert("没有输入正确的值!重新操作!");return false;
		}//end if (returnValue)
	}//end function

function Addbox(){//单箱增加
	var ChooseSign=0;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if ((e.type=="checkbox")&&(e.checked==true)){
			ChooseSign=1;
			var ValueStr=e.value; 
			var Split_ValueStr=ValueStr.split("|");//PO|订单号|中文名|英文名
			var Residualtemp=form1.elements[i+1].value;//未装箱数量
			//可取消，因为装完后禁止再选上
			if (Residualtemp>0){
				var pcstemp=form1.elements[i+2].value;//传递的装箱数量
				var wgtemp=form1.elements[i+3].value;//传递的装箱毛量
				var numtemp=form1.elements[i+4].value;//传递的箱数
				var spectemp=form1.elements[i+5].value;//外箱尺寸
				var boxNUM=document.form1.BoxSUM.value;//读取总箱数
				//判断装箱数量是否超出范围
				var newResidual=Residualtemp*1-(pcstemp*1)*(numtemp*1);
				var newRealWeight=wgtemp*1*(newResidual*1/pcstemp*1);
				//新的未装箱数量不少于0，并且装箱的数量不为0
				if (newResidual>=0  && pcstemp>0 && wgtemp>0 && numtemp>0 && spectemp!=""){					
					//第一列：操作
					oTR=boxTB.insertRow(boxTB.rows.length);	
					tmpNum=oTR.rowIndex+1;
					oTD=oTR.insertCell(0);
					if(numtemp>1){
						oTD.innerHTML="<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>&nbsp;<a href='#' onclick='ReduceOne(this.parentNode.parentNode.rowIndex)' title='减去一箱'>-</a>";
						}
					else{
						oTD.innerHTML="<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>&nbsp;&nbsp;";
						}
					oTD.data=i;
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;
						};
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="80";
					oTD.height="20";
					
					//第二列:箱数
					oTD=oTR.insertCell(1);
					oTD.innerHTML=numtemp;
					oTD.data=numtemp;
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="40";
				
					//第三列:箱号					
					oTD=oTR.insertCell(2);
					var sNUM=boxNUM*1+1;
					if (numtemp>1){	
						var mNUM=boxNUM*1+numtemp*1;
						//oTD.innerText= sNUM+'-'+mNUM;
						oTD.innerHTML= sNUM+'-'+mNUM;
						}
					else{
						//oTD.innerText= sNUM;
						oTD.innerHTML= sNUM;
						}
					oTD.data=1;
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					var newboxNUM=boxNUM*1+numtemp*1;
					form1.BoxSUM.value=newboxNUM;//总箱数记录
					
					//第四列:PO
					oTD=oTR.insertCell(3);
					oTD.className ="A0101";
					oTD.innerHTML= Split_ValueStr[0];
					oTD.data=Split_ValueStr[1];
					oTD.width="80";
					
					//第五列:产品名称
					oTD=oTR.insertCell(4);
					oTD.className ="A0101";
					oTD.innerHTML= Split_ValueStr[2];
					oTD.data=Split_ValueStr[4];
					oTD.width="220";
					
					//第六列：单箱数量
					oTD=oTR.insertCell(5);
					oTD.className ="A0101";
					//oTD.innerText=pcstemp*1;
					oTD.innerHTML=pcstemp*1;
					oTD.data=pcstemp;
					oTD.width="60";
					oTD.align="center";
					
					//第七列：总数量
					oTD=oTR.insertCell(6);
					oTD.className ="A0101";
					//oTD.innerText=pcstemp*numtemp;
					oTD.innerHTML=pcstemp*numtemp;
					oTD.data=pcstemp*numtemp;
					oTD.width="60";
					oTD.align="center";
					
					//第八列：毛重
					oTD=oTR.insertCell(7);
					oTD.className ="A0101";
					//oTD.innerText=wgtemp*1;
					oTD.innerHTML=wgtemp*1;
					oTD.data=wgtemp*1;
					oTD.width="60";
					oTD.align="center";
					
					//第九列：外箱尺寸
					oTD=oTR.insertCell(8);
					oTD.className ="A0101";
					//oTD.innerText=spectemp;
					oTD.innerHTML=spectemp;
					oTD.data=spectemp;
					oTD.width="";
					oTD.align="center";
					
					//清空数据
					
					//form1.elements[i+2].value='';
					//form1.elements[i+3].value='';
					//form1.elements[i+4].value='';
					//重新计算数据
					form1.elements[i+1].value=newResidual;
					form1.elements[i+3].value=newRealWeight;
					//状态重置
					e.checked=false;
					//禁止操作
					if (newResidual==0){
						 form1.elements[i+4].value=0;
						 for(var k=i;k<i+6;k++){
							form1.elements[k].disabled=true;
							}
						}
					//重新初始化
					else{
                         getNewBoxNum(i+1);
					  }
					//结束单箱加入
					}
				else{
					alert("错误的数字:装箱数量、箱数、毛重、外箱尺寸未设置或数值错误,请检查!");
					return false;
					}//end if (newResidual>0 && Residualtemp>0)
				}
			else{
				alert("已全部装箱！");return false;
				}//end if (Residualtemp>0)
			}//end if ((e.type=="checkbox")&&(e.checked==true))
		}//end for (var i=0;i<form1.elements.length;i++)
	if(ChooseSign==0){
		alert("没有选取数据!");return false;		
		}
	}
function checkNUM(NUM)
{
	var i,j,strTemp;
	strTemp=".0123456789"; 
 	if ( NUM.length== 0)
  		return 0
 	for (i=0;i<NUM.length;i++)
	{
  		j=strTemp.indexOf(NUM.charAt(i)); 
  		if(j==-1)
  		{
  			//说明有字符不是数字
   			return 0;
  			}
 		}
 	//说明是数字
 	return 1;
	}

function getNewBoxNum(i){
	var newBoxNum;
	var boxPcs=1*form1.elements[i+1].value;
	if (boxPcs>0){
	  newBoxNum=(1*form1.elements[i].value)/boxPcs;
	  newBoxNum=Math.floor(newBoxNum);
	  form1.elements[i+3].value=newBoxNum;
	}
	else{
	 form1.elements[i+3].value="";
	}
}
</script>
