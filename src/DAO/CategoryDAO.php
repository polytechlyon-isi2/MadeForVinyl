<?php

namespace MadeForVinyl\DAO;

use MadeForVinyl\Domain\Category;

class CategoryDAO extends DAO
{
    /**
     * Return the list of all Categories,
     *
     * @return array A list of all Categories.
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
    
    /**
     * Returns a Category matching the supplied id.
     *
     * @param integer $idCategory
     *
     * @return \MadeForVinyl\Domain\Category|throws an exception if no matching category is found
     */
    public function find($idCategory) {
        $sql = "select * from t_category where category_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($idCategory));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $idCategory);
    }
    
    /**
     * Removes a category from the database.
     *
     * @param integer $id The category id.
     */
    public function delete($id) {
        // Delete the category
        $this->getDb()->delete('t_category', array('category_id' => $id));
    }
    
    /**
     * Creates a Category object based on a DB row.
     *
     * @param array $row The DB row containing Category data.
     * @return \MadeForVinyl\Domain\Category
     */
    protected function buildDomainObject($row) {
        $category = new Category();
        $category->setId($row['category_id']);
        $category->setTitle($row['category_title']);
        return $category;
    }
}