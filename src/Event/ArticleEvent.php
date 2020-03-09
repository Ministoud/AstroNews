<?php


namespace App\Event;

use App\Entity\Article;
use Symfony\Contracts\EventDispatcher\Event;

abstract class ArticleEvent extends Event
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function getArticle()
    {
        return $this->article;
    }
}