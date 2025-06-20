<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::withCount('photos')
            ->with(['photos' => function($query) {
                $query->select('id', 'album_id', 'image_path')->limit(1);
            }])
            ->orderBy('created_at','desc')
            ->get();

        // Append image_url attribute to photos
        $albums->each(function($album) {
            if ($album->photos->isNotEmpty()) {
                $album->photos->each(function($photo) {
                    $photo->append('image_url');
                });
            }
        });

        return Inertia::render('Admin/Albums/AlbumsIndex', ['albums'=>$albums]);
    }

    public function create()
    {
        return Inertia::render('Admin/Albums/AlbumsForm', ['album'=>null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'=>'required|string',
            'event_date'=>'required|date',
            'theme'=>'nullable|string',
            'photos.*'=>'required|image|max:4096',
            'captions.*'=>'nullable|string'
        ]);
        $album = Album::create($data);
        if($request->hasFile('photos')){
            foreach($request->file('photos') as $i=>$file){
                $path=$file->store('albums/'.$album->id,'public');
                Photo::create([
                    'album_id'=>$album->id,
                    'image_path'=>$path,
                    'caption'=>$data['captions'][$i] ?? null
                ]);
            }
        }
        return redirect()->route('admin.albums.index')
            ->with('success','Album créé.');
    }

    public function show(Album $album)
    {
        $album->load('photos');
        return Inertia::render('Admin/Albums/AlbumsShow',['album'=>$album]);
    }

    public function edit(Album $album)
    {
        $album->load('photos');
        return Inertia::render('Admin/Albums/AlbumsForm',['album'=>$album]);
    }

    public function update(Request $request, Album $album)
    {
        $data = $request->validate([
            'title'=>'required|string',
            'event_date'=>'required|date',
            'theme'=>'nullable|string',
            'photos.*'=>'nullable|image|max:4096',
            'captions.*'=>'nullable|string',
            'remove_photos'=>'array'
        ]);
        $album->update($data);
        // remove flagged photos
        if(!empty($data['remove_photos'])){
            foreach($data['remove_photos'] as $pid){
                $photo=Photo::find($pid);
                Storage::disk('public')->delete($photo->image_path);
                $photo->delete();
            }
        }
        // add new
        if($request->hasFile('photos')){
            foreach($request->file('photos') as $i=>$file){
                $path=$file->store('albums/'.$album->id,'public');
                Photo::create([
                    'album_id'=>$album->id,
                    'image_path'=>$path,
                    'caption'=>$data['captions'][$i] ?? null
                ]);
            }
        }
        return redirect()->route('admin.albums.show',$album)
            ->with('success','Album mis à jour.');
    }

    public function destroy(Album $album)
    {
        foreach($album->photos as $photo){
            Storage::disk('public')->delete($photo->image_path);
        }
        $album->delete();
        return redirect()->route('admin.albums.index')
            ->with('success','Album supprimé.');
    }

    public function latest()
    {
        $albums = Album::with('photos')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return response()->json($albums);
    }
}
