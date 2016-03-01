<?php

namespace MadeForVinyl\Domain;

class Vinyl 
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

    /**
     * Vinyl artist.
     *
     * @var string
     */
    private $artist;
    
    /**
     * Vinyl category.
     *
     * @var int
     */
    private $category;
    
    /**
     * Vinyl year.
     *
     * @var string
     */
    private $year;
    
    /**
     * Vinyl sleeve.
     *
     * @var string
     */
    private $sleeve;
    
    /**
     * Vinyl price.
     *
     * @var price
     */
    private $price;

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

    public function getArtist() {
        return $this->artist;
    }

    public function setArtist($artist) {
        $this->artist = $artist;
    }
    
    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }
    
    public function getYear() {
        return $this->year;
    } 
    
    public function setYear($year) {
        $this->year = $year;
    }
    
    public function getSleeve() {
        return $this->sleeve;
    }

    public function setSleeve($sleeve) {
        $this->sleeve = $sleeve;
    }
    
    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }
}