<?php
require_once '/app/templates/header0.php';
?>
<title>Product Add</title>
<?php
require_once '/app/templates/header.php';
?>

<h2>Add a new product</h2>
<?php
    $form = new AddProductForm();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $result = $form->processForm();
        if ($result) {
            redirect("/");
            exit;
        } else {
            $mainElementsE = $form->getMainElementsE();
            $specialE = $form->getSpecialE();
            $mainData = $form->getMainData();
            $specialData = $form->getSpecialData();
            $curType = $form->getCurType();
            $form->buildForm($mainData, $curType, $specialData, $mainElementsE, $specialE);
        }
    } else {
        $mainElementsT = ["", "", ""];
        $curType = 1;
        $specialT = [""];
        $mainElementsE = ["", "", ""];
        $specialE = [""];
        $form->buildForm($mainElementsT, $curType, $specialT, $mainElementsE, $specialE);
    }
?>

<?php
require_once '/app/templates/footer.php'
?>