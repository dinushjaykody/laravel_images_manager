<?php namespace ImagesManager\Http\Controllers;

use Carbon\Carbon;
use ImagesManager\Http\Requests;
use ImagesManager\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ImagesManager\Http\Requests\ShowPhotosRequest;
use ImagesManager\Album;
use ImagesManager\Photo;
use ImagesManager\Http\Requests\CreatePhotoRequest;
use ImagesManager\Http\Requests\EditPhotoRequest;
use ImagesManager\Http\Requests\DeletePhotoRequest;


class PhotoController extends Controller {

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
    public function getIndex(ShowPhotosRequest $request)
    {

        //echo url(); exit;
        $photos = Album::find($request->get('id'))->photos;
        return view('photos.show', ['photos' => $photos, 'id'=> $request->get('id')]);
    }

    /**
     * To display create album form
     * @return string
     */
    public function getCreatePhoto(Request $request)
    {
        $album_id = $request->get('id');

        return view('photos.create-photo', ['id' => $album_id] );
    }

    /**
     * To process create album form
     * @return string
     *
     */
    public function postCreatePhoto(CreatePhotoRequest $request)
    {

        $image = $request->file('image');
        $id = $request->get('id');

      Photo::create(

          [
              'title' => $request->get('title'),
              'description' => $request->get('description'),
              'path' => $this->createImage($image),
              'album_id' => $id
          ]
      );

        return redirect("validated/photos?id=$id")->with(['photo_created' => 'The photo has been created']);
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    public function getEditPhoto($id)
    {
        $photo = Photo::find($id);
        return view('photos.edit-photo', ['photo' => $photo] );
    }

    /**
     * @param EditPhotoRequest $request
     * @return string
     */
    public function postEditPhoto(EditPhotoRequest $request)
    {
        $photo = Photo::find($request->get('id'));
        $photo->title = $request->get('title');
        $photo->description = $request->get('description');
        if($request->hasFile('image'))
        {
            $this->deleteImage($photo->path);
            $image = $request->file('image');
            $photo->path = $this->createImage($image);
        }
        $photo->save();
        return redirect("validated/photos?id=$photo->album_id")->with(['edited' => 'The photo was edited']);
    }

    /**
     * @param DeletePhotoRequest $request
     * @return string
     */
    public function postDeletePhoto(DeletePhotoRequest $request)
    {
        $photo = Photo::find($request->get('id'));
        $this->deleteImage($photo->path);
        $photo->delete();
        return redirect("validated/photos?id=$photo->album_id")->with(['deleted' => 'The photo was deleted']);
    }

    private function createImage($image)
    {
        $path = 'images/';
        $name = sha1(Carbon::now()).'.'.$image->guessExtension();
        $image->move(getcwd().$path,$name);

        return $path.$name;
    }

    public function deleteImage($oldpath)
    {
        $oldpath = getcwd().$oldpath;
        unlink(realpath($oldpath));
    }

}
