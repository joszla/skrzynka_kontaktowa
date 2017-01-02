<?php

namespace MailboxBundle\Controller;

use MailboxBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Person controller.
 */
class PersonController extends Controller
{
    /**
     * Lists all person entities.
     *
     * @Route("/", name="person_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $people = $em->getRepository('MailboxBundle:Person')->findAll();
        
        return $this->render('MailboxBundle:person:index.html.twig', array(
            'people' => $people,
        ));
    }

    /**
     * Creates a new person entity.
     *
     * @Route("/new", name="person_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $person = new Person();
        $form = $this->createForm('MailboxBundle\Form\PersonType', $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush($person);

            return $this->redirectToRoute('person_show', array('id' => $person->getId()));
        }

        return $this->render('MailboxBundle:person:new.html.twig', array(
            'person' => $person,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a person entity.
     *
     * @Route("/{id}", name="person_show")
     * @Method("GET")
     */
    public function showAction(Person $person)
    {

        return $this->render('MailboxBundle:person:show.html.twig', array(
            'person' => $person,
        ));
    }

    /**
     * Displays a form to edit an existing person entity.
     *
     * @Route("/{id}/modify", name="person_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Person $person)
    {
        $editForm = $this->createForm('MailboxBundle\Form\PersonType', $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('person_edit', array('id' => $person->getId()));
        }

        return $this->render('MailboxBundle:person:edit.html.twig', array(
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ));
    }

  /**
     * Deletes a person entity.
     *
     * @Route("/{id}/delete", name="person_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $personRepo = $this->getDoctrine()->getRepository('MailboxBundle:Person');
        $person = $personRepo->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush($person);

        return $this->render('MailboxBundle:person:delete.html.twig');
    }
       
}
