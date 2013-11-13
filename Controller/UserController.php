<?php

namespace Objects\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * user controller.
 */
class UserController extends Controller {

    public function removeImageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $object = $em->getRepository('ObjectsUserBundle:User')->find($id);
        if (!$object) {
            $this->createNotFoundException();
        }
        $object->removeImage();
        $em->flush();
        return new Response('Done.');
    }

}
