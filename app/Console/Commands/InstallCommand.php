<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[Signature('zebra:install
    {--db= : Database driver to use: sqlite or mysql}
    {--admin= : Username for the first administrator}
    {--email= : Email address for the first administrator}
    {--password= : Password for the first administrator}
    {--demo : Load the sample stories and accounts}')]
#[Description('Set up Zebra: environment, database, and the first administrator')]
class InstallCommand extends Command
{
    public function handle(): int
    {
        $this->components->info('Installing '.config('app.name').'.');

        $this->prepareEnvironment();

        if (! $this->configureDatabase()) {
            return self::FAILURE;
        }

        $this->call('migrate', ['--force' => true]);

        if (User::where('is_admin', true)->exists()) {
            $this->components->info('An administrator already exists, so none was created.');
        } else {
            $this->createAdministrator();
        }

        $demo = $this->option('demo') || ($this->input->isInteractive() && confirm('Load the sample stories and accounts?', default: false));

        if ($demo) {
            $this->call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);
            $this->components->info('Sample content loaded. Demo accounts (zebra, maxxx, galazy, okapi, quagga, tapir) all use the password "password".');
        }

        $this->newLine();
        $this->components->info('Done. Start the site with: composer run dev');

        return self::SUCCESS;
    }

    private function prepareEnvironment(): void
    {
        if (! file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
            $this->components->task('Created .env from .env.example');
        }

        if (! config('app.key')) {
            $this->call('key:generate', ['--force' => true]);
            // key:generate writes .env; reload so later steps see the key.
            config(['app.key' => $this->readEnv('APP_KEY')]);
        }
    }

    private function configureDatabase(): bool
    {
        $driver = $this->option('db') ?? ($this->input->isInteractive()
            ? select('Which database?', ['sqlite' => 'SQLite (a single file, no server needed)', 'mysql' => 'MySQL / MariaDB'], default: config('database.default') === 'mysql' ? 'mysql' : 'sqlite')
            : config('database.default'));

        if ($driver === 'sqlite') {
            $path = database_path('database.sqlite');

            if (! file_exists($path)) {
                touch($path);
                $this->components->task('Created database/database.sqlite');
            }

            $this->writeEnv(['DB_CONNECTION' => 'sqlite']);
            config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $path]);
        } elseif ($driver === 'mysql') {
            $current = config('database.connections.mysql');
            $settings = [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $this->input->isInteractive() ? text('Host', default: $current['host']) : $current['host'],
                'DB_PORT' => $this->input->isInteractive() ? text('Port', default: (string) $current['port']) : (string) $current['port'],
                'DB_DATABASE' => $this->input->isInteractive() ? text('Database name', default: $current['database'] === 'laravel' ? 'zebra' : $current['database']) : $current['database'],
                'DB_USERNAME' => $this->input->isInteractive() ? text('Username', default: $current['username']) : $current['username'],
                'DB_PASSWORD' => $this->input->isInteractive() ? password('Password') : (string) $current['password'],
            ];

            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $settings['DB_HOST'],
                'database.connections.mysql.port' => $settings['DB_PORT'],
                'database.connections.mysql.database' => $settings['DB_DATABASE'],
                'database.connections.mysql.username' => $settings['DB_USERNAME'],
                'database.connections.mysql.password' => $settings['DB_PASSWORD'],
            ]);

            $this->writeEnv($settings);
        } else {
            $this->components->error("Unknown database driver [{$driver}]. Use sqlite or mysql.");

            return false;
        }

        DB::purge();

        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            $this->components->error('Could not connect to the database: '.$e->getMessage());
            $this->line('  Check the DB_* settings in .env, then run this command again.');

            return false;
        }

        $this->components->task("Connected to {$driver}");

        return true;
    }

    private function createAdministrator(): void
    {
        $this->components->info('Create the first administrator.');

        $input = [
            'username' => $this->option('admin') ?? text('Username', required: true, validate: ['username' => 'regex:/^[A-Za-z0-9_-]{2,20}$/']),
            'email' => $this->option('email') ?? text('Email', required: true, validate: ['email' => 'email']),
            'password' => $this->option('password') ?? password('Password', required: true, hint: 'At least 8 characters.'),
        ];

        $validator = Validator::make($input, [
            'username' => ['required', 'regex:/^[A-Za-z0-9_-]{2,20}$/', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            $this->line('  No administrator was created. Run zebra:install again to retry.');

            return;
        }

        $user = User::create($input);
        $user->forceFill(['is_admin' => true])->save();

        $this->components->task("Created administrator {$user->username}");
    }

    /**
     * @param  array<string, string>  $values
     */
    private function writeEnv(array $values): void
    {
        $path = base_path('.env');
        $env = file_get_contents($path);

        foreach ($values as $key => $value) {
            $line = $key.'='.(preg_match('/[\s#"\'$]/', $value) ? '"'.addcslashes($value, '"\\$').'"' : $value);

            $env = preg_match("/^#?\s*{$key}=.*$/m", $env)
                ? preg_replace("/^#?\s*{$key}=.*$/m", str_replace(['\\', '$'], ['\\\\', '\\$'], $line), $env, 1)
                : rtrim($env)."\n{$line}\n";
        }

        file_put_contents($path, $env);
    }

    private function readEnv(string $key): ?string
    {
        preg_match("/^{$key}=(.*)$/m", file_get_contents(base_path('.env')), $match);

        return isset($match[1]) ? trim($match[1], '"') : null;
    }
}
