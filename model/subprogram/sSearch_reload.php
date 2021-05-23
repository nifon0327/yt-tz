<?php
	
	if($sSearch!=""){
		
		$newsSearch = " and ";
		$sSearchArray = explode("and", $sSearch);
		
		for($i=1;$i<count($sSearchArray);$i++){
			
				$tempArray = explode("=", $sSearchArray[$i]);
				
				if(strpos($tempArray[0], "Number")){
					
					$newsSearch .= $tempArray[0]." IN (" .$tempArray[1].")";
					
				}else{
					$newsSearch .= $tempArray[0]." =" .$tempArray[1];
				}

		}
	}
	
	
?>