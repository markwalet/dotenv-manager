<?php

namespace MarkWalet\DotenvManager\Commands;

use Illuminate\Console\Command;
use MarkWalet\DotenvManager\DotenvManager;

class AddDotenvValueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:add {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a value to the `.env` file.';

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
        $this->info("Hallo");
        $this->env->add(
            $this->argument('key'),
            $this->argument('value')
        );
    }
}