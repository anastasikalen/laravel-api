<?php
namespace App\Jobs;

use App\Mail\WelcomeEmail; // Предполагается, что у вас есть этот класс почты
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        // Отправляем email с помощью класса почты
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
    }
}
