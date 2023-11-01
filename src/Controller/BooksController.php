<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\BookType;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/book')]


class BooksController extends AbstractController

{  
    private EntityManagerInterface $entityManager;  // Inject the EntityManager

   
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/books', name: 'app_books')]
    public function index(): Response
    {
        return $this->render('books/index.html.twig', [
            'controller_name' => 'BooksController',
        ]);
    }
    #[Route("/list", name: "book_list")]
    public function list(Request $request, BookRepository $rep): Response {
        $searchTerm = $request->query->get('search');
        $result = $rep->findBySearchTerm($searchTerm);
    
        return $this->render('books/show.html.twig', [
            'search' => $searchTerm, // Pass the search term as a variable
            'books' => $result,
        ]);
    }
    
    #[Route('/show', name: 'show')]
    public function fetchStudent(BookRepository $repo,ManagerRegistry $mr ){
//$authors=$repo->findAll();
$books=$mr->getRepository(Book::class);
return $this->render('books/show.html.twig',[
    'books'=>$books->findAll()
]);
    }
    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,AuthorRepository $repauth): Response
    {
        
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        
        $authorBooks = $form->get('Authors')->getData();
        $auth=$repauth->findOneById($authorBooks);
        if ($authorBooks !== null) {
        $res=$auth->getNbbooks(); 
        $auth->setNbbooks($res+1); 
        }
        if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($book);
        $entityManager->flush();
        }
        return $this->renderForm('books/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
        return $this->redirectToRoute('show'); // Replace with your route

    }
      
    #[Route("delete/{id}", name: "book_delete",methods: ['POST'])]
    
    public function delete(Book $book,ManagerRegistry $mr): Response
    {
        // Check if the computer exists
        if (!$book) {
            throw $this->createNotFoundException('book not found');
        }
        // Remove the computer from the database
        
        $em = $mr->getManager();

        $em->remove($book);
        $em->flush();

        // Optionally, redirect to a success page or return a response
        return $this->redirectToRoute('show'); // Replace with your route
    }
    #[Route("/byauthory", name: "byauthory")]

    public function booksListByAuthor(BookRepository $rep,Request $request,ManagerRegistry $mr):Response
        {
            $searchTerm = $request->query->get('search');
            $result = $rep->findBySearchTermbyName($searchTerm);
        
            return $this->render('books/listBynameauth.html.twig', [
                'search' => $searchTerm, // Pass the search term as a variable
                'books' => $result,
            ]);
        }


#[Route("/byauthorid", name: "by_authorid")]

public function booksListById(BookRepository $rep,Request $request,ManagerRegistry $mr):Response
    {
        $searchTerm = $request->query->get('search');
        $result = $rep->findBySearchTermbyId($searchTerm);
    
        return $this->render('books/listByIdAuth.html.twig', [
            'search' => $searchTerm, // Pass the search term as a variable
            'books' => $result,
        ]);
    }
#[Route("/modify", name: "by_cat")]

public function modifyCat(BookRepository $rep){
     $rep->modifyCat("Mystery","romance");

        return $this->redirectToRoute('show'); // Replace with your route

}
#[Route("/countByCategroy/{category}", name: "countCategory")]
public function countByCategory(BookRepository $rep,$category){
$result= $rep->countByCat($category);
return $this->render('books/count.html.twig', [
       'result'=>$result,  
    'catName'=>$category  ]);

}
#[Route("/showBooksBetweenDates", name: "showBooksBetweenDates")]
public function showBooksBetweenDates (BookRepository $rep)
{
    $result=$rep->showBooksBetweenDates('2017-01-01','2017-02-01');
    return $this->render('books/dates.html.twig', [
        'books' => $result,
    ]);}
    #[Route('/edit/{id}', name: 'edit_book')]
    public function editBook(Request $request, int $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('show'); // Replace with your list route
        }

        return $this->render('books/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}