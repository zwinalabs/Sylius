<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\Type;

use Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportDataFetcherFormListener;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Report form type.
 */
class ReportType extends AbstractResourceType
{
    /**
    * Calculator registry.
    *
    * @var ServiceRegistryInterface
    */
    protected $dataFetcherRegistry;

    /**
    * Constructor.
    *
    * @param ServiceRegistryInterface $dataFetcherRegistry
    */
    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $dataFetcherRegistry)
    {
        parent::__construct($dataClass, $validationGroups);
        
        $this->dataFetcherRegistry = $dataFetcherRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildReportDataFetcherFormListener($this->dataFetcherRegistry, $builder->getFormFactory()))
            ->add('name', 'text', array(
                'label' => 'sylius.form.report.name',
                'required' => true
            ))
            ->add('description', 'textarea', array(
                'label'    => 'sylius.form.report.description',
                'required' => false,
            ))
            ->add('dataFetcher', 'sylius_data_fetcher_choice', array(
                'label'    => 'sylius.form.report.data_fetcher',
            ))
        ;

        $prototypes = array();
        $prototypes['dataFetchers'] = array();
     
        foreach ($this->dataFetcherRegistry->all() as $type => $dataFetcher) {
            $formType = sprintf('sylius_data_fetcher_%s', $dataFetcher->getType());
            
            if (!$formType) {
                continue;
            }

            try {
                $prototypes['dataFetchers'][$type] = $builder->create('dataFetcherConfiguration', $formType)->getForm();
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
    * {@inheritdoc}
    */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = array();
     
        foreach ($form->getConfig()->getAttribute('prototypes') as $group => $prototypes) {
            foreach ($prototypes as $type => $prototype) {
               $view->vars['prototypes'][$group.'_'.$type] = $prototype->createView($view);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_report';
    }
}