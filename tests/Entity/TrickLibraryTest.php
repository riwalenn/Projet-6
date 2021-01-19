<?php


namespace App\Tests\Entity;


use App\Entity\Trick;
use App\Entity\TrickHistory;
use App\Entity\TrickLibrary;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TrickLibraryTest extends KernelTestCase
{
    public function getEntity(): TrickLibrary
    {
        return (new TrickLibrary())
            ->setTrick(new Trick())
            ->setType(1)
            ->setLien("8AWdZKMTG3U");
    }

    public function assertHasErrors(TrickLibrary $trickLibrary, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($trickLibrary);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number,$errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testNullEntity()
    {
        $this->assertHasErrors($this->getEntity()->setLien(''), 1);
    }

    public function testInvalidChoiceType()
    {
        $this->assertHasErrors($this->getEntity()->setType(0), 1);
        $this->assertHasErrors($this->getEntity()->setType(4), 1);
    }
}