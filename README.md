# CLI to Browser

See how to make the CLI communicate with the browser (your app).

In this case, we'll create a user in your app via the CLI by sending them to the browser. When the user registers, a valid API token
is retrieved back by the CLI, allowing it to make future API calls against the applications (fictional) API.

## Files to Care About

1. `app/Console/Commands/RegisterCommand.php` - the command that lets users register. In reality this would likely be a separate project
(a cli app that users install locally, which can communicate to your app's API)
2. `routes/api.php` - Defines a Resource controller `App\Http\Controllers\CliSessionController`
3. `app/Http/Controllers/CliSessionController.php` - the controller allow CLI users to create/check on a CLI session
4. `app/Http/Controllers/Auth/RegisteredUserController.php` - Laravel Breeze register controller, which tweaks for CLI session registration
   - `resources/views/auth/register.blade.php` - companion tweaks for Breeze's registration
5. `app/Models/CliSession.php` - the `CLISession` model, which is simple
    - `database/migrations` - a migration to create the `cli_sessions` table
