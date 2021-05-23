<?php

function extraWeigth($productId){
     $boxSql = "SELECT D.Spec,A.Relation, D.Weight, D.TypeId, C.TypeName   
                   FROM $DataIn.pands A,$DataIn.stuffdata D
                   LEFT JOIN $DataIn.stufftype C ON C.TypeId = D.TypeId 
                   where A.ProductId='$productId' AND (D.TypeId IN ( 9040, 9120, 9057, 9103) or D.StuffCname like '隔板%') and D.StuffId=A.StuffId ORDER BY D.TypeId";        
        $boxResult = mysql_query($boxSql);
        while($boxRow = @mysql_fetch_assoc($boxResult)){
            $typeId = $boxRow["TypeId"];
            $tmpWeight = $boxRow["Weight"];
            $name = $boxRow["TypeName"];
            $relation = explode("/", $boxRow["Relation"]);
            $pcs = ($relation[1] == "")?"0":$relation[1];
            $rpcs = ($relation[0] == "")?"0":$relation[0];
            if($pcs == "0"){
                $extraWeight = "error";
                $erorType .= "*请设置对应关系";
            }
            
            if($typeId == "9040"){
                $boxPcs = $pcs;
                $pcs = 1;
            }
            
            if($extraWeight != "error"){
                if($tmpWeight == "0.00"){
                    $errorWeight = $TypeId;
                    $extraWeight = "error";
                    $erorType .= "*无'$name'重量";
                }
                else if($typeId == "9040"){
                    $extraWeight += $tmpWeight;
                }
                else{
                    if($typeId == '9116'){
                        $extraWeight += ($tmpWeight*$rpcs);
                    }else{
                        $count = ($boxPcs%$pcs==0)?$boxPcs/$pcs:$boxPcs/$pcs+1;
                        $extraWeight += $tmpWeight*$count;
                    }
                }
            }
        }

        return $extraWeight;
}

?>