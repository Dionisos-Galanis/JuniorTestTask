<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add product</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
            require_once "../RequireAll.php";
        ?>
    </head>
    <body>
        <h2>Add a new product</h2>
        <?php
            $form = new AddProductForm();
            $form->buildForm();
        ?>
    </body>
</html>