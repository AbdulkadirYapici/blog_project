<?php
namespace App\Controller\Storefront;


use App\Entity\Blog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as BaseRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\BlogType;

class BlogController extends AbstractController {
    /**
     * @BaseRoute("/", name="homepage")
     */
    public function indexAction()
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);
        $blog= $blogRepository->findAll();
        return $this->render('Blog/Storefront/homePage.html.twig', [

                'blog'=> $blog,
            ]

        );    }

    /**
     * @BaseRoute("/blogcontent", name="bContent")
     */
    public function showContent()
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);
        $blog= $blogRepository->findAll();
        return $this->render('Blog/Storefront/showBlog.html.twig', [

                'blog'=> $blog,
            ]

        );
    }
    /**
     *@BaseRoute("/blogcontent/{id}", name= "single_entry_show")
     */
    public function singeleEntryShow($id){

        $content = $this->getDoctrine()
            ->getRepository(Blog::class)
            ->find($id);

        if (!$content) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        return $this->render('Blog/Storefront/singleBlogContent.html.twig', [

                'blog'=> $content,
            ]

        );
    }




}
