<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use App\Http\Requests\UserRequest;
use App\Http\Requests\PasswordRequest;
use Alert;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        return view('backend.users.index');
    }

    public function getData(Request $request)
    {
        $users = $this->user->getUsers($request);

        return DataTables::of($users->toArray())
            ->editColumn('id', function ($user) {
                return '<div class="main__table-text">' . $user['index'] . '</div>';
            })
            ->editColumn('name', function ($user) {
                return '<div class="main__table-text">' . $user['name'] . '</div>';
            })
            ->editColumn('email', function ($user) {
                return '<div class="main__table-text">' . $user['email'] . '</div>';
            })
            ->editColumn('is_active', function ($user) {
                $active = $user['is_active'] == config('config.status_active')
                    ?'<div class="main__table-text main__table-text--green">' . trans('active') . '</div>'
                    :'<div class="main__table-text main__table-text--red">' . trans('hidden') . '</div>';
                return $active;
            })

            ->addColumn('action', function ($user) {
                return
                    '<div class="main__table-btns">
                        <a href="' . route('backend.user.edit', $user['id']) . '" class="main__table-btn main__table-btn--edit open-modal" data-toggle="tooltip" title="Edit">
                            <i class="icon ion-ios-create"></i>
                        </a>
                    </div>';
            })
            ->rawColumns(['id','name', 'email', 'is_active','action'])
            ->make(true);
    }


    public function edit($userId)
    {
        $user = $this->user->find($userId);

        return view('backend.users.edit')->with([
            'user' => $user->toArray(),
        ]);
    }


    public function update(UserRequest $request, $userId)
    {
        $data = $request->all();
        $this->user->update($userId, $data);
        alert()->success(trans('updated'), trans('success'));

        return redirect()->route('backend.user.index');
    }

    public function changeStatus($userId)
    {
        return $this->user->changeStatus($userId);
    }

    public function changePassword(PasswordRequest $request, $userId)
    {
        $user = $this->user->find($userId);
        if (!password_verify($request->get('oldPassword'), $user->password)) {
            Session::flash('status', trans('oldpass_wrong'));

            return redirect()->back();
        }
        $this->user->changePassword($userId, $request);
        alert()->success(trans('updated'), trans('success'));

        return redirect()->route('backend.user.index');
    }

    public function destroy($userId)
    {
        $this->user->delete($userId);
        alert()->success(trans('deleted'), trans('success'));

        return redirect()->route('backend.user.index');
    }

    public function sendEmailForgotPassword(Request $request)
    {
        $user = $this->user->findUserByEmail($request);
        if ($user == null) {
            return view('auth.forgot_password')->with('error', trans('wrong_email'));
        }
        $password = Str::random(config('config.random_password'));
        $this->user->changePassword($user->id, $password);
        $this->user->storeQueue($user, $password);

        return view('auth.email_success');
    }

    public function getFormForgotPassword()
    {
        return view('auth.forgot_password');
    }
}
