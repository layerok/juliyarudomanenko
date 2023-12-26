<?php

namespace App;

class Paginator
{
    private $currentPage;
    private $recordsCount;
    private $perPageLimit = 10;
    private $maxPagesCount;
    private $pagesCount;

    public function getPerPageLimit(): int {
        return $this->perPageLimit;
    }

    public function getMaxPagesCount(): int {
        return $this->maxPagesCount;
    }

    public function getRecordsCount(): int {
        return $this->recordsCount;
    }

    public function getCurrentPage(): int {
        return $this->currentPage;
    }
 
    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
        return $this;
    }
 
    public function setRecordsCount($recordsCount)
    {
        $this->recordsCount = $recordsCount;
        return $this;
    }
 
    public function setPerPageLimit($perPageLimit)
    {
        $this->perPageLimit = $perPageLimit;
        return $this;
    }
 
    public function setMaxPageCount($maxPagesCount)
    {
        $this->maxPagesCount = $maxPagesCount;
        return $this;
    }
 
    private function getPageRange()
    {
        $this->pagesCount = ceil($this->recordsCount / $this->perPageLimit);
 
        $firstPageInRange = $this->currentPage - (int)($this->maxPagesCount / 2);
               
        $firstPageInRange = $this->pagesCount - $firstPageInRange < $this->maxPagesCount
                                       ? $this->pagesCount - $this->maxPagesCount + 1
                                       : $firstPageInRange;
 
        $firstPageInRange = $firstPageInRange < 1 ? 1 : $firstPageInRange;
 
        $lastPageInRange = $firstPageInRange + ($this->maxPagesCount - 1);
               
        $lastPageInRange = $lastPageInRange > $this->pagesCount
                                       ? $this->pagesCount
                                       : $lastPageInRange;
        $lastPageInRange = $lastPageInRange <= 0 ? 1 : $lastPageInRange;
        return range($firstPageInRange, $lastPageInRange);
    }
 
    public function getPages()
    {
 
        $pages = [
                'current' => $this->currentPage,
                'pages'   => $this->getPageRange(),
                'limit'   => $this->perPageLimit
        ];
 
        $prevPage = $this->currentPage != 1 ? $this->currentPage - 1 : null;
        $nextPage = $this->currentPage < $this->pagesCount ? $this->currentPage + 1 : null;
        $lastPage = $nextPage ? $this->pagesCount : null;
 
        !$prevPage ?: $pages['prev'] = $prevPage;
        !$nextPage ?: $pages['next'] = $nextPage;
        !$lastPage ?: $pages['last'] = $lastPage;
 
        return $pages;
    }
}