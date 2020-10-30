<?php

function getGuestChartFileName($chartType){
    $fileName = "charts/" . $userType; 
    if(isset($chartType)){
        $fileName.= $chartType;
    }
    $fileName .=".json";
    return $fileName;
}

function getAuthorizedUserChartFileName($chartType){
    return "charts/" . $chartType . ".json";
}

function storeResultingChart($chartType, $fileName, $content){
    file_put_contents($fileName,$content);
}
?>