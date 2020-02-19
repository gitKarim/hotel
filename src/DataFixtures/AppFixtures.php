<?php

namespace App\DataFixtures;


use App\Entity\Annonce;
use App\Entity\Image;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder ;


    public function __construct(UserPasswordEncoderInterface $encoder ){

    $this->encoder = $encoder ; 
   }


    public function load(ObjectManager $manager )
    {


        // ce que nous voulons faire , c'est de créer des utilisateurs et lier a chacun d'eux au moins une annonce .
        // NON : faux  ! nous allons créer des annonces , et ces annonces seront liéer a certains utilisateurs. ,
        // et pas forcement a tous les utilisateurs . a demain inchallah :)
        // liaison =>         controller === entityManager ===> repository
        // c'est a dire ,faire une boucle sur les utilisateurs , et à l'interieur le code que vous avions avant .

        $faker = Factory::create('fr-FR');




        // Création des utilisateurs
        $users = [];
        for ($u = 1; $u <= 11; $u++) {

            $number = random_int(1 , 99);
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName);
            $user->setIntroduction($faker->paragraph(2));
            $user->setdescription($faker->paragraph(5));
            $user->setPicture( 'https://randomuser.me/api/portraits/women/'.$number.'.jpg');
            $user->setEmail($faker->email);
            $user->setHash($this->encoder->encodePassword($user, 'password' ));
            $user->setSlug($faker->firstName() . '-' . $faker->lastName);

            $manager->persist($user);
            $users[] = $user ;
        }



        // création des annonces
            for ($i = 1; $i <= 30; $i++) {
                $annonce = new Annonce();
                $title = $faker->sentence(2);
                $slugify = new Slugify();
                $slug = $slugify->slugify($title);
                $annonce->setTitle($title);
                $annonce->setSlug($slug);
                $annonce->setIntroduction($faker->paragraph(1));
                $annonce->setContent($faker->paragraph(10));
                $annonce->setPrice(mt_rand(20, 230));
                $annonce->setCoverImage($faker->imageUrl(300, 300));
                $annonce->setAuthor($users[mt_rand(0 , count($users) - 1)]);
                $manager->persist($annonce);


                for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                    $image = new Image();
                    $image->setName($faker->sentence(1));
                    $image->setUrl($faker->imageUrl());
                    $image->setCaption($faker->sentence(2));
                    $image->setAnnonce($annonce);
                    $manager->persist($image);


                }

                $manager->persist($annonce);
            }




        $manager->flush();


    }

}
/* pour hacher un mot de passe :
    tout d'abord on a utilisé la classe UserPasswordEncoderInterface de cette maniére :
        $encoder = new UserPasswordInterface
            dans l'objet $encoder on utilise la méthode encodePassword() qui prends deux paramètre ,
            le premier c'est l'obejt sur le quel on travail , et on deuxième paramètre le mot de passe
            à hacher.
    aprèes on rencontrer une erreur de nn implémentation de l'interface UserInterface , ce qu'on fais
    dans l'entity user ( 5 méthodes de la calsse Userinterface à implémenter , les plus importantes :
        getpassword() et getusername , les trois autres sont : getsalt() , esarecreditials() ,getRoles())