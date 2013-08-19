<?php

namespace Objects\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Objects\AdminBundle\Entity\Page;

class LoadPageData implements FixtureInterface {

    public function load(ObjectManager $manager) {
        $aboutPage = new Page();
        $aboutPage->setSlug('about');
        $aboutPage->setMetaTitle('about');
        $aboutPage->setContent('about');
        $manager->persist($aboutPage);
        $manager->flush();
    }

}