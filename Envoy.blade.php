@setup
date_default_timezone_set('Europe/Amsterdam');

require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['DEPLOY_SERVER', 'DEPLOY_REPOSITORY', 'DEPLOY_PATH'])->notEmpty();

$server = env('DEPLOY_SERVER');
$repository = env('DEPLOY_REPOSITORY');
$baseDir = env('DEPLOY_PATH');
$branch = env('DEPLOY_BRANCH');
$phpPath = env('PHP_PATH');

$releasesDir = "{$baseDir}/releases";
$currentDir = "{$baseDir}/current";
$newReleaseName = date('Ymd-His').'-'.$branch;
$newReleaseDir = "{$releasesDir}/{$newReleaseName}";
$user = get_current_user();

function logMessage($message)
{
return "echo '\033[32m" .$message. "\033[0m';\n";
}

@endsetup

@servers(['local' => '127.0.0.1', 'remote' => [$server]])

@story('deploy')
showInfo
startDeployment
fetchRepo
runComposer
updateSymlinks
generateAssets
cleanupAssets
optimizeInstallation
migrateDatabase
blessNewRelease
cleanOldReleases
@endstory

@story('deployOnlyCode')
pullRepo
@endstory

@story('migrateFreshDatabaseWithSeeds')
migrateFreshDatabaseWithSeeds
@endstory

@task('showInfo', ['on' => 'local'])
{{ logMessage('ℹ️  Make sure you have copied your identify file to the server using `ssh-copy-id '.$server.'`') }}
@endtask

@task('startDeployment', ['on' => 'local', 'confirm' => true])
{{ logMessage('🏃  Starting deployment...') }}
@endtask

@task('fetchRepo', ['on' => 'remote'])
# Setting up releases directory
[ -d {{ $releasesDir }} ] || mkdir {{ $releasesDir }};
cd {{ $releasesDir }};

{{ logMessage('📁  Creating folder for new release...') }}
mkdir {{ $newReleaseDir }};

{{ logMessage('🌀  Cloning repository...') }}
git clone -b {{ $branch }} --depth 1 git@github.com:{{ $repository }}.git {{ $newReleaseName }}

# Mark release
cd {{ $newReleaseDir }}
echo "{{ $newReleaseName }}" > public/release-name.txt
@endtask

@task('runComposer', ['on' => 'remote'])
{{ logMessage('🔨  Running `composer install`...') }}
cd {{ $newReleaseDir }};
{{ $phpPath }} /usr/local/bin/composer install --prefer-dist --no-scripts --no-dev --quiet --optimize-autoloader;
@endtask

@task('updateSymlinks', ['on' => 'remote'])
{{ logMessage('🔨  Creating symbolic links...') }}
# Copy storage directory if it's not there
cd {{ $baseDir }};
[ -d storage ] || cp -rn {{ $newReleaseDir }}/storage {{ $baseDir }};

# Check if .env file is present, if not create it
cd {{ $baseDir }};
[ -f .env ] || touch .env

# Create storage symbolic link
rm -rf {{ $newReleaseDir }}/storage;
cd {{ $newReleaseDir }};
ln -nfs {{ $baseDir }}/storage storage;

# Create .env symbolic link
cd {{ $newReleaseDir }};
ln -nfs {{ $baseDir }}/.env .env;
@endtask

@task('pullRepo', ['on' => 'remote'])
{{ logMessage('📁  Going to current dir...') }}
cd {{ $currentDir }};

{{ logMessage('🌀  Pulling latest version...') }}
git pull
@endtask

@task('optimizeInstallation', ['on' => 'remote'])
{{ logMessage('✨  Optimizing installation...') }}
cd {{ $newReleaseDir }};

{{ $phpPath }} -d disable_functions='' artisan clear-compiled;
{{ $phpPath }} -d disable_functions='' artisan optimize;
@endtask

@task('migrateDatabase', ['on' => 'remote'])
{{ logMessage('✨  Migrating database...') }}
cd {{ $newReleaseDir }};
{{ $phpPath }} -d disable_functions='' artisan migrate --force;
@endtask

@task('migrateFreshDatabaseWithSeeds', ['on' => 'remote'])
{{ logMessage('✨  Refreshing and seeding database...') }}
cd {{ $currentDir }};
{{ $phpPath }} -d disable_functions='' artisan migrate:fresh --seed --force;
@endtask

@task('generateAssets', ['on' => 'remote'])
{{ logMessage('🔨  Generating assets...') }}
cd {{ $newReleaseDir }};
npm install
npm run production
@endtask

@task('cleanupAssets', ['on' => 'remote'])
{{ logMessage('🗑  Cleaning up assets...') }}
cd {{ $newReleaseDir }};
rm -rf node_modules
@endtask

@task('blessNewRelease', ['on' => 'remote'])
{{ logMessage("🙏  Blessing new release...") }}
# Create symbolic link from current folder to new release
ln -nfs {{ $newReleaseDir }} {{ $currentDir }};

# Optimize Laravel
cd {{ $newReleaseDir }}
{{ $phpPath }} -d disable_functions='' artisan config:clear
{{ $phpPath }} -d disable_functions='' artisan cache:clear
{{ $phpPath }} -d disable_functions='' artisan view:clear
{{ $phpPath }} -d disable_functions='' artisan config:cache
{{ $phpPath }} -d disable_functions='' artisan route:cache
{{ $phpPath }} -d disable_functions='' artisan storage:link
@endtask

@task('cleanOldReleases', ['on' => 'remote'])
# This will list our releases by modification time and delete all but the 3 most recent.
ls -dt {{ $releasesDir }}/* | tail -n +4 | xargs -d "\n" rm -rf;
@endtask
