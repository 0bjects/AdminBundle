Installation instructions:

1.add this lines to your composer.json file in "require" section:
"doctrine/doctrine-fixtures-bundle": "dev-master",
"sonata-project/doctrine-orm-admin-bundle": "dev-master"

2.add this lines to your app/AppKernel.php :

new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
new Knp\Bundle\MenuBundle\KnpMenuBundle(),
new Sonata\BlockBundle\SonataBlockBundle(),
new Sonata\jQueryBundle\SonatajQueryBundle(),
new Sonata\CoreBundle\SonataCoreBundle(),
new Sonata\AdminBundle\SonataAdminBundle(),
new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
new Objects\AdminBundle\ObjectsAdminBundle(),

3.add the routes in your app/config/routing.yml:

objects_admin:
    resource: "@ObjectsAdminBundle/Resources/config/routing.yml"
    prefix:   /admin

# Login routes
#login_check:
#    path:  /login-check
#
#logout:
#    path:  /logout
#
#login:
#    path:  /login
#    defaults: { _controller: ObjectsAdminBundle:Admin:login }

4.enable the translation in your config.yml file :

framework:
    esi:             ~
    translator:      { fallback: %locale% }

5.copy the security.yml file into your app/config folder if you need the login functionality of admin bundle

6.add to your config.yml file:
# Sonata Configuration
sonata_admin:
    title: Site Admin
    templates:
        layout: ObjectsAdminBundle:General:standard_layout.html.twig

    dashboard:
        blocks:
            # display a dashboard block
            - { position: left, type: sonata.admin.block.admin_list, settings: { groups: [Site] } }

        groups:
            Site:
                items:
                    - page_admin
                    - block_admin
                    - banner_admin
#                    - user_admin

sonata_block:
    default_contexts: [cms]
    blocks:
        sonata.admin.block.admin_list:
            contexts:   [admin]

7.run composer update

8.update the database
app/console doctrine:schema:update --force

9.load the fixture files
app/console doctrine:fixtures:load --append

optional:
enable the login routes if you do not have the user bundle installed
enable the user admin service in the configuration files if you need it