<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use pfc\InicioBundle\Entity\Viaje;
use pfc\InicioBundle\Form\ViajeType;

/**
 * Viaje controller.
 *
 */
class ViajeController extends Controller
{
    /**
     * Lists all Viaje entities.
     *
     */
    public function listarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario=$this->get('security.context')->getToken()->getUser();
        
//        $entities = $em->getRepository('InicioBundle:Viaje')->findAll();
        $entities=$em->getRepository('InicioBundle:Usuario')->findViajesRecientes($usuario->getId());

        return $this->render('InicioBundle:Viaje:listar.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Viaje entity.
     *
     */
    public function crearAction(Request $request)
    {
        $entity  = new Viaje();
        $form = $this->createForm(new ViajeType(), $entity);
        $usuario=$this->get('security.context')->getToken()->getUser();
        
        $form->bind($request);

        if ($form->isValid()) {
            
            $entity->setUsuario($usuario)
                   ->setFecha(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('viaje_mostrar', array('id' => $entity->getId())));
        }

        return $this->render('InicioBundle:Viaje:crear.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Viaje entity.
     *
     */
    public function nuevoAction()
    {
        $entity = new Viaje();
        $form   = $this->createForm(new ViajeType(), $entity);

        return $this->render('InicioBundle:Viaje:crear.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Viaje entity.
     *
     */
    public function mostrarAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InicioBundle:Viaje')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Viaje no encontrado.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InicioBundle:Viaje:mostrar.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Viaje entity.
     *
     */
    public function editarAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InicioBundle:Viaje')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Viaje no encontrado.');
        }

        $editForm = $this->createForm(new ViajeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InicioBundle:Viaje:editar.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Viaje entity.
     *
     */
    public function actualizarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InicioBundle:Viaje')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Viaje no encontrado.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ViajeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('viaje_editar', array('id' => $id)));
        }

        return $this->render('InicioBundle:Viaje:editar.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Viaje entity.
     *
     */
    public function borrarAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InicioBundle:Viaje')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Viaje no encontrado.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('viaje'));
    }

    /**
     * Creates a form to delete a Viaje entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
