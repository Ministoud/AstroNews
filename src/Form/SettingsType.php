<?php

namespace App\Form;

use App\Event\Subscriber\NotifierSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    private $events;

    public function __construct()
    {
        $this->events = NotifierSubscriber::getSubscribedEvents();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->events as $eventName => $eventFunction) {
            $builder
                ->add('settings', CollectionType::class, [
                    'entry_type' => SettingType::class
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
