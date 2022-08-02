<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AvatarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder->add('avatar', FileType::class, [
        'required' => true,
        'mapped' => false,
        'constraints' => [
            new File([
                'maxSize' => '5k',
                'maxSizeMessage' => 'L\' image est trop volumineux ({{ size }} {{ suffix }}) La taille maximale autorisé est {{ limit }} {{ suffix }}.',
                'mimeTypes' => [
                    'image/png',
                    'image/jpeg'
                ],
                'mimeTypesMessage' => 'Type d\'image invalide ({{ type }}). Les images valides sont {{ types }}'
            ])
        ]
    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}