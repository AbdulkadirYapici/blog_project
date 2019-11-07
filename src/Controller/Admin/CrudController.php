<?php
namespace App\Controller\Admin;


use App\Entity\Blog;
use App\Form\BlogType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as BaseRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CrudController extends AbstractController {

    /**
     * @BaseRoute("/crud", name="crud_page")
     */
    public function showContent()
    {

        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);
        $blog= $blogRepository->findAll();
        return $this->render('Blog/Crud/crud.html.twig', [

                'blog'=> $blog,
            ]

        );
    }


    /**
     * @BaseRoute("/delete", name="delete_page")
     */
    public function deleteContent(Request $request)
    {
        $id = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);

        $deleteItem= $blogRepository->findById($id);
        $em->remove($deleteItem[0]);
        $em->flush();

        $blog= $blogRepository->findAll();


        return $this->render('Blog/Crud/crud.html.twig', [

                'blog'=> $blog,
            ]

        );
    }

    /**
     * @BaseRoute("/new", name="create_blog_page")
     */
    public function new(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $error= "";
        // creates a task object and initializes some data for this example
        $blog = new Blog();
        //$blog->set('Write a blog post');
        $blog->setCreatedAt(new \DateTime());

        $form = $this->createForm(BlogType::class, $blog);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $error = $authenticationUtils->getLastAuthenticationError();

            /** @var UploadedFile $file */
            $file = $form->get('preview_img')->getData();

            $fileName= $this-> randomNameForUploadedFile() . '.' . $file->guessExtension() ;
            $file->move($this->getParameter('afis_folder'), $fileName);

            $blog->setPreviewImg($fileName);



            $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($blog);
                $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('crud_page');
        }

        return $this->render('Blog/Crud/new.html.twig', [
            'form' => $form->createView(),
            'error'=> $error,
        ]);
    }
    private function randomNameForUploadedFile (){
        return md5(uniqid());
    }

    /**
     * @BaseRoute("/edit", name="edit_blog_page")
     */
    public function editContent(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $id = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);

        $deleteItem= $blogRepository->findById($id);

        $error= "";
        // creates a task object and initializes some data for this example
        $blog = new Blog();
        //$blog->set('Write a blog post');
        $blog->setCreatedAt(new \DateTime());

        $form = $this->createForm(BlogType::class, $blog);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $error = $authenticationUtils->getLastAuthenticationError();

            /** @var UploadedFile $file */
            $file = $form->get('preview_img')->getData();

            $fileName= $this-> randomNameForUploadedFile() . '.' . $file->guessExtension() ;
            $file->move($this->getParameter('afis_folder'), $fileName);

            $blog->setPreviewImg($fileName);



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blog);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('crud_page');
        }


        return $this->render('Blog/Crud/edit.html.twig', [
            'form' => $form->createView(),
            'error'=> $error,
        ]);
    }


}
