<?php
/**
 * TextFilter.php
 *
 * @since 19/01/17
 * @author gseidel
 */

namespace Enhavo\Bundle\AppBundle\Filter\Filter;

use Enhavo\Bundle\AppBundle\Filter\AbstractFilter;
use Enhavo\Bundle\AppBundle\Filter\FilterQuery;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoCompleteEntityFilter extends AbstractFilter
{
    public function render($options, $name)
    {
        return $this->renderTemplate($options['template'], [
            'type' => $this->getType(),
            'auto_complete_data' => $this->getAutoCompleteData($options),
            'name' => $name,
        ]);
    }

    private function getAutoCompleteData($options)
    {
        $router = $this->container->get('router');
        $translator = $this->container->get('translator');
        return [
            'url' => $router->generate($options['route'], $options['route_parameters']),
            'route' => $options['route'],
            'route_parameters' => $options['route_parameters'],
            'value' => null,
            'multiple' => false,
            'minimum_input_length' => $options['minimum_input_length'],
            'placeholder' => sprintf('-- %s --', $translator->trans($options['label'], [], $options['translation_domain']))
        ];
    }

    public function buildQuery(FilterQuery $query, $options, $value)
    {
        if ($value == '') {
            return;
        }

        $property = $this->getRequiredOption('property', $options);
        $propertyPath = explode('.', $property);
        $query->addWhere('id', FilterQuery::OPERATOR_EQUALS, $value, $propertyPath);
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->setDefaults([
            'template' => 'EnhavoAppBundle:Filter:auto_complete_entity.html.twig',
            'route_parameters' => [],
            'minimum_input_length' => 3
        ]);

        $optionsResolver->setRequired([
            'route'
        ]);
    }

    public function getType()
    {
        return 'auto_complete_entity';
    }
}