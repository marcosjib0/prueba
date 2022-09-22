<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posts;
use App\Models\User;

class ApiController extends Controller
{
    public function users()
    {
        $users = User::all();
        $cont = 0;
        $value = [];
        foreach($users as $user)
        {
            $value[$cont]['id'] = $user->id;
            $value[$cont]['name'] = $user->name;
            $value[$cont]['email'] = $user->email;
            $value[$cont]['city'] = $user->city;

            $posts = Posts::where('user_id', $user->user_id)->orderBy('rating', 'DESC')->get();

            $cont2 = 0;
            foreach($posts as $post)
            {
                $value[$cont][$cont2]['post']['id'] = $post->id;
                $value[$cont][$cont2]['post']['user_id'] = $post->user_id;
                $value[$cont][$cont2]['post']['body'] = $post->body;
                $value[$cont][$cont2]['post']['title'] = $post->title;
                $cont2++;
            }
            $cont++;
        }
        return json_encode($value);
    }

    public function top()
    {

        $users = User::all();
        $cont = 0;
        $value = [];
        foreach($users as $user)
        {
            $value[$cont]['name'] = $user->name;
            $Maxposts = Posts::where('user_id', $user->user_id)->max('rating');
            $posts = Posts::where('rating', $Maxposts)->where('user_id', $user->user_id)->get();
            foreach($posts as $post)
            {
                $value[$cont]['post']['id'] = $post->id;
                $value[$cont]['post']['body'] = $post->body;
                $value[$cont]['post']['title'] = $post->title;
                $value[$cont]['post']['rating'] = $post->rating;
            }
            $cont++;
        }
        return json_encode($value);
    }

    public function id($id)
    {
        $post =  Posts::where('id', $id)->first();

        if($post == null)
            abort(404);

        $value['id'] = $post->id;
        $value['post']['title'] = $post->title;
        $value['body'] = $post->body;
        $value['name'] = User::where('user_id', $post->user_id)->first()->name;

        return json_encode($value);

    }
}
