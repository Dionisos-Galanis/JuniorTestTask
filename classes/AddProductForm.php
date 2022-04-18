<?php


class AddProductForm
{
    private const FORM_ID = "#product_form";
    private const MAIN_ELEMENTS_TYPE = array("text", "text", "number");
    private const MAIN_ELEMENTS_NAME = array("sku", "name", "price");
    private const MAIN_ELEMENTS_LABEL = array("SKU", "Name", "Price ($)");
    private const MAIN_ELEMENTS_ID = array("sku", "name", "price");
    private const MAIN_ELEMENTS_CHECK_FUNC = array("checkSku", "checkName", "checkPrice");
    private const TYPE_SWITCHER_ID = "productType";
    private const TYPE_SWITCHER_NAME = "productType";
    private const SPECIAL_CHECK_FUNC = "checkSpecial";
    private const ACTION = "add-product/FormProcess.php";
    
    
    public function buildForm()
    {
       
    // JavaScript for Type Switcher
    echo '<script>
        function typeChange() {
            const switcher = document.getElementById("'.$this::TYPE_SWITCHER_ID.'");
            const curDiv = document.getElementById("dynamicDiv");
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                curDiv.innerHTML = this.responseText;
            }
            idType = switcher.options[switcher.selectedIndex].value;
            xmlhttp.open("GET", "getdiv.php?curType=" + idType);
            xmlhttp.send();
        }
        </script>';


        // Opening tag
        echo '<form method="POST" action="'.$this::ACTION.'">';

        // div for the main parameters and tup switcher - the static div
        echo '<div id="staticPart">';

        // Inputs for main parameters
        for ($i=0; $i < count($this::MAIN_ELEMENTS_NAME); $i++) { 
            echo '<label for="'.$this::MAIN_ELEMENTS_NAME[$i].'">'.
                $this::MAIN_ELEMENTS_LABEL[$i].'</label>'.
                '<input type="'.$this::MAIN_ELEMENTS_TYPE[$i].'" 
                name="'.$this::MAIN_ELEMENTS_NAME[$i].'" 
                id="'.$this::MAIN_ELEMENTS_ID[$i].'">';
        }

        // Create the DB connection
        $db = new Database();
                
        // Get all necessary data from the DB
        $curType = 1;
        $productTypes = $db->getAllProductTypes();
        $allBlockIds = $db->getAllBlockIds();
        $allBlockDesc = $db->getAllBlockDesc();
        $currentTypeProps = $db->getCurrentTypeProps($curType);
        $currentTypePropsIds = $db->getCurrentTypePropsIds($curType);
        $currentTypePropsNames = $db->getCurrentTypePropsNames($curType);
 
        // Product type switcher
        echo '<select name="'.$this::TYPE_SWITCHER_NAME.
            '"id="'.$this::TYPE_SWITCHER_ID.'" onchange="typeChange()">';
        for ($i=1; $i < count($productTypes) + 1; $i++) {
            echo '<option value="'.$i.'">'.$productTypes[$i-1].'</option>';
        }
        echo '</select>';

        // Closing the "static" div
        echo '</div>';

        // Dynamic div - with special parameters - for the default product type
        echo '<div id="dynamicDiv">
                <div id="'.$allBlockIds[$curType - 1].'">';
                    for ($i=0; $i < count($currentTypeProps); $i++) { 
                        echo '<label for="'.$currentTypePropsNames[$i].'">'.
                            $currentTypeProps[$i].'</label> 
                            <input type="number" name="'.$currentTypePropsNames[$i].'" 
                            id="'.$currentTypePropsIds[$i].'">';
                    }
        echo '  </div>
            </div>
        ';

        // Save button
        echo '<input type="submit" name="save" id="save" value="Save">';
            
        // Closing form tag
        echo '</form>';

        // Cancel button
        echo '<a href="'.ManageUrls::constructUrl().'">
            <button name="cancel" id="cancel">Cancel</button></a>';
    }


    public function preCleanData($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

// $a = new AddProductForm();
// $a->buildForm();