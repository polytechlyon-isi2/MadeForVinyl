<?php

namespace MadeForVinyl\src\DAO;

use MadeForVinyl\src\Domain\Vinyl;

class VinylDAO extends DAO
{
    /**
     * Return a list of all Vinyls, sorted by date (most recent first).
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
     * Creates an Vinyl object based on a DB row.
     *
     * @param array $row The DB row containing Vinyl data.
     * @return \MadeForVinyl\Domain\Vinyl
     */
    protected function buildDomainObject($row) {
        $vinyl = new Article();
        $vinyl->setId($row['vinyl_id']);
        $vinyl->setTitle($row['vinyl_title']);
        $vinyl->setArtist($row['vinyl_artist']);
        $vinyl->setCategory($row['vinyl_category']);
        $vinyl->setYear($row['vinyl_year']);
        return $vinyl;
    }
}