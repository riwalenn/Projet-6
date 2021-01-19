<?php


namespace App\Tests\Entity;


use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class CommentEntityTest extends KernelTestCase
{
    public function getEntity(): Comment
    {
        return (new Comment())
            ->setUser(new User())
            ->setTrick(new Trick())
            ->setTitle("Repellat excepturi eaque repudiandae unde.")
            ->setContent("<p>Nostrum sunt explicabo omnis facere omnis tenetur. Est quia maiores dolores itaque porro nemo. Repudiandae et quidem assumenda voluptate. Aut itaque deserunt magnam odio corrupti voluptatem.</p><p>Rem ex impedit nesciunt eos laboriosam necessitatibus ut illum. Aliquid tenetur doloremque officia omnis. Quia aut nesciunt quis aut aut. Earum voluptatibus iste rem excepturi nihil sunt quae.</p>")
            ->setCreatedAt(new \DateTime("2021-01-19 18:03:00"));
    }

    public function assertHasErrors(Comment $comment, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($comment);
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

    public function testInvalidLength()
    {
        $this->assertHasErrors($this->getEntity()->setTitle("test"), 1);
        $this->assertHasErrors($this->getEntity()->setContent("test"), 1);
    }
}