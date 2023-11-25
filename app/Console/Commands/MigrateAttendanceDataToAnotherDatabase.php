<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateAttendanceDataToAnotherDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-attendance-data-to-another-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command transfers the data in the anagkazo_attendance table in the anagkazo_attn_db database to the anagkazo_attendance table in the acc_db database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Starting migration of attendance data to another database...');
        $this->info('Connecting to anagkazo_attn_db database...');

    }
}
