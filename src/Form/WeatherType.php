<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class WeatherType
 * @package App\Form
 */
class WeatherType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('city', TextType::class, [
               'required' => true,
               'label' => 'city',
               'empty_data' => 'Wrocław',
               'constraints' => [
                   new NotBlank(),
                   new Length([
                       'min' => 2,
                       'max' => 255
                   ])
               ]
       ])
           ->add('unit', ChoiceType::class, [
               'required' => true,
               'label' => 'unit_of_speed',
               'choices' => [
                   'M/S' => 'm/s',
                   'KM/H' => 'km/h',
                   'MPH' => 'mph'
               ]

           ])
       ;
    }
}