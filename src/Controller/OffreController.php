<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Oeuvrage;
use App\Entity\Offre;
use App\Entity\User;
use App\Form\OffreType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/offre")
 */
class OffreController extends AbstractController
{
    /**
     * @Route("/", name="offre_index", methods={"GET"})
     */
    public function index(): Response
    {
        $offres = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->findAll();

        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    /**
     * @Route("/new", name="offre_new", methods={"GET","POST"})
     */
    /*
     *
     */
    public function new(Request $request): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //$user = $entityManager->find(User::class, 1);

           // $offre->setUser($user);

            $nb = $offre->getNbClient();
            $x=$offre-> getX();
            $oeuvrage=$entityManager->find(Oeuvrage::class, 17);
            $offre ->setOeuvrage($oeuvrage);

            if($x === "fidèles clients") {
               $users =$this->getDoctrine()
                   ->getRepository(Commande::class)
                   ->findfc();
            }
            elseif($x === "nouveaux utilisateurs") {
               $users =$this->getDoctrine()
                   ->getRepository(User::class)
                   ->findBy(
                       array(),
                       array('userId' => 'DESC'),
                         $nb);
            }
            elseif($x === "anciens utilisateurs") {
                $users =$this->getDoctrine()
                    ->getRepository(User::class)
                    ->findBy(
                        array(),
                        array('userId' => 'ASC'),
                        $nb);
            }
                for ($i =0 ; $i < $nb ; ++$i) {
                     $u = $users[$i] ;
                      $offre ->setUser($u);
                     $entityManager->persist($offre);
                     $entityManager->flush();
                   $entityManager->clear(Offre::class);
            }
            return $this->redirectToRoute('offre_index');
        }



        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{offreId}", name="offre_show", methods={"GET"})
     */
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    /**
     * @Route("/{offreId}/edit", name="offre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offre $offre): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offre_index');
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{offreId}", name="offre_delete", methods={"POST"})
     */
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getOffreId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offre_index');
    }
}
