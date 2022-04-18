<?php

class Book extends product
{
    private $weight;


    /**
     * Get the value of weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set the value of weight
     */
    public function setWeight($weight): self
    { 
        if ($weight > 0) {
            $this->weight = $weight;
            $special = $this->getSpecial();
            $special[0] = $weight;
            $this->setSpecial($special);
            return $this;
        } else {
            throw new Exception("Book weight should be positive!");
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
        if($this->checkSpecial($special)) {
            $this->special = $special;
            return $this;
        } else {
            throw new Exception("Invalid specials!");
        }
    }
}