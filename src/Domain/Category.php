<?php

namespace MadeForVinyl\Domain;

class Category 
{
    /**
     * Vinyl id.
     *
     * @var integer
     */
    private $id;

    /**
     * Vinyl title.
     *
     * @var string
     */
    private $title;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }
}