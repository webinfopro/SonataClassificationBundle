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

use Sonata\ClassificationBundle\Model\Category as ModelCategory;

abstract class BaseCategory extends ModelCategory
{
	protected $count;

	public function setCount($cnt) {
		$this->count = $cnt;
		return $this;
	}

	public function getCount() {
		return $this->count;
	}

	public function disableChildrenLazyLoading()
    {
        if (is_object($this->children)) {
            $this->children->setInitialized(true);
        }
    }
}
