<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Users;
use App\Entity\Cryptos;
use App\Entity\Cotation;
use App\Entity\Buy;
use App\Form\ProfileType;
use App\Form\BuyType;
use App\Entity\Transaction;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\UsersRepository;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends AbstractController
{
   /**
    * Permet d'afficher le formulaire de connexion
    *@Route("/", name="admin_login")
    * @param AuthenticationUtils $authenticationUtils
    * @return void
    */
    public function index(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('admin/login.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }
    /**
     * Permet de se deconnecter
     *@Route("/logout", name="admin_logout")
     * @return Response
     */
    public function logout(){
        
    }
    /**
     * Permet d'ajouter un user
     *@Route("/add", name="admin_add")
     *Permet de modifier un user
     *@Route("/update/{id}", name="admin_update")
     *@Security("is_granted('ROLE_ADMIN')")
     * @return Response
     */
    public function AddUpdate(Users $users=null,Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        if(!$users){
            $users= new Users();
        }
        $form=$this->createForm(RegistrationType::class, $users);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodepassword($users, $users->getPassword());
            $users->setPassword($hash);
            $manager->persist($users);
            $manager->flush();
            // ajouter un message flash pour informer l'utilisateur que tous vas bien
            $this->addFlash(
                'success',
                "L'utilisateur <strong> {$users->getUsername()} </strong> a bien été enresistrée !"
            );
            return $this->redirectToRoute("admin_list");
        }
        // var_dump($users->getPicture());
        return $this->render('admin/add.html.twig',[
            'formUsers'=>$form->createView(),
            'editMod'=>$users->getId()!==null,
            'editPhoto'=>$users->getPicture()
        ]);
    }
    /**
     * Permet de lister les utilisateurs
     *@Route("/list", name="admin_list")
     *@Security("is_granted('ROLE_ADMIN')")
     * @return Response
     */
    public function listUsers(UsersRepository $repo){
        $users=$repo->findAll();
         
        return $this->render('admin/listUsers.html.twig',[
            'users'=>$users
        ]);
    }
    /**
     * Permet de supprimer un utilisateur
     *@Route("/list/{id}", name="admin_delete")
     *@Security("is_granted('ROLE_ADMIN')")
     * @return Response
     */
    public function delete($id, ObjectManager $manager){
        $users = new Users();
        $repo=$this->getDoctrine()->getRepository(Users::class);
        $users=$repo->find($id);
        $manager->remove($users);
        $manager->flush();
        // ajouter un message flash pour informer l'utilisateur que tous vas bien
        $this->addFlash(
            'danger',
            "L'utilisateur <strong> {$users->getUsername()} </strong> a bien été supprimé !"
        );
        return $this->redirectToRoute('admin_list');
        return $this->render('admin/delete.html.twig');
    } 
    /**
     * Permet d'editer le profil
     *@Route("/users/profile", name="users_profile")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager){
        // pour editer les informations de l'utilisateur connecté
        $users=$this->getUser();
        $form=$this->createForm(ProfileType::class,$users);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($users);
            $manager->flush();
             // message flash
             $this->addFlash(
                'success',
               "Les données du profil ont été enresistrée avec success"
            );
        }
        return $this->render('admin/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet de modifier le mot de passe
     *@Route("/users/password-update", name="admin_password")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function password(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $passwordUpdate= new PasswordUpdate();
        $form=$this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // pour l'utilisateur connecté
            $users=$this->getUser();
            //1. verifier que le oldPassword soit le même que celui enresistrér dans la bdd
            if(! password_verify($passwordUpdate->getOldPassword(),$users->getPassword())){
                //gerer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe saisi , n'est pas votre actuel mot de passe"));
            }else{
                // 2. recupere le nouveau mot de passe
                $newPassword=$passwordUpdate->getNewPassword();
                // l'encoder avec bcrypt
                $hash=$encoder->encodePassword($users, $newPassword);
                $users->setPassword($hash);

                $manager->persist($users);
                $manager->flush();
                // message flash
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );
            }
        }
        return $this->render('admin/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet d'affiche les cryptos
     * @Route("/cryptos", name="admin_cryptos")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function cryptos(){
        $cryptos= new Cryptos();
        $repo=$this->getDoctrine()->getRepository(Cryptos::class);
        $cryptos=$repo->findAll();
        return $this->render('admin/cryptos.html.twig',[
            'cryptos'=>$cryptos
        ]);
    }
    /**
     * Permet d'acheter des crypto monnaies
     *@Route("/cryptos/achat", name="admin_achat")
     *@isGranted("ROLE_USER")
     * @return Response
     * 
     */
    public function achat(Request $request,ObjectManager $manager){
        $buy= new Buy();
        $form=$this->createForm(BuyType::class, $buy);
        // $form->handleRequest($request){
        //     if($form->isSubmitted() && $form->isValid()){
        //         // recuperation de l'utilisateur connecté ainsi que du solde
        //         $recupUsers=$manager->createQuery("
        //             SELECT * 
        //             FROM App\Entity\Users
        //             WHERE id= : id
        //         ")->setParameter('id', $this->getUser())
        //         ->getResult();
        //         var_dump($recupUsers);
        //         $recupCotation=$manager->createQuery("
        //         SELECT cr.id, cr.nom , cr.sigle, cr.image, cr.slug, cr.content,c.id, c.valeur, c.cours, c.evolution, c.createdAt
        //         FROM App\Entity\Cotation  c
        //         JOIN c.cryptos cr 
        //         GROUP BY cr
        //         HAVING cr= :cr
        //         ")->setParameter('cr',$_GET['id'])
        //         ->getResult();
        //         var_dump($recupCotation);
        //         if($recupUsers['solde']>=$recupCotation['cours']){
        //             $recupBuy=$manager->createQuery("
        //                 INSERT INTO 
        //                 App\Entity\Buy 
        //                 SET cryptos=:cryptos, users=:users, quantity=:quantity, createdAt=:createdAt
        //             ")->setParameter(
        //                 'cryptos'=>$recupBuy->getCryptos,
        //                 'cryptos'=>$recupBuy->getUser(),
        //                 'cryptos'=>$recupBuy->getQuantity,
        //                 'cryptos'=>$recupBuy->setCreatedAt(new \DateTime())
        //                 return $this->redirectoRoute("admin_myAccount");
        //             )
        //             ->getResult();
        //             var_dump($recupBuy);
        //         }else{
        //             $error="Impossible d'achat le solde est trop petit par rapport aucours du bitcoin en cours";
        //         }
        //     }
        //}
        return $this->render("admin/achat.html.twig",[
            "formBuy"=>$form->createView()
        ]);
    }
    /**
     * Permet de vendre des crypto monnaie
     *@Route("/cryptos/vente", name="admin_vente")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function vente(ObjectManager $manager){
        // $requeteDelete=$manager->createQuery("
        //     DELETE 
        //     FROM App\Entity\Buy
        //     WHERE users=:users
        // ")->setParameter('users', $this->getId())
        // ->getResult();
        return $this->render("admin/vente.html.twig");
    }
    /**
     * Permet d'affiche une crypto monnaies en details via le slug
     * @Route("/cryptos/{slug}", name="admin_cryptoSlug")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function cryptoSlug(Cryptos $cryptos, ObjectManager $manager){
        $cryptos=$manager->createQuery("
            SELECT cr.id, cr.nom , cr.sigle, cr.image, cr.slug, cr.content,c.id, c.valeur, c.cours, c.evolution, c.createdAt
            FROM App\Entity\Cotation  c
            JOIN c.cryptos cr 
            GROUP BY cr
            HAVING cr.slug= :slug
        ")->setParameter('slug', $cryptos->getSlug())
        ->getResult();
        // echo"<pre>";
        //     var_dump($cryptos);
        // echo"</pre>";
        return $this->render('admin/cryptoSlug.html.twig',[
            'cryptos'=>$cryptos
        ]);
    }
    /**
     * Permet d'affiché une cotation
     *@Route("/cotations", name="admin_cotation")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function cotations(){
        $cotations= new Cotation();
        $repo=$this->getDoctrine()->getRepository(Cotation::class);
        $cotations=$repo->findAll($repo);
        return $this->render("admin/cotations.html.twig",[
            'cotations'=>$cotations
        ]);
    }
    /**
     * Permet d'afficher les cotations slug
     *@Route("/cotations/12", name="admin_cotationSlug")
     * @return Response
     */
    public function cotationSlug(ObjectManager $manager){
        // $cotation=$manager->createQuery("
        //     SELECT c.valeur, c.cours, c.evolution, c.createdAt, cr.nom, cr.sigle, cr.slug, cr.content, cr.image
        //     FROM App\Entity\Cotation c
        //     JOIN c.cryptos cr
        //     WHERE  
        // ")
        // ->getResult();
        // var_dump($cryptos);
        return $this->render("admin/cotationSlug.html.twig");
    }
    /**
     * Permet d'afficher le profil utilisateur
     *@Route("/myAccount", name="admin_myAccount")
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(ObjectManager $manager){
        // $users= new users();
        // $repo=$this->getDoctrine()->getRepository(Users::class);
        // $users=$repo->findAll();
        // requete qui recupere la moyenne de l'utilisateur correspondant
        $users=$manager->createQuery("
            SELECT avg(w.montant) montant, w.prix  
            FROM App\Entity\Wallet w  
            WHERE w.users= :users
        ")->setParameter('users', $this->getUser())
          ->getResult();
        //  var_dump($users);
            $transactions=$manager->createQuery("
            SELECT count(t.id) id,t.etat, t.type, u.nom, u.picture, u.prenom,c.image
            FROM App\Entity\Transaction t
            JOIN t.users u
            JOIN t.cryptos c
            GROUP BY u
            HAVING u= :u
            ")->setParameter('u', $this->getUser())
            ->getResult();
        return $this->render("admin/myAccount.html.twig",[
            // pour afficher les données de l'utilisateur connecté
            "users"=>$this->getUser(),
            // pour recuperer le wallet de l'utilisateur correspondant
            "essaye"=>$users,
            "transactions"=>$transactions
            
        ]);
    }
    /**
     * Permet d'afficher la list des transaction pour tous les utilisateurs coté admin
     *@Route("/transaction", name="admin_transaction")
     * @return Response
     */
    public function transaction(ObjectManager $manager){
        $transactions=$manager->createQuery("
            SELECT count(t.id) id,t.etat, t.type, u.nom, u.picture, u.prenom
            FROM App\Entity\Transaction t
            JOIN t.users u
            GROUP BY u
        ")->getResult();
        return $this->render("admin/transaction.html.twig",[
            "transactions"=>$transactions
        ]);
    }
    /**
     * Permet d'afficher un transaction en détails
     *@Route("/transaction/{id}", name="admin_transactionShow")
     * @return Response
     */
    public function transactionShow($id, ObjectManager $manager){
        // $transaction= new Transaction();
        
        $repo=$this->getDoctrine()->getRepository(Transaction::class);
        $transaction=$repo->find($id);
    //    $transaction=$manager->createQuery(
    //         "SELECT t.id,t.type, t.etat, t.createdAt, c.image, c.nom, c.content, u.username
    //         FROM App\Entity\Transaction t
    //         JOIN t.cryptos c
    //         JOIN t.users u 
    //         GROUP BY u
    //    ")
    //    ->getResult();
    //    var_dump($transaction);
        return $this->render("admin/transactionShow.html.twig",[
            "transaction"=>$transaction
        ]);
    }
    /**
     * Permet d'aafiché les transaction d'un user connecter
     *@Route("/transactionAccount", name="admin_transactionAccount")
     * @return Response
     */
    public function transactionAccount(ObjectManager $manager){
            $transactions=$manager->createQuery("
            SELECT count(t.id) id,t.etat, t.type, u.nom, u.picture, u.prenom
            FROM App\Entity\Transaction t
            JOIN t.users u
            GROUP BY u
            HAVING u= :u
        ")->setParameter('u', $this->getUser())
        ->getResult();
        var_dump($transactions);
        return $this->render("admin/transactionAccount.html.twig",[
            "transactions"=>$transactions
        ]);
    }
    /**
     * Pemet d'afficher la liste des transactions d'un utilisateur
     *@Route("/transactionUserList", name="admin_transactionUserList")
     * @return Response
     */
    public function transactionUserList(ObjectManager $manager){
        $transactions=$manager->createQuery("
        SELECT t.id, t.etat, t.type, t.createdAt, u.nom, u.picture, u.prenom, c.image, c.content, c.content
        FROM App\Entity\Transaction t
        JOIN t.users u
        JOIN t.cryptos c
        WHERE  u= :u
        ORDER BY t.createdAt DESC
    ")->setParameter('u', $this->getUser())
          ->getResult();
        //   var_dump($transactions);
        return $this->render("admin/transactionUserList.html.twig",[
            "transactions"=>$transactions
        ]);
    }
    /**
     * Permet d'afficher une cotationcryptos
     *@Route("/cotationCryptos", name="admin_cotationCryptos")
     * @return Response
     */
    public function cotationCryptos(ObjectManager $manager){
        // $cotationCryptos= new Cotation();
        // $repo=$this->getDoctrine()->getRepository(Cotation::class);
        // $cotationCryptos=$repo->findAll();
        $cotationCryptos=$manager->createQuery("
            SELECT c.id, c.valeur, c.cours, c.evolution, c.createdAt, cr.nom, cr.image, cr.sigle, cr.slug, cr.content
            FROM App\Entity\Cotation c
            JOIN c.cryptos cr 
            GROUP BY cr
        ")->getResult();
        // var_dump($cotationCryptos);
        return $this->render("admin/cotationCryptos.html.twig",[
            "cotationCryptos"=>$cotationCryptos
        ]);
    }
    /**
     * Permet de faire un text graphique sur cotation
     *@Route("/text", name="admin_text")
     * @return Response
     */
    public function text(){
        $cotations= new Cotation();
        $repo=$this->getDoctrine()->getRepository(Cotation::class);
        $cotations=$repo->findAll();
        
        // var_dump($cotations);
        return $this->render("admin/text.html.twig",[
            "cotations"=>$cotations
        ]);
    }
}
