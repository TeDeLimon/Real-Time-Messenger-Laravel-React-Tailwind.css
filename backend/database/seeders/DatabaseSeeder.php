<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Models\Conversation;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // This is the admin user

        User::factory()->create([
            'name' => 'John Nieve',
            'email' => 'john@nieve.com',
            'password' => bcrypt('password'),
            'is_admin' => true
        ]);

        User::factory()->create([
            'name' => 'Daenerys Targaryen',
            'email' => 'daenerys@targaryen.com',
            'password' => bcrypt('password')
        ]);

        User::factory(10)->create();

        // Create 5 groups with the admin user as owner
        for ($i = 0; $i < 5; $i++) {

            $group = Group::factory()->create([
                'owner_id' => 1
            ]);

            // Get 2 to 5 random users and attach them to the group, including the admin user

            $users = User::inRandomOrder()->limit(rand(2, 5))->pluck('id');

            // Admin user can be duplicated, so we add unique values to the array

            $group->users()->attach(array_unique([1, ...$users]));
        }

        Message::factory(1000)->create();

        // Remember that group_id has a 50% chance of being null

        $messages = Message::whereNull('group_id')->orderBy('created_at')->get();

        // To identify the conversation, we are gonna use the sender_id and receiver_id. We must group by peers [1, 2] and [2, 1] to get the conversation

        $conversations = $messages->groupBy(function ($message) {

            // Create a unique key for the conversation, sorting the ids and joining them with an underscore

            return collect([$message->sender_id, $message->receiver_id])->sort()->implode('_');
        })->map(function ($groupedMessages) {

            return [
                'user_id1' => $groupedMessages->first()->sender_id,
                'user_id2' => $groupedMessages->first()->receiver_id,
                'last_message_id' => $groupedMessages->last()->id,
                'created_at' => new Carbon(),
                'updated_at' => new Carbon()
            ];
        })->values();

        Conversation::insertOrIgnore($conversations->toArray());
    }
}
