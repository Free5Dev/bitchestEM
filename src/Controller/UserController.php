<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @Route("/user/wallet", name="user_wallet")
     * *@isGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $wallets= new Wallet();
        $repo=$this->getDoctrine()->getRepository(Wallet::class);
        $wallets=$repo->findAll();
        return $this->render('user/index.html.twig',[
            "wallets"=>$wallets
        ]);
    }
    /**
     * Permet d'afficher le contenu du wallet
     *@Route("/user/wallet/{id}", name="user_walletDetails")
     *@isGranted("ROLE_ADMIN")
     * @return Response
     */
    public function walletDetails($id){
        $wallet= new Wallet();
        $repo=$this->getDoctrine()->getRepository(Wallet::class);
        $wallet=$repo->find($id);
        return $this->render("user/walletDetails.html.twig",[
            "wallet"=>$wallet
        ]);
    }
    /**
     * Permet dessayer
     *@Route("/user/essaye", name="user_essaye")
     *@isGranted("ROLE_ADMIN")
     * @return Response
     */
    public function essaye(ObjectManager $manager){
        $essaye=$manager->createQuery("
            SELECT CONCAT(AVG(w.montant), ' Euros') montant, c.sigle, c.image, c.nom, w.prix,u.balance,u.prenom,u.nom,u.picture
            FROM App\Entity\Wallet w
            JOIN  w.users u
            JOIN  w.cryptos c
            GROUP BY u
            ORDER BY w.prix ASC
        ")->getResult();
        // var_dump($essaye);
        return $this->render("user/essaye.html.twig",[
            'essaye'=>$essaye
        ]);
    }
    
   
    
}
