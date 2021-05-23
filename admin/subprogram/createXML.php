<?php   
 //Example:
 /* 
  $books = array();
  $books [] = array('title' => 'PHP Hacks','author' => 'Jack Herrington','publisher' => "O'Reilly");
  $books [] = array('title' => 'Podcasting Hacks','author' => 'Jack Herrington','publisher' => "O'Reilly");
  createXML($books,'books|book','../download/cgxml/test.xml');//创建XML文件
  $arrdata=readXML('../download/cgxml/test.xml');//读取XML文件
  print_r($arrdata); 
*/

function createXML($datas,$Elments,$xmlFile){
  $elments=explode("|",$Elments);
  $xmlDoc = new DOMDocument();
  $xmlDoc->formatOutput = true;
  
  $r = $xmlDoc->createElement($elments[0]);
  $xmlDoc->appendChild( $r );
  
  foreach( $datas as $data )
  {
     $b = $xmlDoc->createElement($elments[1]);
      while(list($key,$value)=each($data)){ 
        $c=$xmlDoc->createElement($key);
        $c->appendChild($xmlDoc->createTextNode($value));
        $b->appendChild($c);
      }
     $r->appendChild( $b );
  }
  $xmlDoc->save($xmlFile);
  //if ($xmlDoc=="") echo "生成XML文档失败！";
  return $xmlDoc;
}

function readXML($xmlFile,$wFile){
    $xmlDoc = new DOMDocument();  
    $xmlDoc->load($xmlFile);  
    $root = $xmlDoc->documentElement;  
    $arr=array();
    
    foreach ($root->childNodes as $item)  
    {  
        if($item->hasChildNodes()){  
           $tmp=array();  
           foreach($item->childNodes as $one){  
               if(!empty($one->tagName)){  
                  $tmp[$one->tagName]=$one->nodeValue;  
               }  
            }  
         $arr[]=$tmp; 
       }  
    } 
    if ($wFile!="") $xmlDoc->save($wFile);
   return $arr; 
}
?>