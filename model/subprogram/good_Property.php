<?php
$Property2=0;$Property3=0;$Property4=0;$Property5=0;
$PropertyResult=mysql_query("SELECT Property FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId='$GoodsId' ORDER BY Property",$link_id);
while($PropertyRow=mysql_fetch_array($PropertyResult)){
       $Property=$PropertyRow["Property"];
        if($Property==2)$Property2=1;
        if($Property==3)$Property3=1;
        if($Property==4)$Property4=1;
        if($Property==5)$Property5=1;   
        if($Property>0)$GoodsName=$GoodsName."<img src='../images/good$Property.gif'  width='18' height='18'>";
     }
?>