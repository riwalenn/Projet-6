<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\TrickHistory;
use App\Entity\TrickLibrary;
use App\Entity\User;
use Composer\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrikFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 1; $i <= 5; $i++){
            $user = new User();
            $user->setUsername($faker->userName)
                    ->setEmail($faker->email)
                    ->setPassword($faker->password(12, 18))
                    ->setImage($faker->imageUrl(30, 30, 'sports'))
                    ->setToken(bin2hex(random_bytes(32)));
            $manager->persist($user);

            for ($j = 1; $j <= 2; $j++) {
                $trick = new Trick();
                //fakers array for snowboarding
                $imageArray = array_diff(scandir('public/img/tricks'), array('..', '.'));
                $arrayFakeTerms = array(
                                ['regular', 'goofy'],
                                ['mute', 'sad', 'indy', 'stalefish', 'tail grab', 'nose grab', 'japan', 'seat belt', 'truck driver'],
                                ['180', '360', '540', '720', '900', '1080'],
                                ['front flip', 'back flip'],
                                ['perpendiculaire', 'dans l\'axe', 'désaxé', 'nose slide', 'tail slide']
                          );

                //random fakers
                //$faker->randomElement(['regular', 'goofy']);
                $image = array_rand(array_flip($imageArray));
                $position = array_rand(array_flip($arrayFakeTerms[0]));
                $grabs = array_rand(array_flip($arrayFakeTerms[1]));
                $rotation = array_rand(array_flip($arrayFakeTerms[2]));
                $flip = array_rand(array_flip($arrayFakeTerms[3]));
                $slide = array_rand(array_flip($arrayFakeTerms[4]));
                $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
                $title = $position. ' ' . $grabs . ' à ' . $rotation . '° ' . $flip . ' ' . $slide;

                $trick->setUser($user)
                        ->setTitle($title)
                        ->setImage($image)
                        ->setDescription($content)
                        ->setPosition($position)
                        ->setGrabs($grabs)
                        ->setRotation($rotation)
                        ->setFlip($flip)
                        ->setSlide($slide)
                        ->setCreatedAt($faker->dateTimeBetween('-9 months'));
                $manager->persist($trick);

                for ($k = 1; $k <= 2; $k++) {
                    $comment = new Comment();
                    $comment->setUser($user)
                            ->setTrick($trick)
                            ->setTitle($faker->sentence)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-6 months'));
                    $manager->persist($comment);
                }

                for ($k = 1; $k <= 2; $k++) {
                    $trick_history = new TrickHistory();
                    $trick_history->setTrick($trick)
                                    ->setUser($user)
                                    ->setModifiedAt($faker->dateTimeBetween('-3 months'));
                    $manager->persist($trick_history);
                }

                for ($k = 1; $k <= 3; $k++) {
                    $trick_library = new TrickLibrary();
                    $trick_library->setTrick($trick)
                                    ->setLien($faker->imageUrl(800, 600))
                                    ->setType(1);
                    $manager->persist($trick_library);
                }
            }
        }
        $manager->flush();
    }
}
