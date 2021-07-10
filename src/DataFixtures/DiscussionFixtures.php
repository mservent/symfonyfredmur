<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Discussion;

class DiscussionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++){
            $discussion = new Discussion();
            $discussion->setTitre("Titre de la discussion n°$i")
                        ->setContenu("<p>Texte de la discussion n°$i</p>")
                        ->setImage("https://via.placeholder.com/350x150")
                        ->setCreatedAt(new \DateTime())
                        ->setUpDateAt(new \DateTime());
            
             $manager->persist($discussion); // le manager se prépare à faire persister la discussion          
        }

        $manager->flush();
    }
}
