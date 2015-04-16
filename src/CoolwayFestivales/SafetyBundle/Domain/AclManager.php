<?php

namespace CoolwayFestivales\SafetyBundle\Domain;

use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Core\SecurityContext;
use CoolwayFestivales\SafetyBundle\Model\AclManagerInterface;
use CoolwayFestivales\SafetyBundle\Domain\AbstractAclManager;
use CoolwayFestivales\SafetyBundle\Model\PermissionContextInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class AclManager extends AbstractAclManager {
    private $doctrine;
    private $permissions;
    
    public function __construct(SecurityContext $securityContext, MutableAclProvider $aclProvider, $doctrine) {
        $this->doctrine = $doctrine;
        parent::__construct($securityContext, $aclProvider);
        $this->permissions = array(128=>"owner",64=>"master",32=>"operator",16=>"undelete",8=>"delete",4=>"edit",2=>"create",1=>"view");
    }

    public function addPermission($domainObject, $securityIdentity, $mask, $type = 'object', $installDefaults = false) {
       
        $context = $this->doCreatePermissionContext($type, $securityIdentity, $mask);
        $oid = ObjectIdentity::fromDomainObject($domainObject);
        $acl = $this->doLoadAcl($oid);
        $this->doApplyPermission($acl, $context);

        if ($installDefaults) {
            $this->doInstallDefaults($acl);
        }

        $this->aclProvider->updateAcl($acl);

        return $this;
    }

    public function deleteAclFor($domainObject) {
        $oid = ObjectIdentity::fromDomainObject($domainObject);
        $this->aclProvider->deleteAcl($oid);

        return $this;
    }

    public function revokePermission($domainObject, $securityIdentity, $mask, $type = 'object') {
        $context = $this->doCreatePermissionContext($type, $securityIdentity, $mask);
        $oid = ObjectIdentity::fromDomainObject($domainObject);
        $acl = $this->doLoadAcl($oid);
        $this->doRevokePermission($acl, $context);
        $this->aclProvider->updateAcl($acl);

        return $this;
    }

    public function revokeAllPermissions($domainObject, $securityIdentity, $type = 'object') {
        $securityIdentity = $this->doCreateSecurityIdentity($securityIdentity);
        $oid = ObjectIdentity::fromDomainObject($domainObject);
        $acl = $this->doLoadAcl($oid);
        $this->doRevokeAllPermissions($acl, $securityIdentity, $type);
        $this->aclProvider->updateAcl($acl);

        return $this;
    }
    
    public function getSecurityIdentities($domainObject){
    $acl = $this->getAcl($domainObject);
    $sids = array();
        foreach ($acl->getObjectAces() as $value) {
           $sids[] = $value->getSecurityIdentity();
        }
     return $sids;   
    }
    
    public function getAcl($domainObject) {
        
        $oid = ObjectIdentity::fromDomainObject($domainObject);
        $acl = $this->doLoadAcl($oid);
        return $acl;
    }
    
    public function getObjectType($class){
        $cads = explode("\\", $class);
        return $cads[1].":".$cads[3];
    }
    
    public function getAclClasses(){
         $em = $this->doctrine->getManager();
         $array = $em->getConnection()->fetchAll("SELECT * FROM acl_classes");
         $result = array();
         foreach ($array as $value) {
             $temp=array();
             $cat= $this->getObjectType($value['class_type']);
             $temp[]= $cat;
             $reg=explode(":", $cat);
             $temp[]=$reg[1];
             $result[]=$temp;
         }
        
         return $result;
    }
    
    /*retorna la estructira de un bundle segun las acl
     * 
     */
    public function getBundleStruct(){
        
         $em = $this->doctrine->getManager();
         $array = $em->getConnection()->fetchAll("SELECT * FROM acl_classes");
         $result = array();
         foreach ($array as $value) {
             $temp=array();
             $cat= $this->getObjectType($value['class_type']);
             
             $reg=explode(":", $cat);
             $result[$reg[0]][]=$reg[1];
         }
         return $result;
    }
    
    
    //función para obtener los usuarios con un permiso P sobre el recurso R
    public function getUsersByMask($domainObject,$mask=MaskBuilder::MASK_OWNER){
        $acl = $this->getAcl($domainObject);
        $aces = $acl->getObjectAces();
        
        $result = array();
        foreach ($aces as $ace) {
            if($mask==$ace->getMask()){
                $SI = $ace->getSecurityIdentity();
                $result[] = $SI->getUserName();
            }
        }
        
        return $result;
        
    }
    
    private function getEntity($class, $id){
        $em = $this->doctrine->getManager();
        $repo = $em->getRepository($class);
        $entity = $repo->find($id);
        return $entity;
    }
    
    /*
     * SI debe ser un array donde:
     * SI[0] = Role/User
     * SI[1] = ID
     * 
     * OI debe ser un array
     * OI[0] = Clase del objeto 
     * OI[1] = ID del objeto(en caso de ser solo una clase se deja null)
     * 
     * Devuelve un array de permisos permitidos, un sec id, el objeto del dominio o la clase y todos los permissos 
     */
    public function getPermissionsBy($si, $oi){
        
        $dom_obj = $this->getEntity($oi[0], $oi[1]);
        $sec_id = $this->getEntity("$si[0]", $si[1]);
        $acl = $this->getAcl($dom_obj);
        
        $SI = $this->doCreateSecurityIdentity($sec_id);
        $allowed_permissions = array();
        
        $tiene = false;
        foreach ($this->permissions as $mask=>$name) {
            try {
                $tiene = $acl->isGranted(array($mask), array($SI));
            } catch (\Symfony\Component\Security\Acl\Exception\NoAceFoundException $exc) {
                //no hay ace 
            }
            if ($tiene) {
                $allowed_permissions[] = $name;
            }
        }
        
        
        return array(
             "dom_obj"=>$dom_obj, 
             "sec_id"=>$sec_id, 
             "allowed_permissions"=>$allowed_permissions, 
             "all_permissions"=>  $this->permissions
        );

    }
    
    /*
     * SI debe ser un array donde:
     * SI[0] = Role/User
     * SI[1] = ID
     * 
     * OI debe ser un array
     * OI[0] = Clase del objeto 
     * OI[1] = ID del objeto(en caso de ser solo una clase se deja null)
     * 
     * mask = permission
     * 
     * asigna un permiso a un SI sobre un OI 
     */
    public function setPermission($si, $oi, $mask, $mode){
        
        $securityIdentity = $this->getEntity("$si[0]", $si[1]);
        $domainObject = $this->getEntity($oi[0], $oi[1]);
        $mask = $this->getKeyPermission($mask);
        try{
            if($mode==="add"){
                $this->addPermission($domainObject, $securityIdentity, $mask);
            }
            else{
                $this->revokePermission($domainObject, $securityIdentity, $mask); 
            }
            
        }  catch (\Exception $ex){
            throw new \Exception("error".$ex);
        }
    }
   
    public function getPermissions() {
        return $this->permissions;
    }
    
    public function getKeyPermission($name) {
        foreach ($this->permissions as $key => $value) {
            if($name==$value)
                return $key;
        }
    }



}
