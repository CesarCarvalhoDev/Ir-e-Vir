<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new Service class';

    public function handle()
    {
        $name = $this->argument('name');

        $className = class_basename($name);
        $namespace = str_replace('/', '\\', dirname($name));

        $fullNamespace = "App\\Services" . ($namespace !== '.' ? "\\{$namespace}" : '');
        $path = app_path("Services/{$name}.php");

        if (file_exists($path)) {
            $this->error('Service already exists!');
            return;
        }

        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $stub = "<?php

namespace {{ namespace }};

class {{ class }}
{
    public function execute()
    {
        //
    }
}";

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$fullNamespace, $className],
            $stub
        );

        file_put_contents($path, $stub);

        $this->info("Service {$name} created successfully.");
    }
}
