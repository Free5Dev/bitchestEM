<?php

namespace App\Form;

use App\Form\ApplicationType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class,$this->getConfiguration("Ancien Mot de passe", "Entrer votre mot de passe actuel..."))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau Mot de passe", "Entrer votre nouveau mot de passe..."))
            ->add("confirmPassword", PasswordType::class, $this->getConfiguration("Confirmer le mot de passe","Confirmé le nouveau mot de passe..."))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
