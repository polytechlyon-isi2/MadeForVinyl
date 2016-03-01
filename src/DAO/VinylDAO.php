<?php

namespace MadeForVinyl\DAO;

use MadeForVinyl\Domain\Vinyl;

class VinylDAO extends DAO
{
    /**
     * Return a list of all Vinyls.
     *
     * @return array A list of all Vinyls.
     */
    public function findAll() {
        $sql = "select * from t_vinyl";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $vinyls = array();
        foreach ($result as $row) {
            $vinylId = $row['vinyl_id'];
            $vinyls[$vinylId] = $this->buildDomainObject($row);
        }
        return $vinyls;
    }

    /**
     * Returns a list of Vinyls matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MadeForVinyl\Domain\Vinyl |throws an exception if no matching article is found
     */
    public function findAllId($idCategory) {
        $sql = "select * from t_vinyl where vinyl_category=?";
        
        $result = $this->getDb()->fetchAll($sql, array($idCategory));
        $vinyls = array();
        foreach ($result as $row) {
            $vinylId = $row['vinyl_id'];
            $vinyls[$vinylId] = $this->buildDomainObject($row);
        }
        return $vinyls;
    }
    
    public function find($idVinyl) {
        $sql = "select * from t_vinyl where vinyl_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($idVinyl));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $idVinyl);
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
        $vinyl->setCategory($row['vinyl_category']);
        $vinyl->setYear($row['vinyl_year']);
        $vinyl->setSleeve($row['vinyl_sleeve']);
        return $vinyl;
    }
}