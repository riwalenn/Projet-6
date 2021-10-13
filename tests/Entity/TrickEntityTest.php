<?php


namespace App\Tests\Entity;


use App\Entity\Trick;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TrickEntityTest extends KernelTestCase
{
    public function getEntity(): Trick
    {
        return (new Trick())
            ->setUser(new User())
            ->setTitle("regular sad 180° back flip perpendiculaire")
            ->setDescription("Lorem Ipsum is simply dummy text of the printing...")
            ->setPosition("regular")
            ->setGrabs("sad")
            ->setRotation("180")
            ->setFlip("back flip")
            ->setSlide("perpendiculaire")
            ->setCreatedAt(new \DateTime("2021-01-19 18:03:00"));
    }

    public function assertHasErrors(Trick $trick, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($trick);
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

    public function testInvalidBlanks()
    {
        $this->assertHasErrors($this->getEntity()->setDescription(""), 1);
        $this->assertHasErrors($this->getEntity()->setRotation(""), 1);
    }

    public function testInvalidChoices()
    {
        $this->assertHasErrors($this->getEntity()->setPosition("test"), 1);
        $this->assertHasErrors($this->getEntity()->setGrabs("test"), 1);
        $this->assertHasErrors($this->getEntity()->setFlip("test"), 1);
        $this->assertHasErrors($this->getEntity()->setSlide("test"), 1);
    }

    public function testInvalidLength()
    {
        $this->assertHasErrors($this->getEntity()->setTitle("goofy japan à 180° front"), 1);
        $this->assertHasErrors($this->getEntity()->setTitle("<p>Nostrum sunt explicabo omnis facere omnis tenetur. Est quia maiores dolores itaque porro nemo. Repudiandae et quidem assumenda voluptate. Aut itaque deserunt magnam odio corrupti voluptatem.</p><p>Rem ex impedit nesciunt eos laboriosam necessitatibus ut illum. Aliquid tenetur doloremque officia omnis. Quia aut nesciunt quis aut aut. Earum voluptatibus iste rem excepturi nihil sunt quae.</p>"), 1);
    }

    public function testTitleAlmostUsed()
    {
        $this->assertHasErrors($this->getEntity()->setTitle("goofy japan à 180° front flip perpendiculaire"), 1);
    }
}