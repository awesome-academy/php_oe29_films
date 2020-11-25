<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendForgetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function handle()
    {
        $data = [
            'password' => $this->password,
        ];
        $user = $this->user;

        Mail::send('backend.mails.forgot_password', $data, function ($message) use ($user) {
            $message->from(config('config.contact_email'), config('config.contact_name'));
            $message->to($user->email, $user->name);
            $message->subject(trans('updated') . ' ' . trans('password'));
        });
    }
}
