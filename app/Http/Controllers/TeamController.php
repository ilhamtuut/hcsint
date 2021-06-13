<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('backend.team.index');
    }

    public function getSponsorTree($id,$page)
    {
        $take = 5;
        $skip = $take * $page;
        $nodeDataArray = array();
        $user = User::with('childs')->where('id',$id)->first();
        if($user){
            $data = array(
                'key'=>$user->id,
                'parent'=>$user->parent_id,
                'name'=>ucfirst($user->name),
                'username'=>ucfirst($user->username),
                'program' => $user->program()->sum('amount'),
                'downline' => $user->childs()->count(),
                'page' => $page
            );
            array_push($nodeDataArray, $data);
            $downline = $user->childs()->take($take)->skip($skip)->get();
            if(count($downline) > 0){
                if($skip > 0){
                    $data = array(
                        'key'=>0,
                        'parent'=>intval($id),
                        'name'=> 'Prev',
                        'username'=> 'Prev',
                        'program' => 0,
                        'downline' => 0,
                        'page' => $page
                    );
                    array_push($nodeDataArray, $data);
                }
                foreach ($downline as $key => $value) {
                    $parent_id = $value->parent_id;
                    $data = array(
                        'key'=>$value->id,
                        'parent'=>$value->parent_id,
                        'name'=>ucfirst($value->name),
                        'username'=>ucfirst($value->username),
                        'program' => $value->program()->sum('amount'),
                        'downline' => $value->childs()->count(),
                        'page' => $page
                    );
                    array_push($nodeDataArray, $data);
                }
                if(count($downline) >= $take){
                    $data = array(
                        'key'=>0,
                        'parent'=>intval($id),
                        'name'=> 'Next',
                        'username'=> 'Next',
                        'program' => 0,
                        'downline' => 0,
                        'page' => $page
                    );
                    array_push($nodeDataArray, $data);
                }
            }else{
                $data = array(
                    'key'=>0,
                    'parent'=>$user->id,
                    'name'=>'Empty',
                    'username'=>'Empty',
                    'program' => 0,
                    'downline' => 0,
                    'page' => $page
                );
                array_push($nodeDataArray, $data);
            }
        }

        $data = array(
            "class"=> "go.TreeModel",
            "nodeDataArray"=>$nodeDataArray
        );
        return $data;
    }
}
