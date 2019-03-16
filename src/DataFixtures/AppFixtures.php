<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Cryptos;
use App\Entity\Cotation;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Entity\Role;
use App\Entity\Buy;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");
        // pourv les roles  admin
            $adminRole= new Role();
            $adminRole->setTitle("ROLE_ADMIN");
            $manager->persist($adminRole);
            // insertion d'un utiliateur avec le role admin
            $adminUser= new Users();
            $adminUser->setNom("Job")
                ->setPrenom("Soumah")
                ->setEmail("job@gmail.com")
                ->setVille($faker->city)
                ->setAdresse($faker->address)
                ->setPassword($this->encoder->encodePassword($adminUser, 'password'))
                ->setPicture('https://or-formation.com/uploads/img/produits/44.png')
                ->setCp($faker->postcode)
                ->setTelephone($faker->phoneNumber)
                ->setUsername($faker->username)
                ->setBalance(mt_rand(100,1000))
                ->addUserRole($adminRole);
            $manager->persist($adminUser);
        // pour les utilisateurs
        $users=[];
        $crypto=[];
        $genres=["male","female"];
        // 10 users fakers
        for($i=1; $i<=20; $i++){

            $user= new Users();
            $genre=$faker->randomElement($genres);
            $picture='https://randomuser.me/api/portraits/';
            $pictureId=$faker->numberBetween(1,99).'.jpg';
            $picture .=($genre =='male' ? 'men/' : 'women/' ).$pictureId;
            $hash=$this->encoder->encodePassword($user,"password");
            $user->setNom($faker->firstName)
                  ->setPrenom($faker->lastName)
                  ->setUsername($faker->userName)
                  ->setPassword($hash)
                  ->setEmail($faker->email)
                  ->setVille($faker->city)
                  ->setAdresse($faker->address)
                  ->setCp($faker->postcode)
                  ->setTelephone($faker->phoneNumber)
                  ->setPicture($picture)
                  ->setBalance(mt_rand(100,1000));
            $manager->persist($user);
            $users[]=$user;
        }
        
         // 10 crypto fakers
         for($j=1; $j<=20; $j++){
            $cryptos= new Cryptos();
            $content='<p>'.join('<p></p>', $faker->paragraphs(5)).'</p>';
            $cryptoname=$faker->sentence();
            // 
            // function FirstCotation($cryptoname){
            //     return ord(substr($cryptoname,0,1)) + rand(0, 10);
            // }
            
            // function CotationFor($cryptoname){	
            //     return ((rand(0, 99)>40) ? 1 : -1) * ((rand(0, 99)>49) ? ord(substr($cryptoname,0,1)) : ord(substr($cryptoname,-1))) * (rand(1,10) * .01);
            // }
            // 

            $cryptos->setNom($cryptoname)
                    ->setSigle($faker->languageCode)
                    ->setImage($faker->imageUrl())
                    ->setContent($content);
            $manager->persist($cryptos);
            // $crypto[]=$cryptos;
                // creation des 10 achats faker
            for($buy=1; $buy<=10; $buy++){
                $achat= new Buy();
                $achat->setQuantity(mt_rand(1,9))
                    ->setCreatedAt(new \DateTime())
                    ->setCryptos($cryptos)
                    ->setUsers($user);
                $manager->persist($achat);
            }
            // creation dees cotations
            for($c=1; $c<=20; $c++){

                $cotation= new Cotation();
                $cotation->setValeur($faker->randomFloat())
                        ->setCours(mt_rand(100,500.00))
                        ->setEvolution(mt_rand(600.00,1000))
                        ->setCreatedAt(new \DateTime())
                        ->setCryptos($cryptos);
                $manager->persist($cotation);
            
                
            }
            // creation des transactions
            for($t=1; $t<=20; $t++){
                $user=$users[mt_rand(0, count($users)-1)];
                $transaction= new Transaction();
                $transaction->setType($faker->sentence())
                            ->setEtat($faker->paragraph())
                            ->setCreatedAt(new \DateTime())
                            ->setUsers($user)
                            ->setCryptos($cryptos);
                $manager->persist($transaction);
            }
            // creation d'un wallet
            for($w=1; $w<=20; $w++){
                $wallet= new Wallet();
                $user=$users[mt_rand(0, count($users)-1)];
                $wallet->setPrix(mt_rand(1, 99))
                    ->setMontant(mt_rand(100, 1000))
                    ->setUsers($user)
                    ->setCryptos($cryptos);
                $manager->persist($wallet);
            }
            
        }
    // }
        
        $manager->flush();
    }
}
