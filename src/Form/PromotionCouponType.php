<?php

namespace App\Form;

use App\Entity\PromotionCoupon;
use App\Form\Model\DateTimePickerType;
use App\Form\Model\ToggleableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionCouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'resource.fields.title'])
            ->add('expiresAt', DateTimePickerType::class, ['label' => 'promotionCoupon.fields.expiresAt'])
            ->add('usageLimit', NumberType::class, ['label' => 'promotionCoupon.fields.usageLimit'])
            ->add('toggleable', ToggleableType::class, ['label' => 'resource.toglleable.is_enabled_title']);

        if (!$options['is_edit']) {
            $builder->add('code', TextType::class, ['label' => 'promotionCoupon.fields.code'])
                ->add('percentage', PercentType::class, ['label' => 'promotionCoupon.fields.percentage']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PromotionCoupon::class,
            'is_edit' => false
        ]);
    }
}
