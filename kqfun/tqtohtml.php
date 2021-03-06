<?php 
class Shtml
    {
        var $Templet;
        var $DataSource;
        var $Dir;
        var $fileName;
        var $mod;
        var $handle;

        function Shtml($fileName="")
        {
            $this->fileName=$fileName;
            $this->mod="wb";
            $this->handle=false;

            $this->Templet        = "";
            $this->DataSource    = array();
            $this->Dir            = "";
        }
        
        ///    <描述>
        ///    绑定数据源，参数为一数组。
        ///    </描述>
        function BindData($arr)
        {
            $this->DataSource = $arr;
        }
        
        ///    <描述>
        ///    设置文件存放路径。
        ///    </描述>
        function SetDir($dir)
        {
            $this->Dir = $dir;
        }
        function SetFileName($fileName)
        {
            return $this->fileName=$fileName;
        }

        function GetMod()
        {
            return $this->mod;
        }
        function SetMod($mod)
        {
            return $this->mod=$mod;
        }
        function Open()
        {
            if(substr($this->fileName,0,1)=="/")
                $this->fileName = $_SERVER['DOCUMENT_ROOT'] . $this->fileName;
            if($this->handle=fopen($this->fileName, $this->mod))
                return $this->handle;
            else
                return false;
        }
        function Close()
        {
            return fclose($this->handle);
        }
        function Write($content)
        {
            return fwrite($this->handle,$content);
        }
        function MkDir($pathname)
        {
            $currentPath="";
            str_replace("\\","/",$pathname);
            $pathArr = split("/",$pathname);
            if($pathArr[0] == "")        //使用绝对路径
            {
                $currentPath = $_SERVER['DOCUMENT_ROOT'];
            }
            else
            {
                $currentPath = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']);
            }
            for($i=0; $i<count($pathArr); $i++)
            {
                if($pathArr[$i]=="")
                    continue;
                else
                    if(is_dir($currentPath . "/" . $pathArr[$i]))
                        $currentPath = $currentPath . "/" . $pathArr[$i];
                    else
                        mkdir($currentPath = $currentPath . "/" . $pathArr[$i]);
            }
        }

        ///    <描述>
        ///    生成静态文件。
        ///    </描述>
        function Create()
        {
            $tmp = $this->Templet;
            foreach($this->DataSource as $key=>$value)
            {
                $tmp = str_replace("<FIELD_" . $key . ">", $value, $tmp);
            }
            $this->MkDir(dirname($this->fileName));
            $this->Open();
            $this->Write($tmp);
            $this->Close();
        }
    }

    function CreateShtml()
    {
        ob_start("callback_CteateShtml");
    }
    function callback_CteateShtml($buffer)
    {
        //加入文件名处理,读取session预存值
		$page = intval(@$_REQUEST["page"]);
		$fname=date("Ymd");
        $shtml = new Shtml();
        $shtml->SetFileName($_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . "/kqfun/" .$fname. ".htm");
        $shtml->Templet = $buffer;
        $shtml->Create();
		
        return $buffer;
    }
?>
