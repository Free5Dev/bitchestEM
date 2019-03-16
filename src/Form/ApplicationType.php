<?php 

    namespace App\Form;

  
    use Symfony\Component\Form\AbstractType;
    
    class ApplicationType extends AbstractType
    {

            /**
         * Permet de configurer le label et le placeholder
         *
         * @param string $label
         * @param string $placeholder
         * @return array
         */
        protected function getConfiguration($label, $placeholder){
        return [
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
            ];
        }

    }