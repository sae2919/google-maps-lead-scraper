<?php

namespace App\Services;

use App\Models\Search;
use App\Jobs\ScrapeLeadJob;

class SearchService
{
    public function createSearch(string $query, int $userId): Search
    {
        $search = Search::create([

            'query'           => $query,

            'user_id'         => $userId,

            'status'          => 'running',

            'is_stopped'      => false,

            'is_paused'       => false,

            'processed_count' => 0,

            'total_places'    => 0,

            'started_at'      => now(),
        ]);

        ScrapeLeadJob::dispatch(
            $search->id,
            $query
        );

        return $search;
    }

    public function stop(Search $search): void
    {
        $search->update([

            'is_stopped' => true,

            'is_paused'  => false,

            'status'     => 'stopped'
        ]);
    }

    public function pause(Search $search): void
    {
        $search->update([

            'is_paused'  => true,

            'is_stopped' => false,

            'status'     => 'paused'
        ]);
    }

    public function resume(Search $search): void
    {
        $search->update([

            'is_stopped' => false,

            'is_paused'  => false,

            'status'     => 'running'
        ]);

        ScrapeLeadJob::dispatch(

            $search->id,

            $search->query,

            $search->processed_count
        );
    }
}