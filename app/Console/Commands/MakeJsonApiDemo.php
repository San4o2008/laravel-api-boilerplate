<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class MakeJsonApiDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:demo                    
                    {--force : Overwrite existing files by default}
                    {--test  : Add files postfix for test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create JsonApi Demo entities';

    protected $testPostfix = '-test';

    protected $migrations = [
        'create_likes_table.stub'                   => 'create_likes_table.php',
        'create_membership_table.stub'              => 'create_membership_table.php',
        'create_skills_table.stub'                  => 'create_skills_table.php',
        'create_teams_table.stub'                   => 'create_teams_table.php',
        'add_foreign_keys_to_likes_table.stub'      => 'add_foreign_keys_to_likes_table.php',
        'add_foreign_keys_to_membership_table.stub' => 'add_foreign_keys_to_membership_table.php',
        'add_foreign_keys_to_skills_table.stub'     => 'add_foreign_keys_to_skills_table.php',
        'add_foreign_keys_to_teams_table.stub'      => 'add_foreign_keys_to_teams_table.php'
    ];

    protected $seeds = [
        'TeamsTableSeeder.stub'     => 'TeamsTableSeeder.php',
        'TeamUsersTableSeeder.stub' => 'TeamUsersTableSeeder.php',
        'JsonApiSeeder.stub' => 'JsonApiSeeder.php'
    ];

    protected $controllers = [
        'LikesController.stub' => 'LikesController.php',
        'SkillsController.stub' => 'SkillsController.php',
        'TeamsController.stub' => 'TeamsController.php',
        'UsersController.stub' => 'UsersController.php'
    ];

    protected $models = [
        'Like.stub' => 'Like.php',
        'Skill.stub' => 'Skill.php',
        'Team.stub' => 'Team.php'
    ];

    protected $jsonapiEntities = [
        'JsonApi/Likes/Hydrator.php' => 'JsonApi/Likes/Hydrator.php',
        'JsonApi/Likes/Request.php' => 'JsonApi/Likes/Request.php',
        'JsonApi/Likes/Schema.php' => 'JsonApi/Likes/Schema.php',
        'JsonApi/Likes/Search.php' => 'JsonApi/Likes/Search.php',
        'JsonApi/Likes/Validators.php' => 'JsonApi/Likes/Validators.php',

        'JsonApi/Skills/Hydrator.php' => 'JsonApi/Skills/Hydrator.php',
        'JsonApi/Skills/Request.php' => 'JsonApi/Skills/Request.php',
        'JsonApi/Skills/Schema.php' => 'JsonApi/Skills/Schema.php',
        'JsonApi/Skills/Search.php' => 'JsonApi/Skills/Search.php',
        'JsonApi/Skills/Validators.php' => 'JsonApi/Skills/Validators.php',

        'JsonApi/Teams/Hydrator.php' => 'JsonApi/Teams/Hydrator.php',
        'JsonApi/Teams/Request.php' => 'JsonApi/Teams/Request.php',
        'JsonApi/Teams/Schema.php' => 'JsonApi/Teams/Schema.php',
        'JsonApi/Teams/Search.php' => 'JsonApi/Teams/Search.php',
        'JsonApi/Teams/Validators.php' => 'JsonApi/Teams/Validators.php',

        'JsonApi/Users/Hydrator.php' => 'JsonApi/Users/Hydrator.php',
        'JsonApi/Users/Request.php' => 'JsonApi/Users/Request.php',
        'JsonApi/Users/Schema.php' => 'JsonApi/Users/Schema.php',
        'JsonApi/Users/Search.php' => 'JsonApi/Users/Search.php',
        'JsonApi/Users/Validators.php' => 'JsonApi/Users/Validators.php',
    ];


    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('test')) {
            $this->setupTest();
        }

        $this->fire();

        $this->exportControllers();
        $this->exportModels();

        $this->exportMigrations();
        $this->exportSeeds();

        if (!$this->option('test')) {
            $this::call('optimize');
        }

        $this->info('JsonApi demo entities generated successfully.');
    }

    /**
     *
     */
    protected function setupTest()
    {
        foreach ($this->migrations as $key => $value) {
            $this->migrations[$key] = $value . $this->testPostfix;
        }
        foreach ($this->seeds as $key => $value) {
            $this->seeds[$key] = $value . $this->testPostfix;
        }
        foreach ($this->controllers as $key => $value) {
            $this->controllers[$key] = $value . $this->testPostfix;
        }
        foreach ($this->models as $key => $value) {
            $this->models[$key] = $value . $this->testPostfix;
        }
        foreach ($this->jsonapiEntities as $key => $value) {
            $this->jsonapiEntities[$key] = $value . $this->testPostfix;
        }
    }

    /**
     *
     */
    public function fire()
    {
        $this->createDirectories();

        $this->copyJsonApiEntities();
    }

    /**
     *
     */
    protected function createDirectories()
    {
        if (!is_dir(app_path('JsonApi/Likes'))) {
            mkdir(app_path('JsonApi/Likes'), 0755, true);
        }
        if (!is_dir(app_path('JsonApi/Skills'))) {
            mkdir(app_path('JsonApi/Skills'), 0755, true);
        }
        if (!is_dir(app_path('JsonApi/Teams'))) {
            mkdir(app_path('JsonApi/Teams'), 0755, true);
        }
        if (!is_dir(app_path('JsonApi/Users'))) {
            mkdir(app_path('JsonApi/Users'), 0755, true);
        }
    }

    /**
     *
     */
    protected function copyJsonApiEntities()
    {
        foreach ($this->jsonapiEntities as $key => $value) {
            if (file_exists(app_path('JsonApi/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                base_path('stubs/'.$key),
                app_path($value)
            );
        }
    }

    /**
     *
     */
    protected function exportControllers()
    {
        foreach ($this->controllers as $key => $value) {
            if (file_exists(app_path('Http/Controllers/Api/v1/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                base_path('stubs/Controllers/'.$key),
                app_path('Http/Controllers/Api/v1/'.$value)
            );
        }
    }

    /**
     *
     */
    protected function exportModels()
    {
        foreach ($this->models as $key => $value) {
            if (file_exists(app_path('Models/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] model already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                base_path('stubs/Models/'.$key),
                app_path('Models/'.$value)
            );
        }
    }

    /**
     *
     */
    protected function exportMigrations()
    {
        $counter = 0;
        foreach ($this->migrations as $key => $value) {
            if (file_exists(database_path('migrations/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] migration already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                base_path('stubs/migrations/' . $key),
                database_path('migrations/'. date('Y_m_d_Hi') . '0' . $counter++ . '_' . $value)
            );
        }
    }

    /**
     *
     */
    protected function exportSeeds()
    {
        foreach ($this->seeds as $key => $value) {
            if (file_exists(database_path('seeds/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                base_path('stubs/seeds/'.$key),
                database_path('seeds/'.$value)
            );
        }
    }

    /**
     * @param $src
     * @param $dst
     */
    protected function recurse_copy($src,$dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
