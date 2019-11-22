<?php
namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManager;

class BlogType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('preview_img', FileType::class, array(
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
            ))
            ->add('content', TextareaType::class)
            ->add('summary', TextareaType::class)
            ->add('createdAt', DateType::class, array(
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr'=>array('style'=>'display:none;'),
                'label' => false,

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
                'required' => false,

            ])
            ->add('category1', TextType::class,array(
        'data_class' => null,
        'required' => false,
        'mapped' => false ,

            ))
            ->add('category2', ChoiceType::class, [
                'choices' => ['Kadir' => "Kadir", 'Yapıcı' => 'Yapıcı'
            ],
                'data_class' => null,
                'required' => false,
                'mapped' => false ,

                ])
            ->add('tag', TextType::class,array(
                'data_class' => null,
                'required' => false,
                'mapped' => false ,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => Blog::class,
        ));
    }
}