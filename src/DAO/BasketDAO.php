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
        $basket = new Vinyl();
        $basket->setId($row['basket_id']);
        
        if (array_key_exists('basket_owner', $row)) {
            // Find and set the associated user
            $userId = $row['usr_id'];
            $user = $this->userDAO->find($userId);
            $basket->setOwner($user);
        }
        if (array_key_exists('basket_vinyl', $row)) {
            // Find and set the associated user
            $vinylId = $row['vinyl_id'];
            $vinyl = $this->vinylDAO->find($vinylId);
            $basket->setVinyl($vinyl);
        }
        
        return $vinyl;
    }
    
    
    
}