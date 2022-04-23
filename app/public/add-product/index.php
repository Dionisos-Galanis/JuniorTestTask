<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add product</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../static/styles.css" rel="stylesheet">
        <?php
            require_once "../RequireAll.php";
        ?>
    </head>
    <body>
       <h2>Add a new product</h2>
        <?php
            $form = new AddProductForm();
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $result = $form->processForm();
                $mainData = $form->getMainData();
                $specialData = $form->getSpecialData();
                $curType = $form->getCurType();
                if ($result) {
                    // print_r($mainData);
                    // print_r($specialData);
                    

                } else {
                    $mainElementsE = $form->getMainElementsE();
                    $specialE = $form->getSpecialE();
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
    </body>
</html>