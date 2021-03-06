<?php

namespace Revinate\AnalyticsBundle\Test\Elastica;

use Revinate\AnalyticsBundle\Test\Entity\Tag;
use Revinate\AnalyticsBundle\Test\Entity\View;

class DocumentHelper {

    /** @var  \Elastica\Type */
    protected $type;

    function __construct(\Elastica\Type $type)
    {
        $this->type = $type;
    }


    /**
     * @param $browser
     * @param $device
     * @param $siteId
     * @param null $dateString
     * @param int $views
     * @return $this
     */
    public function createView($browser, $device, $siteId, $dateString = null, $views = 1) {
        $date = $dateString ? new \DateTime($dateString) : new \DateTime('now');
        $dateTimestamp = $dateString ? strtotime($dateString) : strtotime('now');
        $view = new View();
        $view->setBrowser($browser);
        $view->setSiteId($siteId);
        $view->setDevice($device);
        $view->setViews($views);
        $view->setDate($date);
        $view->setDateTimestamp($dateTimestamp);
        $tags = array();
        $tags[] = new Tag("vip", 4.0);
        $tags[] = new Tag("new", 3.0);
        $view->setTags($tags);
        $this->type->addDocument(new \Elastica\Document("", $view->toArray()));
        return $this;
    }

    /**
     *
     */
    public function refresh() {
        $this->type->getIndex()->refresh(true);
    }
}