<?php

namespace MadeForVinyl\DAO;

use MadeForVinyl\Domain\Vinyl;

class VinylDAO extends DAO
{
    /**
     * @var \MadeForVinyl\DAO\CategoryDAO
     */
    private $categoryDAO;

    public function setCategoryDAO(CategoryDAO $categoryDAO) {
        $this->categoryDAO = $categoryDAO;
    }
    
    /**
     * Returns the list of Vinyls which have the same category
     *
     * @param integer $idCategory
     *
     * @return \MadeForVinyl\Domain\Vinyl
     */
    public function findAllByCategory($idCategory) {
        $sql = "select * from t_vinyl where vinyl_category=?";
        
        $result = $this->getDb()->fetchAll($sql, array($idCategory));
        $vinyls = array();
        foreach ($result as $row) {
            $vinylId = $row['vinyl_id'];
            $vinyls[$vinylId] = $this->buildDomainObject($row);
        }
        return $vinyls;
    }
    
    /**
     * Removes a vinyl from the database.
     *
     * @param integer $id The vinyl id.
     */
    public function delete($id) {
        // Delete the article
        $this->getDb()->delete('t_vinyl', array('vinyl_id' => $id));
    }
    
    /**
     * Removes all vinyl for a category.
     *
     * @param integer $id The vinyl id.
     */
    public function deleteByCategory($idCategory) {
        // Delete the article
        $this->getDb()->delete('t_vinyl', array('vinyl_category' => $idCategory));
    }
    
    /**
     * Returns the list of all Vinyls
     *
     * @param empty
     *
     * @return \MadeForVinyl\Domain\Vinyl
     */
    public function findAll() {
        $sql = "select * from t_vinyl";
        $result = $this->getDb()->fetchAll($sql);
        
        $vinyls = array();
        foreach ($result as $row) {
            $vinylId = $row['vinyl_id'];
            $vinyls[$vinylId] = $this->buildDomainObject($row);
        }
        return $vinyls;
    }
    
    /**
     * Returns the Vinyl matching the supplied id.
     *
     * @param integer $idVinyl
     *
     * @return \MadeForVinyl\Domain\Vinyl |throws an exception if no matching article is found
     */
    public function find($idVinyl) {
        $sql = "select * from t_vinyl where vinyl_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($idVinyl));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $idVinyl);
    }
    
    /**
     * Saves a Vinyl into the database.
     *
     * @param \MadeForVinyl\Domain\Vinyl $vinyl The vinyl to save
     */
    public function save(Vinyl $vinyl) {
        $vinylData = array(
            'vinyl_title' => $vinyl->getTitle(),
            'vinyl_artist' => $vinyl->getArtist(),
            'vinyl_category' => $vinyl->getCategory(),
            'vinyl_year' => $vinyl->getYear(),
            'vinyl_price' => $vinyl->getPrice(),
            'vinyl_sleeve' => $vinyl->getSleeve(),
            );
        if ($vinyl->getId()) {
            // The vinyl has already been saved : update it
            $this->getDb()->update('t_vinyl', $vinylData, array('vinyl_id' => $vinyl->getId()));
        } else {
            // The vinyl has never been saved : insert it
            $this->getDb()->insert('t_vinyl', $vinylData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $vinyl->setId($id);
        }
    }
    
    /**
     * Creates an Vinyl object based on a DB row.
     *
     * @param array $row The DB row containing Vinyl data.
     * @return \MadeForVinyl\Domain\Vinyl
     */
    protected function buildDomainObject($row) {
        $vinyl = new Vinyl();
        $vinyl->setId($row['vinyl_id']);
        $vinyl->setTitle($row['vinyl_title']);
        $vinyl->setArtist($row['vinyl_artist']);
        $vinyl->setYear($row['vinyl_year']);
        $vinyl->setSleeve($row['vinyl_sleeve']);
        $vinyl->setPrice($row['vinyl_price']);
        
        if (array_key_exists('vinyl_category', $row)) {
            // Find and set the associated category
            $categoryId = $row['vinyl_category'];
            $category = $this->categoryDAO->find($categoryId);
            $vinyl->setCategory($category);
        }
        return $vinyl;
    }
}