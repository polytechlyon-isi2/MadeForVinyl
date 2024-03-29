<?php

namespace MadeForVinyl\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use MadeForVinyl\Domain\User;

class UserDAO extends DAO implements UserProviderInterface
{
    /**
     * Returns a user matching the supplied id.
     *
     * @param integer $id The user id.
     *
     * @return \MadeForVinyl\Domain\User|throws an exception if no matching user is found
     */
    public function find($id) {
        $sql = "select * from t_user where usr_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No user matching id " . $id);
    }
    
    /**
    *
    * Return 1 if the user name exist
    *
    * @param $username to verif
    *
    *@return 1 if the username exist
    */
    public function userNameExisted($username)
    {
        $sql = "SELECT * FROM t_user WHERE usr_login=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));

        if($row)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
    /**
     * Delete a user matching the supplied id.
     *
     * @param integer $id The user id.
     *
     */
    public function delete($id) {
        // Delete the user
        $this->getDb()->delete('t_user', array('usr_id' => $id));
    }
    
    /**
     * Returns the list of all Users
     *
     * @param empty
     *
     * @return \MadeForVinyl\Domain\User
     */
    public function findAll() {
        $sql = "select * from t_user";
        $result = $this->getDb()->fetchAll($sql);
        
        $users = array();
        foreach ($result as $row) {
            $userId = $row['usr_id'];
            $users[$userId] = $this->buildDomainObject($row);
        }
        return $users;
    }
    
    /**
     * Saves a user into the database.
     *
     * @param \MadeForVinyl\Domain\User $user The user to save
     */
    public function save(User $user) {
        $userData = array(
            'usr_name' => $user->getName(),
            'usr_surname' => $user->getSurname(),
            'usr_adress' => $user->getAdress(),
            'usr_postalCode' => $user->getPostalCode(),
            'usr_town' => $user->getTown(),
            'usr_login' => $user->getUsername(),
            'usr_salt' => $user->getSalt(),
            'usr_password' => $user->getPassword(),
            'usr_role' => $user->getRole()
            );
        if ($user->getId()) {
            // The user has already been saved : update it
            $this->getDb()->update('t_user', $userData, array('usr_id' => $user->getId()));
        } else {
            // The user has never been saved : insert it
            $this->getDb()->insert('t_user', $userData);
            // Get the id of the newly created user and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $sql = "select * from t_user where usr_login=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'MadeForVinyl\Domain\User' === $class;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return \MadeForVinyl\Domain\User
     */
    protected function buildDomainObject($row) {
        $user = new User();
        $user->setId($row['usr_id']);
        $user->setName($row['usr_name']);
        $user->setSurname($row['usr_surname']);
        $user->setAdress($row['usr_adress']);
        $user->setPostalCode($row['usr_postalCode']);
        $user->setTown($row['usr_town']);
        $user->setUsername($row['usr_login']);
        $user->setPassword($row['usr_password']);
        $user->setSalt($row['usr_salt']);
        $user->setRole($row['usr_role']);
        return $user;
    }
}