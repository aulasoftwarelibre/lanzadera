<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 20/08/14
 * Time: 07:41
 */

namespace Lanzadera\FixtureBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\Persistence\ObjectManager;
use Lanzadera\ClassificationBundle\Entity\Classification;
use Lanzadera\FixtureBundle\DataFixtures\DataFixture;

class LoadClassificationData extends DataFixture
{

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $classifications = array(
            'Sociales' => 'Criterios sociales',
            'Ambientales' => 'Criterios ambientales',
            'Comerciales' => 'Criterios comerciales y logísticos',
        );

        foreach($classifications as $id => $name) {
            $organization = $this->createClassification(
                $id,
                $name,
                $this->faker->text,
                $this->faker->numberBetween(50,80)
            );

            $manager->persist($organization);
        }

        $manager->flush();
    }

    private function createClassification($id, $name, $description, $threshold)
    {
        $repo = $this->getClassificationRepository();
        /* @var $classification Classification */
        $classification = $repo->createNew();

        $classification->setName($name);
        $classification->setDescription($description);
        $classification->setThreshold($threshold);

        $this->addReference('Lanzadera.Classification.' . $id, $classification);

        return $classification;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 3;
    }
}