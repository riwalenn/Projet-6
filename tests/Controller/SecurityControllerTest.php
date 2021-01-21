<?php


namespace App\Tests\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail("nfernandes@laposte.net");
        $client->loginUser($testUser);

        $client->request('GET', '/profil/' . $testUser->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Bienvenue ' . $testUser->getUsername() . ' !');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'john@doe.fr',
            '_password' => 'fakepassword'
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}