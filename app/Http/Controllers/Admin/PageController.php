<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Page;
use App\Photo;
use App\Category;
use App\Tag;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Page::all();

        return view('admin.pages.index', compact('pages'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $photos = Photo::all();
        $tags = Tag::all();

        return view('admin.pages.create',compact('categories','photos','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        if(!isset($data['visible'])) {
            $data['visible'] = 0;
        } else {
            $data['visible'] = 1;
        }

        $data['user_id'] = Auth::id();
        //validate
        $page = new Page;
        $page->fill($data);
        $saved = $page->save();
        if(!$saved) {
            return redirect()->back();
        }

        if(isset($data['tags'])) {
            $page->tags()->attach($data['tags']);
        }

        if (isset($data['photos'])) {
            $page->photos()->attach($data['photos']);
        }

        return redirect()->route('admin.pages.show', $page->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.show',compact('page'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $page = Page::findOrFail($id);
        if ($userId != $page->user_id) {
            abort(404);
        }
        $categories = Category::all();
        $photos = Photo::all();
        $tags = Tag::all();

        return view('admin.pages.edit',compact('page','categories','photos','tags'));
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
        $userId = Auth::id();
        $page = Page::findOrFail($id);
        if ($userId != $page->user_id) {
            abort(404);
        }
        $data = $request->all();
        $page->fill($data);
        $page->update();

        $arrayTags = [];
        if(is_array($data['tags'])) {
            foreach($data['tags'] as $tag) {
                $thisTag = Tag::find($tag);
                if($thisTag) {
                    $arrayTags[] = $tag;
                }
            }
        }

        $page->tags()->sync($arrayTags);

        return redirect()->route('admin.pages.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $page = Page::findOrFail($id);
        if ($userId != $page->user_id) {
            abort('404');
        }

        //se abbiamo many to many dobbiamo cancellare record in tabella ponte
        $page->tags()->detach();
        $page->photos()->detach();

        $page->delete();

        return redirect()->back();
    }
}
