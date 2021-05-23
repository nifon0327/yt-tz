<?php 
function check_remote_file_exists($url) 
{ 
	$curl = curl_init($url); 
	// 不取回数据 
	curl_setopt($curl, CURLOPT_NOBODY, true); 
	// 发送请求 
	$result = curl_exec($curl); 
	$found = false; 
	// 如果请求没有发送失败 
	if ($result !== false) { 
		// 再检查http响应码是否为200 
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
		if ($statusCode == 200) { 
			$found = true; 
		} 
	} 
	curl_close($curl); 
	return $found; 
}


function get_url_Image($url,$save_dir='',$filename='',$type=0){
	// echo "$url,$save_dir, $filename";
	if (check_remote_file_exists($url)==false){
		return array('file_name'=>'No exist the file in remote','save_path'=>'','error'=>4);
	}
	
    if(trim($url)==''){
		return array('file_name'=>'','save_path'=>'','error'=>1);
	}
	if(trim($save_dir)==''){
		$save_dir='./';
	}
    if(trim($filename)==''){//保存文件名
        $ext=strrchr($url,'.');
        if($ext!='.gif'&&$ext!='.jpg'){
			return array('file_name'=>'','save_path'=>'','error'=>3);
		}
        $filename=time().$ext;
    }
	if(0!==strrpos($save_dir,'/')){
		$save_dir.='/';
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return array('file_name'=>'','save_path'=>'','error'=>5);
	}
    //获取远程文件所采用的方法 
    if($type){
		$ch=curl_init();
		$timeout=5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$img=curl_exec($ch);
		curl_close($ch);
    }else{
	    ob_start(); 
	    readfile($url);
	    $img=ob_get_contents(); 
	    ob_end_clean(); 
    }
    //$size=strlen($img);
    //文件大小 
	unlink($save_dir.$filename); 
	
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$img);
    fclose($fp2);
	unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}


?>