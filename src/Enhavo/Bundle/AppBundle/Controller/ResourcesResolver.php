<?php
/**
 * ResourcesResolver.php
 *
 * @since 05/07/16
 * @author gseidel
 */

namespace Enhavo\Bundle\AppBundle\Controller;

use Enhavo\Bundle\AppBundle\Filter\FilterQueryBuilder;
use Enhavo\Bundle\AppBundle\Repository\EntityRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesResolverInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration as SyliusRequestConfiguration;

/**
 * Class ResourcesResolver
 *
 * This class is used because sylius don't allow finding by criteria if
 * paginate an limit is false.
 *
 * @package Enhavo\Bundle\AppBundle\Controller
 */
class ResourcesResolver implements ResourcesResolverInterface
{
    /**
     * @var FilterQueryBuilder
     */
    private $filterQueryBuilder;

    /**
     * ResourcesResolver constructor.
     *
     * @param FilterQueryBuilder $filterQueryBuilder
     */
    public function __construct(FilterQueryBuilder $filterQueryBuilder)
    {
        $this->filterQueryBuilder = $filterQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources(SyliusRequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if($requestConfiguration instanceof RequestConfiguration && $repository instanceof EntityRepositoryInterface) {
            if($requestConfiguration->hasFilters()) {
                $query = $this->filterQueryBuilder->buildQueryFromRequestConfiguration($requestConfiguration);
                return $repository->filter($query);
            }
        }

        if (null !== $repositoryMethod = $requestConfiguration->getRepositoryMethod()) {
            $callable = [$repository, $repositoryMethod];
            $resources = call_user_func_array($callable, $requestConfiguration->getRepositoryArguments());

            return $resources;
        }

        if (!$requestConfiguration->isPaginated() && !$requestConfiguration->isLimited()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());
        }

        if (!$requestConfiguration->isPaginated()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting(), $requestConfiguration->getLimit());
        }

        return $repository->createPaginator($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());
    }
}
