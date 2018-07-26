<?php

namespace MarkWalet\DotenvManager\Commands;

use Illuminate\Console\Command;
use MarkWalet\DotenvManager\DotenvManager;

class RemoveDotenvValueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:remove {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a value from the `.env` file.';

    /**
     * The DotenvManager instance.
     *
     * @var DotenvManager
     */
    private $env;

    /**
     * Create a new command instance.
     *
     * @param DotenvManager $env
     */
    public function __construct(DotenvManager $env)
    {
        parent::__construct();

        $this->env = $env;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->env->delete(
            $this->argument('key')
        );
    }
}