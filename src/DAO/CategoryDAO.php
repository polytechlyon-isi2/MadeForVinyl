<?php

namespace MadeForVinyl\DAO;

use MadeForVinyl\Domain\Category;

class CategoryDAO extends DAO
{
    /**
     * Return a list of all Vinyls, sorted by date (most recent first).
     *
     * @return array A list of all Vinyls.
     */
    public function findAll() {
        $sql = "select * from t_category";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $categories = array();
        foreach ($result as $row) {
            $categoryId = $row['category_id'];
            $categories[$categoryId] = $this->buildDomainObject($row);
        }
        return $categories;
    }
    public function find($id) {
        $sql = "select * from t_category where category_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $id);
    }
    /**
     * Creates an Vinyl object based on a DB row.
     *
     * @param array $row The DB row containing Vinyl data.
     * @return \MadeForVinyl\Domain\Vinyl
     */
    protected function buildDomainObject($row) {
        $category = new Category();
        $category->setId($row['category_id']);
        $category->setTitle($row['category_title']);
        return $category;
    }
}