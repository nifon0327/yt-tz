
<?php   
//传入参数：$StuffId、$OrderQty 电信---yang 20120801

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$dataArray=explode("|",$args);
$gStuffId=$dataArray[0];
$StockId=$dataArray[1];
$OrderQty=$dataArray[2];
echo"<table  width='685' cellspacing='1' border='1' align='left' style='table-layout:fixed;word-break:break-all; word-wrap:break-word;margin:2px 0px 2px 30px;' ><tr bgcolor='#CCCCCC'>
		 <td width='30'  align='center'>NO.</td>
         <td width='40'  align='center'>配件ID</td>
		 <td width='250' align='center'>配件名称</td>
         <td width='30'  align='center'>单位</td>
         <td width='150'  align='center'>刀模编号</td>
         <td width='50'  align='center'>刀模图档</td>";
		if ($StockId>0){
		echo "<td width='60' align='center'>片数/码</td>
				 <td width='40'  align='center'>码数</td>
				 <td width='60' align='center'>裁片数量</td>
				 <td width='60' align='center'>已登记数</td>
				 <td width='60' align='center'>登记时间</td>
				 <td width='60' align='center'>备料数量</td>
				 <td width='60' align='center'>备料时间</td>
				</tr>";
		}
		else{
				 echo "<td width='60' align='center'>对应关系</td>
				 <td width='40'  align='center'>采购</td>
				 <td width='80' align='center'>供应商</td>
				 <td width='50' align='center'>存放楼层</td>
				</tr>";
		}
//从配件表和配件关系表中提取配件数据	  
			if ($StockId>0){
				   $StuffSql="SELECT A.Id,A.Relation,A.mStuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,
						    D.Gremark,D.TypeId,D.SendFloor,U.Name AS Unit,A.CutId,C.CutName,C.Picture AS cutPicture,C.cutSign  
							FROM  $DataIn.slice_sheet A  
			                LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
			                LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
					        LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
					        LEFT JOIN $DataIn.cut_data C ON C.Id=A.CutId 
			               WHERE A.StockId='$StockId' AND A.mStuffId>0 
					ORDER BY Id";
			}
			else{
				$StuffSql="SELECT A.Id,A.Relation,A.mStuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,
						    D.Gremark,D.TypeId,D.SendFloor,U.Name AS Unit,A.CutId,C.CutName,C.Picture AS cutPicture,C.cutSign   
							FROM  $DataIn.slice_bom A  
			                LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.mStuffId 
			                LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
					        LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
					        LEFT JOIN $DataIn.cut_data C ON C.Id=A.CutId 
			               WHERE A.StuffId='$gStuffId' AND A.mStuffId>0 
					ORDER BY Id";
			}
			
			$StuffResult = mysql_query($StuffSql,$link_id);
			$k=1;$tId=1;
			//echo $StuffSql;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			     $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			     $dt=anmaIn("download/cut_data/",$SinkOrder,$motherSTR);
			     $dw=anmaIn("download/cut_drawing/",$SinkOrder,$motherSTR);
			     $today=date("Y-m-d");
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$mStuffId=$StuffMyrow["mStuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$TypeId=$StuffMyrow["TypeId"];
					$Price=$StuffMyrow["Price"];
                    $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];

					$Relation=$StuffMyrow["Relation"];
					$bps = mysql_query("SELECT M.Name,P.Forshort 
					FROM $DataIn.bps B
					LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
					LEFT JOIN $DataIn.providerdata P ON P.CompanyId=B.CompanyId
					WHERE B.StuffId='$mStuffId'",$link_id);
					if($bpsMyrow=mysql_fetch_array($bps)){
						$Name=$bpsMyrow["Name"];
						$Forshort=$bpsMyrow["Forshort"];
						}
					
					//$Gfile=$StuffMyrow["Gfile"];
					//$Gstate=$StuffMyrow["Gstate"]; 
					//$Gremark=$StuffMyrow["Gremark"];
					
					$StuffId=$mStuffId;

					//include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
					//检查是否有图片
					$Picture=$StuffMyrow["Picture"];
					include "../model/subprogram/stuffimg_model.php";
					include "../model/subprogram/stuff_property_icon.php";	//属性显示
					$SendFloor=$StuffMyrow["SendFloor"];
					include "../model/subprogram/stuff_GetFloor.php";
					$FloorName=$FloorName=""?"&nbsp":$FloorName;
					
					 $CutId=$StuffMyrow["CutId"];
					$CutName=$StuffMyrow["CutName"];
					
					//刀模图标显示  $CutName,$cutSign->$CutIconFile
					$cutSign=$StuffMyrow["cutSign"];
					include "../admin/subprogram/getCuttingIcon.php";
					//刀模名称
					$Picture=$StuffMyrow["cutPicture"];
                    if($Picture==1){
                       $fn=anmaIn("C".$CutId.".jpg",$SinkOrder,$motherSTR);
                        $CutName="<a href=\"../admin/openorload.php?d=$dt&f=$fn&Type=&Action=6\"target=\"download\">$CutName</a>";
                     }
                     
                        //刀模图档
				  $CutDrawing="&nbsp;";
			      $drawingSql=mysql_query("SELECT Picture FROM $DataIn.slice_drawing WHERE  StuffId='$gStuffId' AND CutId='$CutId'",$link_id);
			     // echo "SELECT Picture FROM $DataIn.slice_drawing WHERE  StuffId='$gStuffId' AND CutId='$CutId'";
			     if($drawingRow=mysql_fetch_array($drawingSql)){
			        $drawingPicture=$drawingRow["Picture"];
				    $fd=anmaIn($drawingPicture,$SinkOrder,$motherSTR);
				    $CutDrawing="<a href=\"../admin/openorload.php?d=$dw&f=$fd&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='刀模图档' width='18' height='18'></a>";
			       } 
			       //检查是否为属性2、3配件
			        //二级BOM表
				    $showProcess="&nbsp;";$ProcessTable="";$colspan=15;
				    $PR_StuffId=$mStuffId; $checkSign=0;
				    include "../model/subprogram/stuff_property.php";
				     if ($Property_gSign==1){
				         $CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.fits_bom A  WHERE  A.StuffId='$StuffId' LIMIT 1",$link_id);
		                  if($CheckProcessRow=mysql_fetch_array($CheckProcessSql)){
		                     // echo "SlicebomTable_$PandsId$tId";
		                      $tId=date("YmdHis") . rand(100,999);
		                     $checkSign=1;
		                              $showProcess="<img onClick='ShowDropTable(FitsbomTable_$PandsId$tId,FitsbomDiv_$PandsId$tId,\"fitsbom_ajax\",\"$StuffId|0\");'  src='../images/showtable.gif' 
					title='显示或隐藏片材BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor'>";
					                $ProcessTable="<tr id='FitsbomTable_$PandsId$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='FitsbomDiv_$PandsId$tId' width='720'></div></td></tr>"; 
					                $tId++;
		                            }
				     }
				     
				     if ($Property_mSign==1 &&  $checkSign==0){
				         $CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.slice_bom A  WHERE  A.StuffId='$StuffId' LIMIT 1",$link_id);
		                  if($CheckProcessRow=mysql_fetch_array($CheckProcessSql)){
		                     // echo "SlicebomTable_$PandsId$tId";
		                     $tId=date("YmdHis") . rand(100,999);
		                              $showProcess="<img onClick='ShowDropTable(SlicebomTable_$PandsId$tId,SlicebomDiv_$PandsId$tId,\"slicebom_ajax\",\"$StuffId|0\");'  src='../images/showtable.gif' 
					title='显示或隐藏片材BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor'>";
					                $ProcessTable="<tr id='SlicebomTable_$PandsId$tId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='SlicebomDiv_$PandsId$tId' width='720'></div></td></tr>"; 
					                $tId++;
		                            }
				     }

				    
					echo "<tr bgcolor=#EAEAEA>";
					echo"<td  align='center' height='21' >$showProcess $k</td>";
					echo"<td  align='center'>$mStuffId</td>";
					echo"<td>$StuffCname</td>";
					echo"<td align='center'>$Unit</td>";
					echo"<td>$CutIconFile $CutName</td>";
					echo"<td align='center'>$CutDrawing</td>";
					
					if ($StockId>0){
					      $sRelation=explode("/",$Relation);
					      if (count($sRelation)>1){
							   $pcsQty=floor($sRelation[1]/$sRelation[0]);//片数/码
							   $mQty=sprintf("%.1f",$OrderQty*($sRelation[0]/$sRelation[1]));//码数
						}
						else{
							  $pcsQty=$sRelation[0];
							  $mQty=sprintf("%.1f",$OrderQty*$sRelation[0]);//码数
						}
						$OrderQty=floatval($OrderQty);
						//$cutQty=floor($pcsQty*$mQty);//裁片数量
                        $llDate="&nbsp;";
                       //检查是否已备料
		               $llSql=mysql_query("SELECT SUM(S.Qty) AS llQty,L.Date FROM  $DataIn.ck5_llsheet S   WHERE  S.StockId='$StockId' AND S.StuffId='$mStuffId'",$link_id);
		               if($llRows=mysql_fetch_array($llSql)){
		                      $llQty = $llRows["llQty"];
		                      $llDate=$llQty==$mQty?$llRows["Date"]:"<div style='color:#FFAA00' title='已备数量:" .$llQty . "'>".$llRows["Date"]. "</div>"; 
		                      
		                        
		               }
		               
		            
		               
                        
                        echo"<td align='center'>$pcsQty</td>";
						echo"<td align='center'>$mQty</td>";
						echo"<td align='center' >$OrderQty</td>";
						echo"<td align='center'>$klQty</td>";
						echo"<td align='center'>$klDate</td>";
						echo"<td align='center'>$llDate</td>";
						echo"<td align='center'>$llQty</td>";
					}
					else{
						echo"<td align='center'>$Relation</td>";
						echo"<td align='center'>$Name</td>";
						echo"<td align='center' >$Forshort</td>";
						echo"<td align='center'>$FloorName</td>";
				   }
					echo"</tr>";
					echo $ProcessTable;
					$CutArray =explode("/",$Relation);
		           if(count($CutArray)==2){$mRelation=$CutArray[1];}
	                     else{$mRelation=$CutArray[0];}
					$k++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
		}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
   else{
	   echo"<tr><td height='30' colspan='9' >没有设置原材料配件资料,请检查.</td></tr>";
   }
   /*
   echo"<table  width='685' cellspacing='1' border='1' align='left' style='table-layout:fixed;word-break:break-all; word-wrap:break-word;margin:0px 0px 10px 15px;' ><tr bgcolor='#CCCCCC'>
         <td width='113'  align='center'>刀模编号</td>
		 <td width='60'  align='center'>裁片图片</td>
         <td width='60'  align='center'>图档</td>
		 <td width='60' align='center'>对应关系</td>
		 <td width='62'  align='center'>片数/码</td>
		 <td width='334'  align='center'>&nbsp;</td>
		</tr>";
   	//从配件表和配件关系表中提取刀模关系 
			$cutResult = mysql_query("SELECT A.Id,A.Relation,A.cutId,C.CutName,C.Picture 
				FROM slice_bom A  
                LEFT JOIN $DataIn.cut_data C ON C.Id=A.cutId  
               WHERE A.StuffId='$gStuffId' AND A.cutId>0 ORDER BY Id",$link_id);
			if($cutMyrow=mysql_fetch_array($cutResult)) {//如果设定了刀模关系
			     $dt=anmaIn("download/cut_data/",$SinkOrder,$motherSTR); 
				do{	
					$n=$m;
					$PandsId=$cutMyrow["Id"];
					$cutId=$cutMyrow["cutId"];
					$CutName=$cutMyrow["CutName"];
					$Relation=$cutMyrow["Relation"];
		           $cutQty=$Relation*$mRelation;
					//刀模图标显示  $CutName->$CutIconFile
					include "../admin/subprogram/getCuttingIcon.php";
					
					$Picture=$cutMyrow["Picture"];
                    if($Picture==1){
                       $fn=anmaIn("C".$cutId.".jpg",$SinkOrder,$motherSTR);
                        $CutName="<a href=\"../admin/openorload.php?d=$dt&f=$fn&Type=&Action=6\"target=\"download\">$CutName</a>";
                     }
                     
					//刀模名称
                    echo "<tr bgcolor=#EAEAEA>";
					//echo"<td  align='center' height='21' >$k</td>";
					echo"<td >$CutIconFile $CutName</td>";
                    echo"<td  align='center'>-</td>";
				    echo"<td  align='center'>-</td>";
                   echo"<td  align='center'>$Relation</td>";
					echo"<td  align='center'>$cutQty</td>";
					echo"<td  align='center'>-</td>";
					echo"</tr>";
					$i++;
					} while ($cutMyrow=mysql_fetch_array($cutResult));
				}
    else{
	       echo"<tr><td height='30' colspan='9' >没有设置刀模资料,请检查.</td></tr>";
   }
			*/
echo"</table>";
?>          