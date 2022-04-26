<?php

class Database extends PDO
{
    public function __construct()
    {
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        parent::__construct(
            "mysql:dbname=jttproducts;host=mysql",
            'wduser',
            'wdaccess'
        );
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // always disable emulated prepared statement when using the MySQL driver
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }


    /**
     * Removes product with the given SKU from the DB
     */
    public function deleteProduct($sku)
    {
        $stmt = $this->prepare("DELETE FROM products WHERE sku = :sku");
        $stmt->execute(['sku' => $sku]);
    }


    /**
     * Returns an instance of Prduct initialized with data from DB according to the given SKU. Returns Null if not found.
     */
    public function getProductFromDb($sku)
    {
        // First check that the product is present in DB
        if (!$this->isSkuInDb($sku)) {
            return Null;
        }

        // Get an instance of Product
        $product = new Product();

        // SKU is present, so lets extract the product. First, the main params:
        $stmt = $this->prepare("SELECT name, price, id_product_type
            FROM products
            WHERE sku = :sku");
        $stmt->execute(['sku' => $sku]);
        $mainParams = $stmt->fetchAll();

        $product->setSku($sku);
        $product->setName($mainParams[0][0]);
        $product->setPrice($mainParams[0][1]);
        $product->setTypeId($mainParams[0][2]);

        // Now the special params
        $stmt = $this->prepare("SELECT property_value
            FROM property_values
            WHERE product_sku = :sku");
        $stmt->execute(['sku' => $sku]);
        $specParams = $stmt->fetchAll();
        $specArr = [];
        foreach ($specParams as $spec) {
            array_push($specArr, $spec[0]);
        }
        $product->setSpecial($specArr);
        $product->setSpecialsNames();

        return $product;
    }


    /**
     * Checks if a product with the given SKU is in DB
     */
    public function isSkuInDb($sku)
    {
        $skuArr = $this->getAllSku();
        if (in_array(strtoupper($sku), $skuArr)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Gets an array of all stored SKUs
     */
    public function getAllSku()
    {
        $stmt = $this->prepare("SELECT sku FROM products");
        $stmt->execute();
        $allSku = $stmt->fetchAll();
        $skuArr = [];
        foreach ($allSku as $skuBlock) {
            array_push($skuArr, $skuBlock[0]);
        }

        return $skuArr;
    }


    /**
     * Gets all available product Type IDs
     */
    public function getAllProductTypeIds()
    {
        $stmt = $this->prepare("SELECT id FROM product_types");
        $stmt->execute();
        $allId = $stmt->fetchAll();
        $idArr = [];
        foreach ($allId as $idBlock) {
            array_push($idArr, $idBlock[0]);
        }

        return $idArr;
    }


    /**
     * Saves a new product to DB
     */
    public function saveNewProduct(Product $product)
    {
        // We need to check that new SKU should not alredy be in DB
        if ($this->isSkuInDb($product->getSku())) {
            throw new Exception("SKU is to be unique!");
            die;
        };

        // Insert new product with base params
        $stmt = $this->prepare("INSERT INTO products (sku, name, price, id_product_type)
            VALUES (:sku, :name, :price, :id_product_type)");
        $stmt->execute([
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'id_product_type' => $product->getTypeId()
        ]);

        // Get the special params list for this product
        $id_prop_names = $this->getIdPropNames($product->getTypeId());
        
        // Insert specials
        $k = 0;
        $special = $product->getSpecial();
        foreach($id_prop_names as $id_p_n) {
            $stmt = $this->prepare("INSERT INTO property_values
                (product_sku, property_id, property_value, id_product_type)
                VALUES (:sku, :id_p_n, :val, :ptype)");
            $stmt->execute([
                'sku' => $product->getSku(),
                'id_p_n' => $id_p_n[0],
                'val' => $special[$k],
                'ptype' => $product->getTypeId()
            ]);
            $k++;
        }
    }


    /**
     * Gets IDs of properties foe a given product type ID
     */
    public function getIdPropNames($typeId)
    {
        $stmt = $this->prepare("SELECT id_property_name FROM junction_ptype_propname
            WHERE id_product_type = :id_product_type");
        $stmt->execute(['id_product_type' => $typeId]);
        return $stmt->fetchAll();
    }


    /**
     * Gets all names of the product types names from the database
     */
    public function getAllProductTypes()
    {
        $stmt = $this->prepare("SELECT product_type FROM product_types");
        $stmt->execute();
        $raw = $stmt->fetchAll();
        $arr = [];
        foreach ($raw as $r) {
            array_push($arr, $r[0]);
        }
        return $arr;
    }


    /**
     * Gets all css IDs of the product types blocks from the database
     */
    public function getAllBlockIds()
    {
        $stmt = $this->prepare("SELECT block_css_id FROM product_types");
        $stmt->execute();
        $raw = $stmt->fetchAll();
        $arr = [];
        foreach ($raw as $r) {
            array_push($arr, $r[0]);
        }
        return $arr;
    }


    /**
     * Gets all descriptions of the product types blocks from the database
     */
    public function getAllBlockDesc()
    {
        $stmt = $this->prepare("SELECT block_description FROM product_types");
        $stmt->execute();
        $raw = $stmt->fetchAll();
        $arr = [];
        foreach ($raw as $r) {
            array_push($arr, $r[0]);
        }
        return $arr;
    }


    /**
     * Gets all property names for the product type with $curType id from the database
     */
    public function getCurrentTypeProps($curType)
    {
        $stmt = $this->prepare("SELECT property_name FROM property_names
            JOIN junction_ptype_propname
            ON property_names.id = junction_ptype_propname.id_property_name
            WHERE id_product_type = :curType");
        $stmt->execute(['curType' => $curType]);
        $raw = $stmt->fetchAll();
        $arr = [];
        foreach ($raw as $r) {
            array_push($arr, $r[0]);
        }
        return $arr;
    }


    /**
     * Gets all property CSS IDs for the product type with $curType id from the database
     */
    public function getCurrentTypePropsIds($curType)
    {
        $stmt = $this->prepare("SELECT property_css_id FROM property_names
            JOIN junction_ptype_propname
            ON property_names.id = junction_ptype_propname.id_property_name
            WHERE id_product_type = :curType");
        $stmt->execute(['curType' => $curType]);
        $raw = $stmt->fetchAll();
        $arr = [];
        foreach ($raw as $r) {
            array_push($arr, $r[0]);
        }
        return $arr;
    }


    /**
     * Gets all property <input> names for the product type with $curType id from the database
     */
    public function getCurrentTypePropsNames($curType)
    {
        $stmt = $this->prepare("SELECT property_input_name FROM property_names
            JOIN junction_ptype_propname
            ON property_names.id = junction_ptype_propname.id_property_name
            WHERE id_product_type = :curType");
        $stmt->execute(['curType' => $curType]);
        $raw = $stmt->fetchAll();
        $arr = [];
        foreach ($raw as $r) {
            array_push($arr, $r[0]);
        }
        return $arr;
    }
}