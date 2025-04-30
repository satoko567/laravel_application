<?php

namespace App\Console\Commands;
use App\Http\Controllers\StoresController;

use Illuminate\Console\Command;

class CleanUpStoreImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '古い・反応のない店舗画像を削除してストレージを整理する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $controller = new StoresController;
        $controller->cleanUp();

        $this->info('不要な画像の削除が完了しました。');
    }
}
