<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {


        $user = new User();
        $user->setName('admin');
        $plainPassword = 'kadir12';
        $password = $this->encoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        $user->setEmail('kadiryapici60@gmail.com');
        $user->setAuthority("admin");
        $manager->persist($user);
        $manager->flush();
    }

}
