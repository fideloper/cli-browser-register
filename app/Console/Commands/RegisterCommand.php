<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

class RegisterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // TODO: CLI should hit your API route,
        //       here we're using the current app
        $baseURl = url('/api/cli-session');

        $cliSessionResponse = Http::post($baseURl, [
            'name' => gethostname(),
        ]);

        if (! $cliSessionResponse->successful()) {
            $this->error("Could not start registration session");
            return Command::FAILURE;
        }

        $cliSession = $cliSessionResponse->json();

        $stop = now()->addMinutes(15);

        // TODO: Using "open" is different per OS
        //   example: https://github.com/laravel/framework/blob/10.x/src/Illuminate/Foundation/Console/DocsCommand.php#L372-L400
        Process::run("open ".$cliSession['url']);

        $apiToken = null;
        while(now()->lte($stop)) {

            // check session status to see if user
            // finished registration process
            $response = Http::get($baseURl.'/'.$cliSession['id']);

            // non-20x response is unexpected
            if (! $response->successful()) {
                $this->error('could not register');
                return Command::FAILURE;
            }

            if ($response->status() == 200) {
                // response includes an API token
                $apiToken = $response->json('api_token');
                $this->info('Success! Retrieved API token: ' . $apiToken);
                break;
            }

            // Else we assume an HTTP 202, meaning
            // "keep trying" every few seconds
            sleep(2);
        }

        // TODO: Store API token somewhere for future use
        //       e.g. ~/.<my-app>/.config.yml
    }
}
