<?php

namespace App\Controller\Storefront;


use App\Entity\Blog;
use App\Entity\Category;
use App\Repository\BlogRepository;
use App\Service\GetUser;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route as BaseRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\BlogType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\Query\ResultSetMapping;

class BlogController extends AbstractController
{




    private $username= "";

    public function __construct(GetUser $ServiceUsername )
    {
        $this->username = $ServiceUsername->getCurrentName();
    }


    const CPP = "cpp";

    private $entityManager;

    public function cache_set($key, $val)
    {
        $val = var_export($val, true);
        // HHVM fails at __set_state, so just use object cast for now
        $val = str_replace('stdClass::__set_state', '(object)', $val);
        // Write to temp file first to ensure atomicity
        $tmp = "/tmp/$key." . uniqid('', true) . '.tmp';
        file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);
        rename($tmp, "/tmp/$key");
    }

    public function cache_get($key)
    {
        @include "/tmp/$key";
        return isset($val) ? $val : false;
    }
    /**
     * @BaseRoute("/newtemplate", name="newtemplate")
     */
    public function newTemp(EntityManagerInterface $entityManager, Security $security)
    {
        return $this->render('Blog/Storefront/newTemplate.html.twig',[
            'username' => $this->username,

        ]);
    }


    /**
     * @BaseRoute("/", name="homepage")
     */
    public function indexAction(EntityManagerInterface $entityManager, Security $security)
    {
        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'dbname' => 'blog_project',
            'user' => 'root',
            'password' => 'kadir',
            'host' => '127.0.0.1:3306',
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        /*$qb = $conn->createQueryBuilder();
        $qb
            ->select('*')
            ->from('blog_category')
            ->where('1=1')
            ->where('u.id = :identifier')
            ->orderBy('u.name', 'ASC')
            ->setParameter('identifier', 100);
        ;
        $query = $qb->execute();
        $blog_categoryCount= $query->rowCount();
*/

        $allCategoriesRepository = $this->getDoctrine()-> getRepository(Category::class);

        $allCategories= $allCategoriesRepository->findAll();
        $allCategoriesArr = [];

        $repository = $this->getDoctrine()->getRepository(Category::class);

        foreach ($allCategories as $cat){
            dump("id'si : " . $cat->getId());
            $qb = $conn->createQueryBuilder();
            $qb
                ->select('*')
                ->from('blog_category', 'b')
                ->where('b.category_id = :catid')
                ->setParameter('catid', $cat->getId());
            ;
            $query = $qb->execute();

            $allCategoriesArr[$cat->getName()] = $blog_categoryCount= $query->rowCount();
            dump("sayisi : ". $blog_categoryCount);

        }
        dump($allCategoriesArr);

        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");
        /*$blogRepository = $this->getDoctrine()->getRepository(blog::class);
        $blog = $blogRepository->findById(100);*/
        $this->entityManager = $entityManager;

        $repository = $this->getDoctrine()->getRepository(Blog::class);
        $query = $repository->createQueryBuilder('p')
            ->where('p.status= :status')
            ->orderBy('p.id', 'ASC')
            ->setParameter('status', 1)
            ->setFirstResult(0)
            ->setMaxResults(5)
            ->getQuery();

        $contents = $query->getResult();


        return $this->render('Blog/Storefront/homePage.html.twig', [
                'categories'=>$allCategoriesArr,
                'username' => $this->username,
                'blog' => $contents,
            ]

        );
    }

    /**
     * @BaseRoute("/blogcontent", name="bContent")
     */
    public function showContent(EntityManagerInterface $entityManager, Request $request )
    {


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

        //dump($contents);
        //dump($query->getSQL());


        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Buraya erisim hakkiniz bulunmamaktadir");

        $blogRepository = $this->getDoctrine()->getRepository(blog::class);
        $blog = $blogRepository->findAll();
        return $this->render('Blog/Storefront/showBlog.html.twig', [
                'username' => $this->username,
                'blog' => $contents,
                'toplam_sayfa'=> $totalPageNumber,
                'current_page' => $page,
            ]
        );
    }

    /**
     * @BaseRoute("/blogcontent/{id}", name= "single_entry_show")
     */
    public function singeleEntryShow($id, GetUser $ServiceUsername)
    {
        $content = $this->getDoctrine()
            ->getRepository(Blog::class)
            ->find($id);

        if (!$content) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }
        return $this->render('Blog/Storefront/singleBlogContent.html.twig', [
                'username' => $this->username,
                'blog' => $content,
            ]

        );
    }


}
