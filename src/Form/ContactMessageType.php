<?php

namespace App\Form;

use App\Entity\ContactMessage;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('name', TextType::class, ['required' => false])
            ->add('message', TextareaType::class)
            ->add('phone', TelType::class, ['required' => false])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'mapped' => false,
                'constraints' => [
                    new \EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
        ]);
    }
}