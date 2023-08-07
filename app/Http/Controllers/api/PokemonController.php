<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\API\BaseController;
use App\Http\Resources\pokemon as pokemonResource;
use App\Models\pokemon;
use Illuminate\Http\Request;
use Validator;

class PokemonController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pokemon = pokemon::all();

        //pokemonResource from resource to make make format for the results
        return $this->sendResponse(pokemonResource::collection($pokemon), 'Posts fetched.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $pokemon = pokemon::create($input);
        return $this->sendResponse(new pokemonResource($pokemon), 'Post Created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pokemon = pokemon::find($id);

        if(is_null($pokemon)){
            return $this->sendError('Post Does Not exist.');
        }
        
        return $this->sendResponse(new pokemonResource($pokemon), 'Post fetched.');
    }

    public function search(Request $request)
    {
        //->dd() or ->dump() //check query
        $arrValidQuery = array('name', 'description', 'img', 'created_at');

        if(!$request->input()){
            return $this->sendError('Please send seach query ('. implode(',',$arrValidQuery) .')');
        }   

        $arrInvalidQuery = array();
        foreach ($request->input() as $key => $value) {
            if(!in_array(strtolower($key),$arrValidQuery)){
                $arrInvalidQuery[] = $key;
            }
        }

        if (count($arrInvalidQuery) > 0)  return $this->sendError('Invalid search query ('. implode(',',$arrInvalidQuery) .')');

        //use when if you want to append query if input is not null or empty
        $pokemon = Pokemon::when($request->input('name'), function ($query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($request->input('description'), function ($query, $description) {
                $query->where('description', 'like', '%' . $description . '%');
            })
            ->when($request->input('created_at'), function ($query, $created_at) {
                $formatted_input_date = date('Y/m/d', strtotime($created_at));
                $query->whereDate('created_at', $formatted_input_date);
            })
            ->get();
        
        if(count($pokemon) <= 0){
            return $this->sendError('Post Does Not exist.');
        }

        return $this->sendResponse(pokemonResource::collection($pokemon), 'Post fetched');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pokemon $pokemon)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required',
            'description'=>'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $pokemon->name = $input['name'];
        $pokemon->description = $input['description'];
        $pokemon->img = $input['img'];
        $pokemon->save();

        return $this->sendResponse(new pokemonResource($pokemon), 'Post Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(pokemon $pokemon)
    {
        $pokemon->delete();
        return $this->sendResponse([],'Post Deleted.');
    }
}
