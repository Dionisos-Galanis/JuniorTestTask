<?php

require_once "/app/classes/Database.php";
require_once "/app/classes/AddProductForm.php";

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
$specialT = $specialE = '';

// Put new dynamic div
$f = new AddProductForm();
$f->putDynDiv(
    $curType,
    $allBlockIds,
    $allBlockDesc,
    $currentTypeProps,
    $currentTypePropsIds,
    $currentTypePropsNames,
    $specialT,
    $specialE
);