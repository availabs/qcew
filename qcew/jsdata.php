<?php
    error_reporting(0);
    // QCEW Open Data Access JavaScript Example
    //
    // The JavaScript functions called from qcew.js 
    // reference this php file. This php file sits on 
    // the server and handles csv file requests.
    // the content of the CSV file is returned in as
    // a text blob. This text blob is stored within 
    // the "Qcew" global namespace within the browser.

    $year=$_GET["year"];
    $qtr=$_GET["qtr"];
    $industry=$_GET["industry"];
    $area=$_GET["area"];
    $size=$_GET["size"];
    $res = "";
    $rootUrl = "http://www.bls.gov/cew/data/api/";
    $url = "";
    

    if ( strlen($year) != 4) {
        echo "Invalid Year";
    }
    
    if ( strlen($qtr) > 0) {
        if( (strlen($qtr) > 1 ) or (!preg_match('/^[1-4a]/',$qtr)) ){
            echo "ERROR: Invalid quarter";
            return;
        }        
    } 
    
    if ( strlen($industry) > 0) {
        if ((strlen($industry) > 6) or (!preg_match('/^[0-9_]/', $industry)) ) {
            echo "ERROR: Invalid industry";
            return;
        }
    } 
    
    if (strlen($area) > 0) {
        if ( (strlen($area) > 5) or (!ctype_alnum($area)) ) {
            echo "ERROR: Invalid area";
            return;
        }
    }
    
    if (strlen($size) > 0) { 
        if( (strlen($size) > 1) or (!ctype_digit($size)) ) {
            echo "ERROR: Invalid size";
            return;
        }
    } 
    
    if (strlen($area) > 0 and strlen($industry) > 0) {
        echo "ERROR: Invalid input.\n";
        echo "Please provide one of the following combinations:\n";
        echo "year, qtr, industry\n";
        echo "year, qtr, area\n";
        echo "-OR-\n";
        echo "year, size\n";
        return;
    }
    
    if (strlen($size)>0 and (strlen($qtr)>0 or strlen($area)>0 or strlen($industry)>0) ) {
        echo "ERROR: Invalid input.\n";
        echo "Please provide one of the following combinations:\n";
        echo "year, qtr, industry\n";
        echo "year, qtr, area\n";
        echo "-OR-\n";
        echo "year, size\n";
        return;
    } 
    
    if (strlen($area) > 0 ) {
        // NOTE: For a complete list of area codes and titles see
        // http://www.bls.gov/cew/doc/titles/size/size_titles.htm
        $url = $rootUrl . $year . "/" . $qtr . "/area/" . $area . ".csv";
        $res = file_get_contents($url);
    } elseif (strlen($industry) > 0) {
        // NOTE: Some industry codes contain hyphens. The csv files use underbars
        // instead of hyphens, so we replace any hyphens ("-") with underbars ("_")
        // for a complete list of industry codes and titles see
        // http://www.bls.gov/cew/doc/titles/area/area_titles.htm
        $industry = str_replace("-","_",$industry);
        $url = $rootUrl . $year . "/" . $qtr . "/industry/" . $industry . ".csv";
        $res = file_get_contents($url);        
    } elseif (strlen($size) > 0 ) {
        // NOTE: Size is only available for the first quarter of each year.
        // for a complete list of size codes and titles see
        // http://www.bls.gov/cew/doc/titles/size/size_titles.htm
        $url = $rootUrl . $year . "/1/size/" . $size . ".csv";
        $res = file_get_contents($url);        
    }
   
    //escape new-line characters
    $csvText = $res;
    $csvText = str_replace("\n","\\n",$csvText);
    $csvText = str_replace("\r","\\r",$csvText);
    $json = "var Qcew = Qcew || {};\n";
    $json .= "Qcew[\"" . $year . "\"] = Qcew[\"" . $year . "\"] || {};\n";
    $json .= "Qcew[\"" . $year . "\"][\"" . $qtr . "\"] = Qcew[\"" . $year . "\"][\"" . $qtr . "\"] || {};\n";
    if ( strlen($industry) > 0) {
        $json .= "Qcew[\"" . $year . "\"][\"" . $qtr . "\"][\"industry\"] = Qcew[\"" . $year . "\"][\"" . $qtr . "\"][\"industry\"] || {};\n";
        $json .= "Qcew[\"" . $year . "\"][\"" . $qtr . "\"][\"industry\"][" . $industry . "] = { \"csvText\":'" . $csvText . "' };";
    } elseif ( strlen($area) > 0 ) {
        $json .= "Qcew[\"" . $year . "\"][\"" . $qtr . "\"][\"area\"] = Qcew[\"" . $year . "\"][\"" . $qtr . "\"][\"area\"] || {};\n";
        $json .= "Qcew[\"" . $year . "\"][\"" . $qtr . "\"][\"area\"][" . $area . "] = {  \"csvText\":'" . $csvText . "' };";
    } elseif ( strlen($size) > 0 ) {
        //NOTE: Size data only available in first quarter 2013.
        $json .= "Qcew[\"" . $year . "\"][\"1\"][\"size\"] = Qcew[\"" . $year . "\"][\"1\"][\"size\"] || {};\n";
        $json .= "Qcew[\"" . $year . "\"][\"1\"][\"size\"][" . $size . "] = {  \"csvText\":'" . $csvText . "' };";
    }

    echo $json;
?>