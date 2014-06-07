<?php
use Phalcon\Paginator\Adapter\Model as Paginator;

class MediaController extends ControllerBase {

	public function uploadAction() {
        $media = $this->request->getJsonRawBody();

		if ($this->request->hasFiles() == true) {
			$upload = $this->request->getUploadedFiles()[0];

            // get extension of file
            $extension = pathinfo($upload->getname(), PATHINFO_EXTENSION);

            // move tmp file to file path
            $fileName = sprintf('%s-%s', $this->common->generateRandomString(5), date('YmdHis'));

            $type = $this->request->getPost()['type'];
            $size = $upload->getsize();
            $path = 'temp/'.$fileName.'.'.$extension;
            $upload->moveTo($path);

            // create thumbnail and get information
            $uploader = $this->uploader;
            if ($type == "image") {
                // get thumbnail small image
                $thumb_path  =  dirname($path) . '/' .sprintf('%s-thumbnail', $fileName).'.'.$extension;
                $uploader->ResizeToDimension(300, $path, $extension, $thumb_path);
                $thumbnail_size = $uploader->get_image_size($thumb_path);
            }

            if ($type == "video") {
                // get thumnail small image
                $video_thumb_path = $uploader->videoThumnail($path, $fileName);
                $thumb_path  =  dirname($video_thumb_path) . '/' .sprintf('%s-thumbnail', $fileName).'.jpg';
                $uploader->ResizeToDimension(300, $video_thumb_path, 'jpg', $thumb_path);
                $thumbnail_size = $uploader->get_image_size($thumb_path);

                if (filesize($path)/1024 > 5*1024) {
                    $newVideo = $uploader->resize_video($path, $fileName, $extension);
                    $new_path = $newVideo;
                    $size = filesize($newVideo);

                    unlink($path);
                } else {
                    
                    
                }
            }

            $phql = "INSERT INTO Medias (extension, size, origin_path, thumbnail_path, thumbnail_size, created_at, modified_at)
                    VALUES(:extension:, :size:, :origin_path:, :thumbnail_path:, :thumbnail_size:, :created_at:, :modified_at:)";
            $status = $this->modelsManager->executeQuery($phql, array(
                'extension' => $extension,
                'size' => $size,
                'origin_path' => $path,
                'thumbnail_path' => $thumb_path,
                'thumbnail_size' => $thumbnail_size,
                'created_at' => date('Y-m-d H:i:s'),
                'modified_at' => date('Y-m-d H:i:s')
            ));

            // set response to json
            $this->setJsonResponse();

            // check if inserton was successful
            if ($status->success() == true) {
                // change the HTTP status
                $this->response->setStatusCode(201, "Created");

                $media = $status->getModel();
                // $media->id = $status->getModel()->id;
                $media->full_path = BASE_URL.$status->getModel()->origin_path;
                $media->full_thumbnail_path = BASE_URL.$status->getModel()->thumbnail_path;

                return array('status' => 'OK', 'data' => $media);

            } else {
                $this->response->setStatusCode(409, "Conflict");

                // send errors to client
                $errors = array();
                foreach ($status->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                return array('status' => 'ERROR', 'message' => $errors);
            }
		}
	}

    public function indexAction() {
        // start from 1
        $currentPage = 1;

        $phql = "SELECT * FROM Medias";
        $result = $this->modelsManager->executeQuery($phql);

        $page = new Paginator(array(
            'data' => $result,
            'limit' => 5,
            'page' => $currentPage
        ));
        $medias = $page->getPaginate();

        // set response to json
        $this->setJsonResponse();

        $data = array();
        foreach ($medias->items as $media) {
            $data[] = array(
                'id' => $media->id,
                'origin_path' => BASE_URL.$media->origin_path,
                'thumbnail_path' => BASE_URL.$media->thumbnail_path,
                'thumbnail_size' => $media->thumbnail_size,
            );
        }

        return array('status' => 'OK', 'data' => $data);
    }
}