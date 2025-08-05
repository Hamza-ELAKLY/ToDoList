<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartWebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websockets:serve {--host=127.0.0.1 : The host to serve on} {--port=6001 : The port to serve on}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the local WebSocket server using Soketi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        
        $this->info("Starting WebSocket Server...");
        $this->line("Server: <comment>http://{$host}:{$port}</comment>");
        $this->line("Press <comment>Ctrl+C</comment> to stop the server");
        $this->newLine();

        // check config file
        $configPath = base_path('soketi.config.json');
        if (!file_exists($configPath)) {
            $this->error('soketi.config.json not found!');
            $this->line('Make sure the config file exists.');
            return Command::FAILURE;
        }

        // check if soketi exists
        $checkProcess = new Process(['which', 'soketi']);
        $checkProcess->run();
        
        if (!$checkProcess->isSuccessful()) {
            $this->error('Soketi is not installed globally!');
            $this->line('Install with: <comment>npm install -g @soketi/soketi</comment>');
            return Command::FAILURE;
        }

        // start server
        $process = new Process([
            'soketi', 
            'start', 
            '--config=' . $configPath,
            '--host=' . $host,
            '--port=' . $port,
            '--debug' // Force debug mode for verbose output
        ]);

        $process->setTimeout(null);
        
        try {
            $process->start();
            
            $this->info('Server started!');
            $this->newLine();
            
            // show output
            foreach ($process as $type => $data) {
                if ($process::OUT === $type) {
                    $this->line($data);
                } else {
                    $this->error($data);
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to start WebSocket server: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
