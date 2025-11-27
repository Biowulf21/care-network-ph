<?php

namespace Tests\Feature\Livewire\Appointments;

use App\Livewire\Appointments\Calendar;
use Livewire\Livewire;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(Calendar::class)
            ->assertStatus(200);
    }
}
