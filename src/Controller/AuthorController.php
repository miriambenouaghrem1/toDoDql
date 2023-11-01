<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/author')]

class AuthorController extends AbstractController
{
    public function __construct(ManagerRegistry $mr)
    {
        
    }
    #[Route('/', name: 'app_author')]
    public function index(AuthorRepository $authorRepository ): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $authorRepository->findAll(),
        ]);
    }
    #[Route('/show', name: 'showauth', methods: ['GET'])]
  public function fetchStudent(AuthorRepository $repo,ManagerRegistry $mr ){
//$authors=$repo->findAll();
$authors=$mr->getRepository(Author::class);
return $this->render('author/show.html.twig',[
    'authors'=>$repo->findAll()
]);
    }

    #[Route('/add', name: 'add')]
    public function AddStudent(ManagerRegistry $em ){
$auth=new Author();
$manager=$em->getManager();
$auth->setName('ali');
$auth->setEmail('test');
$auth->setNbbooks(300);
$manager->persist($auth);
$manager->flush();
return new Response('added');
    }
    #[Route('/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $mr,): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$mr->getManager();

        $em->persist($author);
        $em->flush();
        }
        return $this->renderForm('author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }
      
    #[Route("/{id}", name: "auth_delete",methods: ['POST'])]
    
    public function delete(Author $auth,ManagerRegistry $mr): Response
    {
        // Check if the computer exists
        if (!$auth) {
            throw $this->createNotFoundException('auth not found');
        }
        // Remove the computer from the database
        
        $em = $mr->getManager();

        $em->remove($auth);
        $em->flush();

        // Optionally, redirect to a success page or return a response
        return $this->redirectToRoute('email'); // Replace with your route
    }
    #[Route("/byemail", name: "email")]

    public function listAuthorByEmail(AuthorRepository $authRepo){
       $result=$authRepo->listAuthorByEmail();
       return $this->renderForm('author/listAuthorByEmail.html.twig', [
        'auth'=>$result,]);
    }
    #[Route("/edit/{id}", name: "auth_update")]
public function editAuthor(Author $author, EntityManagerInterface $em, Request $request): Response

{
    if (!$author) {
        throw $this->createNotFoundException('Author not found');
    }

    // Create a form for editing the author
    $form = $this->createForm(AuthorType::class, $author);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Get the new number of books from the form
        $newNumberOfBooks = $form->get('nbbooks')->getData();

        // Use Query Builder to update the number of books for the specific author
        $qb = $em->createQueryBuilder();
        $qb->update(Author::class, 'a')
            ->set('a.nbbooks', ':newNumberOfBooks')
            ->where('a.id = :authorId')
            ->setParameter('newNumberOfBooks', $newNumberOfBooks)
            ->setParameter('authorId', $author->getId())
            ->getQuery()
            ->execute();

        return $this->redirectToRoute('show'); // Redirect to the show page or another appropriate route
    }
    return $this->render('author/edit.html.twig', [
        'form' => $form->createView(),
        'author' => $author,
    ]);
}
#[Route("/minmax", name: "minmax")]

public function minMaxBooks(Request $request,AuthorRepository $rep){
    $minvalue = $request->query->get('minvalue');
    $maxvalue = $request->query->get('maxvalue');

    $result=$rep->minMax($minvalue,$maxvalue);
    return $this->renderForm('author/minmax.html.twig', [
        'authors' => $result,
        'minvalue'=>$minvalue,
        'maxvalue'=>$maxvalue
    ]);
}
#[Route("/bookzero", name: "bookzero")]

public function deleteauthbooks(AuthorRepository $rep,EntityManagerInterface $entityManager){
    $res=$rep->authbook( $entityManager);
    return $this->redirectToRoute('showauth'); // Replace with your route

}

    }

