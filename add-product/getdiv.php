<?php

require_once "../classes/Database.php";

// Get the product type from the request
$curType = $_REQUEST["curType"];

// Create the DB connection
$db = new Database();
                
// Get all necessary data from the DB
$allBlockIds = $db->getAllBlockIds();
$allBlockDesc = $db->getAllBlockDesc();
$currentTypeProps = $db->getCurrentTypeProps($curType);
$currentTypePropsIds = $db->getCurrentTypePropsIds($curType);
$currentTypePropsNames = $db->getCurrentTypePropsNames($curType);

// Prepare the dynamic <div> contents for changeType()

$divCont = "<div id='".$allBlockIds[$curType - 1]."'><p id='SpecDesc'>".
    $allBlockDesc[$curType - 1]."</p>";
for ($i=0; $i < count($currentTypeProps); $i++) { 
    $divCont .= "<label for='".$currentTypePropsNames[$i]."'>".
        $currentTypeProps[$i].
        "</label><input type='number' name='".$currentTypePropsNames[$i].
        "'id='".$currentTypePropsIds[$i]."'>";
}
$divCont .= "</div>";

echo $divCont;