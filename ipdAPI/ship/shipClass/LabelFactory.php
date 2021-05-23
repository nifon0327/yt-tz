<?php
Class LabelFactory{
    static function CreateLabel($companyId, $size, $DataPublic){

        
        $specificLabel = $companyId."+".$size;
        $parameter = "";
        $labelFinderSql = "SELECT ClientId, Parameters From $DataPublic.labelprintformatter
                           WHERE (ClientId = '$companyId' OR ClientId = '$specificLabel')
                           AND Estate = 1";
        //echo $labelFinderSql.'<br>';
        $labelFinderResult = mysql_query($labelFinderSql);
        while($labelItem = mysql_fetch_assoc($labelFinderResult)){
            if($labelItem['ClientId'] === $specificLabel){
                $parameter = $labelItem['Parameters'];
                break;
            }
            else{
                $parameter = $labelItem['Parameters'];
            }
        }
        
        $labelClass = '';

        //先根据CL的lotto码检索
        $isInLottoListSql = "SELECT * From ch_initallotto WHERE companyId = $companyId";
        //echo $isInLottoListSql.'<br>';
        $isInLottoListResult = mysql_query($isInLottoListSql);
        if(mysql_num_rows($isInLottoListResult)){
            return array('class' => 'CLKindLabel', 'formatter' => $parameter);
        }



        switch($companyId){
            case '1004':
            case '1059':
                $labelClass = 'ClLabel';
                break;
            case '100024':
                $labelClass = 'ArtcomLabel';
                break;
            case '1066':
                $labelClass = 'Mco';
            break;
            case '1097':
            case '100306':
                $labelClass = 'TanLabel';
                break;
            case '100072':
            case '100083':
                $labelClass = 'VITLabel';
                break;
            case '1046':
                $labelClass = 'QTUSD';
                break;
            case '1102':
                $labelClass = 'HoiLabel';
                break;
            case '2397':
                $labelClass = 'CamLabel';
                break;
            case '2588':
            case '100299':
                $labelClass = 'CasLabel';
                break;
            case '100035':
                $labelClass = 'SeaLabel';
                break;
            case '2553':
                $labelClass = 'TexLabel';
                break;
            case '100057':
                $labelClass = 'BfLabel';
                break;
            case '1077':
                $labelClass = 'MLineLable';
                break;
            case '100113':
            case '100298':
            case '100360':
                $labelClass = 'BosLabel';
                break;
            case '1103':
            case '100290':
                $labelClass = 'SunLabel';
                break;
            case '100153':
                $labelClass = 'RobiLabel';
                break;
            case '2459':
            case '100297':
            case '100363':
                $labelClass = 'TimeLabel';
                break;
            case '100109':
                $labelClass = 'BauLabel';
                break;
            case '100148':
            case '100271':
                $labelClass = 'TianLabel';
                break;
            case '2549':
                $labelClass = 'MayLabel';
                break;
            case '1080':
            case '1081':
            case '1064':
                $labelClass = 'MELabel';
                break;
            case '100126':
                $labelClass = 'DcAsiaLabel';
                break;
            case '100105':
                $labelClass = 'ksixLable';
                break;
            case '100139':
                $labelClass = 'hxLabel';
                break;
            case '2580':
                $labelClass = 'YaoLabel';
                break;
            case '1074':
                $labelClass = 'SasLabel';
                break;
            case '1090':
                $labelClass = 'AvenirLabel';
                break;
            case '100145':
                $labelClass = 'RjLabel';
                break;
            case '100181':
                $labelClass = 'LieLabel';
                break;
            case '100087':
            case '1091':
            case '100443':
            case '100446':
            case '100435':
            case '100444':
            case '100447':
            case '100263':
            case '100453':
                $labelClass = 'SkechLabel';
                break;
            case '100185':
                $labelClass = 'InnovHKLabel';
                break;
            case '2642':
            case '100359':
                $labelClass = 'GComLabel';
                break;
            case '100170':
                $labelClass = 'SmartsLabel'; 
                break;
            case '100027':
                $labelClass = 'GearLabel'; 
                break;
            case '2402':
            case '100300':
            case '100364':
                $labelClass = 'GianLabel';
                break;
            case '100236':
                $labelClass = 'HQLabel';
                break;
            case '1098':
                $labelClass = 'RobiLabel';
                break;
            default:
                break;

        }

        return array('class' => $labelClass, 'formatter' => $parameter);
    }
}
?>