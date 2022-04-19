<?php

class Product
{
    protected $sku;
    protected $name;
    protected $price;
    protected $typeId;
    protected $special;
    protected $specialsNames;

    
    /**
     * Try to add object data to the DB
     */
    public function addProductToDb()
    {
        // Check if all properties are set
        if (
            $this->sku != null
            && $this->name != null
            && $this->price != null
            && $this->typeId != null
            && $this->special != null
        ) {
            $db = new Database();
            $db->saveNewProduct($this);
        } else {
            throw new Exception("All product properties have to be set!");
        }
    }


    /**
     * Validate sku
     */
    public static function checkSku($sku): bool
    {
        // Get all existing SKUs
        $db = new Database();
        $skuArr = $db->getAllSku();
        if (in_array($sku, $skuArr)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate Name
     */
    public static function checkName($name): bool
    {
        return true;
    }

    /**
     * Validate Price
     */
    public static function checkPrice($price): bool
    {
        if ($price < 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate the product type ID
     */
    public static function checkTypeId($typeId): bool
    {
        // Get all valid product type IDs
        $db = new Database();
        $idArr = $db->getAllProductTypeIds();
        if (in_array($typeId, $idArr)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate special (product specific) parameters
     */
    public static function checkSpecial($special): bool
    {
        if (is_numeric($special)) {
            if ($special <= 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Get the value of sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set the value of sku
     */
    public function setSku($sku): self
    {
        if ($this->checkSku($sku)) {
            $this->sku = $sku;
            return $this;
        } else {
            throw new Exception("SKU should be unique!");
        }
        
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName($name): self
    {
        if ($this->checkName($name)) {
            $this->name = $name;
            return $this;
        } else {
            throw new Exception("Name is invalid!");
        }
    }

    /**
     * Get the value of price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     */
    public function setPrice($price): self
    {
        if ($this->checkPrice($price)) {
            $this->price = $price;
            return $this;
        } else {
            throw new Exception("Price can't be negative!");
        }
    }

    /**
     * Get the value of type
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set the value of type
     */
    public function setTypeId($typeId): self
    {
        if ($this->checkTypeId($typeId)) {
            $this->typeId = $typeId;
            return $this;
        } else {
            throw new Exception("Invalid product type ID!");
        }
    }


    /**
     * Get the value of special
     */
    public function getSpecial()
    {
        return $this->special;
    }


    /**
     * Set the value of special
     */
    public function setSpecial($special): self
    {
        $result = true;
        foreach ($special as $spec) {
            if (!$this->checkSpecial($spec)) {
                $result = false;
            }
        }
        if ($result) {
            $this->special = $spec;
            return $this;
        } else {
            throw new Exception("One or more of the special parameters is invalid!");
        }
    }

    /**
     * Get the value of specialsNames
     */
    public function getSpecialsNames()
    {
        return $this->specialsNames;
    }

    /**
     * Set the value of specialsNames
     */
    public function setSpecialsNames(): self
    {
        $db = new Database();
        $specialsNames = $db->getCurrentTypeProps($this->typeId);
        $this->specialsNames = $specialsNames;

        return $this;
    }
}