<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BlogFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 487; $i++) {
            $blog = new Blog();
            $blog->setSummary('Blog Summary '.$i);
            $blog->setPreviewImg('blog-intro2.png');
            $blog->setSlug('Blog Slug '.$i);
            $blog->setContent('Blog Content '.$i);
            $blog->setCreatedAt(new \DateTime());
            $blog->setUpdatedAt(new \DateTime());
            $blog->setTitle('Blog Title '.$i);
            $blog->setStatus(1);

            $manager->persist($blog);
            $manager->flush();

        }

    }

}
