<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\BusinessPartnerStatusEnum;
use App\Enums\LegalFormEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessPartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('status', EnumType::class, ['class' => BusinessPartnerStatusEnum::class])
            ->add('legalForm', EnumType::class, ['class' => LegalFormEnum::class])
            ->add('address')
            ->add('city')
            ->add('zip')
            ->add('country')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BusinessPartner::class,
        ]);
    }
}
