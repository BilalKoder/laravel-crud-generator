<?php
namespace Laravelcrudgenerator\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {name}';
    protected $description = 'Generate CRUD (model, migration, request, controller, repo, routes)';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $this->info("Generating CRUD for: {$name}");

       
        $this->call('make:model', ['name' => $name, '-m' => true]);
        $this->call('make:request', ['name' => "{$name}Request"]);
        $this->call('make:controller', ['name' => "{$name}Controller", '--api' => true]);

        $this->generateRepository($name);
        $this->appendRoute($name);
        $this->info("CRUD for {$name} generated!");
    }

    protected function generateRepository($name)
    {
        $repoPath = app_path("Repositories/{$name}Repository.php");
        $interfacePath = app_path("Repositories/Interfaces/{$name}RepositoryInterface.php");

        if (!File::exists(app_path('Repositories/Interfaces'))) {
            File::makeDirectory(app_path('Repositories/Interfaces'), 0755, true);
        }

       $repoStub = <<<EOT
            <?php

            namespace App\Repositories;

            use App\Models\\$name;
            use App\Repositories\Interfaces\\{$name}RepositoryInterface;

            class {$name}Repository implements {$name}RepositoryInterface
            {
                public function all()
                {
                    return $name::all();
                }

                // Add more methods as needed
            }
        EOT;
        
        $interfaceStub = <<<EOT
            <?php

            namespace App\Repositories\Interfaces;

            interface {$name}RepositoryInterface
            {
                public function all();
            }
        EOT;

        File::put($repoPath, $repoStub);
        File::put($interfacePath, $interfaceStub);
    }

    protected function appendRoute($name)
    {
        $route = "\nRoute::apiResource('" . Str::kebab(Str::plural($name)) . "', {$name}Controller::class);";
        File::append(base_path('routes/api.php'), $route);
    }
}
