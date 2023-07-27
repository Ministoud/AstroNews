<?php

namespace App\Form;

use App\Event\Subscriber\NotifierSubscriber;
use App\Notification\Channel\DatabaseChannel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Notifier\Channel\BrowserChannel;
use Symfony\Component\Notifier\Channel\ChatChannel;
use Symfony\Component\Notifier\Channel\EmailChannel;
use Symfony\Component\Notifier\Channel\SmsChannel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationSettingsType extends AbstractType
{
    private $notificationEvents;
    private $choices = [];

    public function __construct(iterable $channels = [])
    {
        $this->notificationEvents = NotifierSubscriber::getSubscribedEvents();

        foreach ($channels as $channel)
        {
            $this->choices[self::getChannelName(get_class($channel))] = self::getChannelValue(get_class($channel));
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        foreach ($this->notificationEvents as $eventName => $eventValue)
        {
            $builder->add(str_replace('.', '_', $eventName), ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->choices,
                'label' => self::getEventDescription($eventName),
                'label_attr' => array(
                    'class' => 'checkbox-inline',
                )
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    private static function getChannelName($className): ?string
    {
        $names = [
            DatabaseChannel::class => "Centre de notification",
            BrowserChannel::class => "Popup de notification",
            EmailChannel::class => "Email",
            SmsChannel::class => "SMS",
            ChatChannel::class => "Slack"
        ];

        if(array_key_exists($className, $names))
        {
           return $names[$className];
        }

        return 'default channel name';
    }

    public static function getChannelValue($className): ?string
    {
        $values = [
            DatabaseChannel::class => "chanDatabase",
            BrowserChannel::class => "chanBrowser",
            EmailChannel::class => "chanEmail",
            SmsChannel::class => "chanSms",
            ChatChannel::class => "chanChat"
        ];

        if(array_key_exists($className, $values))
        {
            return $values[$className];
        }

        return 'default channel value';
    }

    private static function getEventDescription($eventName): string
    {
        $descriptions = [
            'article.created' => 'Création d\'un article dans un section que vous suivez',
            'article.edited' => 'Édition d\'un de vos articles par un administrateur'
        ];

        if(array_key_exists($eventName, $descriptions))
        {
            return $descriptions[$eventName];
        }

        return 'default event description';
    }
}
