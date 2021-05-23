<?php
	
		$boxPcs = "";
	    $extraWeight = "0";	
	    $errorWeight = "";
	    $erorType = "";
	    $boxSql = "SELECT D.Spec,A.Relation, D.Weight, D.TypeId, C.TypeName   
				   FROM $DataIn.pands A,$DataIn.stuffdata D
				   LEFT JOIN $DataIn.stufftype C ON C.TypeId = D.TypeId 
				   WHERE A.ProductId='$productId' AND D.TypeId IN ( 9040, 9120, 9057, 9103)  
				   and D.StuffId=A.StuffId ORDER BY D.TypeId";	
				
		$boxResult = mysql_query($boxSql);
		while($boxRow = @mysql_fetch_assoc($boxResult)){
			$typeId = $boxRow["TypeId"];
			$tmpWeight = $boxRow["Weight"];
			$name = $boxRow["TypeName"];

			$relation = explode("/", $boxRow["Relation"]);
			
			$pcs = ($relation[1] == "")?"0":$relation[1];
		
			if($pcs == "0")
			{
				$extraWeight = "error";
				$erorType .= "*请设置对应关系";
			}
			
			if($typeId == "9040")
			{
				$boxPcs = $pcs;
				$pcs = 1;
			}
			
			
			if($extraWeight != "error")
			{
				if($tmpWeight == "0.00")
				{
					$errorWeight = $TypeId;
					$extraWeight = "error";
					$erorType .= "*无'$name'重量";
				}
				else if($typeId == "9040")
				{
					$extraWeight += $tmpWeight;
				}
				else
				{
					$count = ($boxPcs%$pcs==0)?$boxPcs/$pcs:$boxPcs/$pcs+1;
					$extraWeight += $tmpWeight*$count;
				}
			}
		}
		if($boxPcs==""){ //成品
		
		      $stuffRow = mysql_fetch_array(mysql_query("SELECT D.BoxPcs  
				   FROM $DataIn.pands A
				   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				   WHERE A.ProductId='$productId' AND D.BoxPcs>0 LIMIT 1",$link_id));	
		     $boxPcs = $stuffRow["BoxPcs"];
			 
		}
?>