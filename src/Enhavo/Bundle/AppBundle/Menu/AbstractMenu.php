<?php
/**
 * AbstractMenuBuilder.php
 *
 * @since 20/09/16
 * @author gseidel
 */

namespace Enhavo\Bundle\AppBundle\Menu;

use Enhavo\Bundle\AppBundle\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractMenu extends AbstractType implements MenuInterface
{
    public function getPermission(array $options)
    {
        return $this->getOption('role', $options, null);
    }

    public function isHidden(array $options)
    {
        return (boolean)$this->getOption('hidden', $options, null);
    }

    public function isActive(array $options)
    {
        $route = $this->getOption('route', $options, null);
        $request = $this->container->get('request_stack')->getMasterRequest();
        $currentRoute = $request->get('_route');
        return $currentRoute == $route;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'route' => null,
            'role' => null,
            'hidden' => false,
        ]);
    }
}