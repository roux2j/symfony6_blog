<?php

namespace App\Controller\Visitor\Contact;

use App\Form\ContactType;
use App\Service\SendEmailService;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'visitor.contact.index')]
    public function index(Request $request, MailerInterface $mailer, SendEmailService $sendEmailService): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('roux2.j@orange.fr')
                ->subject('vous avez reçu un email de '.$contactFormData['nom'])
                ->text(
                    $contactFormData['message'],
                    'text/plain');
            $mailer->send($message);

            $this->addFlash('success', 'Vore message a été envoyé');

            return $this->redirectToRoute('visitor.contact.index');
        }

        return $this->render('page/visitor/contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
