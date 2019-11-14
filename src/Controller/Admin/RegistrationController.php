<?php

namespace App\Controller\Admin;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function register(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //$error = $authenticationUtils->getLastAuthenticationError();
        $error="";
        // 1) build the form
        $user = new User();
        //$blog->set('Write a blog post');
        $user->setAuthority('');
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $error = $authenticationUtils->getLastAuthenticationError();

            //var_dump($error); exit();
            $newEmail= $form->get('email')->getData();
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $existEmail = $this->getDoctrine()
                ->getRepository(User::class)
                ->findByEmail($newEmail);

            if($existEmail != NULL){
                //throw new \Exception('Email already exists.');
                $error = "Girilen email başka bir kullanıcı tarafından kullanılıyor." ;
                return $this->render(
                    'registration/register.html.twig',[

                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }
            else
            {
                $entityManager->persist($user);
                $entityManager->flush();
            }

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'registration/register.html.twig',[

            'form' => $form->createView(),
            'error' => $error
        ]);
    }
}
