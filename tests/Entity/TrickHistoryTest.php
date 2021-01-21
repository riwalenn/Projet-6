<?php


namespace App\Tests\Entity;


use App\Entity\Trick;
use App\Entity\TrickHistory;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TrickHistoryTest extends KernelTestCase
{
    public function getEntity(): TrickHistory
    {
        return (new TrickHistory())
            ->setUser(new User())
            ->setTrick(new Trick())
            ->setModifiedAt(new \DateTime("2021-01-19 18:03:00"));
    }

    public function assertHasErrors(TrickHistory $trickHistory, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($trickHistory);
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
}