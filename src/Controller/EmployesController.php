<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EmployeRepository $repo): Response
    {
        $employes = $repo->findAll();

        return $this->render('employes/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    /**
     * @Route("/add", name="employe_create")
     * @Route("/update/{id}", name="employe_edit")
     */
    public function form(Request $superGlobals, EntityManagerInterface $manager, Employe $employe = null)
    {
        if (!$employe) {
            $employe = new Employe();
            $messageForm = "L'employeur a bien été crée !";
        } else {
            $messageForm = "L'employeur n° ".$employe->getId()." a bien été modifié !";
        }

        // CREATEFORM permet de récupérer un formulaire existant #}
        $form = $this->createForm(EmployeType::class, $employe);

        // HandleRequest permet d'insérer les données du formulaire dans l'objet $article
        //Elle permet aussi de faire des vérifications sur le formulaire
        $form->handleRequest($superGlobals);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($employe);
            $manager->flush();
            $this->addFlash('success', $messageForm);
            return $this->redirectToRoute('index', [
                'id' => $employe->getId()
            ]);
        }

        return $this->renderForm("employes/form.html.twig", [
            'formemploye' => $form,
            'editMode' => $employe->getId() !== NULL
        ]);

        // 2nd méthode d'envoyer un formulaire à un template

        // return $this-.render('blog/form.html.twig', [
        //     'formArticle' => $form->createView()
        // });           
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show($id, EmployeRepository $repo): Response
    {
        $employe = $repo->find($id);
        return $this->render('employes/show.html.twig', [
            'employe' => $employe
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(EntityManagerInterface $manager, $id, EmployeRepository $repo)
    {
        $employe = $repo->find($id);

        $manager->remove($employe);

        $manager->flush();

        // addFlash() permet de créer un message de notification
        // Le 1er argument est le type du message que l'on veut
        // Le 2nd argument est le message
        $this->addFlash('success', "L'employe n° $id a bien été supprimé !");

        return $this->redirectToRoute('index');
    }
}
