<?php
namespace PalmaReal\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use PalmaReal\Http\Controllers\Controller;
use PalmaReal\Property;
use PalmaReal\User;
use PalmaReal\Media;
use PalmaReal\Message;
use PalmaReal\Historical;
use PalmaReal\PropertyTypes;
use PalmaReal\PropertiesTypesRelations;
use PalmaReal\Tag;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = PropertyTypes::paginate(5);
        $tags = Tag::paginate(5);
        $properties = Property::select('properties.*', 'admins.first_name as first_name', 'admins.last_name as last_name', 'admins.username as username')
        ->join('admins', 'admins.id', 'properties.admin')
        ->get();
        return view('admin.propiedades.index')->with(['properties' => $properties, 'tags' => $tags, 'types' => $types]);
    }
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $property = Property::FindOrFail($id);
        $modality = explode(',', $property -> modality);
        $p_types = explode(',', $property -> PropertyTypes);
        $p_tas = explode(',', $property -> tags);
        $types = PropertyTypes::all();
        $tags = Tag::all();
        return view('admin.propiedades.edit')->with([ 'property' => $property, 'modality' => $modality, 'types' => $types, 'p_types' => $p_types, 'tags' => $tags]);

        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = PropertyTypes::all();
        $tags = Tag::all();
        return view('admin.propiedades.create')->with(['types' => $types, 'tags' => $tags]);
    }

    /**
     * Store the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         try{                
            if (!empty($request -> file('image'))) {
                $formats = ['jpg', 'jpeg', 'png', 'svg'];
                foreach ($request -> file('image') as $element) {
                    if (in_array($element->getClientOriginalExtension(), $formats) && $element->getClientSize()>2000000) {
                        flash('Una o varias imagenes superan los 2MB de tamaño', 'danger');
                        return back();
                        exit(0);
                    }
                }
                if(empty($request -> tags)){
                    $request -> request -> add(['tags' => []]);
                }
                // $type = implode(',', $request -> type);
                $modality = implode(',', $request -> modality);
                $tags = implode(',', $request -> tags);
                $request -> merge(['modality' => $modality, 'tags' => $tags]);

                $types = array();

                // your arrays can be done like this
                foreach($request->get('type') as $val)
                {
                    array_push($types, $val);
                }

                $request-> request -> add([
                    'views' => 0,
                    'admin' => Auth::user() -> id,
                    'code' => mt_rand(000000, 999999)
                ]);
                Property::create($request ->all());                 
                $image = $request -> file('image');
                $idProperty = Property::all() -> last() -> id;

                foreach( $types as $type){
                    $relation = new PropertiesTypesRelations;
                    $relation->property_id = $idProperty;
                    $relation->properties_type_id = (int)$type;

                    $relation->save();
                }
                
                foreach ($image as $element) {
                    $type_file = $element->getClientOriginalExtension();                      
                    $file_name = time() . mt_rand() . $type_file;
                    $element -> move('imgs/properties/', $file_name ); 
                    $files_records[] = ['table' => 'properties', 'item' => $idProperty, 'url' => $file_name, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];                  
                }

                $insert_media = Media::insert($files_records);                                 
            }


            Historical::insert([
                'transaction' => 1, 
                'description' => 'La propiedad ' . $idProperty . ' fue creada', 
                'user' => Auth::user()->id, 
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            Log::error('Registro exitoso en PropertyController -> Store');
            flash('Proceso exitoso', 'success');
        }catch (\Exception $e) {
            Log::error('Error en PropertyController -> Store. Error: ['.$e.']');
            flash('¡Error! Ha ocurrido un problema', 'danger');

        }
        return redirect('admin/propiedades');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {        
        $property = Property::where('properties.id', $id)
        ->select('properties.*', 'admins.first_name as first_name', 'admins.last_name as last_name')
        ->join('admins', 'properties.admin', 'admins.id')
        ->first();

        $images = Media::where(['item' => $id])->get();
        $proximities = explode(',', $property -> proximities);
        $characteristics = explode(',', $property -> characteristics);
        $modalities = explode(',', $property -> modality);
        $types = explode(',', $property -> types);
        $tags = explode(',', $property -> tags);

        return view('admin.propiedades.show')
        ->with([
            'property' => $property, 
            'images' => $images,
            'proximities' => $proximities,
            'characteristics' => $characteristics,
            'modalities' => $modalities,
            'types' => $types,
            'tags' => $tags
        ]);
    }
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{  
            
            $type = implode(',', $request -> type);
            $modality = implode(',', $request -> modality);
            $tags = implode(',', $request -> tags);
            $request -> merge(['modality' => $modality, 'type' => $type, 'tags' => $tags]);
          
            Property::FindOrFail($id)->
            update($request -> all()); 

            Historical::insert([
                'transaction' => 2, 
                'description' => 'La propiedad ' . $id . ' fue editada', 
                'user' => Auth::user()->id, 
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            Log::error('Operacion exitosa en PropertyController -> update');
            flash('Proceso exitoso', 'success');
        }catch (\Exception $e) {
            Log::error('Error en PropertyController -> update. Error: ['.$e.']');
            flash('¡Error! Ha ocurrido un problema', 'danger');

        }
        return redirect()->route('propiedades.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       try{               
            $media = Media::where('item', $id)->get();       
            foreach ($media as $element) {
                if ($delete=Storage::disk('properties')->has($element -> url)) {
                    $delete=Storage::disk('properties')->delete($element -> url);
                }
            } 
            
            Media::where('item', $id) -> delete();
            Property::FindOrFail($id) -> delete();
            Historical::insert([
                'transaction' => 3, 
                'description' => 'La propiedad ' . $id . ' fue eliminada', 
                'user' => Auth::user()->id, 
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            Log::notice('Registro exitoso en propertyController -> destroy');
        }catch (\Exception $e) {
            Log::error('Error en propertyController -> destroy. Error: ['.$e.']');
            flash('¡Error! Ha ocurrido un problema', 'danger');
        }
        
        flash('Propiedad eliminada exitosamente ', 'success');
        return redirect()->route('propiedades.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id){
        try{
            if ($request -> status == 0) {$status = 1;}else{ $status = 0;}

            $property = property::where('id', $id)
            ->update(['status' => $status]);
            
            Historical::insert([
                'transaction' => 4, 
                'description' => 'Cambio de estatus a la propiedad '. $id, 
                'user' => Auth::user()->id, 
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            Log::notice('Registro exitoso en propertyController -> Update');
            flash('Estatus cambiado exitosamente', 'success');
        }catch (\Exception $e) {
            Log::error('Error en propertyController -> Update. Error: ['.$e.']');
            flash('¡Error! Ha ocurrido un problema', 'danger');
        }
        return back();
    }
}