<?php

namespace MailboxBundle\Controller;

use MailboxBundle\Entity\Email;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Email controller.
 *
 * @Route("email")
 */
class EmailController extends Controller
{
    /**
     * Lists all email entities.
     *
     * @Route("/", name="email_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $emails = $em->getRepository('MailboxBundle:Email')->findAll();

        return $this->render('MailboxBundle:email:index.html.twig', array(
            'emails' => $emails,
        ));
    }

    /**
     * Creates a new email entity.
     *
     * @Route("/new/{personId}", name="email_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $personId)
    {
        $personRepo = $this->getDoctrine()->getRepository('MailboxBundle:Person');
        $person = $personRepo->find($personId);
        
        $email = new Email();
        $form = $this->createForm('MailboxBundle\Form\EmailType', $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $email->setPerson($person);
            $em->persist($email);
            $em->flush($email);

            return $this->redirectToRoute('email_show', array('id' => $email->getId()));
        }

        return $this->render('MailboxBundle:email:new.html.twig', array(
            'personId' => $personId,
            'email' => $email,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays an email entity.
     *
     * @Route("/{id}", name="email_show")
     * @Method("GET")
     */
    public function showAction(Email $email)
    {

        return $this->render('MailboxBundle:email:show.html.twig', array(
            'email' => $email,
        ));
    }

    /**
     * Displays a form to edit an existing email entity.
     *
     * @Route("/{id}/edit", name="email_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Email $email)
    {
        $editForm = $this->createForm('MailboxBundle\Form\EmailType', $email);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('person_show', array('id' => $email->getPerson()));
        }

        return $this->render('MailboxBundle:email:edit.html.twig', array(
            'email' => $email,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes an email entity.
     *
     * @Route("/{id}/delete", name="email_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $emailRepo = $this->getDoctrine()->getRepository('MailboxBundle:Email');
        $email = $emailRepo->find($id);
        $person = $email->getPerson();
        $personId = $person->getId();
            $em = $this->getDoctrine()->getManager();
            $em->remove($email);
            $em->flush($email);

        return $this->redirectToRoute('person_show', array('id' => $personId));
    }
}
