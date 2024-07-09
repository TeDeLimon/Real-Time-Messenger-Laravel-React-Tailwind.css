<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // This will generate the fake data for the message

        $sender_id = $this->faker->randomElement([0, 1]);

        // Then, we are gonna pick a random receiver from the users table

        if ($sender_id === 0) {

            $sender_id = $this->faker
                ->randomElement(\App\Models\User::where('id', '!=', 1)
                    ->pluck('id')->toArray());
            $receiver_id = 1;
        } else {

            $receiver_id = $this->faker
                ->randomElement(\App\Models\User::where('id', '!=', 1)
                    ->pluck('id')->toArray());
        }

        $group_id = null;

        // We are gonna select a group with a probability of 50% chance

        if ($this->faker->boolean(50)) {

            // Get a random group id from the groups table

            $group_id = $this->faker->randomElement(\App\Models\Group::pluck('id')->toArray());

            // Get the group to get the users and select a random sender from the group

            $group = \App\Models\Group::find($group_id);

            $sender_id = $this->faker->randomElement($group->users->pluck('id')->toArray());

            $receiver_id = null;
        }

        return [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'group_id' => $group_id,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
