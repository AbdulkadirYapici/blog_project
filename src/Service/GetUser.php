<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class GetUser
{

    /**

     */
    private $user;
    private $username= "";

    public function __construct (Security $security){
        $this->user = $security->getUser();
        if($this->user == NULL || $this->user == "" ){
            $this->username= "Ziyaretçi";
        }
        else{
            $this->username= $this->user->getUsername();
        }
    }

    public function getCurrentName(){

        return $this->username;
    }
    public function __toString() {
        return $this->username;
    }

}
