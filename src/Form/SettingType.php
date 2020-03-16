<?php

namespace App\Form;

use App\Entity\UserNotificationSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// TODO: Récupérer la liste des channels dynamiquement et créer une table de "traduction" ici pour les labels
class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chanDatabase', null, [
                'label' => 'Centre de notification'
            ])
            ->add('chanEmail', null, [
                'label' => 'Email'
            ])
            ->add('chanSms', null, [
                'label' => 'SMS'
            ])
            ->add('chanBrowser', null, [
                'label' => 'Popup de notificaiton'
            ])
            ->add('chanChat', null, [
                'label' => 'Slack'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserNotificationSettings::class,
        ]);
    }
}
