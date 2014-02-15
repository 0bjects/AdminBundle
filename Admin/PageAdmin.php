<?php

namespace Objects\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin;

class PageAdmin extends Admin {

    /**
     * this variable holds the route name prefix for this actions
     * @var string
     */
    protected $baseRouteName = 'page_admin';

    /**
     * this variable holds the url route prefix for this actions
     * @var string
     */
    protected $baseRoutePattern = 'page';

    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('id')
                ->add('metaTitle')
                ->add('metaDescription')
                ->add('metaKeywords')
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
                ->add('content')
                ->add('metaTitle')
                ->add('metaDescription')
                ->add('metaKeywords')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id')
                ->add('content')
                ->add('metaTitle')
                ->add('metaDescription')
                ->add('metaKeywords')
        ;
    }

    public function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->with('Required fields')
                ->add('metaTitle')
                ->add('content', 'textarea', array('required' => false, 'attr' => array('class' => 'ckeditor')))
                ->end()
                ->with('optional fields')
                ->add('metaDescription')
                ->add('metaKeywords')
                ->end()
        ;
    }

    public function configureRoutes(RouteCollection $collection) {
        $collection->remove('create')->remove('delete');
    }

}

?>
