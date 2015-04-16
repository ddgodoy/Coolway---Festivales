<?php

namespace CoolwayFestivales\SafetyBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @ORM\Entity(repositoryClass="CoolwayFestivales\SafetyBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements AdvancedUserInterface, \Serializable {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @Assert\Email()
     * @ORM\Column(type="string", length=100)
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $accountNonExpired;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    protected $credentialsNonExpired;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $accountNonLocked;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $salt;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $user_roles;

    public function __construct() {
        $this->user_roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = true;
        $this->accountNonExpired = true;
        $this->accountNonLocked = true;
        $this->credentialsNonExpired = true;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set accountNonExpired
     *
     * @param boolean $accountNonExpired
     */
    public function setAccountNonExpired($accountNonExpired) {
        $this->accountNonExpired = $accountNonExpired;
    }

    /**
     * Get accountNonExpired
     *
     * @return boolean 
     */
    public function getAccountNonExpired() {
        return $this->accountNonExpired;
    }

    /**
     * Set credentialsNonExpired
     *
     * @param boolean $credentialsNonExpired
     */
    public function setCredentialsNonExpired($credentialsNonExpired) {
        $this->credentialsNonExpired = $credentialsNonExpired;
    }

    /**
     * Get credentialsNonExpired
     *
     * @return boolean 
     */
    public function getCredentialsNonExpired() {
        return $this->credentialsNonExpired;
    }

    /**
     * Set accountNonLocked
     *
     * @param boolean $accountNonLocked
     */
    public function setAccountNonLocked($accountNonLocked) {
        $this->accountNonLocked = $accountNonLocked;
    }

    /**
     * Get accountNonLocked
     *
     * @return boolean 
     */
    public function getAccountNonLocked() {
        return $this->accountNonLocked;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password) {
        $confg = Yaml::parse(__DIR__ . '/../../../../app/config/security.yml');
        $params = $confg['security']['encoders'][get_class($this)];
        $encode = new MessageDigestPasswordEncoder(
                $params['algorithm'], true, $params['iterations']
        );

        $this->password = $encode->encodePassword($password, $this->salt);
        //$this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt) {
        //$this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    function eraseCredentials() {
        
    }

    /**
     * {@inheritdoc}
     */
    function equals(UserInterface $user) {
        if (!$user instanceof User)
            return false;

        if ($this->password !== $user->getPassword())
            return false;
        if ($this->getSalt() !== $user->getSalt())
            return false;
//        if ($this->getToken() !== $user->getToken())
//            return false;
        if ($this->enabled !== $user->isEnabled())
            return false;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    function isAccountNonExpired() {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    function isAccountNonLocked() {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    function isCredentialsNonExpired() {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    function isEnabled() {
        return $this->enabled;
    }

    public function __toString() {
        return $this->username;
    }

    public function getRoles() {
        return $this->user_roles->toArray();
    }

    /**
     * Add user_roles
     *
     * @param CoolwayFestivales\SafetyBundle\Entity\Role $userRoles
     */
    public function addRole(\CoolwayFestivales\SafetyBundle\Entity\Role $userRoles) {
        $this->user_roles[] = $userRoles;
    }

    /**
     * Get user_roles
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUserRoles() {
        return $this->user_roles;
    }

    public function setUserRoles($user_roles) {
        $this->user_roles = $user_roles;
    }

//Para apptibasse    
    public function getClass() {
        return "SafetyBundle:User";
    }

    public function getUserXmppPassword() {
        return $this->username;
    }

    public function getUserSlug() {
        return $this->username;
    }

    public function getAvatar() {
        return "avatar.png";
    }


    /**
     * Add user_roles
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\Role $userRoles
     * @return User
     */
    public function addUserRole(\CoolwayFestivales\SafetyBundle\Entity\Role $userRoles)
    {
        $this->user_roles[] = $userRoles;
    
        return $this;
    }

    /**
     * Remove user_roles
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\Role $userRoles
     */
    public function removeUserRole(\CoolwayFestivales\SafetyBundle\Entity\Role $userRoles)
    {
        $this->user_roles->removeElement($userRoles);
    }

    public function serialize()
    {
           return serialize($this->id);
    }

    public function unserialize($data)
    {
           $this->id = unserialize($data);
    }  
  
}