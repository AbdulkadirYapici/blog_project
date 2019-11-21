<?php
namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\Tag;
use App\Form\BlogType;
use App\Service\GetUser;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Type;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route as BaseRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;

class CrudController extends AbstractController {
    const TOKEN_INVALID = "Token yanlış";
    public $error= "";
public $entityManager;
public $definedCat= "";
public $definedTag= "";

    const CPP = "cpp";
    private $username= "";

    public function __construct(GetUser $ServiceUsername )
    {
        $this->username = $ServiceUsername->getCurrentName();
    }
    /**
     * @BaseRoute("/crud", name="crud_page")
     */
    public function showContent(EntityManagerInterface $entityManager, Request $request, Security $security)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $this->entityManager = $entityManager;

        $repository = $this->getDoctrine()->getRepository(Blog::class);

        $page = (int)$request->query->get('page');
        $contentPerPage = (int)$request->request->get(self::CPP);
        /** @var Session $session */
        $session = $this->get("session");
        if ($contentPerPage >= 100) {
            $contentPerPage = 10;
        }
        if ($contentPerPage < 0) {
            $contentPerPage = 5;
        }
        if ($contentPerPage == 0) {
            if ($session->get(self::CPP) === null) {
                $session->set(self::CPP, 5);
            }
        } else {
            $session->set(self::CPP, $contentPerPage);
        }
        $contentPerPage = $session->get(self::CPP);

        /* ESKI CACHE HALI
        if($cpp <= 0){
            $cpp = 0;
        }

        if($cpp == 0){
            $cacheDataReturn['cpp'] = (int) $this->cache_get('cpp');
            if($cacheDataReturn['cpp'] == NULL){
                $cpp = 10; //değer kullanıcıdan alınacak
                $this->cache_set('cpp', $cpp);
            }
            else{
                $cpp= $cacheDataReturn['cpp'];
            }
        }
        else{
            $this->cache_set('cpp', $cpp);
        }
        */

// createQueryBuilder() automatically selects FROM AppBundle:Product
// and aliases it to "p"

        //apc_store('cpp', 2);
        //var_dump(apc_fetch('$contentPerPage'));


        $query = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.status= :status')
            ->orderBy('b.id', 'DESC')
            ->setParameter('status', 1)
            ->getQuery();
        $countOfDbItems = (int)$query->getSingleScalarResult();
        //var_dump(count($countOfDbItems));

        $totalPageNumber = ceil($countOfDbItems / $contentPerPage);

        if ($page == NULL || $page > $totalPageNumber || $page == 0) {
            $page = 1;
        }

        $getContentBaseNumber = ($page - 1) * $contentPerPage;

        //dump($totalPageNumber);
        $query = $repository->createQueryBuilder('p')
            ->where('p.status= :status')
            ->orderBy('p.id', 'DESC')
            ->setParameter('status', 1)
            ->setFirstResult($getContentBaseNumber)
            ->setMaxResults($contentPerPage)
            ->getQuery();

        $contents = $query->getResult();

        $query = $repository->createQueryBuilder('p')
            ->where('p.status= :status')
            ->orderBy('p.id', 'DESC')
            ->setParameter('status', 1)
            ->getQuery();

        $contents = $query->getResult();
        //dump($contents);
        //dump($query->getSQL());


        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);
        $blog= $blogRepository->findAll();
        return $this->render('Blog/Crud/crud.html.twig', [
                'username' => $this->username,

                'blog'=> $contents,
                'toplam_sayfa'=> $totalPageNumber,
                'current_page' => $page,
            ]

        );
    }


    /**
     * @BaseRoute("/delete", name="delete_page")
     */
    public function deleteContent(Request $request)
    {


        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $repository = $this->getDoctrine()->getRepository(Blog::class);
        $page = (int)$request->query->get('page');
        $contentPerPage = (int)$request->request->get(self::CPP);
        /** @var Session $session */
        $session = $this->get("session");
        if ($contentPerPage >= 100) {
            $contentPerPage = 10;
        }
        if ($contentPerPage < 0) {
            $contentPerPage = 5;
        }
        if ($contentPerPage == 0) {
            if ($session->get(self::CPP) === null) {
                $session->set(self::CPP, 5);
            }
        } else {
            $session->set(self::CPP, $contentPerPage);
        }
        $contentPerPage = $session->get(self::CPP);

        /* ESKI CACHE HALI
        if($cpp <= 0){
            $cpp = 0;
        }

        if($cpp == 0){
            $cacheDataReturn['cpp'] = (int) $this->cache_get('cpp');
            if($cacheDataReturn['cpp'] == NULL){
                $cpp = 10; //değer kullanıcıdan alınacak
                $this->cache_set('cpp', $cpp);
            }
            else{
                $cpp= $cacheDataReturn['cpp'];
            }
        }
        else{
            $this->cache_set('cpp', $cpp);
        }
        */

// createQueryBuilder() automatically selects FROM AppBundle:Product
// and aliases it to "p"

        //apc_store('cpp', 2);
        //var_dump(apc_fetch('$contentPerPage'));


        $query = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.status= :status')
            ->orderBy('b.id', 'DESC')
            ->setParameter('status', 1)
            ->getQuery();
        $countOfDbItems = (int)$query->getSingleScalarResult();
        //var_dump(count($countOfDbItems));

        $totalPageNumber = ceil($countOfDbItems / $contentPerPage);

        if ($page == NULL || $page > $totalPageNumber || $page == 0) {
            $page = 1;
        }

        $getContentBaseNumber = ($page - 1) * $contentPerPage;

        //dump($totalPageNumber);
        $query = $repository->createQueryBuilder('p')
            ->where('p.status= :status')
            ->orderBy('p.id', 'ASC')
            ->setParameter('status', 1)
            ->setFirstResult($getContentBaseNumber)
            ->setMaxResults($contentPerPage)
            ->getQuery();

        $contents = $query->getResult();


        //dump($contents);
        //dump($query->getSQL());
        $id = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(blog::class);

        $deleteItem= $blogRepository->findById($id);
        $em->remove($deleteItem[0]);
        $em->flush();
        $query = $repository->createQueryBuilder('p')
            ->where('p.status= :status')
            ->orderBy('p.id', 'DESC')
            ->setParameter('status', 1)
            ->getQuery();

        $contents = $query->getResult();
        $blog= $blogRepository->findAll();


        return $this->render('Blog/Crud/crud.html.twig', [
                'username' => $this->username,

                'blog'=> $contents,
                'toplam_sayfa'=> $totalPageNumber,
                'current_page' => $page,
            ]

        );
    }

    /**
     * @BaseRoute("/new", name="create_blog_page")
     */
    public function new(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");

        // creates a task object and initializes some data for this example
        $blogClass = new Blog();
        //$blog->set('Write a blog post');
        $blogClass->setCreatedAt(new \DateTime());
        $blogClass->setUpdatedAt(new \DateTime());

        $allCategoriesRepository = $this->getDoctrine()-> getRepository(Category::class);
        $allCategoriesArr = [];
        $allCategories= $allCategoriesRepository->findAll();
        foreach ($allCategories as $category){
            $allCategoriesArr[$category->getName()]= $category->getName();
        }


        $allTagsRepository = $this->getDoctrine()-> getRepository(Tag::class);
        $allTagsArr = [];
        $allTags= $allTagsRepository->findAll();
        foreach ($allTags as $tag){
            $allTagsArr[$tag->getName()]= $tag->getName();
        }


        $form = $this->createFormBuilder($blogClass)
            ->add('title', TextType::class)

            ->add('slug', TextType::class)
            ->add('preview_img', FileType::class, array(
                'data_class' => null,
                'required' => false,
                'mapped' => false ,

            ))
            ->add('content', TextType::class)
            ->add('summary', TextType::class)
            ->add('createdAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false,

            ))
            ->add('updatedAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false
            ))
            ->add('status',  CheckboxType::class, [
                'label'    => 'Show this entry publicly?',
                'required' => false,

            ])
            ->add('category2', ChoiceType::class, [
                'choices' => $allCategoriesArr,
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
                'preferred_choices' =>  'Please select category'
            ])

            ->add('tag2', ChoiceType::class, [
                'choices' => $allTagsArr,
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
                'preferred_choices' => 'Please select tag'
            ])
            ->getForm();
/*$form->handleRequest($request);
        if ($form->isSubmitted()) {
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('edit', $submittedToken)) {
            }else{
            }*/
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('new', $submittedToken)) {

                $entityManager= $this->getDoctrine()->getManager();
                $categoryrepo= $entityManager->getRepository(Category::class);

                /** @var Category $categories */

                $categories = $categoryrepo->findByName($file = $form->get('category2')->getData());

                if($categories != NULL) {
                    $blogClass->addCategoryId($categories[0]);
                }
                else{
                    $this->error = $this->error . " Category bulunamadı! ";

                }

                $tagrepo= $entityManager->getRepository(Tag::class);

                /** @var Tag $tag */

                $tag = $tagrepo->findByName($file = $form->get('tag2')->getData());

                if($tag != NULL) {
                    $blogClass->addTagId($tag[0]);
                }
                else{
                    $this->error = $this->error . " Tag bulunamadı! ";

                }

                /** @var UploadedFile $file */
                if($file = $form->get('preview_img')->getData() != NULL){
                    $file = $form->get('preview_img')->getData();

                    $fileName= $this-> randomNameForUploadedFile() . '.' . $file->guessExtension() ;
                    $file->move($this->getParameter('afis_folder'), $fileName);

                    $blogClass->setPreviewImg($fileName);

                }





                // ... do any other work - like sending them an email, etc
                // maybe set a "flash" success message for the user
                if(empty($this->error)){

                    $entityManager->persist($blogClass);
                    $entityManager->flush();
                    return $this->redirectToRoute('crud_page');
                }
                else{

                    return $this->render('Blog/Crud/new.html.twig', [
                        'username' => $this->username,

                        'categories' => $allCategoriesArr,
                        'tags'=>$allTagsArr,
                        'form' => $form->createView(),
                        'error'=> $this->error,
                    ]);
                }
            }else{

            }

        }

        return $this->render('Blog/Crud/new.html.twig', [
            'username' => $this->username,

            'categories' => $allCategoriesArr,
            'tags'=>$allTagsArr,
            'form' => $form->createView(),
            'error'=> $this->error,
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $id = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        $blogRepository = $this->getDoctrine()-> getRepository(Blog::class);
        /** @var Blog $blog */
        $blog= $blogRepository->findById($id);

        $blogClass = new Blog();
        //$blog->set('Write a blog post');
        $blogClass->setCreatedAt($blog[0]->getCreatedAt());
        $blogClass->setUpdatedAt(new \DateTime());

        $currentDateTimeC= new \DateTime();
        $currentDateTimeU= new \DateTime();


        $allCategoriesRepository = $this->getDoctrine()-> getRepository(Category::class);
        $allCategoriesArr = [];
        $allCategories= $allCategoriesRepository->findAll();
        foreach ($allCategories as $category){
            $allCategoriesArr[$category->getName()]= $category->getName();
        }

        $allTagsRepository = $this->getDoctrine()-> getRepository(Tag::class);
        $allTagsArr = [];
        $allTags= $allTagsRepository->findAll();
        foreach ($allTags as $tag){
            $allTagsArr[$tag->getName()]= $tag->getName();
        }

        // creates a task object and initializes some data for this example
        //$form = $this->createForm(BlogType::class,$blogClass);
        $form = $this->createFormBuilder($blogClass)


            ->add('id', IntegerType::class,array(
                'attr'=>array('style'=>'display:none;'),
                'label' => false,
            ))

            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('preview_img', FileType::class, array(
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
            ))
            ->add('content', TextType::class)
            ->add('summary', TextType::class)
            ->add('createdAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false,

            ))
            ->add('updatedAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false
            ))
            ->add('status',  CheckboxType::class, [
                'label'    => 'Show this entry publicly?',
                'required' => false,

            ])
            ->add('category2', ChoiceType::class, [
                'choices' => $allCategoriesArr,
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
                'preferred_choices' =>  'Please select category'
            ])

            ->add('tag2', ChoiceType::class, [
                'choices' => $allTagsArr,
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
                'preferred_choices' => 'Please select tag'
            ])
            ->getForm();

        $form->get('id')->setData((int) $blog[0]->getId());
        $form->get('title')->setData($blog[0]->getTitle());
        $form->get('slug')->setData($blog[0]->getSlug());
        $form->get('content')->setData($blog[0]->getContent());
        $form->get('summary')->setData($blog[0]->getSummary());
        $form->get('status')->setData($blog[0]->getStatus());

        if($blog != NULL) {
            foreach ($blog as $blogs) {
                foreach ($blogs->getCategoryId() as $blogcat){
                    $this->definedCat= $blogcat->getName();
                    $form->get('category2')->setData($blogcat->getName());
                }
            }
            foreach ($blog as $blogs) {
                foreach ($blogs->getTagId() as $blogcat){
                    $this->definedTag= $blogcat->getName();

                    $form->get('tag2')->setData($blogcat->getName());
                }
            }
        }
        else{
            $this->error = $this->error . " Tag veya Category bulunamadı! ";
        }


            $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            $submittedToken = $request->request->get('token');
            // 'delete-item' is the same value used in the template to generate the token
            if ($this->isCsrfTokenValid('edit', $submittedToken)) {
                $entityManager= $this->getDoctrine()->getManager();

                $categoryrepo= $entityManager->getRepository(Category::class);

                /** @var Category $categories */

                $categories = $categoryrepo->findByName($file = $form->get('category2')->getData());

                if($categories != NULL) {
                    $blogClass->addCategoryId($categories[0]);

                }
                else{
                    $this->error = $this->error . " Category bulunamadı! ";

                }

                $tagrepo= $entityManager->getRepository(Tag::class);

                /** @var Tag $tag */

                $tag = $tagrepo->findByName($form->get('tag2')->getData());

                if($tag != NULL) {
                    $blogClass->addTagId($tag[0]);

                }
                else{
                    $this->error = $this->error . " Tag bulunamadı! ";

                }
                //$error = $authenticationUtils->getLastAuthenticationError();

                if($file = $form->get('preview_img')->getData() != NULL) {

                    $file = $form->get('preview_img')->getData();
                    $extension= $file->guessExtension();
                    if($extension == "jpeg" or $extension=="jpg" or $extension == "png"){
                        $fileName = $this->randomNameForUploadedFile() . '.' . $extension;
                        $file->move($this->getParameter('afis_folder'), $fileName);
                        $blogClass->setPreviewImg($fileName);

                    }else{
                        $this->error = $this->error . "Dosya uzantısı yanlış! Yalnız jpeg, jpg ve png uzantılı dosyalar yüklenebilir. ";
                    }

                }
                else{
                    $blogClass->setPreviewImg($blog[0]->getPreviewImg());

                }

                $categories = $categoryrepo->findByName($file = $form->get('category2')->getData());
                $tag = $tagrepo->findByName($file = $form->get('tag2')->getData());



                if(empty($this->error)){

                    $blogClass->setId($blog[0]->getId());
                    $em->remove($blog[0]);
                    $em->flush();

                    $em->persist($blogClass);
                    $em->flush();
                    return $this->redirectToRoute('crud_page');
                }
                else{

                    return $this->render('Blog/Crud/edit.html.twig', [
                        'username' => $this->username,

                        'categories' => $allCategoriesArr,
                        'tags'=>$allTagsArr,
                        'definedCat'=> $this->definedCat,
                        'definedTag' => $this->definedTag,
                        'blog'=> $blog,
                        'form' => $form->createView(),
                        'error'=> $this->error,
                    ]);
                }


            }else{
                throw new BadRequestHttpException(self::TOKEN_INVALID);
            }

        }

        return $this->render('Blog/Crud/edit.html.twig', [
            'username' => $this->username,

            'categories' => $allCategoriesArr,
            'tags'=>$allTagsArr,
            'definedCat'=> $this->definedCat,
            'definedTag' => $this->definedTag,
            'blog'=> $blog,
            'form' => $form->createView(),
            'error'=> $this->error,
        ]);
    }


}
