<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();


        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);

    }

    /**
     * @Route( "/logout" , name="account_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/register" , name="account_registration")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */


    public function registration(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
// commentaire
        //other comment
        //comment
        //an other comment
        // ajout de commenataire par la branche commenataire
        $slugify = new Slugify();
        $slug = $slugify->slugify($user->getFirstName(), '-');

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $user->setSlug($slug);
            $user->setHash($encoder->encodePassword($user, $user->getHash()));
            $manager->flush();
            $this->addFlash('success', 'bravo '.$user->getFirstName().' vous êtes inscris sur le site ! ');
            return $this->redirectToRoute("account_login" , [
                'user' => $user
            ]);

        }

        return $this->render("account/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

}
