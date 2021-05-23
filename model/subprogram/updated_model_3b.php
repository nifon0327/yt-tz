<?php 
//二合一已更新
//默认
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		if($KillOnline==1){
			include "killonline_model.php";
			}
		include "updated_model_3a.php";		
		$x++;
		}
	}	
?>