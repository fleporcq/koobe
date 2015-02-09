<?php namespace App\Http\Controllers;

use App\Commands\ParseBook;
use App\Models\Book;
use App\Models\Download;
use File;
use Flow;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Queue;
use Response;

class BookController extends KoobeController
{

    public function cover($slug)
    {
        self::notFoundIfNull($slug);

        $coverFilePath = storage_path(Book::COVERS_DIRECTORY . DIRECTORY_SEPARATOR . $slug . ".jpg");
        $noCoverFileName = storage_path(Book::COVERS_DIRECTORY . DIRECTORY_SEPARATOR . Book::NO_COVER_FILE . ".jpg");

        if (File::exists($coverFilePath)) {
            $cover = Image::make($coverFilePath);
        } else if (File::exists($noCoverFileName)) {
            $cover = Image::make($noCoverFileName);
        } else {
            abort(404);
        }

        return $cover->response();
    }

    public function download($slug)
    {
        self::notFoundIfNull($slug);

        $epubFilePath = storage_path(Book::EPUBS_DIRECTORY . DIRECTORY_SEPARATOR . $slug . ".epub");

        $book = Book::bySlug($slug);

        self::notFoundIfNull($book);

        if (File::exists($epubFilePath)) {
            $download = new Download($book->id, $this->connectedUser->id);
            $download->save();
            $response = response()->download($epubFilePath);
        } else {
            abort(404);
        }

        return $response;
    }

    public function get()
    {
        $books = Book::with('authors', 'themes')->whereEnabled(true)->paginate(20);
        return Response::json($books);
    }

    public function upload(Encrypter $encrypter)
    {
        return view('book/upload', ['csrfToken' => $encrypter->encrypt(csrf_token())]);
    }

    public function flow(Request $request)
    {
        $flowRequest = new Flow\Request();
        $destination = storage_path('/epubs/' . uniqid() . '.epub');
        $config = new Flow\Config(array(
            'tempDir' => storage_path('/epubs/chunks')
        ));
        $file = new Flow\File($config, $flowRequest);
        $response = Response::make('', 200);

        if ($request->isMethod('get')) {
            if (!$file->checkChunk()) {
                return Response::make('', 204);
            }
        } else {
            if ($file->validateChunk()) {
                $file->saveChunk();
            } else {
                // error, invalid chunk upload request, retry
                return Response::make('', 400);
            }
        }
        if ($file->validateFile() && $file->save($destination)) {
            Queue::push(new ParseBook($destination));
            $response = Response::make('pass some success message to flow.js', 200);
        }
        return $response;
    }
}
