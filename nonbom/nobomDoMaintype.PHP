<?php 

$host="192.168.1.250";
$user="Admin";
$pass="Admin@12345";
$db="mysql"; 
$link_id=mysql_connect($host,$user,$pass);
mysql_query("SET NAMES 'utf8'");
						//泥黄：行选定色
$DataPublic="d0";
$DataIn="d7";		//当前网站，不一致的地方
/*
//申购单
$sheetInSql="INSERT INTO $DataIn.nonbom6_cgsheet (Id,Mid,GoodsId,CompanyId,Qty,Price,Remark,ReturnReasons,rkSign,Estate,Locks,Date,Operator) VALUES (NULL,'0','$GoodsId','$CompanyId','$Qty','$Price','$Remark','','1','2','1','$sgDate','$Operator') ";

			$inRecode="INSERT INTO $DataIn.nonbom6_cgmain (Id,PurchaseID,CompanyId,BuyerId,Remark,Locks,Date,Operator) VALUES (NULL,'$PurchaseID','$CompanyId','$BuyerId','终审后自动生成的非BOM采购单','0','$DateTime','$Operator')";
			$inAction=@mysql_query($inRecode);
			$Mid=mysql_insert_id();
			if($inAction && mysql_affected_rows()>0){ 
				$Log="$TitleSTR 成功!<br>";
				$Sql = "UPDATE $DataIn.nonbom6_cgsheet A
				LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId
				SET A.Mid='$Mid',A.Locks='0',B.oStockQty=B.oStockQty+A.Qty WHERE A.Id IN ($Ids) AND A.Estate='1' AND A.Mid='0'";//已审核的才加入采购单，同时更新采购库存

//请款单 
$inRecode="INSERT INTO $DataIn.nonbom12_cwsheet SELECT NULL,'0',S.Id,S.GoodsId,M.CompanyId,S.Qty,S.Price,S.Qty*S.Price,'$Month','2','1','$DateTime','$Operator'  FROM $DataIn.nonbom6_cgsheet S 
		LEFT JOIN $DataIn.nonbom6_cgmain M ON M.Id=S.Mid  WHERE S.rkSign='0' AND S.Id IN ($Ids)";


//主结付单
$IN_recode="INSERT INTO $DataIn.nonbom12_cwmain (Id,BankId,CompanyId,PayDate,PayAmount,djAmount,Payee,Receipt,Checksheet,Remark,Date,Locks,Operator) VALUES (NULL,'$BankId','$CompanyId','$Date','0','0','0','0','0','','$DateTime','0','$Operator')";
$sql = "UPDATE $DataIn.nonbom12_cwsheet SET Estate=0,Mid=$Mid,Locks=0 WHERE Id IN ($Ids)";

$djSql = "UPDATE $DataIn.nonbom11_djsheet SET Estate=0,Did='$Mid',Locks=0 WHERE Id IN ($djIds)";
//货款，订金
$upSql = "UPDATE $DataIn.nonbom12_cwmain SET PayAmount=(SELECT SUM(Amount) FROM $DataIn.nonbom12_cwsheet WHERE Mid='$Mid')-(SELECT SUM(Amount) FROM $DataIn.nonbom11_djsheet WHERE Did='$Mid'),djAmount=(SELECT SUM(Amount) FROM $DataIn.nonbom11_djsheet WHERE Did='$Mid') WHERE Id='$Mid' LIMIT 1";
//只有货款
$upSql = "UPDATE $DataIn.nonbom12_cwmain SET PayAmount=(SELECT SUM(Amount) FROM $DataIn.nonbom12_cwsheet WHERE Mid=$Mid)WHERE Id=$Mid LIMIT 1";
*/

// SELECT sum(Qty*price),sUm(Amount)  from  d7.nonbom12_cwsheet S where S.MId=11 这两个值为什么不相等？？？？？？？？？？？？？？？

//同一个结付主ID，不同的采购单主ID，不是同一类的，把它拆分成多个结付记录？
$mySql="SELECT A.WId,count(*) as cs from (
        SELECT W.Id as WId,M.Id,D.TypeId FROM $DataIn.nonbom12_cwmain W
        LEFT JOIN $DataIn.nonbom12_cwsheet S ON S.Mid=W.Id
		LEFT JOIN $DataIn.nonbom6_cgsheet  C ON C.Id=S.CgId
		LEFT JOIN $DataIn.nonbom6_cgmain   M ON M.Id=C.Mid
		LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=C.GoodsId
	    LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=D.TypeId 
		GROUP BY W.Id,M.Id,D.TypeId   )A  GROUP BY A.WID having count(*)>1"; 
//echo "$mySql <br>";  //WHERE W.Id=11,WHERE W.Id=11,WHERE W.Id='334' 
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$WId=$myRow["WId"];  //结付主ID
		$checkQty=mysql_fetch_array(mysql_query("SELECT PayAmount,djAmount,Payee,Receipt,Checksheet
												FROM $DataIn.nonbom12_cwmain  WHERE Id='$WId' ",$link_id));
		$PayAmount=$checkQty["PayAmount"];  //结付货款
		$djAmount=$checkQty["djAmount"];    //订金
		if ($djAmount>0) {
			echo "?????????????????????????<br>";
		}
		
		$Payee=$checkQty["Payee"];    //凭证
		$Receipt=$checkQty["Receipt"];    //回执
		$Checksheet=$checkQty["Checksheet"];    //对帐单
		echo "货款+订金($PayAmount+ $djAmount)---------------<br>";
		
		//把结付主ID相同的，采购单主ID选出来，并按金额由高到低，好用来抵付订金，尽量不用拆
		$SuB1_Sql="
				SELECT M.Id AS OId,D.TypeId,sum(S.Qty*S.Price) SumAmout,sum(S.Amount) as AllAmount FROM $DataIn.nonbom12_cwsheet S 
				LEFT JOIN $DataIn.nonbom6_cgsheet  C ON C.Id=S.CgId
				LEFT JOIN $DataIn.nonbom6_cgmain   M ON M.Id=C.Mid
				LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=C.GoodsId
				LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=D.TypeId
				WHERE S.Mid='$WId' GROUP BY M.Id,D.TypeId order by sum(S.Amount) DESC
				"; 	
		//echo "$SuB1_Sql <br>";		
		$SuB1_Result = mysql_query($SuB1_Sql,$link_id);
		if($SuB1_Row = mysql_fetch_array($SuB1_Result)){
			$SuB1_i=1;
			do{	
			  //echo "************************* <br>";
			  
			  $OId=$SuB1_Row["OId"];  //采购主ID
			  $TypeId=$SuB1_Row["TypeId"];  //配件分类ID
			  //$SumAmout=$SuB1_Row["SumAmout"]; //访相同主ID，配件分类相同总金额
			  $SumAmout=$SuB1_Row["AllAmount"];  //发现 S.Amount!=S.Qty*S.Price  ???????????为什么呢
			  echo "应付款: $SumAmout <br>";
			  if($SumAmout>=$djAmount) {//表示，金额可以抵付订金，则不用拆订单
			    $SuB1_djAmount=$djAmount;
			  	$SuB1_PayAmount=$SumAmout-$djAmount;
				$djAmount=0;  //订金清零
				
			  }
			  else{  //分拆订金
				$SuB1_djAmount=$SumAmout;
				$SuB1_PayAmount=0;
				$djAmount=$djAmount-$SuB1_djAmount; //余下的订金
				
			  }
			  
			  
			  if ($SuB1_i==1) {  //表示第一批记录，只有更改PayAmount, djAmount,作为第一个分拆的
			  	$Sql = "UPDATE $DataIn.nonbom12_cwmain SET PayAmount='$SuB1_PayAmount',djAmount='$SuB1_djAmount' WHERE Id='$WId' ";//
				echo " 1. UPDATE $DataIn.nonbom12_cwmain SET PayAmount='$SuB1_PayAmount',djAmount='$SuB1_djAmount' WHERE Id='$WId' <br>";
				$Result = mysql_query($Sql,$link_id);
				if($Result && mysql_affected_rows()>0){
					echo "分拆 $WId 第($SuB1_i)个成功（更新)！<br>";
					}
				else {
					echo "分拆 $WId 第($SuB1_i)个失败（更新)！<br>";					
				}
				$SuB1_i=$SuB1_i+1;
				
				if($SuB1_djAmount>0) { // 第一次，如果使用了订金>0，则要更新分拆的订金.多个订金怎么办？一个个扫描，
					$Dj_Sql="SELECT Id,Did,Amount FROM  $DataIn.nonbom11_djsheet Where  Did='$WId' order by Amount desc ";
					echo "1.1: $Dj_Sql <br>";
					$Dj_Result = mysql_query($Dj_Sql,$link_id);
					if($Dj_Row = mysql_fetch_array($Dj_Result)){
						$SumDj=0;
						$isDjOver=0;
						do{		
						  $Id=$Dj_Row["Id"];  //
						  $Did=$Dj_Row["Did"];  //
						  $Amount=$Dj_Row["Amount"];  //
						  $SumDj=$SumDj+$Amount;
						  echo "使用的订金跟累加订金比（SuB1_djAmount VS SumDj）: $SuB1_djAmount VS $SumDj <br>";
						  if ($SuB1_djAmount>=$SumDj) {
							  if($SuB1_djAmount==$SumDj) {$isDjOver=1;} //刚好，后面的不用再分拆
							   continue;
						  }
						  else { //需要分拆成两条记录，并把余下的全部置为未用,即Estate=9;
						      if($isDjOver==0) {
									$DjA1=$SumDj-$SuB1_djAmount;  //这个要加一条记录.
									$DjA2=$Amount-$DjA1;  //这个要更新掉;
									
									$djSql = "UPDATE $DataIn.nonbom11_djsheet SET Amount='$DjA2' WHERE Id=$Id";
									 echo " >>>>>分拆后更新订金抵付数 $djSql >>>>>>>>>>>> <br>";
									$djResult = mysql_query($djSql,$link_id);
									if($djResult && mysql_affected_rows()>0){
										echo "Id在($Id)的预付订金更新成功。<br>";
									}else {
										echo "Id在($Id)的预付订金更新失败。<br>";
									}
									if($DataIn !== 'ac'){
										$IN_recode="INSERT INTO $DataIn.nonbom11_djsheet
										SELECT NULL,Mid,Did,CompanyId,PurchaseID,'$DjA1',ContractFile,Remark,ReturnReasons,Date,'9',Locks,Operator
										FROM $DataIn.nonbom11_djsheet WHERE Id='$Id' ";  //把Estate=9,表示未提定的订金抵付
									}else{
										$IN_recode="INSERT INTO $DataIn.nonbom11_djsheet
										SELECT NULL,Mid,Did,CompanyId,PurchaseID,'$DjA1',ContractFile,Remark,ReturnReasons,Date,'9',Locks,Operator, 0, '$Operator', NOW(), '$Operator', NOW()
										FROM $DataIn.nonbom11_djsheet WHERE Id='$Id' ";  //把Estate=9,表示未提定的订金抵付
									}
									 echo " >>>>>分拆后插入订金抵付数 $IN_recode >>>>>>>>>>>> <br>";
									$inRes=mysql_query($IN_recode,$link_id);	
									
									$isDjOver=1;
									echo "分拆订金（Id:$Id)：$DjA1，$DjA2：<br>";
									
							  }
							  else { //把未抵完的Estate全部更改为9,以便下个抵付
								   $djSql = "UPDATE $DataIn.nonbom11_djsheet SET Estate='9' WHERE Id=$Id"; 
								   $djResult = mysql_query($djSql,$link_id);
							  }
							  
						  }
						  
						}while ($Dj_Row = mysql_fetch_array($Dj_Result));
					}			
				} //if($djAmount>0) { // 第一次，如果订金>0，则要更新分析的订金.多个订金怎么办？一个个扫描，
				
				continue; //准备下当前while下一条循环	
			  }
			  
			  else { //需要新建的结付主ID，并把它的Mid更新
				  
					//，采购单主ID相同，配件分类相同的抽出来，注意，此时要选的DataIn.nonbom12_cwsheet 表，
					$Ids="";
					$SuB2_Sql="
								SELECT S.Id FROM $DataIn.nonbom12_cwsheet S 
								LEFT JOIN $DataIn.nonbom6_cgsheet  C ON C.Id=S.CgId
								LEFT JOIN $DataIn.nonbom6_cgmain   M ON M.Id=C.Mid
								LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=C.GoodsId
								LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=D.TypeId
								WHERE S.Mid='$WId' AND C.Mid='$OId' AND D.TypeId='$TypeId'
								"; 	
						echo "获取需要生成新的结付主Id的明细记录: $SuB2_Sql <br>";		
						$SuB2_Result = mysql_query($SuB2_Sql,$link_id);
						if($SuB2_Row = mysql_fetch_array($SuB2_Result)){
							do{		
							  $Id=$SuB2_Row["Id"];  //把Id提取出来,好加入到新的主单中
							  $Ids=$Ids==""?$Id:($Ids.",".$Id);
							}while ($SuB2_Row = mysql_fetch_array($SuB2_Result));
						}//采购单主ID相同，配件分类相同的抽出来，注意，此时要选的DataIn.nonbom12_cwsheet 表，
						
					//插入结付主单，并把资料从原单设置	
					if($DataIn !== 'ac'){
						$IN_recode="INSERT INTO $DataIn.nonbom12_cwmain 
						SELECT NULL,BankId,CompanyId,PayDate,'$SuB1_PayAmount','$SuB1_djAmount',Payee,Receipt,Checksheet,Remark,Date,Locks,Operator
						FROM $DataIn.nonbom12_cwmain  WHERE Id='$WId' ";
					}else{
						$IN_recode="INSERT INTO $DataIn.nonbom12_cwmain 
						SELECT NULL,BankId,CompanyId,PayDate,'$SuB1_PayAmount','$SuB1_djAmount',Payee,Receipt,Checksheet,Remark,Date,Locks,Operator,1,0,Operator, NOW(), Operator, NOW()
						FROM $DataIn.nonbom12_cwmain  WHERE Id='$WId' ";
					}
					echo "$IN_recode <br>";
					$inRes=mysql_query($IN_recode,$link_id);
					$Mid=mysql_insert_id();		
					if($inRes && $Mid>0){
						echo "分拆 $WId 第($SuB1_i)个结付成功！新结付主单：$Mid <br>";
						//***************复制结付文件 ********
						
						$ImgDir="../download/cwnonbom/";
						if($Checksheet==1){
							$Checksheet=$ImgDir."C".$WId.".jpg";
							$NChecksheet=$ImgDir."C".$Mid.".jpg";
							copy($Checksheet, $NChecksheet);
							
						}
						if($Payee==1){
							$Payee=$ImgDir."P".$WId.".jpg";		
							$NPayee=$ImgDir."P".$Mid.".jpg";
							copy($Payee, $NPayee);
						}
						
					   if($Receipt==1){
							$Receipt=$ImgDir."R".$WId.".jpg";	
							$NReceipt=$ImgDir."R".$Mid.".jpg";
							copy($Receipt, $NReceipt);
					   }
						//把分拆出来的结付明细单更新的新建的结付主单中
						$Sql = "UPDATE $DataIn.nonbom12_cwsheet SET Mid='$Mid'  WHERE Id in ($Ids) ";//
						echo "更新结付的为新的主结付单($Mid):$Sql <br>";
						$Result = mysql_query($Sql,$link_id);
						if($Result ){
							echo "新结付主单：$Mid 的结付子单ID: $Ids 更新成功<br>";
						}
						else {
							echo "新结付主单：$Mid 的结付子单ID: $Ids 更新失改<br>";
						}						
						
						
						if($SuB1_djAmount>0) { // 如果订金>0，把未抵的，把上次Estate=9的，全部提取出来,要计算则要更新分析的订金.多个订金怎么办？一个个扫描，
							$Dj_Sql="SELECT Id,Did,Amount FROM  $DataIn.nonbom11_djsheet Where  Did='$WId' AND Estate=9 order by Amount desc";
							$Dj_Result = mysql_query($Dj_Sql,$link_id);
							if($Dj_Row = mysql_fetch_array($Dj_Result)){
								$SumDj=0;
								$isDjOver=0;
								do{		
								  $Id=$Dj_Row["Id"];  //
								  $Did=$Dj_Row["Did"];  //
								  $Amount=$Dj_Row["Amount"];  //
								  $SumDj=$SumDj+$Amount;
								   echo "使用的订金跟订金2（SuB1_djAmount VS SumDj）: $SuB1_djAmount VS $SumDj <br>";
								  if ($SuB1_djAmount>=$SumDj) {
									  if($SuB1_djAmount==$SumDj) {$isDjOver=1;} //刚好，后面的不用再分拆
									   $djSql = "UPDATE $DataIn.nonbom11_djsheet SET Did='$Mid',Estate=0 WHERE Id=$Id";  //新的货款结付加入订金,并置成已抵付
									   echo " >>>>>分拆后更新订金抵付数2: $djSql >>>>>>>>>>>> <br> ";
									   $djResult = mysql_query($djSql,$link_id);								   
									   continue;
								  }
								  else { //需要分拆成两条记录，并把余下的全部置为未用,即Estate=9;
									  if($isDjOver==0) {
											$DjA1=$SumDj-$SuB1_djAmount;  //这个要加一条记录.
											$DjA2=$Amount-$DjA1;  //这个要更新掉;
											if($DataIn !== 'ac'){
												$IN_recode="INSERT INTO $DataIn.nonbom11_djsheet
												SELECT NULL,Mid,Did,CompanyId,PurchaseID,'$DjA1',ContractFile,Remark,ReturnReasons,Date,'9',Locks,Operator
												FROM $DataIn.nonbom11_djsheet WHERE Id='$Id' ";  //把Estate=9,表示未提定的订金抵付
											}else{
												$IN_recode="INSERT INTO $DataIn.nonbom11_djsheet
												SELECT NULL,Mid,Did,CompanyId,PurchaseID,'$DjA1',ContractFile,Remark,ReturnReasons,Date,'9',Locks,Operator, 0, Operator, NOW(), Operator, NOW()
												FROM $DataIn.nonbom11_djsheet WHERE Id='$Id' ";  //把Estate=9,表示未提定的订金抵付
											}
											echo ">>>>>>分拆后插入订金抵付数2:$IN_recode; <br>";
											$inRes=mysql_query($IN_recode,$link_id);
											
											//不能跟上面调顺序，Mid，要用新的，而上面insert要用旧的Mid
											$djSql = "UPDATE $DataIn.nonbom11_djsheet SET Amount='$DjA2',Did='$Mid',Estate=0 WHERE Id=$Id";
											$djResult = mysql_query($djSql,$link_id);
											if($djResult && mysql_affected_rows()>0){
												echo "Id在($Id)的预付订金更新成功。<br>";
											}else {
												echo "Id在($Id)的预付订金更新失败。($djSql) <br>";
											}
											$isDjOver=1;
											
									  } // if($isDjOver==0) {

								  }
								  
								}while ($Dj_Row = mysql_fetch_array($Dj_Result));
							}			
						} //if($djAmount>0) { // 第一次，如果订金>0，则要更新分析的订金.多个订金怎么办？一个个扫描，
						
						
					}  //if($inRes && $Mid>0){
					
			  } // if ($SuB1_i==1) {  //表示第一批记录，只有更改PayAmount, djAmount,作为第一个分析的
              
			}while ($SuB1_Row = mysql_fetch_array($SuB1_Result));
		}//把结付主ID相同的，采购单主ID选出来
		
	}while ($myRow = mysql_fetch_array($myResult));
}



?> 