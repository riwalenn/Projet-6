<?php


namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserEntityTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('test')
            ->setEmail('user@gmail.com')
            ->setPassword('FM<gbO!SI)FD?ASy5"')
            ->setImage(4)
            ->setToken(bin2hex(random_bytes(32)))
            ->setCreatedAt(new \DateTime("2021-01-19 18:03:00"))
            ->setIsActive(0)
            ->setRoles((array)'ROLE_USER');
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
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

    public function testInvalidPasswordEntity()
    {
        $this->assertHasErrors($this->getEntity()->setPassword('1244'), 1);
    }

    public function testInvalidBlanks()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setImage(''), 1);
        $this->assertHasErrors($this->getEntity()->setPassword(''), 1);
    }

    public function testUniqueEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('nfernandes@laposte.net'), 1);
    }
}