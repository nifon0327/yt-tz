<?php
if (!isset($ipadTag)) $ipadTag = "no";
if ($ipadTag != "yes") {
    include "../basic/chksession.php";
}
include "../basic/parameter.inc";
include "../basic/config.inc";
?>
<!doctype html>

<html>

<head>

    <meta charset="utf-8">

    <title>出货照片</title>

    <script src="../plugins/unslider/jquery-1.11.1.min.js"></script>

    <script src="../plugins/unslider/unslider.min.js"></script>

    <style>

        html, body { font-family: Segoe, "Segoe UI", "DejaVu Sans", "Trebuchet MS", Verdana, sans-serif;}

        ul, ol { padding: 0;}



        .banner { position: relative; overflow: auto; text-align: center;}

        .banner li { list-style: none; }

        .banner ul li { float: left; }

    </style>

</head>



<body>

<style>

    #b06 { width: 640px;}

    #b06 .dots { position: absolute; left: 0; right: 0; bottom: 20px;}

    #b06 .dots li

    {

        display: inline-block;

        width: 10px;

        height: 10px;

        margin: 0 4px;

        text-indent: -999em;

        border: 2px solid #fff;

        border-radius: 6px;

        cursor: pointer;

        opacity: .4;

        -webkit-transition: background .5s, opacity .5s;

        -moz-transition: background .5s, opacity .5s;

        transition: background .5s, opacity .5s;

    }

    #b06 .dots li.active

    {

        background: #fff;

        opacity: 1;

    }

    #b06 .arrow { position: absolute; top: 200px;}

    #b06 #al { left: 15px;}

    #b06 #ar { right: 15px;}

</style>



<div class="banner" id="b06">

    <ul>


        <?php
        $ImageUrlResult = mysql_query("SELECT ImageUrl FROM ch1_shipmain WHERE Id = $Id",$link_id);
        if ($myRow = mysql_fetch_array($ImageUrlResult)) {
            $ImageUrlRow = $myRow["ImageUrl"];
            $ImageUrls = explode(";", str_replace('../','/',$ImageUrlRow));
            $ImageUrlCount = count($ImageUrls);
            for ($i=0;$i<$ImageUrlCount;$i++) {
                echo "<li><img class='sliderimg' src=http://".$_SERVER['HTTP_HOST']."/weixin/chuhuoconfirm".$ImageUrls[$i]." alt=".$ImageUrls[$i]." width='80%'></li>";

            }


        }
        ?>


    </ul>

    <a href="javascript:void(0);" class="unslider-arrow06 prev"><img class="arrow" id="al" src="../plugins/unslider/arrow.png" alt="prev" width="50%" height="100%"></a>

    <a href="javascript:void(0);" class="unslider-arrow06 next"><img class="arrow" id="ar" src="../plugins/unslider/arrow.png" alt="next" width="50%" height="100%"></a>

</div>



<script>

    function imgReload()

    {

        var imgHeight = 0;

        var wtmp = $("body").width();

        $("#b06 ul li").each(function(){

            $(this).css({width:wtmp + "px"});

        });

        $(".sliderimg").each(function(){

            $(this).css({width:wtmp + "px"});

            imgHeight = $(this).height();

        });

    }



    $(window).resize(function(){imgReload();});



    $(document).ready(function(e) {

        imgReload();

        var unslider06 = $('#b06').unslider({

                dots: true,

                fluid: true

            }),

            data06 = unslider06.data('unslider');



        $('.unslider-arrow06').click(function() {

            var fn = this.className.split(' ')[1];

            data06[fn]();

        });

    });

</script>



