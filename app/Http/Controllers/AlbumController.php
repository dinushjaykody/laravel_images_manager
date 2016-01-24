<?php namespace ImagesManager\Http\Controllers;

use ImagesManager\Http\Requests;
use ImagesManager\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ImagesManager\Album;
use Auth;
use ImagesManager\User;
use ImagesManager\Http\Requests\CreateAlbumRequest;
use ImagesManager\Http\Requests\EditAlbumRequest;



class AlbumController extends Controller {


    /**
     * Constructor
     *
     * middleware auth is used to verify if these users are authenticated
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * To display all the albums
     */
    public function getIndex()
    {


        $user_id = Auth::user()->id;
        $albums = User::find($user_id)->albums;

        return view('album.show', ['albums' => $albums]);
    }

    /**
     * To display create album form
     * @return string
     */
    public function getCreateAlbum()
    {
        return view('album.create-album');
    }

    /**
     * To process create album form
     * @return string
     *
     */
    public function postCreateAlbum(CreateAlbumRequest $request)
    {
        $user = Auth::user();
        $title = $request->get('title');
        $description = $request->get('description');
        Album::create
        (
            [
                'title' => $title,
                'description' => $description,
                'user_id' => $user->id
            ]
        );
        return redirect('validated/albums/')->with(['album_created' => 'The Album has been created.']);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getEditAlbum(Request $request)
    {
        $album_id = $request->get('id');

        $album = Album::find($album_id);

        return view('album.edit-album', ['album' => $album]);
    }

    /**
     * @param EditAlbumRequest $request
     * @return string
     */
    public function postEditAlbum(EditAlbumRequest $request)
    {

        $album = Album::find($request->get('id'));

        $album->title = $request->get('title');
        $album->description = $request->get('description');
        $album->save();


        return redirect('validated/albums')->with(['album_edited' => 'The album has been edited']);

    }

    /**
     * @param DeleteAlbumRequest $request
     * @return string
     */
    public function postDeleteAlbum(DeleteAlbumRequest $request)
    {
        $album = Album::find($request->get('id'));
        $photos = $album->photos;
        $controller = new PhotoController;
        foreach ($photos as $photo)
        {
            $controller->deleteImage($photo->path);
            $photo->delete();
        }
        $album->delete();
        return redirect('validated/albums')->with(['deleted' => 'The album was deleted']);
    }
}
