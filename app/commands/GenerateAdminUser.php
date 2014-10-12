<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateAdminUser extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'users:createadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an administrator user.';

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
     * @return mixed
     */
    public function fire()
    {
        $password = $this->argument( 'password' );
        $firstname = $this->argument( 'first_name' );
        $lastname = $this->argument( 'last_name' );
        $email = $this->argument( 'email' );

        try {
            $user = new User();
            $user->password = Hash::make( $password );
            $user->email_address = $email;
            $user->first_name = $firstname;
            $user->last_name = $lastname;
            $user->save();
            $this->info( 'User created successfully.' );
        } catch ( Exception $e ) {
            $this->error( $e );
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [ 'first_name', InputArgument::REQUIRED, 'The user\'s first name.' ],
            [ 'last_name', InputArgument::REQUIRED, 'The user\'s last name.' ],
            [ 'email', InputArgument::REQUIRED, 'User email.' ],
            [ 'password', InputArgument::REQUIRED, 'User password.' ]
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [ ];
    }

}
