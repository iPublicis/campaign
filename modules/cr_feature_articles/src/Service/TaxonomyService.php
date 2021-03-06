<?php

namespace Drupal\cr_feature_articles\Service;

use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Class TaxonomyService
 *
 * @package Drupal\cr_feature_articles\Service
 */
class TaxonomyService {

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory|\Drupal\Core\Entity\Query\QueryInterface
   */
  private $query;

  /**
   * @var \Drupal\Core\Entity\EntityManager
   */
  private $em;

  /**
   * TaxonomyService constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query
   * @param \Drupal\Core\Entity\EntityManager $em
   */
  public function __construct(QueryFactory $query, EntityManager $em) {
    $this->query = $query;
    $this->em = $em;
  }

  /**
   * @param null $tid
   * @param int $limit
   *
   * @return mixed
   */
  public function getArticleNodesByTermId($tid = null, $limit = 3) {
    $nodeQuery = $this->query->get('node', 'AND')
      ->condition('status', 1)
      ->condition('type', 'article');

    if ($tid) {
      $nodeQuery->condition('field_article_category.entity.tid', $tid);
    }

    $nodeQuery->condition('field_article_exclude_aggr', false)
      ->sort('field_article_publish_date', 'DESC')
      ->range(0, $limit);

    $nodeIds = $nodeQuery->execute();

    $nodes = $this->em->getStorage('node')->loadMultiple($nodeIds);

    return $this->em->getViewBuilder('node')->viewMultiple($nodes, 'teaser');
  }

}
