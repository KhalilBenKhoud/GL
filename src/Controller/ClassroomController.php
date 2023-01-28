<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
use App\Form\ClassroomType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClassroomController extends AbstractController
{
    #[Route('/', name: 'read')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $classrooms = $doctrine
        ->getRepository(Classroom::class)
        ->findAll() ;
        return $this->render('classroom/read.html.twig', [
            "classrooms" => $classrooms
        ]);
    }
    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request ,ManagerRegistry $doctrine, $id ): Response 
    {
        $classroom = $doctrine->getRepository(Classroom::class)->find($id);
        $form = $this->createForm(ClassroomType::class,$classroom) ;
        $form->add('update',SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted())
        {
            $em = $doctrine->getManager() ;
            $em->flush() ;
            return $this->redirectToRoute("read") ;
        }
        return $this->renderForm('classroom/update.html.twig', [
            "f" => $form,
         ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Request $request ,ManagerRegistry $doctrine, $id ): Response 
    {
        $classroom = $doctrine->getRepository(Classroom::class)->find($id);
        $em = $doctrine->getManager() ;
        $em->remove($classroom) ;
        $em->flush() ;
        return $this->redirectToRoute("read") ;
    }
}
