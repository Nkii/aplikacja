<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends AbstractBaseFixtures
{
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(10,'users',function ($i){
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([User::ROLE_USER]);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'user1234'
                )
            );
            return $user;
        });

        $this->createMany(10,'admins', function ($i){
            $admin = new User();
            $admin->setEmail(sprintf('admin%d@example.com', $i));
            $admin->setRoles([User::ROLE_USER,User::ROLE_ADMIN]);
            $admin->setPassword(
                $this->passwordEncoder->encodePassword(
                    $admin,
                    'admin1234'
                )
            );
            return $admin;
        });

        $this->manager->flush();
    }


}