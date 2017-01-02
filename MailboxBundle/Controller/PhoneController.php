<?php

namespace MailboxBundle\Controller;

use MailboxBundle\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Phone controller.
 *
 * @Route("phone")
 */
class PhoneController extends Controller
{
    /**
     * Lists all phone entities.
     *
     * @Route("/", name="phone_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $phones = $em->getRepository('MailboxBundle:Phone')->findAll();

        return $this->render('MailboxBundle:phone:index.html.twig', array(
            'phones' => $phones,
        ));
    }

    /**
     * Creates a new phone entity.
     *
     * @Route("/new{personId}", name="phone_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $personId)
    {
        $personRepo = $this->getDoctrine()->getRepository('MailboxBundle:Person');
        $person = $personRepo->find($personId);
        
        $phone = new Phone();
        $form = $this->createForm('MailboxBundle\Form\PhoneType', $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $phone->setPerson($person);
            $em->persist($phone);
            $em->flush($phone);

            return $this->redirectToRoute('phone_show', array('id' => $phone->getId()));
        }

        return $this->render('MailboxBundle:phone:new.html.twig', array(
            'personId' => $personId,
            'phone' => $phone,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a phone entity.
     *
     * @Route("/{id}", name="phone_show")
     * @Method("GET")
     */
    public function showAction(Phone $phone)
    {

        return $this->render('MailboxBundle:phone:show.html.twig', array(
            'phone' => $phone,
        ));
    }

    /**
     * Displays a form to edit an existing phone entity.
     *
     * @Route("/{id}/edit", name="phone_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Phone $phone)
    {
        $editForm = $this->createForm('MailboxBundle\Form\PhoneType', $phone);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('person_show', array('id' => $phone->getPerson()));
        }

        return $this->render('MailboxBundle:phone:edit.html.twig', array(
            'phone' => $phone,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a phone entity.
     *
     * @Route("/{id}/delete", name="phone_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $phoneRepo = $this->getDoctrine()->getRepository('MailboxBundle:Phone');
        $phone = $phoneRepo->find($id);
        $person = $phone->getPerson();
        $personId = $person->getId();
            $em = $this->getDoctrine()->getManager();
            $em->remove($phone);
            $em->flush($phone);
        
        return $this->redirectToRoute('person_show', array('id' => $personId));
    }
}
