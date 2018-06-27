<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'customer.fields.firstName'])
            ->add('lastName', TextType::class, ['label' => 'customer.fields.lastName'])
            ->add('email', EmailType::class, ['label' => 'customer.fields.email'])
            ->add('phoneNumber', TextType::class, ['label' => 'customer.fields.phoneNumber'])
            ->add('customerAddresses', CollectionType::class, [
                'entry_type' => CustomerAddressesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'address.title_single',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
