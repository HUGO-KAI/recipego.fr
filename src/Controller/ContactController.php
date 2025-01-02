<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $email = (new TemplatedEmail())
                    ->from('hello@example.com')
                    ->to($contact->getService())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject('Time for Symfony Mailer!')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->locale('fr')
                    ->context([
                        'contact' => $contact
                    ]);

                $mailer->send($email);
                $this->addFlash(
                    'success',
                    'Votre message a bien été envoyé au ' . $contact->getService() . ' !'
                );
                return $this->redirectToRoute('contact.index');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash(
                    'danger',
                    "Impossible d'envoyer votre mail !"
                );
            }
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }
}
