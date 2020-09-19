<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\{Tag, Post, Category};
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function index(Request $request)
    {
        // return Post::with('author', 'tags', 'category')->latest()->get();
        $posts = Post::latest()->paginate(10);
        return view('posts.index', [
            'posts' => $posts,
        ]);

        // return view('posts.index', [
        //     'posts' => $posts::pagination(10),
        // ]);
    }

    public function show(Post $post)
    {
        $posts = Post::where('category_id', $post->category_id)->latest()->limit(6)->get();
        return view('posts.show', compact('post', 'posts'));
    }

    public function create()
    {
        return view('posts.create', [
            'post' => new post(),
            'categories' => Category::get(),
            'tags' => Tag::get()
            ]);
    }

    public function store(PostRequest $request)
    {
        // $post = new Post;
        // $post->title = $request->title;
        // $post->slug = \Str::slug($request->title);
        // $post->body = $request->body;
        // $post->save();
        // return redirect()->to('posts/create');
        // Post::create([
        //     'title' => $request->title,
        //     'slug' => \Str::slug($request->title),
        //     'body' => $request->body,
        // ]);

        // Memasukan slug berdasarkan title
        $request->validate([
            'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $attr = $request->all();

        $slug = Str::slug(request('title'));
        $attr['slug'] = $slug;

        $thumbnail = request()->file('thumbnail') ? request()->file('thumbnail')->store("images/photos") : null;



        $attr['category_id'] = request('category');
        $attr['thumbnail'] = $thumbnail;
        // $attr['user_id'] = auth()->id();

        // menimypan post berdasarkan yg sedang login shg dimodel post tidak memasukan user_id
        $post = auth()->user()->posts()->create($attr);

        $post->tags()->attach(request('tags'));


        // session()->flash('error', 'Post Gagal ditambahkan');

        session()->flash('success', 'Post Berhasil ditambahkan');
        return redirect('posts');
        // return back();
    }

    public function edit(Post $post)
    {
        return view('posts.edit', [
            'post' => $post,
            'categories' => Category::get(),
            'tags' => Tag::get()
        ]);
    }

    public function update(PostRequest $request, Post $post)
    {
        $request->validate([
            'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $this->authorize('update', $post);
        if(request()->file('thumbnail')){
            Storage::delete($post->thumbnail);
        } else{
            $thumbnail = $post->thumbnail;
        }


        $attr = $request->all();
        $attr['category_id'] = request('category');
        $attr['thumbnail'] = $thumbnail;

        $post->update($attr);
        $post->tags()->sync(request('tags'));

        session()->flash('success', 'Post Berhasil di update');
        return redirect('posts');
    }

    public function destroy(Post $post)
    {
        // if(auth()->user()->is($post->author)){
        //     $post->tags()->detach();
        //     $post->delete();
        //     session()->flash('success', 'Post Berhasil di Hapus');
        //     return redirect('posts');
        // }else{
            $this->authorize('delete', $post);
            \Storage::delete($post->tumbnail);
            $post->tags()->detach();
            $post->delete();
            session()->flash('error', 'Post Berhasil dihapus');
            return redirect('posts');
    }
}
