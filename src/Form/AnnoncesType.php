<?php

namespace App\Form;

use App\Entity\Annonces;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class AnnoncesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('region')
            ->add('prix')
            ->add('type')
            ->add('email')
            ->add('duree')
            ->add('categorie', ChoiceType::class, [
                'choices'  => [
                    'A Vendre'=>'a vendre' ,
                    'A louer'=>'a louer',
                ],
             ])
            ->add('image', FileType::class, [
                'label' => 'image de votre Annonce ',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Ajouter une image valide ',
                    ])
                ],
            ])
            // ...
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        
        ]);
    }
}
