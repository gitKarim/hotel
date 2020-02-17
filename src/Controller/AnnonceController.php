<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Component\Form\FormView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Cocur\Slugify\Slugify;


class AnnonceController extends AbstractController
{


    /**
     * @Route("/", name="accueil")
     * @param AnnonceRepository $repo
     * @return Response
     */
    public function index(AnnonceRepository $repo )
    {

        $myannonce = $repo->findAll();


        // une fonction dans un controller doit obligatoirement retourné une réponse ( instance de Response)
        return $this->render('annonce/index.html.twig', [
            'myannonce' => $myannonce
        ]);

    }

    /**
     * pemet de créer une annonce
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @Route("/new" , name="new_annonce")
     * @return Response
     */

    public function create(Request $request, EntityManagerInterface $manager)
    {
        $annonce = new Annonce();

         $image = new Image();
        $image->setUrl('http://placehold.it/300X400') ;
        $image->setCaption('coucou');
        $image->setName('nom') ;
        $annonce->addImage($image);
        $image2 = new Image() ;
        $image2->setUrl('http://placehold.it/30àx400') ;
        $image2->setCaption('deuxieme coucou') ;
        $image2->setName('nom') ;

        $annonce->addImage($image2);

        $form = $this->CreateForm(AnnonceType::class, $annonce);
      ;
        //  pour traiter les données du formulaire , avant de persister
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($annonce->getImages() as $image){
                $image->setAnnonce($annonce) ;
                $manager->persist($image);
            }

            foreach ($annonce->getImages() as $image2){
                $image2->setAnnonce($annonce) ;
                $manager->persist($image2);
            }
           // by Karim aha :)
            $slugify = new Slugify() ;
            $annonce->setSlug($slugify->slugify($annonce->getTitle() , '_')) ;

            $manager->persist($annonce);
            $manager->flush();

            $this->addFlash(
                'success',' vous avez bien soumis l\'annonce '
            );
            return $this->redirectToRoute('annonce_show_slug', [
                'slug' => $annonce->getSlug()

            ]);
        }


        return $this->render('annonce/new.html.twig', [
            'formAnnonce' => $form->createView()
        ]);
    }


    /**
     * @param Annonce $annonce
     * @Route("/{slug}" , name="annonce_show_slug")
     * @return Response
     */

    public function show(Annonce $annonce)
    {
        // ligne désactiver suite a l'utilisation de paramconverter , aussi $slug enlevé comme parametre
        // $annonce = $repo->findOneBySlug($slug);

            return $this->render('annonce/show.html.twig' , [
                'annonce' => $annonce
            ]) ;


    }


    /**
     *
     * @Route("{slug}/edit" , name="annonce_edition")
     * @param Annonce $annonce
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    public function edit (Annonce $annonce,  Request $request , EntityManagerInterface $manager )
    {
        // pour l'edition , on crée pas l'annonce , mais ol la recois , c'est pour ca ca sera injecté dans la fonction
        // edit
        // $annonce = new Annonce();
        $form = $this->CreateForm(AnnonceType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // by Karim aha :)
            $slugify = new Slugify() ;
            $annonce->setSlug($slugify->slugify($annonce->getTitle() , '_')) ;

            $manager->persist($annonce);
            $manager->flush();

            $this->addFlash(
                'success',' vous avez bien modifé l\'annonce '
            );
            return $this->redirectToRoute('annonce_show_slug', [
                'slug' => $annonce->getSlug()

            ]);
        }
  return $this->render('annonce/edit.html.twig' , [
    'formAnnonce' => $form->createView() ,
    'annonce'=> $annonce
]) ;

   }
}
