<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(

            ))
            ->add('description')
            ->add('situation', ChoiceType::class, array(
                'choices' => array(
                    'Ativo' => true,
                    'Inativo' => false
                ),
            ))
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker form-control'],
                'html5' => false,
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker form-control'],
                'html5' => false,
            ))
            ->add('status', EntityType::class, array(
                'class' => 'AppBundle:Status',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.item', 'DESC');
                },
                'choice_label' => 'item',
            ))
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Activity::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_activity';
    }


}
