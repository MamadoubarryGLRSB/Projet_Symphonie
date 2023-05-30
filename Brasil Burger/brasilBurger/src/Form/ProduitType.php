<?php

namespace App\Form;

use App\Entity\Produit;

use PHPUnit\TextUI\CliArguments\Builder;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('libelle')
        ->add('image', FileType::class,[
            "label"=>false,
            "by_reference"=>true,
            "mapped"=>false,
            "multiple"=>true,
            "required"=>false
        ])
        ->add('prix')

        ->add('type', ChoiceType::class,[
            'choices'  => [
                'burger' => 'BURGER',
                'frite' => 'FRITE',
                'boisson' => 'BOISSON',
                'menu' => 'MENU'
            ],])
            ->add('etat', ChoiceType::class,[
                'choices'  => [
                    'disponible' => 'disponible',
                    'indisponible' => 'indisponible',
                    
                ],])

    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
