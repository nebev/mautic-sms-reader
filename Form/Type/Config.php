<?php
namespace MauticPlugin\SmsreaderBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use MauticPlugin\CustomCrmBundle\Entity\Opportunity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class Config extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('accountId', TextType::class)
        ->add('unsubscribeKeywords', TextType::class, array(
             'label' => 'Unsubscribe Keywords (comma-separated)'
            )
        )
         ->add('resubscribeKeywords', TextType::class, array(
              'label' => 'Re-Subscribe Keywords (comma-separated)'
             )
         )
        ->add('save', SubmitType::class, array('label' => 'Save Config'));
        return $builder;
    }
    public function getName()
    {
        return 'smsreader_config';
    }
}