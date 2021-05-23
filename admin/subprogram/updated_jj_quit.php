<?php   

//传入参数:$upDataMain主结付单数据表,$upDataSheet明细数据表;
//已结付的退回电信---yang 20120801
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//找结付金额以及相应的主结付单、结付日期
		$checkMain=mysql_query("SELECT S.Mid,S.Amount,M.PayDate ,S.Number,S.ItemName FROM $upDataSheet S 
		LEFT JOIN $upDataMain M ON M.Id=S.Mid WHERE S.Id=$Id ORDER BY S.Id DESC LIMIT 1",$link_id);
		if($checkRow=mysql_fetch_array($checkMain)){
		
			$Mid=$checkRow["Mid"];
			$PayDate=$checkRow["PayDate"];
			$Amount=$checkRow["Amount"];
			$ItemName=$checkRow["ItemName"];
			$Number=$checkRow["Number"];
			//明细退回
			//$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id=$Id";
			$sql="DELETE FROM $upDataSheet  WHERE Id=$Id";
			$result = mysql_query($sql);
			if ($result){					
			    //更新cw11_jjsheet_first  表的Estate 状态
			    $updateSql="UPDATE $DataIn.cw11_jjsheet_frist SET $SetStr WHERE Number='$Number' AND ItemName='$ItemName'";
			    $updateResult=mysql_query($updateSql);
	            //A:明细记录更新，恢复为待结付状态
				$Log.="$x-ID号为 $Id 的 $Log_Item $Log_Funtion 成功!</br>";
				//检查主结付单是否仍有资料:有，则更新金额，无则删除金额；然后处理现金流水帐
				$checkSheet=mysql_query("SELECT Id FROM $upDataSheet WHERE Mid=$Mid ORDER BY Mid",$link_id);
				$sheetRows=@mysql_num_rows($checkSheet);
				if($sheetRows>0){
					$upMain = "UPDATE $upDataMain SET PayAmount=PayAmount-$Amount $UpdepotCharge WHERE Id=$Mid";  //此句有个Bug无法实现,需要改成如下!
					//echo "$upMain";
					$mainResult = mysql_query($upMain);
					$Log.="&nbsp;&nbsp;主Id号为 $Mid 的主结付单的结付金额更新成功! $upMain</br>";
					}
				else{
					$delMain = "DELETE FROM $upDataMain WHERE Id=$Mid";
					$delResult = mysql_query($delMain);
					$Log.="&nbsp;&nbsp;主Id号为 $Mid 的主结付单的结付单删除成功!</br>";
					//删除图片文件
					$FilePathC="../download/$FileDir/C".$Mid.".jpg";
					if(file_exists($FilePathC)){
						unlink($FilePathC);
						}
					$FilePathP="../download/$FileDir/P".$Mid.".jpg";
					if(file_exists($FilePathP)){
						unlink($FilePathP);
						}
					$FilePathR="../download/$FileDir/R".$Mid.".jpg";
					if(file_exists($FilePathR)){
						unlink($FilePathR);
						}
					//$OPTIMIZE = mysql_query("OPTIMIZE TABLE $upDataMain ");
					}
				$y++;
				}//end if ($result)
			else{
				$Log.="$x-ID号为 $Id 的 $Log_Item $Log_Funtion 成功!</br>";
				$OperationResult="N";
				}//end if ($result)
			$x++;
			}//end if($checkRow=mysql_fetch_array($checkMain))
		}//end if
	}//end for
if($IdCount==$y){			//当月已无记录，转默认设置
	$Page="";$Pagination="";$chooseMonth="";
	}
?>