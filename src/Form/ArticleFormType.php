<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Tag;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;

class ArticleFormType extends AbstractType
{
    private ArticleRepository $articleRepository;
    private TagRepository $tagRepository;

    public function __construct(ArticleRepository $articleRepository,TagRepository $tagRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->tagRepository = $tagRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $articleData = new Article();
        $articleData->setTitle('');
        $articleData->setImageUrl('');
        $articleData->setContent('');
        $articleData->setColor(1);
      

       
        if ($options['id'] != null) {
        
              $articleData = $this->articleRepository->find($options['id']);
               
        }
        $builder
            ->add('title', TextType::class,[
                'data' => $articleData->getTitle()
            ])
            ->add('image_url', FileType::class, [
                'label' => 'Cover image',
                'required' => false,
                'data' => $articleData->getImageUrl(),
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ]
                    ]
                    )
                ]
            ])
            ->add('content',TextareaType::class, [
                'data' => $articleData->getContent(),
                'attr' => array(
                    'class' => 'tinymce'
                )
            ])
            ->add(
                'color',
                ColorType::class,
                [
                    'data' => $articleData->getColor() ?? '#00FF00',
                    'label' => 'Select accent color'
                ]
            )
            ->add(
                'tags_id', EntityType::class, [
                    'data' => $articleData->getTagsId(),
                    'class' => Tag::class,
                    'label' => "Select one or more tags",
                    'choice_label' => 'tag_title',
                    'multiple' => true
                ])
            ->add('submit', SubmitType::class)
                    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'id' => null
            
        ]);
    }
    
}
