<?php

use App\Folder;
use App\User;
use App\Mailbox;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id');
        $mailboxIds = Mailbox::pluck('id');

        foreach ($mailboxIds as $mailboxId) {
                Folder::firstOrCreate([
                    'mailbox_id'   => $mailboxId,
                    'type'         => 10,
                    'total_count'  => 0,
                    'active_count' => 0,
                ]);
        }
    }
}
