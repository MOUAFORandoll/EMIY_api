<?php

namespace App\Controller;

use App\Entity\TypeUser;
use App\Entity\UserPlateform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserCreateController
{
    private $passwordHasher;
    private $em;
    public function __construct(EntityManagerInterface $em,   UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    public function __invoke(UserPlateform $data)
    {
        $password = $this->passwordHasher->hashPassword(
            $data,
            $data->getPassword()
        );
        $data->setPassword($password);
        
        $this->em->flush();
        return $data;
    }
}
