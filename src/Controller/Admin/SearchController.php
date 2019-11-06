<?php
namespace App\Controller\Admin;


use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as BaseRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController {
    private $entityManager;
    /**
     * @BaseRoute("/result", name="search_page")
     */
    public function resultPage( EntityManagerInterface $entityManager,Request $request)
    {
        $blogArr=[];
        $error= "Aradağınız içerik bulunamadı.";

        $this->entityManager= $entityManager;
        $searching = $request->query->get('search');

        $entityManager= $this->getDoctrine()->getManager();
        /** @var BlogRepository $blogrepo */
        $blogrepo= $entityManager->getRepository(Blog::class);
        $blogs = $blogrepo->findByContentContains($searching);

        if($blogs == NULL){

            $categoryrepo= $entityManager->getRepository(Category::class);
            /** @var Category $category */

            $categories = $categoryrepo->findByName($searching);

            $categoryMatchedBlogs= [];
            if($categories != NULL){
                /** @var Category $item */
                foreach ($categories as $category){
                    $blogArr = $category->getBlogId();
                    /*
                                    foreach ($blogArr as $blog) {
                                        array_push($categoryMatchedBlogs,$blog->getTitle());
                                        array_push($categoryMatchedBlogs,$blog->getContent());
                                        array_push($categoryMatchedBlogs,$blog->getPreviewImg());

                                        var_dump($categoryMatchedBlogs);


                                    }
                    */
                }


                return $this->render('search/search.html.twig',[
                    "category" => $blogArr,
                ]);

            }
            else{
                return $this->render('search/search.html.twig',[
                    "error" => $error,
                ]);
            }

        }
        else{
            return $this->render('search/search.html.twig',[
                "blogs" => $blogs,
            ]);
        }

        /*EĞER ARAMAYI DIREKT MATCH ISTIYOSAK
        $searching = $request->query->get('search');
        $entityManager = $this->getDoctrine()->getManager();

        $existTitle = $this->entityManager->getRepository(Blog::class)->findAll();
        */


    }


}
