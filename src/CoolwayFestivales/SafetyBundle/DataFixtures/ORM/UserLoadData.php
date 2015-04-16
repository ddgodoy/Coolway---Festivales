<?php

namespace CoolwayFestivales\SafetyBundle\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use CoolwayFestivales\SafetyBundle\Entity\User;
use CoolwayFestivales\SafetyBundle\Entity\Role;
use Doctrine\ORM\EntityManager;

class UserLoadData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param EntityManager $manager
     */
    public function load($manager) {        
        $role = new Role();
        $role->setName('ROLE_CUSTOMER');
        $role->setDescription('Clientes');
        $manager->persist($role);
        
        $role2 = new Role();
        $role2->setName('ROLE_ADMIN');
        $role2->setDescription('Administrador');
        $manager->persist($role2);
        
       // Crear el usuario para la administración
       $admin = new User();
       $admin->setUserName('admin');
       $admin->setName('Administrador del Sistema');
       $admin->setEmail('admin@apptibase.com');
       $admin->setPassword('adminpass');
       $admin->addRole($role);
       $manager->persist($admin);

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

}

?>
