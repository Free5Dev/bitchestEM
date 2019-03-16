<?php

namespace App\Form;

use App\Entity\Users;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;



class RegistrationType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, $this->getConfiguration("Nom", "Entrer votre nom..."))
            ->add('prenom',TextType::class, $this->getConfiguration("Prenom", "Entrer votre Prenom..."))
            ->add('username',TextType::class, $this->getConfiguration("Nom d'utilisateur","Entrer votre nom d'utilisateur"))
            ->add('password',PasswordType::class,$this->getConfiguration("Mot de passe", "Entrer votre mot de passe"))
            ->add('confirm_password',PasswordType::class,$this->getConfiguration("Confirmer Mot de passe", "Répéter le mot de passe"))

            ->add('email',TextType::class, $this->getConfiguration("Email", "Entrer votre Email"))
            ->add('ville',TextType::class, $this->getConfiguration("Ville", "Tapez votre ville"))
            ->add('adresse',TextType::class, $this->getConfiguration("Adresse", "Entrer votre adresse"))
            ->add('cp',TextType::class, $this->getConfiguration("Code postal", "Entrer votre code postal"))
            ->add('telephone',TextType::class, $this->getConfiguration("Télephone", "Votre numero de téléphone"))
            ->add('balance',IntegerType::class, $this->getConfiguration("Solde", "Entrer votre solde"))
            ->add('picture',UrlType::class, $this->getConfiguration("Url de la photo","Entrer l'url de votre avatar"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
