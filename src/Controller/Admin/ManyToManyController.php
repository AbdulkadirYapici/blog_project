<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ManyToManyController extends Controller
{
    /**
     * @Route("/denemeMany", name="deneme_many")
     */
    public function register(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager= $this->getDoctrine()->getManager();

        $categoryrepo= $entityManager->getRepository(Category::class);
        /** @var Category $category */

        $categories = $categoryrepo->findByName('aksiyon');

        if($categories != NULL) {
            /** @var Category $item */
            foreach ($categories as $category) {
                foreach ($category->getBlogId() as $blog){
                    $blogArr = $category->removeBlogId($blog);
                    $entityManager->persist($category);
                    $entityManager->flush();
                }
            }
        }

            return $this->render('manytomany/deneme.html.twig',[

            'category' => $blogArr
        ]);
        }
}
