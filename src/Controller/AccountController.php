<?php

namespace App\Controller;


use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     *
     */
    public function login(AuthenticationUtils $utils)
    {
        $error =  $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();


        return $this->render('account/login.html.twig' , [
            'hasError' => $error !== null ,
            'username' => $username
        ]);

    }

    /**
     * @Route( "/logout" , name="account_logout")
     */
    public function logout()
    {

    }
}
