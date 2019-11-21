<?php

namespace App\Controller\Admin;

use App\Service\GetUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $username= "";

    public function __construct(GetUser $ServiceUsername )
    {
        $this->username = $ServiceUsername->getCurrentName();
    }
    /**
    * @Route("/login", name="app_login")
    */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'username' => $this->username,

            'last_username' => $lastUsername,
        'error' => $error
        ]);
    }

}