<?php

namespace Pharaonic\Laravel\Translatable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Transtable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:translatable {name} {--migrations}?';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create translatable Models & Migrations';

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
     * @return int
     */
    public function handle()
    {
        $this->info(':: Pharaonic - Translatable ::');

        $name       = $this->argument('name');
        $migrations = $this->option('migrations');

        $path       = File::exists(app_path('/Models')) ? app_path('/Models/') : app_path('/');
        $mainName   = explode('/', $name);
        $mainName   = ucfirst(array_pop($mainName));

        $mainTable  = Str::snake(Str::pluralStudly($mainName));
        $mainKey    = Str::snake(Str::studly($mainName)) . '_id';
        $transTable = Str::snake(Str::pluralStudly($mainName . 'Translation'));

        // MAIN FILE
        $this->call('make:model', ['name' => $name, '--migration' => $migrations]);
        if (File::exists($path . $name . '.php')) {
            $content = str_replace(
                [
                    "\n    //\n",
                    '}',
                    '{',
                    "\nclass $mainName"
                ],
                [
                    '',
                    PHP_EOL . '    /**' . PHP_EOL .
                        '     * The table associated with the model.' . PHP_EOL .
                        '     *' . PHP_EOL .
                        '     * @var string' . PHP_EOL .
                        '     */' . PHP_EOL .
                        '    protected $table = \'' . $mainTable . '\';' . PHP_EOL . PHP_EOL .
                        '    /**' . PHP_EOL .
                        '     * The attributes that are mass assignable.' . PHP_EOL .
                        '     *' . PHP_EOL .
                        '     * @var array' . PHP_EOL .
                        '     */' . PHP_EOL .
                        '    protected $fillable = [];' . PHP_EOL .
                        PHP_EOL . '    /**' . PHP_EOL .
                        '     * Translatable attributes names.' . PHP_EOL .
                        '     *' . PHP_EOL .
                        '     * @var array' . PHP_EOL .
                        '     */' . PHP_EOL .
                        '    protected $translatableAttributes = [];' . PHP_EOL .
                        '}',
                    '{' . PHP_EOL . '    use Translatable;' . PHP_EOL,
                    "use Pharaonic\Laravel\Translatable\Translatable;\n\nclass $mainName"
                ],
                File::get($path . $name . '.php')
            );

            File::put($path . $name . '.php', $content);
        }

        sleep(2);

        // TRANSLATION FILE
        $this->call('make:model', ['name' => $name . 'Translation', '--migration' => $migrations]);
        if (File::exists($path . $name . 'Translation.php')) {
            $content = str_replace(
                [
                    "\n    //\n",
                    '}',
                ],
                [
                    '',
                    PHP_EOL . '    /**' . PHP_EOL .
                        '     * The table associated with the model.' . PHP_EOL .
                        '     *' . PHP_EOL .
                        '     * @var string' . PHP_EOL .
                        '     */' . PHP_EOL .
                        '    protected $table = \'' . $transTable . '\';' . PHP_EOL . PHP_EOL .
                        '    /**' . PHP_EOL .
                        '     * The attributes that are mass assignable.' . PHP_EOL .
                        '     *' . PHP_EOL .
                        '     * @var array' . PHP_EOL .
                        '     */' . PHP_EOL .
                        '    protected $fillable = [\'locale\', \'' . $mainKey . '\'];' . PHP_EOL .
                        PHP_EOL . '    /**' . PHP_EOL .
                        '     * Indicates if the model should be timestamped.' . PHP_EOL .
                        '     *' . PHP_EOL .
                        '     * @var bool' . PHP_EOL .
                        '     */' . PHP_EOL .
                        '    public $timestamps = false;' . PHP_EOL .
                        '}',
                ],
                File::get($path . $name . 'Translation.php')
            );

            File::put($path . $name . 'Translation.php', $content);
        }

        // MIGRATIONS
        if ($migrations) {
            $file = File::glob(database_path('migrations/') . '*create_' . $transTable . '_table.php');
            if (!empty($file)) {
                $file   = $file[0];
                $content    = File::get($file);
                $hasId      = strpos($content, '$table->id()') !== false;

                $content    = str_replace(
                    '$table->timestamps();',
                    PHP_EOL . '            $table->string(\'locale\')->index();' . PHP_EOL .
                        '            $table->unsigned' . ($hasId ? 'Big' : '') . 'Integer(\'' . $mainKey . '\');' . PHP_EOL .
                        '            $table->unique([\'' . $mainKey . '\', \'locale\']);' . PHP_EOL .
                        '            $table->foreign(\'' . $mainKey . '\')->references(\'id\')->on(\'' . $mainTable . '\')->onDelete(\'cascade\');' . PHP_EOL,
                    $content
                );
                File::put($file, $content);
            }
        }

        return true;
    }
}
