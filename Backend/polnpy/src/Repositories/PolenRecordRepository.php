<?php
namespace App\Repositories;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Document\PolenDocument;
use App\Document\PolenRecord;

class PolenRecordRepository extends DocumentRepository
{
    public function findInRange($start, $end)
    {
        $qb = $this->createQueryBuilder(PolenRecord::class);
        
        $qb->field('recordDate')->gte($start);
        $qb->field('recordDate')->lte($end);
        
        return $qb->getQuery()->execute();
    }
    
    public function findByPolenAndRange(PolenDocument $polen, $start, $end)
    {
        $qb = $this->createQueryBuilder(PolenRecord::class)
            ->field('polen')->equals($polen);
        
        if ($start) {
            $qb->field('recordDate')->gt($start);
        }
        if ($end) {
            $qb->field('recordDate')->lt($end);
        }
        
        return $qb->getQuery()->execute();
    }
    
    public function findInfoForPolen(PolenDocument $polen)
    {
        $qb = $this->createAggregationBuilder()
            ->match()
                ->field('polen')->equals($polen)
            ->group()
                ->field('_id')->expression('null')
                ->field('max')->max('$recordDate')
                ->field('min')->min('$recordDate')
                ->field('max-concentration')->max('$concentration')
                ->field('min-concentration')->min('$concentration');
        
        return $qb->execute()->getSingleResult();
    }
}

