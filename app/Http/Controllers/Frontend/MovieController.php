<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Type;
use App\Models\Source;
use App\Models\Video;
use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Tag;

class MovieController extends Controller
{
    public function index()
    {
        //Nominations
        $movie_nominations = Movie::select('id', 'name', 'name_origin', 'description', 'card_cover', 'runtime', 'slug', 'rate')
            ->where('nominations', '1')
            ->orderBy('rate', 'desc')
            ->take(12)
            ->get();

        foreach ($movie_nominations as $movie) {
            $movie->types = $movie->Types()
            ->select('title', 'slug')
            ->get()
            ->toArray();
        }
        $movie_nominations = $movie_nominations->toArray();

        $movies = $this->getMovieWithGenre(1)->toArray();

        $tv_series = $this->getMovieWithGenre(2)->toArray();

        return view('frontend.pages.movie.home')->with([
            'movie_nominations' => $movie_nominations,
            'movies' => $movies,
            'tv_series' => $tv_series,
        ]);
    }

    public function getMovieWithGenre($genre)
    {
        $films = Movie::select('id', 'name', 'name_origin', 'age', 'description', 'card_cover', 'runtime', 'slug', 'rate', 'quality')
            ->where('genre', $genre)
            ->orderBy('release_year', 'desc')
            ->take(12)
            ->get();

        foreach ($films as $film) {
            $film->types = $film->Types()
                ->select('title', 'slug')
                ->get();

            $film->quality = $this->getOptionValueOfFilm('quality', $film);
            $film->country = $this->getOptionValueOfFilm('country', $film);
        }

        return $films;
    }

    public function getOptionValueOfFilm($name, $film)
    {
        $options = Option::select('id', 'name')->get();
        foreach ($options as $option) {
            if ($option->name == $name) {
                $optionValues = $option->optionValues;
                foreach ($optionValues as $optionValue) {
                    if ($optionValue->order == $film->$name) {
                        return $optionValue->name;
                    }
                }
            }
        }
    }

    public function watchMovie($slug, $sever = 1, $prioritize = 1, $video = null)
    {
        $movie = Movie::select('id', 'name', 'name_origin', 'age', 'description', 'card_cover', 'runtime', 'slug', 'rate', 'genre', 'country', 'quality', 'release_year')
            ->where('slug', $slug)
            ->first();
        if ($movie == null) {
            return abort(404);
        }
        $types = $movie->types->toArray();
        $tags = $movie->tags->toArray();

        $movie->quality = $this->getOptionValueOfFilm('quality', $movie);
        $movie->country = $this->getOptionValueOfFilm('country', $movie);

        if ($movie['genre'] == 1) {
            //Trường hợp phim lẻ
            if ($sever == 1 && $prioritize ==1) {
                $source = $movie->sources()
                    ->select('id', 'source_key', 'video_id', 'prioritize', 'status', 'movie_id', 'channel_id')
                    ->where('status', 1)
                    ->where('prioritize', $prioritize)
                    ->first();
            } else {
                $source = Source::select('sources.id', 'source_key', 'video_id', 'prioritize', 'sources.status', 'movie_id', 'channel_id')
                    ->join('movies', function ($join) {
                        $join->on('sources.movie_id', '=', 'movies.id');
                    })
                    ->join('channels', function ($join) {
                        $join->on('sources.channel_id', '=', 'channels.id');
                    })
                    ->where('channels.order', $sever)
                    ->where('sources.status', 1)
                    ->where('movies.id', $movie->id)
                    ->where('prioritize', $prioritize)
                    ->first();
            }
            if ($source == null) {
                return abort(404);
            }

            $channel = Channel::select('id', 'title', 'channel_type', 'status', 'order')
                ->where('status', 1)
                ->where('channels.id', $source->channel_id)
                ->first()
                ->toArray();

            $backups = Channel::select('channels.id', 'channels.title', 'channels.status', 'channel_type', 'order', 'sources.prioritize')
                ->join('sources', function ($join) {
                    $join->on('channels.id', '=', 'channel_id');
                })
                ->join('videos', function ($join) {
                    $join->on('sources.video_id', '=', 'videos.id');
                })
                ->where('channels.status', 1)
                ->where('videos.id', $source->video_id)
                ->get()
                ->toArray();

            $video=$movie->videos()
                ->select('videos.id', 'tags')
                ->first();
            if ($video == null) {
                return abort(404);
            }
            $source = $source->toArray();
            $video = $video->toArray();

            return view('frontend.pages.movie.movie_detail')->with([
                'movie' => $movie,
                'types' => $types,
                'source' => $source,
                'channel' => $channel,
                'backups' => $backups,
                'tags' => $tags,
                'video' => $video,
            ]);
        } else {
            //Trường hợp phim bộ
            if ($video == null) {
                $chap = Video::select('videos.id', 'videos.title', 'videos.status', 'tags', 'slug', 'videos.movie_id', 'playlist_id', 'chap')
                    ->where('videos.movie_id', $movie['id'])
                    ->where('chap', '1')
                    ->where('videos.status', '1')
                    ->where('playlists.status', '1')
                    ->join('playlists', function ($join) {
                        $join->on('playlist_id', '=', 'playlists.id');
                    })
                    ->where('order', 1)
                    ->first();
            } else {
                $chap = Video::select('id', 'title', 'status', 'slug', 'movie_id', 'playlist_id', 'chap')
                    ->where('status', '1')
                    ->where('slug', $video)
                    ->first();
            }
            if ($chap == null) {
                 return abort(404);
            }

            if ($sever == 1 && $prioritize == 1) {
                $source = Source::select('id', 'source_key', 'video_id', 'status', 'prioritize', 'status', 'movie_id', 'channel_id', 'video_id')
                    ->where('prioritize', $prioritize)
                    ->where('status', '1')
                    ->where('video_id', $chap->id)
                    ->first();
            } else {
                $source = Source::select('sources.id', 'source_key', 'sources.status', 'video_id', 'prioritize', 'sources.status', 'sources.movie_id', 'channel_id')
                    ->join('channels', function ($join) {
                        $join->on('sources.channel_id', '=', 'channels.id');
                    })
                    ->where('channels.order', $sever)
                    ->where('sources.status', '1')
                    ->where('video_id', $chap->id)
                    ->first();
            }

            $channel = Channel::select('id', 'channel_type', 'status', 'order')
                ->where('id', $source->channel_id)
                ->where('status', 1)
                ->first()
                ->toArray();

            $backups = Channel::select('channels.id', 'channels.title', 'channel_type', 'order', 'sources.prioritize')
                ->join('sources', function ($join) {
                    $join->on('channels.id', '=', 'channel_id');
                })
                ->join('videos', function ($join) {
                    $join->on('sources.video_id', '=', 'videos.id');
                })
                ->where('channels.status', 1)
                ->where('videos.id', $chap['id'])
                ->get()
                ->toArray();

            $playlists = $movie->playlists()
                ->select('id', 'title', 'description', 'status', 'order')
                ->where('status', 1)
                ->orderBy('order', 'asc')
                ->get();

            foreach ($playlists as $playlist) {
                $playlist->videos = $playlist->videos()
                    ->orderBy('chap')
                    ->get();
            }
            $playlists = $playlists->toArray();
            $movie = $movie->toArray();
            $source = $source->toArray();

            return view('frontend.pages.movie.movie_series')->with([
                'movie' => $movie,
                'types' => $types,
                'source' => $source,
                'playlists' => $playlists,
                'channel' => $channel,
                'backups' => $backups,
                'chap' => $chap,
                'tags' => $tags,
            ]);
        }
    }


    public function listMovie($key, $slug)
    {
        if ($key == 'type') {
            $type = Type::select('id', 'title', 'slug')
                ->where('slug', $slug)
                ->first();

            $movies = $type->movies()
                ->select('movies.id', 'name', 'name_origin', 'age', 'description', 'card_cover', 'runtime', 'slug', 'rate', 'genre', 'country', 'quality', 'release_year')
                ->paginate(12);

            foreach ($movies as $video) {
                $video->type = $video->types()
                    ->select('title', 'slug')
                    ->get();
            }
            $params['type'] = $type->toArray();
        } else if ($key == 'country') {
            $movies = $this->getMovieFromOption($key, $slug);
            $params['country'] = $this->getOptionValue($key, $slug)->toArray();
        } else if ($key == 'genre') {
            $movies = $this->getMovieFromOption($key, $slug);
            $params['genre'] = $this->getOptionValue($key, $slug)->toArray();
        }
        $filters= $this->getDataForFilter();

        return view('frontend.pages.movie.catalog_grid')->with([
            'movies' => $movies,
            'params' => $params,
            'filters' => $filters,
        ]);
    }

    public function getDataForFilter()
    {
        $filters['types'] = Type::select('id', 'title', 'slug')->get();
        $options = Option::select('id', 'name')->get();
        foreach ($options as $option) {
            if ($option->name == 'genre') {
                $filters['genre'] = $option->optionValues;
            } elseif ($option->name == 'quality') {
                $filters['quality'] = $option->optionValues;
            } elseif ($option->name == 'country') {
                $filters['country'] = $option->optionValues;
            }
        }

        return $filters;
    }

    public function getOptionValue($key, $slug)
    {
        $option_id = Option::select('id')
                ->where('name', $key)
                ->first();
        $option_value = OptionValue::select('name', 'order')
            ->where('option_id', $option_id->id)
            ->where('order', $slug)
            ->first();

        return $option_value;
    }

    public function getMovieFromOption($key, $slug)
    {
        $movies = Movie::select('id', 'name', 'age', 'card_cover', 'runtime', 'slug', 'rate', 'genre', 'country', 'quality', 'release_year')
                ->where($key, $slug)
                ->paginate(12);
        foreach ($movies as $video) {
            $video->type = $video->types()->select('title', 'slug')->get()->toArray();
        }

        return $movies;
    }

    public function filter(Request $request)
    {
        $movies = Movie::select('movies.id', 'name', 'name_origin', 'age', 'card_cover', 'runtime', 'slug', 'rate', 'genre', 'country', 'quality', 'release_year');

        if ($request->get('type') != null) {
            $movies = $movies->join('movie_type', function ($join) {
                $join->on('movies.id', '=', 'movie_type.movie_id');
            })
                ->where('movie_type.type_id', '=', $request->get('type'));

            $params['type'] = Type::select('id', 'title')
                ->where('id', $request->get('type'))
                ->first()
                ->toArray();
        }

        if ($request->get('genre') != null) {
            $movies = $movies->where('genre', $request->get('genre'));
            $option_id = Option::select('id')->where('name', 'genre')->first();
            $genre = OptionValue::select('id', 'name', 'order')
                ->where('option_id', $option_id->id)
                ->where('order', $request->get('genre'))
                ->first();
            $params['genre'] = $genre->toArray();
        }

        if ($request->get('country') != null) {
            $movies = $movies->where('country', $request->get('country'));
            $option_id = Option::select('id')->where('name', 'country')->first();
            $country = OptionValue::select('id', 'name', 'order')
                ->where('option_id', $option_id->id)
                ->where('order', $request->get('country'))
                ->first();
            $params['country'] = $country->toArray();
        }

        $movies = $movies->paginate(12);
        foreach ($movies as $video) {
            $video->type = $video->Types()
                ->select('types.id', 'title', 'slug')
                ->get()
                ->toArray();
        }

        $filters= $this->getDataForFilter();

        return view('frontend.pages.movie.catalog_grid')->with([
            'movies' => $movies,
            'params' => $params,
            'filters' => $filters,
        ]);
    }

    public function search(Request $request)
    {
        $movies = Movie::select('id', 'name', 'name_origin', 'age', 'card_cover', 'runtime', 'slug', 'rate', 'genre', 'country', 'quality', 'release_year')
        ->where('name', 'like', '%' . $request->get('search') . '%')
        ->paginate(12);
        foreach ($movies as $video) {
            $video->type = $video->types()
                ->select('title', 'slug')
                ->get()
                ->toArray();
        }

        return view('frontend.pages.movie.search')->with([
            'movies' => $movies,
            'key' => $request->get('search'),
        ]);
    }

    public function searchTag($slug)
    {
        $tag=  Tag::select('id', 'name', 'slug')
            ->where('slug', $slug)
            ->first();

        $movies = $tag->movies()
            ->select('movies.id', 'name', 'name_origin', 'age', 'card_cover', 'runtime', 'slug', 'rate', 'genre', 'country', 'quality', 'release_year')
            ->paginate(12);
        foreach ($movies as $video) {
            $video->type = $video->types()
                ->select('title', 'slug')
                ->get()
                ->toArray();
        }
        $tag = $tag->toArray();
        return view('frontend.pages.movie.searchTag')->with([
            'movies' => $movies,
            'tag' => $tag,
        ]);
    }
}
