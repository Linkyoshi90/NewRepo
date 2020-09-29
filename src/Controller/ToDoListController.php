<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="to_do_list")
     */
    public function index() {
        /*
        * I want to define the order in a method in the future, 
        */
        $order = 0;
        if (!$order) {
            $order = 'DESC';
        }
        $repo = $this->getDoctrine()
            ->getRepository(Task::class);
        /* 
        * Query searches for status and id in Task
        */
        $query = $repo->createQueryBuilder('t')
            ->orderBy('t.status', 'ASC', 't.id', $order)
            ->getQuery();
        if (!$query) {
            throw $this->createNotFoundException('Not found...');
        }
        return $this->render('index.html.twig', ['tasks'=>$query->execute()]);
    }
    
    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request) {
        $title = trim($request->request->get('title'));
        if (empty($title)) {
            return $this->redirectToRoute('to_do_list');
        }
        /*
        This code throws errors, so I commented it out
        $whereStatement = 't.title = ' . $title;
        $repo = $this->getDoctrine()
            ->getRepository(Task::class);
        $query = $repo->createQueryBuilder('t')
            ->where('t.title = :title')
            ->setParameter('title', $title)
            ->getQuery();
        $queryResult = $query->execute();
        if ($queryResult != $title) {
            $entityManager = $this->getDoctrine()->getManager();
            $task = new Task;
            $task->setTitle($title);
            $entityManager->persist($task);
            $entityManager->flush();
        }
        else {
            throw Exception("Already exists");
        }
        */
        $entityManager = $this->getDoctrine()->getManager();
        $task = new Task;
        $task->setTitle($title);
        $entityManager->persist($task);
        $entityManager->flush();
        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(! $task->getStatus() );
        $entityManager->flush();
        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/order-switch", name="order_switch")
     */
    public function orderSwitch() {
        if ($order == 'DESC') {
            $order = 'ASC';
        }
        else {
            $order = 'DESC';
        }
        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="task_delete")
     */
    public function delete(int $id) {
        $entityManager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()
            ->getRepository(Task::class);
        $query = $repo->createQueryBuilder('t')
            ->delete('Task', 't')
            ->where('t.id = :id')
            ->setParameter('id', $id);
        $query->execute();
        // Attempted to call an undefined method named "execute" of class "Doctrine\ORM\QueryBuilder".
        return $this->redirectToRoute('to_do_list');
    }
}
