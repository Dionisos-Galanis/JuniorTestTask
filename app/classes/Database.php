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


    public function saveNewProduct(Product $product)
    {
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
        $stmt = $this->prepare("SELECT id_property_name FROM junction_ptype_propname
            WHERE id_product_type = :id_product_type");
        $stmt->execute(['id_product_type' => $product->getTypeId()]);
        $id_prop_names = $stmt->fetchAll();
        
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
     * Gets all names of the product types from the database
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