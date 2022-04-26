<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add product</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/app/static/styles.css" rel="stylesheet">
        <?php
            require_once "../RequireAll.php";

            function redirect($url){
                if (!headers_sent()){    
                  header('Location: '.$url);
                  exit;
                } else {  
                  echo '<script type="text/javascript">';
                  echo 'window.location.href="'.$url.'";';
                  echo '</script>';
                  echo '<noscript>';
                  echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
                  echo '</noscript>'; exit;
                }
             }
        ?>
    </head>
    <body>
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
    </body>
</html>