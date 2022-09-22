<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posts;
use App\Models\User;

class GetApiController extends Controller
{
    const TOTAL_RESULTADOS = 50;

    private function postRepetido()
    {
        //Compruebo si hay repetidos, si no hay return -1 si hay return userId
        $post =  Posts::all();

        for($i = 0; $i < self::TOTAL_RESULTADOS - 1; $i++)
        {
            if((strcmp($post[$i]->body, $post[$i++]->body) === 0) && (strcmp($post[$i]->title, $post[$i++]->title) === 0))
                return $post[$i]->id;
        }

        return -1;
    }

    private function setUser()
    {
        //Obtenemos los usuarios únicos de la base de datos
        $users = Posts::select('user_id')->distinct()->get();

        $json = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/users'), true);

        for($i = 0; $i < count($users); $i++)
        {
            for($j = 0; $j < count($json); $j++)
            {
                if($users[$i]->user_id == $json[$j]['id'])
                {
                    $user = new User();
                    $user->name = $json[$j]['name'];
                    $user->email = $json[$j]['email'];
                    $user->city = $json[$j]['address']['city'];
                    $user->user_id = $users[$i]->user_id;
                    $user->save();
                }
            }//guardamos los usuarios únicos
        }

    }

    public function index()
    {
        //Obtenemmos la información de la APi
        $json = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/posts'), true);


        for( $i = 0; $i < self::TOTAL_RESULTADOS; $i++)
        {   //almacenamos los 50 primeros valores
            $post = new Posts();
            $post->user_id = $json[$i]['userId'];
            $post->title = $json[$i]['title'];
            $post->body = $json[$i]['body'];
            $post->rating = str_word_count($post->title, 0) * 2 + str_word_count($post->body, 0);
            $post->save();
        }

        $postRepetido = $this->postRepetido();

        if($postRepetido != -1)
        {
            $json = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/posts'), true);

            $post = Posts::where('id', $postRepetido)->first();


            for( $i = 0; $i < self::TOTAL_RESULTADOS; $i++)
            {   //buscamos el post repetidos
                if($json[$i]['id'] == $json[$i]['userId']);
                {
                    $post->body = $json[$i]['body'];
                    $post->save();
                    break; // lo hemos encontrado y salidmos
                }
            }
        }
        $this->setUser();
    }
}
