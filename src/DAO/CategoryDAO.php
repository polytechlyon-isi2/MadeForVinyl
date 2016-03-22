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
        $sql = "select * from t_category order by category_title";
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
     * Return the list of all Categories,
     *
     * @return array A list of all Categories.
     */
    public function findAllName() {
        $sql = "select * from t_category order by category_title";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $categoriesNames = array();
        foreach ($result as $row) 
        {
            $categoryName = $row['category_title'];
            $categoriesNames[$categoryName] = $this->buildDomainObject($row);
        }
        return $categoriesNames;
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
     * Saves a category into the database.
     *
     * @param \MadeForVinyl\Domain\category $category The category to save
     */
    public function save(Category $category) {
        $categoryData = array(
            'category_title' => $category->getTitle(),
            );
        if ($category->getId()) {
            // The vinyl has already been saved : update it
            $this->getDb()->update('t_category', $categoryData, array('category_id' => $category->getId()));
        } else {
            // The vinyl has never been saved : insert it
            $this->getDb()->insert('t_category', $categoryData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $category->setId($id);
        }
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