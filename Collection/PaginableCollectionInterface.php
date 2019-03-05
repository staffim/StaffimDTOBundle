<?php

namespace Staffim\DTOBundle\Collection;

interface PaginableCollectionInterface extends \Countable
{
    /**
     * @return \Staffim\DTOBundle\Collection\Pagination
     */
    public function getPagination(): ?Pagination;

    public function count(): int;
}
