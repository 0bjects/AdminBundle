<?php

namespace Objects\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends Admin {

    /**
     * this variable holds the route name prefix for this actions
     * @var string
     */
    protected $baseRouteName = 'user_admin';

    /**
     * this variable holds the url route prefix for this actions
     * @var string
     */
    protected $baseRoutePattern = 'user';

    /**
     * this function configure the list action fields
     * @author Mahmoud
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('id')
                ->add('firstName')
                ->add('lastName')
                ->add('loginName')
                ->add('email', null, array('template' => 'ObjectsAdminBundle:General:list_email.html.twig'))
                ->add('image', null, array('template' => 'ObjectsAdminBundle:General:list_image.html.twig'))
                ->add('gender', null, array('template' => 'ObjectsAdminBundle:General:list_gender.html.twig'))
                ->add('createdAt')
                ->add('locked')
                ->add('enabled')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
        ;
    }

    /**
     * this function configure the show action fields
     * @author Mahmoud
     * @param ShowMapper $showMapper
     */
    public function configureShowField(ShowMapper $showMapper) {
        $showMapper
                ->add('id')
                ->add('firstName')
                ->add('lastName')
                ->add('loginName')
                ->add('email', null, array('template' => 'ObjectsAdminBundle:General:show_email.html.twig'))
                ->add('image', null, array('template' => 'ObjectsAdminBundle:General:show_image.html.twig'))
                ->add('gender', null, array('template' => 'ObjectsAdminBundle:General:show_gender.html.twig'))
                ->add('createdAt')
                ->add('locked')
                ->add('enabled')
        ;
    }

    /**
     * this function configure the list action filters fields
     *
     * @author Mahmoud
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id')
                ->add('firstName')
                ->add('lastName')
                ->add('loginName')
                ->add('email')
                ->add('gender')
                ->add('createdAt')
                ->add('locked')
                ->add('enabled')
        ;
    }

    /**
     * this function configure the new, edit form fields
     * @author Mahmoud
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper) {
        $imageAttributes = array(
            'onchange' => 'readURL(this);'
        );
        if ($this->getSubject() && $this->getSubject()->getId() && $this->getSubject()->getImage()) {
            $imageAttributes['data-image-url'] = $this->getRequest()->getBasePath() . '/' . $this->getSubject()->getSmallImageUrl(60, 60);
            $imageAttributes['data-image-remove-url'] = $this->generateObjectUrl('remove_image', $this->getSubject());
        }
        $loginNameLabel = 'Slug';
        if($this->getConfigurationPool()->getContainer()->getParameter('login_name_required')) {
            $loginNameLabel = 'Login Name';
        }
        $formMapper
                ->with('Required Fields')
                ->add('firstName')
                ->add('loginName', null, array('label' => $loginNameLabel))
                ->add('email')
                ->add('userPassword', 'repeated', array(
                    'required' => false,
                    'type' => 'password',
                    'first_options' => array('label' => 'Password', 'attr' => array('autocomplete' => 'off')),
                    'second_options' => array('label' => 'Repeat Password', 'attr' => array('autocomplete' => 'off')),
                    'invalid_message' => "The passwords don't match"))
                ->end()
                ->with('Not Required Fields', array('collapsed' => true))
                ->add('gender', 'choice', array('empty_value' => false, 'required' => false, 'choices' => array('1' => 'Male', '0' => 'Female'), 'expanded' => true, 'multiple' => false))
                ->add('lastName')
                ->add('file', 'file', array('required' => false, 'label' => 'image', 'attr' => $imageAttributes))
                ->add('locked', NULL, array('required' => false))
                ->add('enabled', NULL, array('required' => false))
                ->end()
                ->setHelps(array(
                    'locked' => 'to prevent the user from logging into his account',
                    'enabled' => 'uncheck to mark the user account as deleted'
                ))
        ;
    }

    /**
     * this function is used to set a different validation group for the form
     */
    public function getFormBuilder() {
        if (is_null($this->getSubject()->getId())) {
            $this->formOptions = array('validation_groups' => array('signup', 'loginName'));
        } else {
            $this->formOptions = array('validation_groups' => array('edit', 'loginName'));
        }
        $formBuilder = parent::getFormBuilder();
        return $formBuilder;
    }

    /**
     * this function is for editing the routes of this class
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection->remove('delete')->add('remove_image', $this->getRouterIdParameter() . '/remove-image');
    }

    /**
     * @param \Objects\UserBundle\Entity\User $user
     */
    public function prePersist($user) {
        $userRole = $this->getConfigurationPool()->getContainer()->get('doctrine')->getEntityManager()
                        ->getRepository('ObjectsUserBundle:Role')->findOneByName('ROLE_USER');
        $user->addRole($userRole);
    }

}

?>
