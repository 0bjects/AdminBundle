<?php

namespace Objects\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Objects\AdminBundle\Entity\Block;

class LoadBlockData implements FixtureInterface {

    public function load(ObjectManager $manager) {
        $footerBlock = new Block();
        $footerBlock->setName('footer-menu');
        $footerBlock->setContent('');
        $manager->persist($footerBlock);
        $manager->flush();
    }

}