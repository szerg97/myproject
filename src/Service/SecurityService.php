<?php

namespace App\Service;



use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityService
{
    /** @var EntityManagerInterface  */
    private $em;

    /** @var UserPasswordEncoderInterface  */
    private $encoder;

    /**
     * SecurityService constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function registerUser(string $email, string $clearPass, string $firstname, string $lastname): void{
        $user = new User();
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->encoder->encodePassword($user, $clearPass));
        $this->em->persist($user);
        $this->em->flush();
    }

    public function checkPassword(string $email, string $clearPass): bool{
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(["email" => $email]);
        if(!$user) return false;
        return $this->encoder->isPasswordValid($user, $clearPass);
    }
}