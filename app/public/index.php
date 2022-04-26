<?php
require_once '/app/templates/header0.php';
?>
<title>Product List</title>
<?php
require_once '/app/templates/header.php';
?>

<h2>Product list</h2>
<form action="<?php echo ManageUrls::constructUrl("add-product");?>">
    <button id="add" type="submit">ADD</button>
</form>
<form method="POST" action="index.php">
    <button id="massdel" type="submit">MASS DELETE</button>
    <div id="allProducts">
        <?php
            $db = new Database();
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST)) {
                    redirect("/");
                    exit;
                } else {
                    foreach ($_POST as $sku) {
                        // Just in case still check that sku is in DB - generally obsolete
                        if ($db->isSkuInDb($sku)) {
                            $db->deleteProduct($sku);
                        } else {
                            throw new Exception("Somehow a wrong SKU was pssed for deletion!");
                            die;
                        }
                    }
                    redirect("/");
                    exit;
                }
            } else {
                $allSku = $db->getAllSku();
                sort($allSku);
                foreach ($allSku as $sku) {
                    $product = $db->getProductFromDb($sku);
                    $product->echoProduct();
                }
            }
        ?>
    </div>
</form>

<?php
require_once '/app/templates/footer.php'
?>