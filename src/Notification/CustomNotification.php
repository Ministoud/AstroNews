<?php


namespace App\Notification;


use Symfony\Component\Notifier\Notification\Notification;

class CustomNotification extends Notification
{
    public const ACTION_NONE = 0;
    public const ACTION_ARTICLE_REDIRECT = 1;

    private $action;
    private $subjectId;

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return CustomNotification
     */
    public function action($action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @param mixed $subjectId
     */
    public function subjectId($subjectId): self
    {
        $this->subjectId = $subjectId;

        return $this;
    }
}