<?php

namespace Database\Factories;

use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'     => $this->faker->word(),
            'status'    => $this->faker->randomElement(VideoStatus::cases()),
        ];
    }
}
