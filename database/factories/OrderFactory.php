<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1,50),
            'order_amount' => $this->faker->randomNumber(3),
            'order_status'=>$this->faker->randomElement(['pending','delivered','failed']),
            'restaurant_id'=>$this->faker->randomElement([3,4,5]),
        ];
    }
}
