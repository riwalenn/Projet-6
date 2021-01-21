<?php


namespace App\Tests\Repository;

use App\DataFixtures\TrikFixtures;
use App\Repository\CommentRepository;
use App\Repository\TrickHistoryRepository;
use App\Repository\TrickLibraryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AppFixturesTest extends KernelTestCase
{
    use FixturesTrait;

    public function testFixturesUsersCount()
    {
        self::bootKernel();
        $this->loadFixtures([TrikFixtures::class]);
        $users = self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(5, $users);
        $tricks = self::$container->get(TrickRepository::class)->count([]);
        $this->assertEquals(10, $tricks);
        $comments = self::$container->get(CommentRepository::class)->count([]);
        $this->assertEquals(20, $comments);
        $tricks_history = self::$container->get(TrickHistoryRepository::class)->count([]);
        $this->assertEquals(20, $tricks_history);
        $tricks_library = self::$container->get(TrickLibraryRepository::class)->count([]);
        $this->assertEquals(30, $tricks_library);
    }
}