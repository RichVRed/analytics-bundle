<?php

namespace Revinate\AnalyticsBundle\Controller;

use Revinate\AnalyticsBundle\AnalyticsInterface;
use Revinate\AnalyticsBundle\Elastica\FilterHelper;
use Revinate\AnalyticsBundle\Query\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends Controller {

    /**
     * Stats Controller
     */
    public function searchStatsAction($source) {
        $container = $this->get('service_container');
        $config = $container->getParameter('revinate_analytics.config');
        $post = json_decode($this->get('request_stack')->getMasterRequest()->getContent(), true);
        if (!isset($config['sources'][$source])) {
            return new JsonResponse(array('ok' => false), Response::HTTP_NOT_FOUND);
        }
        if (empty($post) || empty($post['dimensions']) || empty($post['metrics'])) {
            return new JsonResponse(array('ok' => false, '_help' => $this->getHelp()), Response::HTTP_BAD_REQUEST);
        }

        $sourceConfig = $config['sources'][$source];
        /** @var AnalyticsInterface $analytics */
        $analytics = new $sourceConfig['class']($container);

        $isNestedDimensions = isset($post['flags']['nestedDimensions']) ? $post['flags']['nestedDimensions'] : false;
        $elastica = $container->get('revinate.elastica.client');
        $queryBuilder = new QueryBuilder($elastica, $analytics);
        $queryBuilder
            //->setFilter(new \Elastica\Filter\Terms("propertyId", array(2, 362)))
            ->setIsNestedDimensions($isNestedDimensions)
            ->addDimensions($post['dimensions'])
            ->addMetrics($post['metrics'])
        ;
        if (! empty($post['filters'])) {
            $queryBuilder->setFilter($this->getFilters($post['filters']));
        }

        $resultSet = $queryBuilder->execute();
        $format = isset($post['format']) ? $post['format'] : 'nested';
        return new JsonResponse($resultSet->getResult($format));
    }

    /**
     * @param $postFilters
     * @return \Elastica\Filter\BoolAnd
     */
    protected function getFilters($postFilters) {
        $andFilter = new \Elastica\Filter\BoolAnd();
        foreach ($postFilters as $field => $filter) {
            $type = $filter[0];
            $value = $filter[1];
            switch ($type) {
                case FilterHelper::TYPE_VALUE:
                    $filter = FilterHelper::getValueFilter($field, $value);
                    break;
                case FilterHelper::TYPE_RANGE:
                    $filter = FilterHelper::getRangeFilter($field, $value);
                    break;
                case FilterHelper::TYPE_EXISTS:
                    $filter = FilterHelper::getExistsFilter($field);
                    break;
                case FilterHelper::TYPE_MISSING:
                    $filter = FilterHelper::getMissingFilter($field);
                    break;
            }
            $andFilter->addFilter($filter);
        }
        return $andFilter;
    }

    /**
     * @return array
     */
    protected function getHelp() {
        return array(
            'post' => array(
                'dimensions' => 'array of dimension names',
                'metrics' => 'array of metric names',
                'filters' => array(
                    'value' => array('field' => 'array values'),
                    'range' => array('field' => array('from' => 'value', 'to' => 'value'))
                ),
                'flags' => array(
                    'nestedDimensions' => 'true/false',
                ),
                'format' => 'flattened/raw/nested/tabular/google_data_table/chartjs'
            )
        );
    }
}