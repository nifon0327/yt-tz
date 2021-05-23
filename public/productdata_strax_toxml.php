<?php   
 $sheetResult = mysql_query("SELECT P.ProductId,P.eCode,P.Price,P.Code,P.Weight,P.Description,C.Forshort,P.MainWeight,P.productsize
					          FROM $DataIn.productdata P 
					          LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
						      WHERE P.CompanyId='$CompanyId' AND P.Estate=1",$link_id);  			      
  $mainArray=array(); $InnerArray=array(); $OutterArray=array(); 
   if($sheetRows = mysql_fetch_array($sheetResult)){
		     $i=1;
		    do{
		        $ProductId=$sheetRows["ProductId"];
				$eCode=$sheetRows["eCode"];
				$Code=$sheetRows["Code"];
				$CodeArray=explode("|", $Code);
                $Description=$sheetRows["Description"];
                $MainWeight=$sheetRows["MainWeight"];
                $Weight=$sheetRows["Weight"];
				$CodeSTR=count($CodeArray)==2?$CodeArray[1]:$CodeArray[0];
                $productsize=$sheetRows["productsize"];
               
                //读取外箱条码:
                $OuterCarton="";
                $pandResult=mysql_query("SELECT D.StuffCname FROM $DataIn.pands P 
			                LEFT JOIN stuffdata D ON D.StuffId=P.StuffId 
			                WHERE P.ProductId=95401 AND D.TypeId=9124 AND D.StuffCname Like '条码(外箱)Strax-%'",$link_id);  	
                if($pandRows = mysql_fetch_array($pandResult)){
                      $StuffCname=$pandRows['StuffCname'];
                      $CartonArray=explode("-", $StuffCname);
                      $OuterCarton=count($CartonArray)==2?$CartonArray[1]:"";
                }
                
                $productsize=explode("cm",$productsize);
                $tempsize=str_replace( 'x', '*',$productsize[0]);
                 $SizeArray=explode("*", $tempsize);
				$mainArray[]=array(
		                        'StraxItemNo'=>$eCode,
		                        'EAN'=>$CodeSTR,
		                        'UPC'=>"",
		                        'Description'=>$Description,
                                'VendorItemNo'=>"",
                                'NetWeight'=>$MainWeight,
                                'GrossWeight'=>$Weight,
                                'WidthInCentimeter'=>$SizeArray[0],
                                'HeightInCentimeter'=>$SizeArray[1],
                                'DepthInCentimeter'=>$SizeArray[2],
                                'InnerCarton'=>"",
                                'OuterCarton'=>""
		                        );
            //内箱数据
            $InnerArray[]=array(
		                        'UPC'=>"",
		                        'Qty'=>0,
		                        'GrossWeight'=>0,
		                        'HeightInCentimeter'=>0,
		                        'WidthInCentimeter'=>0,
		                        'DepthInCentimeter'=>0
		                        );
        $RelationResult=mysql_fetch_array(mysql_query("SELECT P.Relation,S.Spec,S.Weight  FROM $DataIn.pands P 
         LEFT JOIN $DataIn.stuffdata S ON S.StuffId=P.StuffId WHERE P.ProductId=$ProductId AND S.TypeId=9040",$link_id));
         $Relation=$RelationResult["Relation"];
         $RelationArray=explode("/", $Relation);
         $OutQty=$RelationArray[1]==""?$RelationArray[0]:$RelationArray[1];
        $OutWeight=$RelationResult["Weight"];
        $OutSpec=$RelationResult["Spec"];
         $OutSpec=explode("CM",$OutSpec);
          $tempSpec=str_replace( '×', '*',$OutSpec[0]);
          $SpecArray=explode("*", $tempSpec);
            //外箱数据
            $OutterArray[]=array(
		                        'UPC'=>"",
		                        'Qty'=>$OutQty,
		                        'GrossWeight'=>$OutWeight,
		                        'HeightInCentimeter'=>$SpecArray[0],
		                        'WidthInCentimeter'=>$SpecArray[1],
		                        'DepthInCentimeter'=>$SpecArray[2],
		                        'EAN'=>"$OuterCarton"
		                        );
		                $i++;
        }while($sheetRows = mysql_fetch_array($sheetResult)); 

  	     //生成XML文件 
  	    $xmlDoc = new DOMDocument('1.0', 'utf-8');
 	     $xmlDoc->formatOutput = true;
  
	      $r = $xmlDoc->createElement('ARTICLES');
	      $xmlDoc->appendChild( $r );
          $SupplierID = $xmlDoc->createElement('SupplierID');
          $SupplierID->appendChild($xmlDoc->createTextNode('V71381'));
   	       $r->appendChild( $SupplierID );

          $SupplierName = $xmlDoc->createElement('SupplierName');
          $SupplierName->appendChild($xmlDoc->createTextNode('AshCloud'));
   	       $r->appendChild( $SupplierName );
           $tempk=0;
           foreach($mainArray as $maindata ){
     	      $Item = $xmlDoc->createElement('ITEM');
    	      while(list($key,$value)=each($maindata)){ 
     	           $show1=$xmlDoc->createElement($key);
                   if($key=='InnerCarton'){
                             	      while(list($key2,$value2)=each($InnerArray[$tempk])){ 
     	                                     $show2=$xmlDoc->createElement($key2);
                                             $show2->appendChild($xmlDoc->createTextNode($value2));
    	                                    $show1->appendChild($show2);
                                         }     
                      }
                   if($key=='OuterCarton'){
                             	      while(list($key3,$value3)=each($OutterArray[$tempk])){ 
     	                                     $show3=$xmlDoc->createElement($key3);
                                             $show3->appendChild($xmlDoc->createTextNode($value3));
    	                                    $show1->appendChild($show3);
                                         }     
                      }
                   $show1->appendChild($xmlDoc->createTextNode($value));
    	           $Item->appendChild($show1);
                 }
   	         $r->appendChild( $Item );
            $tempk++;
 	     }

  	      $FilePath="../client/strax";
   	     if(!file_exists($FilePath)) makedir($FilePath);
    
    	    $wFile=$FilePath . "/Productdata.xml";
    	    $xmlDoc->save($wFile);
   	       if($xmlDoc){
       	      $Log.="<br>生成" . $CompanyId . "的产品xml文件成功！";
      	       }
    	     else{
         	    $Log.="<br><div class='redB'>生成" . $CompanyId . "的产品xml文件失败！</div>" ;
    	     }
  	       $xmlDoc=null;
   }
?>
