<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Upload;

class UploadsController extends Controller
{

    // returns list of uploads
    public function list() {

        // returns list of uploads by filtering some rows like created_at, updated_at, ...
        return response()->json(
            array(
                'status'    => 1,
                'data'      => Upload::select(array(
                    'id', 'name', 'size', 'chunks', 'chunkSize', 'uploadedChunks',
                    'uploadedFileID', 'uploadedFileName', 'fileType', 'uploaded'
                ))->orderBy('id', 'DESC')->get()
            )
        );
    }


    // upload operation
    public function upload(Request $request) {

        // data extraction by headers
        $content = $request->getContent();
        $fileID = $request->header('X-File-ID');
        $fileName = $request->header('X-File-Name');
        $fileType = $request->header('X-File-Type');
        $fileSize = $request->header('X-File-Size');
        $chunks = $request->header('X-File-Chunks');
        $chunkSize = $request->header('X-File-Chunk-Size');
        $chunk = $request->header('X-File-Current-Chunk');

        // file name size validation
        if(strlen($fileName) > 20) {
            return response()->json(array('status' => 0, 'error' => 'File name must be less than 20 character'));
        }

        // searches if the file has been uploaded before or not
        $upload = Upload::where('uploadedFileID', $fileID)->first();
        $type = 'a';

        if(!$upload) {

            // creating database row, for the first part taken

            $upload = Upload::create(array(
                'uploadedFileID'    => $fileID,
                'uploadedFileName'  => $fileName,
                'fileType'          => $fileType,
                'name'              => time() . str_random(15) . $fileName,
                'chunks'            => $chunks,
                'chunkSize'         => $chunkSize,
                'uploadedChunks'    => $chunk,
                'size'              => $fileSize
            ));

            $type = 'w';

        }

        // writing or appending to the file based on the status
        $fp = fopen(public_path('uploads/' . $upload->name), $type);
        fwrite($fp, $content);
        fclose($fp);

        // checks if all parts uploaded or not
        $uploaded = 0;
        if($upload->uploadedChunks == $upload->chunks) {
            $uploaded = 1;
        }

        // updating after each chunk file uploaded
        $upload->update(array(
            'uploadedChunks'    => $chunk,
            'uploaded'          => $uploaded
        ));

        // returning json response
        return response()->json(array('status' => 1, 'uploaded' => $uploaded));

    }


    // Shows information about an uploaded file before
    public function show($fileID) {

        // find by fileID key
        $upload = \App\Upload::where('uploadedFileID', $fileID)->first();

        // Not found handling
        if(!$upload)
            return response()->json(array('status' => 0, 'error' => 'File Not Found!'));

        // returning response
        return response()->json(array(
            'status'            => 1,
            'uploadedChunks'    => $upload->uploadedChunks,
            'chunks'            => $upload->chunks,
            'chunkSize'         => $upload->chunkSize
        ));

    }

}
