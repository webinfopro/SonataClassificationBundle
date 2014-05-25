<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Document;

use Sonata\CoreBundle\Model\BaseDocumentManager;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class CategoryManager extends BaseDocumentManager implements CategoryManagerInterface
{
	/**
	 * @var array
	 */
	protected $categories;

	public function getRootCategoriesPager($page = 1, $limit = 25, $criteria = array())
	{
		$page = (int) $page == 0 ? 1 : (int) $page;

		$queryBuilder = $this->getRepository()->createQueryBuilder($this->class)
		->field('parent')->Exists(false);

		$pager = new Pager($limit);
		$pager->setQuery(new ProxyQuery($queryBuilder));
		$pager->setPage($page);
		$pager->init();

		return $pager;
	}

	/**
	 * @param integer $categoryId
	 * @param integer $page
	 * @param integer $limit
	 * @param array   $criteria
	 *
	 * @return PagerInterface
	 */
	public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25, $criteria = array())
	{
		$queryBuilder = $this->getRepository()->createQueryBuilder($this->class)
		->field('parent.$id')->Equals($categoryId);

		$pager = new Pager($limit);
		$pager->setQuery(new ProxyQuery($queryBuilder));
		$pager->setPage($page);
		$pager->init();

		return $pager;
	}

	/**
	 * @return CategoryInterface
	 */
	public function getRootCategory()
	{
		$this->loadCategories();

		return $this->categories[0];
	}

	/**
	 * @return array
	 */
	public function getCategories()
	{
		$this->loadCategories();

		return $this->categories;
	}

	/**
	 * Load all categories from the database, the current method is very efficient for < 256 categories
	 *
	 */
	protected function loadCategories()
	{
		if ($this->categories !== null) {
			return;
		}

		$class = $this->getClass();

		$root = $this->create();
		$root->setName('root');

		$this->categories = array(
				0 => $root
		);

		$categories = $this->getRepository()->findAll();


		foreach ($categories as $category) {
			$this->categories[$category->getId()] = $category;

			$parent = $category->getParent();

			$category->disableChildrenLazyLoading();

			if (!$parent) {
				$root->addChild($category, true);

				continue;
			}

			$parent->addChild($category);
		}
	}
    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $maxPerPage = 10, array $sort = array())
    {
        $parameters = array();

        $query = $this->getRepository()
            ->createQueryBuilder($this->class);

        $criteria['enabled'] = isset($criteria['enabled']) ? $criteria['enabled'] : true;
        $query->field('enabled')->Equals($criteria['enabled']);

        $pager = new Pager();
        $pager->setMaxPerPage($maxPerPage);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
