<?php

namespace App\Http\Controllers\Backend;

use App\Models\Req;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReqRequest;
use Yajra\Datatables\Datatables;

class RequestController extends Controller
{
    public function index()
    {
        return view('backend.requests.index');
    }

    public function getData(Request $request)
    {
        $reqs = Req::select('id', 'content', 'user_id', 'created_at')->get();
        foreach ($reqs as $req) {
            $req->email = $req->user->email;
        }

        return DataTables::of($reqs->toArray())
            ->editColumn('id', function ($req) {
                return '<div class="main__table-text">' . $req['id'] . '</div>';
            })
            ->editColumn('email', function ($req) {
                return '<div class="main__table-text">' . $req['email'] . '</div>';
            })
            ->editColumn('content', function ($req) {
                return '<div class="main__table-text">' . $req['content'] . '</div>';
            })
            ->addColumn('action', function ($req) {
                return
                    '<div class="main__table-btns">
                        <form action="'. route('request.destroy', $req['id']) . '" method="POST">
                            '. csrf_field() .'
                            '. method_field('DELETE') .'
                            <button type="submit" class="main__table-btn main__table-btn--delete" data-toggle="tooltip" title="Delete">
                                <i class="icon ion-ios-trash"></i>
                            </button>
                        </form>
                    </div>';
            })
            ->rawColumns(['id', 'email', 'content', 'action'])
            ->make(true);
    }

    public function store(ReqRequest $request)
    {
        $req = new Req();
        $req->content = $request->get('content');
        $req->user_id = $request->get('user_id');
        $req->save();
    }

    public function destroy($reqId)
    {
        $req = Req::destroy($reqId);

        return redirect()->route('request.index');
    }
}
