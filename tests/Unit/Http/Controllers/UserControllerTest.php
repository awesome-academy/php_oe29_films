<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Backend\UserController;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Http\RedirectResponse;
use App\Repositories\User\UserRepositoryInterface;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;

class UserControllerTest extends TestCase
{
    use WithoutMiddleware;

    protected $user;

    public function setUp(): void
    {
        $this->userMock = Mockery::mock(UserRepositoryInterface::class);
        $this->userController = new Usercontroller(
            $this->userMock,
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->userController);
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_email_with_user()
    {
        $request = [
            'email' => 'minhquang31011998@gmail.com',
        ];
        $requestData = new Request($request);

        $user = new User();
        $user->id = 1;
        $user->email = "minhquang31011998@gmail.com";
        $user->name = "Quang";

        $this->userMock
            ->shouldReceive('findUserByEmail')
            ->with($requestData)
            ->once()
            ->andReturn($user);

        $password = Str::random(config('config.random_password'));

        $this->userMock
            ->shouldReceive('changePassword')
            ->once()
            ->andReturn(true);
        $this->userMock
            ->shouldReceive('storeQueue')
            ->once();
        $data = [
            'password' => $password,
        ];
        $result = $this->userController->sendEmailForgotPassword($requestData);
        $this->assertEquals('auth.email_success', $result->getName());
    }

    public function test_send_email_with_user_null()
    {
        $request = [
            'email' => 'minhquang31011998@gmail.com',
        ];
        $requestData = new Request($request);

        $this->userMock
            ->shouldReceive('findUserByEmail')
            ->with($requestData)
            ->once()
            ->andReturn(null);

        $result = $this->userController->sendEmailForgotPassword($requestData);
        $this->assertEquals('auth.forgot_password', $result->getName());
        $data = $result->getData();
        $this->assertArrayHasKey('error', $data);
    }

    public function test_get_form_forgot_password_function()
    {
        $result = $this->userController->getFormForgotPassword();
        $this->assertEquals('auth.forgot_password', $result->getName());
    }
}
