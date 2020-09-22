<?php
namespace App\Http\ViewComposers;

use App\Models\Option;
use App\Models\Type;
use Illuminate\View\View;

class MovieComposer
{
    public $main_menu = [];

    public function __construct()
    {
        $this->main_menu['types_movie'] = Type::select('title', 'slug')->get()->toArray();
        $genre = Option::where('name', 'genre')->first();
        $country = Option::where('name', 'country')->first();
        $this->main_menu['country'] = $country->optionValues->toArray();
        $this->main_menu['genre'] = $genre->optionValues->toArray();
    }

    public function compose(View $view)
    {
        $view->with('main_menu', $this->main_menu);
    }
}
