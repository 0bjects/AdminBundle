<?php

namespace Objects\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin;
use Objects\AdminBundle\Entity\Banner;

class BannerAdmin extends Admin {

    /**
     * this variable holds the route name prefix for this actions
     * @var string
     */
    protected $baseRouteName = 'banner_admin';

    /**
     * this variable holds the url route prefix for this actions
     * @var string
     */
    protected $baseRoutePattern = 'banner';

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('id')
                ->add('code')
                ->add('url', null, array('template' => 'ObjectsAdminBundle:General:list_url.html.twig'))
                ->add('position')
                ->add('createdAt')
                ->add('numberOfClicks')
                ->add('numberOfViews')
                ->add('image', null, array('template' => 'ObjectsAdminBundle:General:list_image.html.twig'))
                ->add('fileName', null, array('template' => 'ObjectsAdminBundle:General:list_swf_file.html.twig'))
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
        ;
    }

    public function configureShowField(ShowMapper $showMapper) {
        $showMapper
                ->add('id')
                ->add('code')
                ->add('url', null, array('template' => 'ObjectsAdminBundle:General:show_url.html.twig'))
                ->add('position')
                ->add('createdAt')
                ->add('numberOfClicks')
                ->add('numberOfViews')
                ->add('image', null, array('template' => 'ObjectsAdminBundle:General:show_image.html.twig'))
                ->add('fileName', null, array('template' => 'ObjectsAdminBundle:General:show_swf_file.html.twig'))
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id')
                ->add('code')
                ->add('url')
                ->add('position')
                ->add('createdAt')
                ->add('numberOfClicks')
                ->add('numberOfViews')
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $newBanner = new Banner();
        $imageAttributes = array(
            'onchange' => 'readURL(this);'
        );
        if ($this->getSubject() && $this->getSubject()->getId() && $this->getSubject()->getImage()) {
            $imageAttributes['data-image-url'] = $this->getRequest()->getBasePath() . '/' . $this->getSubject()->getSmallImageUrl(60, 60);
            $imageAttributes['data-image-remove-url'] = $this->generateObjectUrl('remove_image', $this->getSubject());
        }
        $formMapper
                ->with('Required fields')
                ->add('position', 'choice', array('choices' => $newBanner->getValidPositions()))
                ->add('code')
                ->add('url')
                ->add('SWF', 'file', array('required' => false, 'label' => 'swf'))
                ->add('file', 'file', array('required' => false, 'label' => 'image', 'attr' => $imageAttributes))
                ->end()
        ;
    }

    public function configureRoutes(RouteCollection $collection) {
        $collection->add('remove_image', $this->getRouterIdParameter() . '/remove-image');
    }

}

?>
