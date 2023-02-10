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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
* @IsGranted("IS_AUTHENTICATED_FULLY") 
*/
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
    #[Route('/add', name: 'addclassroom')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $classroom = new Classroom() ;  
        $form = $this->createForm(ClassroomType::class,$classroom) ;
        $form->add('add',SubmitType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted()) {
            $em = $doctrine->getManager() ;
            $em->persist($classroom) ;
            $em->flush() ;
            return $this->redirectToRoute("read") ;
        }

        return $this->renderForm('classroom/add.html.twig', [
           "f" => $form,
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
    #[Route('/search', name: 'search')]
    public function search(ClassroomRepository $repo, Request $request): Response 
    {
        $classrooms = $repo
        ->findAll() ;
        if($request->isMethod("post"))
        { 
            $name = $request->get('name') ;
            $classroom = $repo->findByName($name) ;
            if($name != '')
            {return $this->render('classroom/search.html.twig', [
                "classroom" => $classroom,
             ]);
            }
            else {
               
                return $this->render('classroom/read.html.twig', [
                    "classrooms" => $classrooms
                ]);
            }
        }
        else {
            return $this->render('classroom/read.html.twig', [
                "classrooms" => $classrooms
            ]);
        }
       
      
    }
}
