<?php

namespace MadeForVinyl\DAO;

use MadeForVinyl\Domain\Basket;

class BasketDAO extends DAO
{    
    /**
     * Returns a Basket matching the supplied id of the user.
     *
     * @param integer $idUser
     *
     * @return \MadeForVinyl\Domain\Basket |throws an exception if no matching basket is found
     */
    public function find($idUser) {
        $sql = "select * from t_basket where basket_owner=?";
        $row = $this->getDb()->fetchAssoc($sql, array($idUser));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $idUser);
    }
    
    /**
     * Returns all Baskets matching the supplied id of the user.
     *
     * @param integer $idUser
     *
     * @return \MadeForVinyl\Domain\Basket |throws an exception if no matching basket is found
     */
     public function findAllByIdUser($idUser) {
        $sql = "select * from t_basket where basket_owner=?";
        
        $result = $this->getDb()->fetchAll($sql, array($idUser));
        $baskets = array();
        foreach ($result as $row) {
            $basketId = $row['basket_id'];
            $baskets[$basketId] = $this->buildDomainObject($row);
        }
        return $baskets;
    }
    
    /**
     * Removes all vinyl for a category.
     *
     * @param integer $idUser the user id.
     */
     public function deleteByUser($idUser){
        // Delete all baskets of user
        $this->getDb()->delete('t_basket', array('basket_owner' => $idUser));
    }
    
    /**
     * Saves a basket into the database.
     *
     * @param \MadeForVinyl\Domain\Basket $basket the basket to save
     */
    public function save(Basket $basket) {
        $basketData = array(
            'basket_owner' => $basket->getOwner()->getId(),
            'basket_vinyl' => $basket->getVinyl()->getId(),
            'basket_quantity' => $basket->getQuantity(),
            );
        if ($basket->getId()) {
            // The basket has already been saved : update it
            $this->getDb()->update('t_basket', $basketData, array('basket_id' => $basket->getId()));
        } else {
            // The basket has never been saved : insert it
            $this->getDb()->insert('t_basket', $basketData);
            
            $id = $this->getDb()->lastInsertId();
            $basket->setId($id);
        }
    }
    
    /**
     * @var \MadeForVinyl\DAO\UserDAO
     */
    private $userDAO;

    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    
      /**
     * @var \MadeForVinyl\DAO\VinylDAO
     */
    private $vinylDAO;

    public function setVinylDAO(VinylDAO $vinylDAO) {
        $this->vinylDAO = $vinylDAO;
    }
    
     /**
     * Creates an Basket object based on a DB row.
     *
     * @param array $row The DB row containing Basket data.
     * @return \MadeForVinyl\Domain\Basket
     */
    protected function buildDomainObject($row) {
        $basket = new Basket();
        $basket->setId($row['basket_id']);
        $basket->setQuantity($row['basket_quantity']);
        
        if (array_key_exists('basket_owner', $row)) {
            // Find and set the associated user
            $userId = $row['basket_owner'];
            $user = $this->userDAO->find($userId);
            $basket->setOwner($user);
        }
        if (array_key_exists('basket_vinyl', $row)) {
            // Find and set the associated user
            $vinylId = $row['basket_vinyl'];
            $vinyl = $this->vinylDAO->find($vinylId);
            $basket->setVinyl($vinyl);
        }
        
        return $basket;
    }
    
    
    
}