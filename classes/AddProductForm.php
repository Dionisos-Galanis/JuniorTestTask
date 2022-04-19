<?php


class AddProductForm
{
    private const FORM_ID = "#product_form";
    private const MAIN_ELEMENTS_TYPE = array("text", "text", "number");
    private const MAIN_ELEMENTS_NAME = array("sku", "name", "price");
    private const MAIN_ELEMENTS_LABEL = array("SKU: ", "Name: ", "Price ($): ");
    private const MAIN_ELEMENTS_ID = array("sku", "name", "price");
    private const MAIN_ELEMENTS_CHECK_FUNC = array("checkSku", "checkName", "checkPrice");
    private const MAIN_ELEMENTS_SET_FUNC = array("setSku", "setName", "setPrice");
    private const TYPE_SWITCHER_ID = "productType";
    private const TYPE_SWITCHER_NAME = "productType";
    private const SPECIAL_CHECK_FUNC = "checkSpecial";
    private const SPECIAL_SET_FUNC = "setSpecial";
    private const ACTION = "index.php";
    private const ERR_MSG_FIELD_REQ = "Please, submit required data";
    private const ERR_MSG_INVALID_VAL = "Please, provide the data of indicated type";

    public $mainData;
    public $specialData;
    public $mainElementsE;
    public $specialE;
    public $curType;
    
    
    public function processForm()
    {
        // Result: True - ok / False - have to correct
        $result = True;

        // Main inputs
        for ($i=0; $i < count($this::MAIN_ELEMENTS_NAME); $i++) {
            if (empty($_POST[$this::MAIN_ELEMENTS_NAME[$i]])) {
                $mainElementsE[$i] = $this::ERR_MSG_FIELD_REQ;
                $result = False;
            } else {
                $preData = $this->preCleanData($_POST[$this::MAIN_ELEMENTS_NAME[$i]]);
                $func = 'Product::'.$this::MAIN_ELEMENTS_CHECK_FUNC[$i];
                $res = $func($preData);
                if ($res) {
                    $mainData[$i] = $preData;
                } else {
                    $mainData[$i] = '';
                    $mainElementsE[$i] = $this::ERR_MSG_INVALID_VAL;
                    $result = False;
                }
            }
        }

        // Special inputs

        // Get product type
        $curType = $_POST[$this::TYPE_SWITCHER_NAME];

        // Get list of the special parameters from DB
        $db = new Database();
        $currentTypePropsNames = $db->getCurrentTypePropsNames($curType);

        // Get and process the special inputs
        for ($i=0; $i < count($currentTypePropsNames); $i++) {
            if (empty($_POST[$currentTypePropsNames[$i]])) {
                $specialE[$i] = $this::ERR_MSG_FIELD_REQ;
                $result = False;
            } else {
                $preData = $this->preCleanData($_POST[$currentTypePropsNames[$i]]);
                $func = 'Product::'.$this::SPECIAL_CHECK_FUNC;
                $res = $func($preData);
                if ($res) {
                    $specialData[$i] = $preData;
                } else {
                    $specialData[$i] = '';
                    $specialE[$i] = $this::ERR_MSG_INVALID_VAL;
                    $result = False;
                }
            }
        }

        // Output processed data and errors to the object properties
        $this->setMainData($mainData);
        $this->setSpecialData($specialData);
        $this->setMainElementsE($mainElementsE);
        $this->setSpecialE($specialE);
        $this->setCurType($curType);

        // If result is ok then instantiate a new product and save it to the DB
        if ($result) {
            $product = new Product();
            // Initialaze with main data
            for ($i=0; $i<count($mainData); $i++) {
                $func = '$product->'.$this::MAIN_ELEMENTS_SET_FUNC[$i];
                $func($mainData[$i]);
            }
            // Initialaze with the special data
            $func = '$product->'.$this::SPECIAL_SET_FUNC;
            $func($specialData);

            // Save the initialized product to DB
           $product->addProductToDb();
        }

        // Return
        return $result;
    }


    /**
     * Function echoes the product add form with dynamic product-specific part
     */
    public function buildForm($mainElementsT, $curType, $specialT, $mainElementsE, $specialE)
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

        // div for the main parameters and type switcher - the static div
        echo '<div id="staticPart">';

        // Inputs for main parameters
        for ($i=0; $i < count($this::MAIN_ELEMENTS_NAME); $i++) { 
            echo '<div class="InputContainer"><label for="'.
                $this::MAIN_ELEMENTS_NAME[$i].'" class="Lab">'.
                $this::MAIN_ELEMENTS_LABEL[$i].'</label>'.
                '<input type="'.$this::MAIN_ELEMENTS_TYPE[$i].'" 
                name="'.$this::MAIN_ELEMENTS_NAME[$i].'" 
                id="'.$this::MAIN_ELEMENTS_ID[$i].
                '" value="'.$mainElementsT[$i].'"><span> * </span><span class="ErrMsg">'.
                $mainElementsE[$i].'</span></div>';
        }

        // Create the DB connection
        $db = new Database();
                
        // Get all necessary data from the DB
        $productTypes = $db->getAllProductTypes();
        $allBlockIds = $db->getAllBlockIds();
        $allBlockDesc = $db->getAllBlockDesc();
        $currentTypeProps = $db->getCurrentTypeProps($curType);
        $currentTypePropsIds = $db->getCurrentTypePropsIds($curType);
        $currentTypePropsNames = $db->getCurrentTypePropsNames($curType);
 
        // Product type switcher
        echo '<div class="InputContainer"><label class="Lab" for="'.
            $this::TYPE_SWITCHER_NAME.'">Choose type: </label>'.
            '<select name="'.$this::TYPE_SWITCHER_NAME.
            '"id="'.$this::TYPE_SWITCHER_ID.'" onchange="typeChange()">';
        for ($i=1; $i < count($productTypes) + 1; $i++) {
            echo '<option value="'.$i.'"';
            if ($i == $curType) {
                echo ' selected';
            }
            echo '>'.$productTypes[$i-1].'</option>';
        }
        echo '</select></div>';

        // Closing the "static" div
        echo '</div>';

        // Dynamic div - with special parameters - for the default product type
        echo '<div id="dynamicDiv">';
        $this->putDynDiv(
            $curType,
            $allBlockIds,
            $allBlockDesc,
            $currentTypeProps,
            $currentTypePropsIds,
            $currentTypePropsNames,
            $specialT,
            $specialE
        );
        echo '</div>';

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


    public function putDynDiv(
        $curType,
        $allBlockIds,
        $allBlockDesc,
        $currentTypeProps,
        $currentTypePropsIds,
        $currentTypePropsNames,
        $specialT,
        $specialE
        )
    {
        // Prepare the dynamic <div> contents for changeType()
        $divCont = '<div id="'.$allBlockIds[$curType - 1].'"><p id="SpecDesc">'.
            $allBlockDesc[$curType - 1].'</p>';
        for ($i=0; $i < count($currentTypeProps); $i++) { 
            $divCont .= '<div class="InputContainer"><label for="'.
                $currentTypePropsNames[$i].'" class="Lab">'.
                $currentTypeProps[$i].
                '</label><input type="number" name="'.$currentTypePropsNames[$i].
                '"id="'.$currentTypePropsIds[$i].'" value="'.
                $specialT[$i].'"><span> * </span><span class="ErrMsg">'.
                $specialE[$i].'</div>';
        }
        $divCont .= '</div>';

        // Putting the prepared <div>
        echo $divCont;
    }

    /**
     * Get the value of mainData
     */
    public function getMainData()
    {
        return $this->mainData;
    }

    /**
     * Set the value of mainData
     */
    private function setMainData($mainData): self
    {
        $this->mainData = $mainData;

        return $this;
    }

    /**
     * Get the value of specialData
     */
    public function getSpecialData()
    {
        return $this->specialData;
    }

    /**
     * Set the value of specialData
     */
    private function setSpecialData($specialData): self
    {
        $this->specialData = $specialData;

        return $this;
    }

    /**
     * Get the value of mainElementsE
     */
    public function getMainElementsE()
    {
        return $this->mainElementsE;
    }

    /**
     * Set the value of mainElementsE
     */
    private function setMainElementsE($mainElementsE): self
    {
        $this->mainElementsE = $mainElementsE;

        return $this;
    }

    /**
     * Get the value of specialE
     */
    public function getSpecialE()
    {
        return $this->specialE;
    }

    /**
     * Set the value of specialE
     */
    private function setSpecialE($specialE): self
    {
        $this->specialE = $specialE;

        return $this;
    }

    /**
     * Get the value of curType
     */
    public function getCurType()
    {
        return $this->curType;
    }

    /**
     * Set the value of curType
     */
    private function setCurType($curType): self
    {
        $this->curType = $curType;

        return $this;
    }
}