<?php

namespace MadeForVinyl\Domain;

class Basket 
{
    /**
     * Basket id.
     *
     * @var integer
     */
    private $id;

    /**
     * Basket owner.
     *
     * @var \MadeForVinyl\Domain\User
     */
    private $owner;
    
    /**
     * Vinyl in the basket.
     *
     * @var \MadeForVinyl\Domain\Vinyl
     */
    private $vinyl;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner(User $owner) {
        $this->owner = $owner;
    }
    
    public function getVinyl() {
        return $this->vinyl;
    }

    public function setVinyl(Vinyl $vinyl) {
        $this->vinyl = $vinyl;
    }
    
}