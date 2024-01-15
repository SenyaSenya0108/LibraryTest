<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use App\Services\ParserBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ContactController extends AbstractController
{
    public function __construct(private readonly MailerInterface $mailer, private ParserBook $parserBook)
    {
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contactMessage);
            $entityManager->flush();

            $email = (new Email())
                ->from('senya01088@gmail.com')
                ->to('senyasenya010896@gmail.com')
                ->subject('Subject of the email')
                ->text('Body of the email');
            $this->mailer->send($email);

            $this->addFlash('success', 'Your message has been sent successfully.');

            return $this->redirectToRoute('main');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }
}
