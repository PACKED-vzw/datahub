<?php

namespace App\Console\Commands;

use App\Services\SearchManager;
use App\Services\StorageManager;
use Illuminate\Console\Command;

class SearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $searchManager;

    protected $storageManager;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SearchManager $searchManager, StorageManager $storageManager)
    {
        parent::__construct();
        $this->searchManager = $searchManager;
        $this->storageManager = $storageManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bag = $this->storageManager->all();
        foreach ($bag['rows'] as $package) {
            $this->searchManager->index($package);
        }
    }
}
