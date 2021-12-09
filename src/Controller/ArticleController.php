<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Entity\Comment;
#[Route('article')]

class ArticleController extends AbstractController
{
    /** ****************************
     *  ARTICLES LIST ROUTE 
     ******************************/

    #[Route('/articles/{page}', name: 'articles')]
    public function articles(int $page = 1)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAllPaginated($page);
        $mostPopularTags = $repo->getMostPopularTags();
        return $this->render('front/articles.html.twig', [
            'articles' => $articles,
            'mostPopularTags' => $mostPopularTags
        ]);
    }
    /** ****************************
     *  SINGLE ARTICLE ROUTE 
     ******************************/
    #[Route('/article-single/{id}', name: 'article-single')]
    public function singleArticle($id, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $mostPopularTags = $repo->getMostPopularTags();
        $articleData = $repo->getAllArticleData($id);
        $article = $repo->find($id);
        // $article = $repo->find($id);
        // $comments = $article->getComments();
        // dump($comments);
        // $commentsRepo = $this->getDoctrine()->getRepository(Comment::class);
        // $commentsWithUsers = $commentsRepo->getCommentWithUsersData($comments);

        $commentForm = $this->createForm(CommentFormType::class);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $commentForm->getData();
            $comment = new Comment();
            $comment->setContent($data->getContent());
            $comment->setUser($this->getUser());
            $comment->setArticle($article);
            $em->persist($comment);
            $em->flush();
           
            
        }
      
        return $this->render('front/article-single.html.twig', [
            'article' => $articleData,
            'mostPopularTags' => $mostPopularTags,
            'commentForm' => $commentForm->createView()
        ]);
    }
     /** ****************************
     *  NEW ARTICLE ROUTE 
     ******************************/

    #[Route('/article-new', name: 'article-new')]
    public function articleNew(Request $request)
    {
        $articleForm = $this->createForm(ArticleFormType::class);
        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        
           $em = $this->getDoctrine()->getManager();
           $formData = $articleForm->getData();
          // dump($formData);exit;
           $article = new Article();
           $article->setUser($this->getUser());
           $article->setTitle($formData->getTitle());
           $article->setContent($formData->getContent());
           $article->setColor($formData->getColor());
           $tagsList = $formData->getTagsId();
           
           foreach ($tagsList as $tag) {
               $article->addTagsId($tag);
           }
         //  $article->addTagsId($formData->getTagsId());
           $coverImage = $articleForm->get('image_url')->getData();
           if ($coverImage) {
              $pathForUploads = $this->getParameter('covers_dir');
              try {
              $originalFilename = pathInfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
              $filename = $originalFilename . md5(uniqid()) . '.' . $coverImage->guessExtension();
              $coverImage->move($pathForUploads, $filename);
              
              $article->setImageUrl($pathForUploads . '/' . $filename);
              } catch (FileException $e) {
                echo "error";exit;
              }
           }
           $em->persist($article);
           $em->flush();
           return $this->redirect($this->generateUrl('profile'));
           
        }

        return $this->render('front/article-new.html.twig', [
            'articleForm' => $articleForm->createView()
        ]);
    }

     /** ****************************
     *  EDIT EXISITING ARTICLE ROUTE 
     ******************************/

     #[Route('/article-edit/{id}', name: "article-edit")]
     public function editArticle(int $id, Request $request) {

        // Check if this id's article owns user
        $currentUserId = $this->getUser()->getId();

        $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id);
        if ($currentUserId != $article->getUser()->getId()) {
          echo ("Wrong user. Access denied");exit;  
        }
        $articleForm = $this->createForm(ArticleFormType::class, $data = null, array(
            'id' => $id
        ));
        $articleForm->handleRequest($request);
       
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
          
            $articleEdited = $articleForm->getData();
            $article->setTitle($articleEdited->getTitle());
            $article->setContent($articleEdited->getContent());
            $article->setColor($articleEdited->getColor());

            $coverImage = $articleForm->get('image_url')->getData();

            if ($coverImage) {
                $pathForUploads = $this->getParameter('covers_dir');
                try {
                $originalFilename = pathInfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = $originalFilename . md5(uniqid()) . '.' . $coverImage->guessExtension();
                $coverImage->move($pathForUploads, $filename);
                
                $article->setImageUrl($pathForUploads . '/' . $filename);
                } catch (FileException $e) {
                  echo "error";exit;
                }
             } 
            $tags = $article->getTagsId();
            foreach($article->getTagsId() as $tag ) {
            $article->removeTagsId($tag);
            }
            foreach($articleEdited->getTagsId() as $tag) {
                $article->addTagsId($tag);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirect($this->generateUrl('profile'));
           
        }
        return $this->render('front/article-edit.html.twig', [
            'article' => $article,
            'articleForm' => $articleForm->createView()
        ]);
     }

      /** ****************************
     *  REMOVE ARTICLE ROUTE 
     ******************************/
    #[Route('/remove/{id}', name:'remove')]
    public function remove($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('profile');
    }
}
