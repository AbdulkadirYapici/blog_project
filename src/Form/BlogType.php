<?php
namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('preview_img', FileType::class)
            ->add('content', TextType::class)
            ->add('summary', TextType::class)
            ->add('createdAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false

            ))
            ->add('updatedAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false
            ))
            ->add('status',  CheckboxType::class, [
                'label'    => 'Show this entry publicly?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Blog::class,
        ));
    }
}