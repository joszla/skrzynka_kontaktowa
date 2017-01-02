<?php

namespace MailboxBundle\Controller;

use MailboxBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Address controller.
 *
 * @Route("address")
 */
class AddressController extends Controller
{
    /**
     * Lists all address entities.
     *
     * @Route("/", name="address_index")
     * @Method("GET")
     */
    public function indexAction()
    {   
       
        $em = $this->getDoctrine()->getManager();
        
        
        
        $addresses = $em->getRepository('MailboxBundle:Address')->findAll();

        return $this->render('MailboxBundle:address:index.html.twig', array(
            'addresses' => $addresses,
        ));
    }

    /**
     * Creates a new address entity.
     *
     * @Route("/new/{personId}", name="address_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $personId)
    {   
        $personRepo = $this->getDoctrine()->getRepository('MailboxBundle:Person');
        $person = $personRepo->find($personId);
        
        $address = new Address();
        $form = $this->createForm('MailboxBundle\Form\AddressType', $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $address->setPerson($person);
            $em->persist($address);
            $em->flush($address);

            return $this->redirectToRoute('address_show', array('id' => $address->getId()));
        }

        return $this->render('MailboxBundle:address:new.html.twig', array(
            'personId' => $personId,
            'address' => $address,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays an address entity.
     *
     * @Route("/{id}", name="address_show")
     * @Method("GET")
     */
    public function showAction(Address $address)
    {

        return $this->render('MailboxBundle:address:show.html.twig', array(
            'address' => $address,
        ));
    }

    /**
     * Displays a form to edit an existing address entity.
     *
     * @Route("/{id}/edit", name="address_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Address $address)
    {   
        $editForm = $this->createForm('MailboxBundle\Form\AddressType', $address);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('person_show', array('id' => $address->getPerson()));
        }

        return $this->render('MailboxBundle:address:edit.html.twig', array(
            'address' => $address,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes an address entity.
     *
     * @Route("/{id}/delete", name="address_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $addressRepo = $this->getDoctrine()->getRepository('MailboxBundle:Address');
        $address = $addressRepo->find($id);
        $person = $address->getPerson();
        $personId = $person->getId();
        
            $em = $this->getDoctrine()->getManager();
            $em->remove($address);
            $em->flush($address);

        return $this->redirectToRoute('person_show', array('id' => $personId));
    }

}
